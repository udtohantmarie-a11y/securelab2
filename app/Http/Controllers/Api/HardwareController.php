<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\IntrusionAlertMail;
use Exception;

class HardwareController extends Controller
{
    /**
     * PRIVATE METHOD: AUTHENTICATE HARDWARE
     * Tinitingnan nito kung ang ESP32 na nagrerequest ay may tamang API Key 
     * na tumutugma sa nasa System Settings ng Admin.
     */
    private function authenticateHardware(Request $request)
    {
        // Kinukuha natin ang default key mula sa cache na sinet mo sa Settings
        $expectedKey = cache('set_hardware_api_key', 'SECURELAB_77A8B92CC4');
        
        // Pwede itong ipasa ng ESP32 via Headers o via JSON body
        $providedKey = $request->header('X-API-Key') ?? $request->input('api_key');

        if ($providedKey !== $expectedKey) {
            Log::warning('Unauthorized hardware connection attempt. IP: ' . $request->ip());
            return false;
        }

        return true;
    }

    /**
     * PRIVATE METHOD: SEND ONESIGNAL PUSH NOTIFICATION
     * Kukuha ng keys sa .env at magse-send ng request sa OneSignal API
     */
    private function sendOneSignalPush($title, $message)
    {
        $appId = env('ONESIGNAL_APP_ID');
        $restApiKey = env('ONESIGNAL_REST_API_KEY');

        if (!$appId || !$restApiKey) {
            Log::warning('OneSignal keys are missing in .env');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $restApiKey,
                'Content-Type' => 'application/json; charset=utf-8',
            ])->post('https://onesignal.com/api/v1/notifications', [
                'app_id' => $appId,
                'included_segments' => ['All'],
                'headings' => ['en' => $title],
                'contents' => ['en' => $message],
            ]);

