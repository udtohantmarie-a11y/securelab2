<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; 

// ==========================================================
// 1. API to receive logs from ESP32 Hardware
// ==========================================================
Route::post('/hardware-log', function (Request $request) {
    // DYNAMIC DEVICE & ROOM DETECTION
    $deviceCode = $request->input('device_code', 'ESP32-BSIS-01');
    $device = DB::table('devices')->where('device_code', $deviceCode)->first();
    
    if (!$device) {
        return response()->json(['status' => 'error', 'message' => 'Unregistered device'], 404);
    }

    $roomId = $device->room_id;
    $deviceId = $device->device_id;

    $eventType = $request->input('event_type');
    $description = $request->input('description');
    $doorState = $request->input('door_state', 'locked'); 

    $userId = null; 
    $userName = $request->input('user_name', 'System'); 
    
    $action = $doorState === 'unlocked' ? 'unlock' : 'lock';
    $method = 'hardware_keypad';
    
    $skipAuditLog = false; 
    $notifiedUserId = null;

    // DOORBELL LOGIC
    if ($eventType === 'DOORBELL') {
        $skipAuditLog = true; 
        
        $lastUserLog = DB::table('audit_logs')
            ->where('room_id', $roomId)
            ->whereNotNull('user_id')
            ->orderBy('logged_at', 'desc')
            ->first();
            
        $notifiedUserId = $lastUserLog ? $lastUserLog->user_id : null;

        DB::table('doorbell_events')->insert([
            'room_id' => $roomId,
            'device_id' => $deviceId,
            'buzzer_triggered' => 1,
            'pwa_notified' => 1,
            'notified_user_id' => $notifiedUserId, 
            'pressed_at' => now()
        ]);
    } 
    elseif ($eventType === 'DOOR STATUS') {
        $skipAuditLog = true; 
    }
    elseif ($eventType === 'AUTO-LOCK') {
        $method = 'auto_lock';
    }
    elseif ($eventType === 'KEYPAD ACCESS') {
        $usedPin = $request->input('pin_code');
        $assignment = DB::table('room_assignments')
            ->where('room_id', $roomId)
            ->where('is_active', 1)
            ->where('pin_code', $usedPin)
            ->first();
        if ($assignment) {
            $userId = $assignment->user_id;
            $user = DB::table('users')->where('user_id', $userId)->first();
            if ($user) $userName = $user->full_name;
        }
    } 
    elseif ($eventType === 'NORMAL EXIT') {
        $userId = null;
        $userName = 'Inside User';
        $method = 'emergency_button';
        $action = 'unlock';
    } 
    elseif ($eventType === 'SECURITY ALERT') {
        $action = 'attempt_failed';
    } 
    elseif ($eventType === 'FINGER ENROLLED') {
        $room = DB::table('rooms')->where('room_id', $roomId)->first();
        if ($room && $room->enroll_user_id) {
            $userId = $room->enroll_user_id;
            $user = DB::table('users')->where('user_id', $userId)->first();
            if ($user) $userName = $user->full_name;
        }
    } 
    elseif ($eventType === 'FINGER DELETED') {
        $deletedId = $request->input('delete_fp_id');
        if ($deletedId) {
            $fp = DB::table('fingerprints')
                ->where('room_id', $roomId)
                ->where('template_slot', $deletedId)
                ->first();
            if ($fp) {
                $userId = $fp->user_id;
                $user = DB::table('users')->where('user_id', $userId)->first();
                if ($user) $userName = $user->full_name;
            }
        }
    } 
    elseif ($eventType === 'BIOMETRIC ACCESS') {
        $matchedId = $request->input('matched_fp_id');
        if ($matchedId) {
            $fp = DB::table('fingerprints')
                ->where('room_id', $roomId)
                ->where('template_slot', $matchedId)
                ->where('is_active', 1)
                ->first();
            if ($fp) {
                $userId = $fp->user_id;
                $user = DB::table('users')->where('user_id', $userId)->first();
                if ($user) $userName = $user->full_name;
            }
        }
        $method = 'fingerprint';
    } 
    elseif ($eventType === 'WEB DASHBOARD' || $eventType === 'REMOTE ACCESS') {
        $method = 'remote_pwa';
        $skipAuditLog = true; 
        
        $lastRemote = DB::table('audit_logs')
            ->where('room_id', $roomId)
            ->whereNotNull('user_id')
            ->whereIn('action', ['remote_lock', 'remote_unlock'])
            ->orderBy('logged_at', 'desc')
            ->first();

        if ($lastRemote && $userName === 'System') {
            $userId = $lastRemote->user_id;
            $user = DB::table('users')->where('user_id', $userId)->first();
            if ($user) $userName = $user->full_name;
        }
    } 
    elseif ($eventType === 'SYSTEM BOOT' || $eventType === 'SYSTEM') {
        $method = 'auto_schedule';
    }
    elseif ($eventType === 'SET OCCUPIED' || $eventType === 'SET VACANT') {
        $method = 'hardware_keypad';
    }

    if (is_null($userId) && $userName !== 'System' && $userName !== '' && !str_contains($userName, 'Inside User')) {
        $matchedUser = DB::table('users')->where('full_name', $userName)->first();
        if ($matchedUser) {
            $userId = $matchedUser->user_id;
        }
    }

    // ALWAYS SAVE TO AUDIT LOGS
    if (!$skipAuditLog) {
        DB::table('audit_logs')->insert([
            'room_id' => $roomId,
            'device_id' => $deviceId,
            'user_id' => $userId, 
            'action' => $action,
            'notes' => $description,
            'method' => $method,
            'door_state_after' => $doorState,
            'logged_at' => now()
        ]);
    }

    // ==========================================================
    // APPLY THE "FAILED SCAN THRESHOLD" TO THE SYSTEM
    // ==========================================================
    if ($eventType === 'SECURITY ALERT') {
        $maxAttempts = (int) cache('set_failed_threshold', 3);

        $failedCount = DB::table('audit_logs')
            ->where('room_id', $roomId)
            ->where('action', 'attempt_failed')
            ->where('logged_at', '>=', now()->subMinutes(3))
            ->count();

        if ($failedCount >= $maxAttempts) {
            $alertType = 'invalid_passcode';
            $severity = 'warning';
            
            if (str_contains($description, 'Revoked') || str_contains($description, 'Unrecognized fingerprint')) {
                $alertType = 'unregistered_fingerprint';
                $severity = 'critical';
            } elseif (str_contains($description, 'left open')) {
                $alertType = 'door_left_open';
                $severity = 'warning';
            } elseif (str_contains($description, 'forced open')) {
                $alertType = 'door_forced_open';
                $severity = 'critical';
            }

            DB::table('intrusion_alerts')->insert([
                'room_id' => $roomId,
                'device_id' => $deviceId,
                'alert_type' => $alertType,
                'severity' => $severity,
                'description' => $description ?: 'Unauthorized attempt limit reached (' . $failedCount . ' attempts).',
                'is_resolved' => 0,
                'triggered_at' => now()
            ]);

            if (cache('set_audit_alerts', true)) {
                $adminEmails = DB::table('users')->where('role', 'Admin')->pluck('email');
                if ($adminEmails->isNotEmpty()) {
                    $alertMessage = "INTRUSION DETECTED at Room ID: $roomId. Threshold Reached: $failedCount failed attempts. Reason: " . ($description ?: 'Unauthorized attempt');
                    try {
                        foreach ($adminEmails as $email) {
                            \Illuminate\Support\Facades\Mail::to($email)->queue(new \App\Mail\IntrusionAlertMail($alertMessage));
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to queue Intrusion Email: ' . $e->getMessage());
                    }
                }
            }
        }
    }

    if ($eventType === 'FINGER ENROLLED') {
        $room = DB::table('rooms')->where('room_id', $roomId)->first();
        $enrolledId = $request->input('enroll_fp_id') ?? ($room->enroll_fp_id ?? null);

        if ($room && $room->enroll_user_id && $enrolledId) {
            DB::table('fingerprints')->updateOrInsert(
                [ 'room_id' => $roomId, 'template_slot' => $enrolledId ],
                [
                    'user_id'      => $room->enroll_user_id,
                    'enrolled_by'  => $room->enroll_user_id,
                    'finger_label' => 'Right Index',
                    'is_active'    => 1,
                    'enrolled_at'  => now(),
                ]
            );
        }
    }

    if ($eventType === 'FINGER DELETED') {
        $deletedId = $request->input('delete_fp_id');
        if ($deletedId) {
            DB::table('fingerprints')
                ->where('room_id', $roomId)
                ->where('template_slot', $deletedId)
                ->update(['is_active' => 0]);
        }
    }

    $roomUpdates = ['door_state' => $doorState];

    if ($doorState === 'unlocked' && in_array($eventType, ['BIOMETRIC ACCESS', 'KEYPAD ACCESS', 'WEB DASHBOARD', 'REMOTE ACCESS'])) {
        $roomUpdates['occupancy_status'] = 'occupied';
    }

    if ($eventType === 'SET OCCUPIED') {
        $roomUpdates['occupancy_status'] = 'occupied';
    } elseif ($eventType === 'SET VACANT') {
        $roomUpdates['occupancy_status'] = 'vacant';
    }

    DB::table('rooms')->where('room_id', $roomId)->update($roomUpdates);

    if ($eventType === 'DOOR STATUS' || $eventType === 'SYSTEM') {
        return response()->json(['status' => 'success']);
    }

    $sendNotification = true;

    if ($eventType === 'DOORBELL') {
        $notifType = 'doorbell';
        $notifTitle = '🔔 Doorbell Rung';
        $notifBody = 'A visitor is at the door. Please check the laboratory entrance.';
        
        $targetUser = null;
        if ($notifiedUserId) {
            $targetUser = DB::table('users')->where('user_id', $notifiedUserId)->where('is_active', 1)->first();
        }

        if ($targetUser) {
            $targetUsers = collect([$targetUser]); 
        } else {
            $targetUsers = DB::table('users')->where('role', 'Admin')->where('is_active', 1)->get();
        }
    } else {
        $targetUsers = DB::table('users')->where('is_active', 1)->get();
        
        if ($eventType === 'SECURITY ALERT') {
            $maxAttempts = (int) cache('set_failed_threshold', 3);
            $failedCount = DB::table('audit_logs')->where('room_id', $roomId)->where('action', 'attempt_failed')->where('logged_at', '>=', now()->subMinutes(3))->count();

            if ($failedCount >= $maxAttempts) {
                $notifType = 'intrusion_alert';
                if (str_contains($description, 'left open')) {
                    $notifTitle = '⚠️ Security Alert: Door Left Open';
                    $notifBody = 'The laboratory door has been left open for more than 1 minute.';
                } elseif (str_contains($description, 'forced open')) {
                    $notifTitle = '🚨 CRITICAL: Door Forced Open!';
                    $notifBody = 'The laboratory door was opened forcibly without unlocking!';
                } elseif (str_contains($description, 'Revoked')) {
                    $notifTitle = '⚠️ Intrusion Alert: Revoked Fingerprint';
                    $notifBody = 'A fingerprint that no longer has active access was used at the laboratory door.';
                } elseif (str_contains($description, 'Unrecognized fingerprint')) { 
                    $notifTitle = '⚠️ Intrusion Alert: Unknown Fingerprint';
                    $notifBody = 'Someone attempted to use an unregistered fingerprint at the laboratory door.';
                } else {
                    $notifTitle = '⚠️ Intrusion Alert: Invalid Passcode';
                    $notifBody = 'Someone attempted to enter an incorrect PIN code at the laboratory door.';
                }
            } else {
                $sendNotification = false;
            }
        } 
        elseif ($eventType === 'NORMAL EXIT') {
            $notifType = 'door_unlocked';
            $notifTitle = '🚪 Exit Button Pressed';
            $notifBody = 'someone press exit button';
        } 
        elseif ($eventType === 'FINGER ENROLLED') {
            $notifType = 'system_alert';
            $notifTitle = '✅ Fingerprint Enrolled';
            $notifBody = $userName !== 'System' ? "{$userName} successfully enrolled a new fingerprint." : $description;
        } 
        elseif ($eventType === 'FINGER DELETED') {
            $notifType = 'system_alert';
            $notifTitle = '🗑️ Fingerprint Removed';
            $notifBody = $userName !== 'System' ? "{$userName}'s fingerprint was successfully removed." : $description;
        } 
        elseif ($eventType === 'SET OCCUPIED' || $eventType === 'SET VACANT') {
            $sendNotification = false; 
        } 
        elseif ($eventType === 'AUTO-LOCK') {
            $notifType = 'door_locked';
            $notifTitle = '🔒 Auto-Locked';
            $notifBody = $description; 
        } 
        else {
            $notifType = $doorState === 'unlocked' ? 'door_unlocked' : 'door_locked'; 
            $actionWord = $doorState === 'unlocked' ? 'unlocked' : 'locked';
            $methodWord = $eventType === 'KEYPAD ACCESS' ? 'Keypad Passcode' : ($eventType === 'BIOMETRIC ACCESS' ? 'Fingerprint' : ($eventType === 'WEB DASHBOARD' || $eventType === 'REMOTE ACCESS' ? 'Web Dashboard' : 'System'));
            $notifTitle = ($doorState === 'unlocked' ? '🔓 Door Unlocked' : '🔒 Door Locked') . ($eventType === 'KEYPAD ACCESS' ? ' (Passcode)' : '');
            $notifBody = "{$userName} successfully {$actionWord} the room via {$methodWord}.";
        }
    }

    if ($sendNotification) {
        foreach ($targetUsers as $recipient) {
            DB::table('notifications')->insert([
                'user_id' => $recipient->user_id,
                'room_id' => $roomId,
                'type'    => $notifType,
                'title'   => $notifTitle,
                'body'    => $notifBody,
                'is_read' => 0,
                'sent_at' => now()
            ]);
        }
    }

    return response()->json(['status' => 'success']);
});

// ==========================================================
// 2. API for ESP32 to fetch all configured PINs (includes names)
// ==========================================================
Route::get('/get-room-pin/{room_id}', function ($room_id) {
    $assignments = DB::table('room_assignments')
        ->join('users', 'room_assignments.user_id', '=', 'users.user_id')
        ->where('room_assignments.room_id', $room_id)
        ->where('room_assignments.is_active', 1)
        ->where('room_assignments.access_level', '!=', 'readonly')
        ->where(function($query) {
            $query->whereNull('room_assignments.valid_until')
                  ->orWhereDate('room_assignments.valid_until', '>=', now()->toDateString());
        })
        ->whereNotNull('room_assignments.pin_code')
        ->where('room_assignments.pin_code', '!=', '')
        ->select('room_assignments.pin_code', 'users.full_name')
        ->get();

    if ($assignments->isEmpty()) {
        return response('', 200)->header('Content-Type', 'text/plain');
    }

    $pairs = $assignments->map(function ($a) {
        return $a->pin_code . ':' . $a->full_name;
    });

    return response($pairs->implode(','), 200)->header('Content-Type', 'text/plain');
});

// ==========================================================
// 3. API to check the state (Remote Control)
// ==========================================================
Route::get('/get-room-state/{room_id}', function ($room_id) {
    $room = DB::table('rooms')->where('room_id', $room_id)->first();
    return response($room ? $room->door_state : "locked", 200)->header('Content-Type', 'text/plain');
});

// ==========================================================
// 4. API for Health Ping (Vitals & Physical Door Status)
// ==========================================================
Route::post('/device-health', function (Request $request) {
    $deviceCode = $request->input('device_code', 'ESP32-BSIS-01');
    $device = DB::table('devices')->where('device_code', $deviceCode)->first();

    if (!$device) {
        return response()->json(['status' => 'error', 'message' => 'Device not registered'], 404);
    }

    $physicalDoorState = $request->input('physical_door_state', 'closed');

    DB::table('device_health_logs')->insert([
        'device_id' => $device->device_id, 
        'room_id' => $device->room_id,
        'wifi_signal_dbm' => $request->input('wifi_signal', -50),
        'battery_voltage' => $request->input('battery_voltage', 12.00), 
        'battery_pct' => $request->input('battery_pct', 100),       
        'power_source' => $request->input('power_source', 'ac_main'), 
        'door_state' => $request->input('door_state', 'locked'),
        'uptime_seconds' => $request->input('uptime', 0),
        'sd_total_mb' => $request->input('sd_total', 0), 
        'sd_used_mb' => $request->input('sd_used', 0),   
        'recorded_at' => now()
    ]);

    DB::table('devices')->where('device_id', $device->device_id)->update([
        'is_online' => 1, 
        'last_ping_at' => now(), 
        'ip_address' => $request->ip()
    ]);

    if (Schema::hasColumn('rooms', 'physical_door_state')) {
        DB::table('rooms')->where('room_id', $device->room_id)->update([
            'physical_door_state' => $physicalDoorState
        ]);
    }

    return response()->json(['status' => 'success']);
});

// ==========================================================
// 5. API for Logs Auto-Refresh
// ==========================================================
Route::get('/latest-log-id', function () {
    $latestLog = DB::table('audit_logs')->orderBy('log_id', 'desc')->first();
    return response()->json(['latest_id' => $latestLog ? $latestLog->log_id : 0]);
});

// ==========================================================
// 6. API for Alerts Auto-Refresh
// ==========================================================
Route::get('/latest-alert-id', function () {
    $latestAlert = DB::table('intrusion_alerts')->orderBy('alert_id', 'desc')->first();
    return response()->json(['latest_id' => $latestAlert ? $latestAlert->alert_id : 0]);
});

// ==========================================================
// 7. API: For SD Card Bulk Sync (Offline to Online)
// ==========================================================
Route::post('/sync-bulk-logs', function (Request $request) {
    $deviceCode = $request->input('device_code', 'ESP32-BSIS-01');
    $device = DB::table('devices')->where('device_code', $deviceCode)->first();
    
    if (!$device) {
        return response()->json(['status' => 'error', 'message' => 'Device not found'], 404);
    }

    $roomId = $device->room_id;
    $deviceId = $device->device_id;
    $logs = $request->input('logs'); 

    if (!$logs || !is_array($logs)) {
        return response()->json(['status' => 'error', 'message' => 'No logs provided or invalid format'], 400);
    }

    $insertedCount = 0;

    foreach ($logs as $log) {
        $eventType = $log['event_type'] ?? 'UNKNOWN';
        $description = $log['description'] ?? 'Offline log synced from SD Card';
        $doorState = $log['door_state'] ?? 'locked';
        $loggedAt = $log['timestamp'] ?? now(); 

        $userId = null;
        $action = $doorState === 'unlocked' ? 'unlock' : 'lock';

        if ($eventType === 'KEYPAD ACCESS') {
            $assignment = DB::table('room_assignments')->where('room_id', $roomId)->whereNotNull('pin_code')->first();
            if ($assignment) {
                $userId = $assignment->user_id;
            }
        } elseif ($eventType === 'SECURITY ALERT') {
            $action = 'attempt_failed';
        }

        DB::table('audit_logs')->insert([
            'room_id' => $roomId,
            'device_id' => $deviceId,
            'user_id' => $userId,
            'action' => $action,
            'notes' => '[OFFLINE SYNC] ' . $description,
            'method' => 'hardware_keypad',
            'door_state_after' => $doorState,
            'logged_at' => $loggedAt
        ]);
        $insertedCount++;
    }
    return response()->json(['status' => 'success', 'synced_count' => $insertedCount]);
});

// ==========================================================
// 8. Poll Commands (ALARM TRIGGER & MAIN WIFI ARE PASSED HERE)
// ==========================================================
Route::get('/poll-commands/{room_id}', function ($room_id) {
    $room = DB::table('rooms')->where('room_id', $room_id)->first();
    if (!$room) return response()->json(['status' => 'error'], 404);

    $lastRemote = DB::table('audit_logs')
        ->join('users', 'audit_logs.user_id', '=', 'users.user_id')
        ->where('audit_logs.room_id', $room_id)
        ->whereIn('audit_logs.action', ['remote_lock', 'remote_unlock'])
        ->orderBy('audit_logs.logged_at', 'desc')
        ->select('users.full_name')
        ->first();

    $maxAttempts = (int) cache('set_failed_threshold', 3);

    $failedCount = DB::table('audit_logs')
        ->where('room_id', $room_id)
        ->where('action', 'attempt_failed')
        ->where('logged_at', '>=', now()->subMinutes(3))
        ->count();

    $triggerAlarm = $failedCount >= $maxAttempts;
    
    // 🟢 FETCH MAIN WIFI SETTINGS FOR ESP32 TO RECEIVE
    $mainWifiSSID = cache('hardware_main_wifi_ssid', '');
    $mainWifiPass = cache('hardware_main_wifi_pass', '');

    return response()->json([
        'status' => 'success',
        'door_state' => $room->door_state,
        'enrollment_mode' => (bool) $room->enrollment_mode,
        'enroll_user_id' => $room->enroll_user_id,
        'enroll_fp_id' => $room->enroll_fp_id,
        'delete_mode' => (bool) $room->delete_mode,
        'delete_fp_id' => $room->delete_fp_id,
        'occupancy_status' => $room->occupancy_status,
        'remote_user_name' => $lastRemote ? $lastRemote->full_name : null,
        'backup_ssid' => $room->backup_ssid,
        'backup_password' => $room->backup_password,
        'alarm_triggered' => $triggerAlarm,
        'main_ssid' => $mainWifiSSID,     // 🟢 SENDING MAIN WIFI
        'main_password' => $mainWifiPass  // 🟢 SENDING MAIN WIFI PASS
    ]);
});

// ==========================================================
// 9. Reset the enrollment flag after successful enrollment
// ==========================================================
Route::post('/clear-enrollment/{room_id}', function ($room_id) {
    DB::table('rooms')->where('room_id', $room_id)->update([
        'enrollment_mode' => 0,
        'enroll_user_id' => null,
        'enroll_fp_id' => null
    ]);
    return response()->json(['status' => 'success']);
});

// ==========================================================
// 10. Reset the delete flag after successful deletion from sensor
// ==========================================================
Route::post('/clear-deletion/{room_id}', function ($room_id) {
    DB::table('rooms')->where('room_id', $room_id)->update([
        'delete_mode' => 0,
        'delete_fp_id' => null
    ]);
    return response()->json(['status' => 'success']);
});

// ==========================================================
// 11. Verify with the server if the fingerprint match
// is still valid before unlocking the door (includes user name)
// ==========================================================
Route::get('/verify-fingerprint/{room_id}/{fp_id}', function ($room_id, $fp_id) {
    $fp = DB::table('fingerprints')
        ->where('room_id', $room_id)
        ->where('template_slot', $fp_id)
        ->where('is_active', 1)
        ->first();

    if (!$fp) return response()->json(['authorized' => false]);

    $assignment = DB::table('room_assignments')
        ->where('room_id', $room_id)
        ->where('user_id', $fp->user_id)
        ->where('is_active', 1)
        ->where('access_level', '!=', 'readonly') // Harangan ang readonly
        ->where(function($query) {                // Harangan ang expired
            $query->whereNull('valid_until')
                  ->orWhereDate('valid_until', '>=', now()->toDateString());
        })
        ->first();

    $user = DB::table('users')
        ->where('user_id', $fp->user_id)
        ->where('is_active', 1)
        ->first();

    $authorized = (bool) ($assignment && $user);

    return response()->json([
        'authorized' => $authorized,
        'name' => $authorized ? $user->full_name : null,
    ]);
});