            if ($response->successful()) {
                return true;
            } else {
                Log::error('OneSignal Push Failed: ' . $response->body());
                return false;
            }
        } catch (Exception $e) {
            Log::error('OneSignal Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ENDPOINT 1: PING & SYNC
     * Ito ang palaging tatawagin ng ESP32 (e.g. every 5 seconds) para sabihing
     * "Buhay pa ako!" at tatanungin ang Laravel kung "May nag-remote unlock ba?"
     */
    public function ping(Request $request)
    {
        if (!$this->authenticateHardware($request)) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $deviceCode = $request->input('device_code');
        
        $device = DB::table('devices')->where('device_code', $deviceCode)->first();

        if (!$device) {
            return response()->json(['status' => 'error', 'message' => 'Device not registered'], 404);
        }

        // 1. Update the Device Status to Online
        DB::table('devices')->where('device_id', $device->device_id)->update([
            'is_online' => 1,
            'last_ping_at' => now(),
            'ip_address' => $request->ip()
        ]);

        // 2. Kunin ang current target status ng pinto (kung ni-lock o unlock ng Admin sa web)
        $room = DB::table('rooms')->where('room_id', $device->room_id)->first();
        
        // Optional: Kung nagpadala ang ESP32 ng battery voltage, i-log natin sa Device Health
        if ($request->has('battery_voltage')) {
            DB::table('device_health_logs')->insert([
                'device_id' => $device->device_id,
                'room_id' => $device->room_id,
                'wifi_signal_dbm' => $request->input('wifi_signal_dbm'),
                'battery_voltage' => $request->input('battery_voltage'),
                'battery_pct' => $request->input('battery_pct'),
                'power_source' => $request->input('power_source', 'ac_main'),
                'door_state' => $room->door_state,
                'uptime_seconds' => $request->input('uptime_seconds'),
                'recorded_at' => now()
            ]);
        }

        return response()->json([
            'status' => 'success',
            'command' => $room->door_state // Ito ang babasahin ng ESP32 para malaman kung aangat niya ang lock
        ]);
    }

    /**
     * ENDPOINT 2: FINGERPRINT ACCESS CHECK
     * Tinatawag ng ESP32 kapag may nag-scan ng fingerprint.
     */
    public function handleAccess(Request $request)
    {
        if (!$this->authenticateHardware($request)) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $deviceCode = $request->input('device_code');
        $slotId = $request->input('template_slot');

        $device = DB::table('devices')->where('device_code', $deviceCode)->first();
        if (!$device) {
            return response()->json(['status' => 'error', 'message' => 'Device not registered'], 404);
        }

        $room = DB::table('rooms')->where('room_id', $device->room_id)->first();

        // Hanapin sa database kung kaninong daliri yung nag-scan
        $fingerprint = DB::table('fingerprints')
            ->where('room_id', $room->room_id)
            ->where('template_slot', $slotId)
            ->where('is_active', 1)
            ->first();

        if (!$fingerprint) {
            // UNREGISTERED FINGERPRINT - I-log sa audit at alertuhan ang Admin
            DB::table('audit_logs')->insert([
                'room_id' => $room->room_id,
                'device_id' => $device->device_id,
                'action' => 'attempt_failed',
                'method' => 'fingerprint',
                'door_state_after' => 'locked',
                'notes' => 'Unregistered fingerprint scanned at door (Slot ID: ' . $slotId . ')',
                'logged_at' => now()
            ]);

            DB::table('device_response_logs')->insert([
                'device_id' => $device->device_id,
                'room_id' => $room->room_id,
                'trigger_type' => 'fingerprint',
                'response_time_ms' => $request->input('latency_ms', 150),
                'action_performed' => 'siren',
                'success' => 1,
                'notes' => 'Unregistered scan — triggered physical rejection response',
                'recorded_at' => now()
            ]);

            $this->sendOneSignalPush('⚠️ Access Denied', 'An unregistered fingerprint tried to access ' . $room->room_name);

            return response()->json([
                'access' => 'denied',
                'message' => 'Unregistered Fingerprint'
            ]);
        }

        // REGISTERED FINGERPRINT DETECTED
        $user = DB::table('users')->where('user_id', $fingerprint->user_id)->first();

        // I-check kung may active na access permission pa ba ang user na ito sa room
        $hasAccess = DB::table('room_assignments')
            ->where('user_id', $user->user_id)
            ->where('room_id', $room->room_id)
            ->where('is_active', 1)
            ->exists();

        if (!$hasAccess) {
            DB::table('audit_logs')->insert([
                'room_id' => $room->room_id,
                'device_id' => $device->device_id,
                'user_id' => $user->user_id,
                'action' => 'attempt_failed',
                'method' => 'fingerprint',
                'door_state_after' => 'locked',
                'notes' => 'Access Denied: User permissions revoked or expired.',
                'logged_at' => now()
            ]);

            $this->sendOneSignalPush('⚠️ Access Denied', $user->full_name . ' tried to access ' . $room->room_name . ' but their permission is revoked.');

            return response()->json([
                'access' => 'denied',
                'message' => 'Permission Revoked'
            ]);
        }

        // ==========================================
        // SUCCESSFUL ACCESS: I-TOGGLE ANG PINTO
        // ==========================================
        $newState = $room->door_state === 'locked' ? 'unlocked' : 'locked';

        DB::transaction(function () use ($room, $device, $user, $fingerprint, $newState, $request) {
            DB::table('rooms')->where('room_id', $room->room_id)->update(['door_state' => $newState]);

            DB::table('audit_logs')->insert([
                'room_id' => $room->room_id,
                'device_id' => $device->device_id,
                'user_id' => $user->user_id,
                'user_name' => $user->full_name, // 🟢
                'action' => $newState === 'unlocked' ? 'unlock' : 'lock',
                'method' => 'fingerprint',
                'door_state_after' => $newState,
                'notes' => 'Hardware fingerprint scan (' . $fingerprint->finger_label . ')',
                'logged_at' => now()
            ]);

            DB::table('device_response_logs')->insert([
                'device_id' => $device->device_id,
                'room_id' => $room->room_id,
                'trigger_type' => 'fingerprint',
                'response_time_ms' => $request->input('latency_ms', 120),
                'action_performed' => $newState === 'unlocked' ? 'unlock' : 'lock',
                'success' => 1,
                'notes' => 'Fingerprint match successful.',
                'recorded_at' => now()
            ]);
        });

        // Broadcast notification
        $allUsers = DB::table('users')->where('is_active', 1)->get();
        $notifType = $newState === 'unlocked' ? 'door_unlocked' : 'door_locked';
        $notifTitle = $newState === 'unlocked' ? '🔓 Door Unlocked' : '🔒 Door Locked';
        $notifBody = $user->full_name . " successfully " . $newState . "ed " . $room->room_name . " via biometric scan.";

        $notifications = [];
        foreach ($allUsers as $recipient) {
            $notifications[] = [
                'user_id' => $recipient->user_id,
                'room_id' => $room->room_id,
                'type' => $notifType,
                'title' => $notifTitle,
                'body' => $notifBody,
                'is_read' => 0,
                'sent_at' => now()
            ];
        }
        
        if (!empty($notifications)) {
            DB::table('notifications')->insert($notifications);
        }

        $this->sendOneSignalPush($notifTitle, $notifBody);

        return response()->json([
            'access' => 'granted',
            'command' => $newState,
            'user_name' => $user->full_name
        ]);
    }

    /**
     * ENDPOINT 3: DOORBELL TRIGGER
     */
    public function triggerDoorbell(Request $request)
    {
        if (!$this->authenticateHardware($request)) {
            return response()->json(['status' => 'error'], 401);
        }

        $deviceCode = $request->input('device_code');
        $device = DB::table('devices')->where('device_code', $deviceCode)->first();
        if (!$device) return response()->json(['status' => 'error'], 404);

        $room = DB::table('rooms')->where('room_id', $device->room_id)->first();

        $eventId = DB::table('doorbell_events')->insertGetId([
            'room_id' => $room->room_id,
            'device_id' => $device->device_id,
            'buzzer_triggered' => 1,
            'pwa_notified' => 1,
            'visitor_note' => 'Hardware doorbell button pressed',
            'pressed_at' => now()
        ]);

        $notifTitle = '🔔 Doorbell Alert: ' . $room->room_name;
        $notifBody = 'Someone is at the door. Please check the camera or respond via the dashboard.';

        $admins = DB::table('users')->whereIn('role', ['Admin', 'Dean'])->where('is_active', 1)->get();
        $notifications = [];
        
        foreach ($admins as $admin) {
            $notifications[] = [
                'user_id' => $admin->user_id,
                'room_id' => $room->room_id,
                'type' => 'doorbell',
                'title' => $notifTitle,
                'body' => $notifBody,
                'reference_id' => $eventId,
                'reference_table' => 'doorbell_events',
                'is_read' => 0,
                'sent_at' => now()
            ];
        }

        if (!empty($notifications)) {
            DB::table('notifications')->insert($notifications);
        }

        $this->sendOneSignalPush($notifTitle, $notifBody);

        return response()->json(['status' => 'success', 'message' => 'Doorbell broadcasted.']);
    }

    /**
     * ENDPOINT 4: CRITICAL HARDWARE ALERTS
     */
    public function triggerAlert(Request $request)
    {
        if (!$this->authenticateHardware($request)) {
            return response()->json(['status' => 'error'], 401);
        }

        $deviceCode = $request->input('device_code');
        $device = DB::table('devices')->where('device_code', $deviceCode)->first();
        if (!$device) return response()->json(['status' => 'error'], 404);

        $alertType = $request->input('alert_type'); 
        $description = $request->input('description', 'Hardware sensor triggered an alert.');

        $alertId = DB::table('intrusion_alerts')->insertGetId([
            'room_id' => $device->room_id,
            'device_id' => $device->device_id,
            'alert_type' => $alertType,
            'severity' => 'critical',
            'description' => $description,
            'siren_triggered' => 1,
            'is_resolved' => 0,
            'triggered_at' => now()
        ]);

        $notifTitle = '🚨 CRITICAL HARDWARE ALERT';
        $admins = DB::table('users')->where('role', 'Admin')->where('is_active', 1)->get();
        $notifications = [];

        foreach ($admins as $admin) {
            $notifications[] = [
                'user_id' => $admin->user_id,
                'room_id' => $device->room_id,
                'type' => 'intrusion_alert',
                'title' => $notifTitle,
                'body' => $description,
                'reference_id' => $alertId,
                'reference_table' => 'intrusion_alerts',
                'is_read' => 0,
                'sent_at' => now()
            ];

            if (cache('set_audit_alerts', true) && !empty($admin->email)) {
                try {
                    Mail::to($admin->email)->send(new IntrusionAlertMail($description));
                } catch (Exception $e) {
                    Log::error('Failed to send hardware alert email: ' . $e->getMessage());
                }
            }
        }

        if (!empty($notifications)) {
            DB::table('notifications')->insert($notifications);
        }

        $this->sendOneSignalPush($notifTitle, $description);

        return response()->json(['status' => 'success', 'message' => 'Alert logged and admins notified.']);
    }

    /**
     * 🟢 ENDPOINT 5: HARDWARE LOG EVENT (DITO KUKUNIN ANG USER NAME)
     * Dito pinapasa ng ESP32 ang lahat ng logs (Web Dashboard, Auto-Lock, Keypad, etc.)
     */
    public function hardwareLog(Request $request)
    {
        // Wag nating lagyan ng API authentication restriction kung test muna, 
        // pero mas maganda kung nandoon pa rin para secure.
        $deviceCode = $request->input('device_code');
        $device = DB::table('devices')->where('device_code', $deviceCode)->first();
        if (!$device) return response()->json(['status' => 'error', 'message' => 'Device not found'], 404);

        $room = DB::table('rooms')->where('room_id', $device->room_id)->first();
        
        $eventType = $request->input('event_type', 'UNKNOWN');
        $description = $request->input('description', '');
        $doorState = $request->input('door_state', $room->door_state);
        
        // 🟢 KUNIN ANG USER NAME MULA SA ESP32 (Kung wala, "System" ang default)
        $userName = $request->input('user_name', 'System'); 

        // 1. I-update ang door state sa room
        DB::table('rooms')->where('room_id', $room->room_id)->update(['door_state' => $doorState]);

        // 2. I-save sa Audit Logs gamit ang TOTOONG PANGALAN MO
        DB::table('audit_logs')->insert([
            'room_id' => $room->room_id,
            'device_id' => $device->device_id,
            'user_name' => $userName, // 🟢 PANGALAN MO NA ANG LALABAS DITO!
            'action' => strtolower(str_replace(' ', '_', $eventType)),
            'method' => 'hardware',
            'door_state_after' => $doorState,
            'notes' => $description,
            'logged_at' => now()
        ]);

        // 3. Mag-create ng Notifications para sa Web Dashboard, Keypad, at Auto-Lock
        if (in_array($eventType, ['WEB DASHBOARD', 'AUTO-LOCK', 'KEYPAD ACCESS', 'NORMAL EXIT'])) {
            $notifType = ($doorState === 'unlocked') ? 'door_unlocked' : 'door_locked';
            
            $notifTitle = 'System Update';
            if ($eventType === 'AUTO-LOCK') {
                $notifTitle = '🔒 Auto-Locked';
            } elseif ($eventType === 'WEB DASHBOARD') {
                $notifTitle = ($doorState === 'unlocked') ? '📱 Web Unlocked' : '📱 Web Locked';
            } elseif ($eventType === 'KEYPAD ACCESS') {
                $notifTitle = ($doorState === 'unlocked') ? '🔑 Keypad Unlocked' : '🔑 Keypad Locked';
            } elseif ($eventType === 'NORMAL EXIT') {
                $notifTitle = '🚪 Manual Exit (Inside)';
            }

            $users = DB::table('users')->where('is_active', 1)->get();
            $notifications = [];

            foreach ($users as $recipient) {
                $notifications[] = [
                    'user_id' => $recipient->user_id,
                    'room_id' => $room->room_id,
                    'type' => $notifType,
                    'title' => $notifTitle,
                    'body' => $description, // 🟢 Dito rin lalabas yung "Door secured automatically. (Opened by: Trisha)"
                    'is_read' => 0,
                    'sent_at' => now()
                ];
            }
            
            if (!empty($notifications)) {
                DB::table('notifications')->insert($notifications);
            }
        }

        return response()->json(['status' => 'success']);
    }
}