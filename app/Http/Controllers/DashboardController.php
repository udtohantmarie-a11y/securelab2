<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    private function getInboxQuery($userId)
    {
        $userId = (int) $userId;

        $queryA = DB::table('conversations as c')
            ->leftJoin('users as ub', 'ub.user_id', '=', 'c.participant_b')
            ->where('c.participant_a', $userId)
            ->where('c.deleted_by_a', 0) 
            ->select(
                'c.convo_id',
                'c.participant_a as my_user_id',
                'c.participant_b as contact_id',
                DB::raw("COALESCE(ub.full_name, 'Unknown User') as contact_name"),
                DB::raw("COALESCE(ub.role, 'User') as contact_role")
            )
            ->selectRaw('(SELECT body FROM messages WHERE messages.convo_id = c.convo_id ORDER BY sent_at DESC, message_id DESC LIMIT 1) as last_message')
            ->selectRaw('(SELECT sent_at FROM messages WHERE messages.convo_id = c.convo_id ORDER BY sent_at DESC, message_id DESC LIMIT 1) as last_message_at')
            ->selectRaw('(SELECT COUNT(*) FROM messages WHERE messages.convo_id = c.convo_id AND is_read = 0 AND sender_id != ' . $userId . ') as unread_count');

        $queryB = DB::table('conversations as c')
            ->leftJoin('users as ua', 'ua.user_id', '=', 'c.participant_a')
            ->where('c.participant_b', $userId)
            ->where('c.deleted_by_b', 0)
            ->select(
                'c.convo_id',
                'c.participant_b as my_user_id',
                'c.participant_a as contact_id',
                DB::raw("COALESCE(ua.full_name, 'Unknown User') as contact_name"),
                DB::raw("COALESCE(ua.role, 'User') as contact_role")
            )
            ->selectRaw('(SELECT body FROM messages WHERE messages.convo_id = c.convo_id ORDER BY sent_at DESC, message_id DESC LIMIT 1) as last_message')
            ->selectRaw('(SELECT sent_at FROM messages WHERE messages.convo_id = c.convo_id ORDER BY sent_at DESC, message_id DESC LIMIT 1) as last_message_at')
            ->selectRaw('(SELECT COUNT(*) FROM messages WHERE messages.convo_id = c.convo_id AND is_read = 0 AND sender_id != ' . $userId . ') as unread_count');

        return $queryB->unionAll($queryA);
    }

    private function getAuditLogQuery()
    {
        return DB::table('audit_logs as al')
            ->join('rooms as r', 'al.room_id', '=', 'r.room_id')
            ->leftJoin('devices as d', 'al.device_id', '=', 'd.device_id')
            ->leftJoin('users as u', 'al.user_id', '=', 'u.user_id')
            ->select(
                'al.log_id', 'r.room_code', 'r.room_name', 'al.action', 'al.method',
                'al.door_state_after',
                DB::raw("COALESCE(u.full_name, 'Unknown') as user_name"),
                DB::raw("COALESCE(u.role, 'N/A') as user_role"),
                'u.profile_photo',
                'd.device_code', 'al.ip_address', 'al.notes', 'al.logged_at'
            );
    }

    private function getActiveAlertsQuery()
    {
        return DB::table('intrusion_alerts as ia')
            ->join('rooms as r', 'ia.room_id', '=', 'r.room_id')
            ->leftJoin('devices as d', 'ia.device_id', '=', 'd.device_id')
            ->where('ia.is_resolved', 0)
            ->select(
                'ia.alert_id', 'r.room_code', 'r.room_name', 'ia.alert_type',
                'ia.severity', 'ia.description', 'ia.siren_triggered',
                'd.device_code', 'ia.triggered_at'
            );
    }

    private function getDeviceHealthLatestQuery()
    {
        $latestLogsSubquery = DB::table('device_health_logs')
            ->select('device_id', DB::raw('MAX(log_id) as max_log_id'))
            ->groupBy('device_id');

        return DB::table('devices as d')
            ->join('rooms as r', 'd.room_id', '=', 'r.room_id')
            ->leftJoinSub($latestLogsSubquery, 'latest_logs', function ($join) {
                $join->on('d.device_id', '=', 'latest_logs.device_id');
            })
            ->leftJoin('device_health_logs as dh', function ($join) {
                $join->on('dh.device_id', '=', 'd.device_id')
                    ->on('dh.log_id', '=', 'latest_logs.max_log_id');
            })
            ->select(
                'd.device_id', 'd.device_code', 'd.firmware_version', 'd.is_online', 
                DB::raw('COALESCE(dh.recorded_at, d.last_ping_at, d.created_at) as recorded_at'),
                'r.room_code', 'r.room_name',
                'dh.wifi_signal_dbm', 'dh.battery_voltage', 'dh.battery_pct', 'dh.power_source', 'dh.door_state',
                'dh.uptime_seconds', 'dh.sd_total_mb', 'dh.sd_used_mb'
            );
    }

    // =========================================================================
    // MAIN CONTROLLER LOGIC
    // =========================================================================

    private function getNavbarData()
    {
        DB::table('devices')
            ->where('is_online', 1)
            ->where('last_ping_at', '<', now()->subMinutes(3))
            ->update(['is_online' => 0]);

        $userId = Auth::id();
        $inboxData = DB::query()->fromSub($this->getInboxQuery($userId), 'inbox')
            ->whereNotNull('last_message')
            ->orderByDesc('unread_count')
            ->orderByDesc('last_message_at')
            ->get();

        return [
            'messages' => $inboxData->take(5),
            'notifications' => DB::table('notifications')
                ->where('user_id', $userId)
                ->where('type', '!=', 'new_message')
                ->orderBy('sent_at', 'desc')
                ->limit(5)
                ->get(),
            'totalUnreadMessages' => $inboxData->sum('unread_count'),
            'totalUnreadNotifs' => DB::table('notifications')
                ->where('user_id', $userId)
                ->where('is_read', 0)
                ->where('type', '!=', 'new_message')
                ->count()
        ];
    }

    public function index()
    {
        $navData = $this->getNavbarData();
        $logs = $this->getAuditLogQuery()->orderByDesc('logged_at')->limit(5)->get();
        $alerts = $this->getActiveAlertsQuery()->orderByDesc('triggered_at')->get();
        $health = $this->getDeviceHealthLatestQuery()->first();

        return view('admin.dashboard', array_merge($navData, [
            'logs' => $logs,
            'alerts' => $alerts,
            'health' => $health
        ]));
    }

    public function inbox()
    {
        $userId = Auth::id();
        $activeSenderId = request('user');
        $msgs = [];

        if ($activeSenderId) {
            $selectedUser = DB::table('users')->where('user_id', $activeSenderId)->first();
            
            if ($selectedUser) {
                $convo = DB::table('conversations')
                    ->where(function($q) use ($userId, $activeSenderId) {
                        $q->where('participant_a', $userId)->where('participant_b', $activeSenderId);
                    })->orWhere(function($q) use ($userId, $activeSenderId) {
                        $q->where('participant_b', $userId)->where('participant_a', $activeSenderId);
                    })->first();

                if ($convo) {
                    $isDeletedForMe = ($convo->participant_a == $userId && $convo->deleted_by_a == 1) || 
                                      ($convo->participant_b == $userId && $convo->deleted_by_b == 1);
                    
                    if (!$isDeletedForMe) {
                        $msgs = DB::table('messages')->where('convo_id', $convo->convo_id)->orderBy('sent_at', 'asc')->get();
                        DB::table('messages')->where('convo_id', $convo->convo_id)->where('sender_id', '!=', $userId)->where('is_read', 0)->update(['is_read' => 1]);
                    }
                }
            }
        }

        $navData = $this->getNavbarData();
        $allMessages = DB::query()->fromSub($this->getInboxQuery($userId), 'inbox')->orderByDesc('last_message_at')->get();

        return view('admin.inbox', array_merge($navData, [
            'allMessages' => $allMessages,
            'msgs' => $msgs
        ]));
    }

    public function getUnreadCount(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'error' => 'Unauthenticated'], 401);
        }

        $userId = Auth::id();

        $inboxData = DB::query()->fromSub($this->getInboxQuery($userId), 'inbox')
            ->whereNotNull('last_message')
            ->orderByDesc('unread_count')
            ->orderByDesc('last_message_at')
            ->get();

        $unreadCount = (int) $inboxData->sum('unread_count');

        $recentMessages = $inboxData->take(20)->map(function ($msg) {
            $lastMsg = (string) ($msg->last_message ?? '');
            $isImage = Str::startsWith($lastMsg, '[IMAGE]:');
            $isFile = Str::startsWith($lastMsg, '[FILE]:');

            if ($isImage) {
                $preview = 'Sent a photo';
            } elseif ($isFile) {
                $preview = 'Sent a file';
            } else {
                $preview = Str::limit($lastMsg, 42);
            }

            return [
                'contact_id' => $msg->contact_id,
                'contact_name' => $msg->contact_name,
                'last_message' => $msg->last_message,
                'preview_text' => $preview,
                'is_image' => $isImage,
                'is_file' => $isFile,
                'unread_count' => (int) ($msg->unread_count ?? 0),
                'last_message_at' => $msg->last_message_at,
                'time_ago' => $msg->last_message_at ? \Carbon\Carbon::parse($msg->last_message_at)->diffForHumans(null, true, true) : ''
            ];
        })->values();

        $recentNotifs = DB::table('notifications')
            ->where('user_id', $userId)
            ->where('type', '!=', 'new_message')
            ->orderBy('sent_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($notif) {
                $notif->time_ago = \Carbon\Carbon::parse($notif->sent_at)->diffForHumans(null, true, true);
                return $notif;
            });

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'recent_messages' => $recentMessages,
            'unread_notifs_count' => DB::table('notifications')
                ->where('user_id', $userId)
                ->where('is_read', 0)
                ->where('type', '!=', 'new_message')
                ->count(),
            'recent_notifications' => $recentNotifs
        ]);
    }

    public function markAsRead(Request $request)
    {
        $userId = Auth::id();

        if ($request->has('notif_id')) {
            $notif = DB::table('notifications')
                ->where('notif_id', $request->notif_id)
                ->where('user_id', $userId)
                ->first();

            if ($notif) {
                DB::table('notifications')
                    ->where('notif_id', $notif->notif_id)
                    ->update([
                        'is_read' => 1,
                        'read_at' => now()
                    ]);

                if ($notif->type === 'intrusion_alert' || $notif->type === 'doorbell') {
                    DB::table('notifications')
                        ->where('type', $notif->type)
                        ->where('title', $notif->title)
                        ->where('sent_at', $notif->sent_at)
                        ->update([
                            'is_read' => 1,
                            'read_at' => now()
                        ]);
                }
            }
            return response()->json(['success' => true]);
        }

        if ($request->has('sender_id')) {
            $senderId = $request->sender_id;

            try {
                if ($senderId === 'clear_all_notifications') {
                    DB::table('notifications')
                        ->where('user_id', $userId)
                        ->where('is_read', 0)
                        ->update([
                            'is_read' => 1,
                            'read_at' => now()
                        ]);
                    return response()->json(['success' => true, 'message' => 'Cleared.']);
                }

                if ($senderId === 'all') {
                    $convoIds = DB::table('conversations')
                        ->where('participant_a', $userId)
                        ->orWhere('participant_b', $userId)
                        ->pluck('convo_id');

                    DB::table('messages')
                        ->whereIn('convo_id', $convoIds)
                        ->where('sender_id', '!=', $userId)
                        ->where('is_read', 0)
                        ->update(['is_read' => 1]);

                    DB::table('notifications')
                        ->where('user_id', $userId)
                        ->where('type', 'new_message')
                        ->where('is_read', 0)
                        ->update([
                            'is_read' => 1,
                            'read_at' => now()
                        ]);

                    return response()->json(['success' => true, 'message' => 'Cleared.']);
                }

                $parsedSenderId = (int)$senderId;
                $convo = DB::table('conversations')
                    ->where(function($q) use ($userId, $parsedSenderId) {
                        $q->where('participant_a', $userId)->where('participant_b', $parsedSenderId);
                    })->orWhere(function($q) use ($userId, $parsedSenderId) {
                        $q->where('participant_b', $userId)->where('participant_a', $parsedSenderId);
                    })->first();

                if ($convo) {
                    DB::table('messages')
                        ->where('convo_id', $convo->convo_id)
                        ->where('sender_id', $parsedSenderId)
                        ->where('is_read', 0)
                        ->update(['is_read' => 1]);

                    DB::table('notifications')
                        ->where('user_id', $userId)
                        ->where('type', 'new_message')
                        ->where('is_read', 0)
                        ->update([
                            'is_read' => 1,
                            'read_at' => now()
                        ]);
                }

                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        }
        
        return response()->json(['success' => false, 'error' => 'Invalid request'], 400);
    }

    public function readStatus(Request $request)
    {
        $userId = Auth::id();
        $senderId = $request->sender_id;

        if ($senderId) {
            $convo = DB::table('conversations')
                ->where(function($q) use ($userId, $senderId) {
                    $q->where('participant_a', $userId)->where('participant_b', $senderId);
                })->orWhere(function($q) use ($userId, $senderId) {
                    $q->where('participant_b', $userId)->where('participant_a', $senderId);
                })->first();

            if ($convo) {
                DB::table('messages')
                    ->where('convo_id', $convo->convo_id)
                    ->where('sender_id', $senderId)
                    ->where('is_read', 0)
                    ->update(['is_read' => 1]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function getConversationMessages(Request $request, $userId)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'error' => 'Unauthenticated'], 401);
        }

        $myId = Auth::id();
        $otherId = (int) $userId;
        $afterId = (int) $request->query('after', 0);

        $otherUser = DB::table('users')->where('user_id', $otherId)->first();
        if (!$otherUser) {
            return response()->json(['success' => false, 'error' => 'User not found'], 404);
        }

        $isOnline = !is_null($otherUser->last_seen) && \Carbon\Carbon::parse($otherUser->last_seen)->gt(now()->subMinutes(5));

        $convo = DB::table('conversations')
            ->where(function($q) use ($myId, $otherId) {
                $q->where('participant_a', $myId)->where('participant_b', $otherId);
            })->orWhere(function($q) use ($myId, $otherId) {
                $q->where('participant_b', $myId)->where('participant_a', $otherId);
            })->first();

        if (!$convo) {
            return response()->json([
                'success' => true,
                'messages' => [],
                'read_status' => (object)[],
                'is_online' => $isOnline
            ]);
        }

        // Mark any unread messages from other user as read
        DB::table('messages')
            ->where('convo_id', $convo->convo_id)
            ->where('sender_id', $otherId)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        DB::table('notifications')
            ->where('user_id', $myId)
            ->where('type', 'new_message')
            ->where('is_read', 0)
            ->update(['is_read' => 1, 'read_at' => now()]);

        // Query messages
        $query = DB::table('messages')->where('convo_id', $convo->convo_id);
        if ($afterId > 0) {
            $query->where('message_id', '>', $afterId);
        }
        $rawMsgs = $query->orderBy('sent_at', 'asc')->get();

        $storageUrl = asset('storage') . '/';

        $messages = $rawMsgs->map(function ($m) use ($storageUrl, $myId) {
            $body = (string) $m->body;
            $isImage = Str::startsWith($body, '[IMAGE]:');
            $isFile = Str::startsWith($body, '[FILE]:');
            $imgUrl = null;
            $fileUrl = null;
            $fileName = null;

            if ($isImage) {
                $imgUrl = $storageUrl . str_replace('[IMAGE]:', '', $body);
            } elseif ($isFile) {
                $fData = explode('|', str_replace('[FILE]:', '', $body));
                $fileUrl = $storageUrl . ($fData[0] ?? '');
                $fileName = $fData[1] ?? 'Attachment';
            }

            return [
                'message_id' => $m->message_id,
                'sender_id' => $m->sender_id,
                'is_me' => ($m->sender_id == $myId),
                'body' => $body,
                'is_image' => $isImage,
                'is_file' => $isFile,
                'img_url' => $imgUrl,
                'file_url' => $fileUrl,
                'file_name' => $fileName,
                'reaction' => $m->reaction,
                'sent_at' => $m->sent_at,
                'time_formatted' => \Carbon\Carbon::parse($m->sent_at)->format('h:i A'),
                'is_read' => (int) $m->is_read
            ];
        })->values();

        // Read status of recent sent messages in this convo
        $readStatus = DB::table('messages')
            ->where('convo_id', $convo->convo_id)
            ->where('sender_id', $myId)
            ->orderBy('message_id', 'desc')
            ->limit(30)
            ->pluck('is_read', 'message_id');

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'read_status' => $readStatus,
            'is_online' => $isOnline
        ]);
    }

    public function reactMessage(Request $request, $id)
    {
        $request->validate(['reaction' => 'nullable|string']);

        try {
            DB::table('messages')->where('message_id', $id)->update([
                'reaction' => !empty($request->reaction) ? $request->reaction : null
            ]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer',
            'message'     => 'nullable|string',
            'images'      => 'nullable|array',
            'images.*'    => 'image|max:5120', 
            'documents'   => 'nullable|array',
            'documents.*' => 'file|max:10240', 
        ]);

        $userId = Auth::id();
        $receiverId = $request->receiver_id;

        if (empty($request->message) && !$request->hasFile('images') && !$request->hasFile('documents')) {
            return response()->json(['success' => false, 'message' => 'Cannot send an empty message.'], 422);
        }

        try {
            $convo = DB::table('conversations')
                ->where(function($q) use ($userId, $receiverId) {
                    $q->where('participant_a', $userId)->where('participant_b', $receiverId);
                })->orWhere(function($q) use ($userId, $receiverId) {
                    $q->where('participant_b', $userId)->where('participant_a', $receiverId);
                })->first();

            if (!$convo) {
                $convoId = DB::table('conversations')->insertGetId([
                    'participant_a' => $userId,
                    'participant_b' => $receiverId,
                    'deleted_by_a' => 0, 
                    'deleted_by_b' => 0,
                    'created_at' => now(),
                    'last_message_at' => now()
                ]);
            } else {
                $convoId = $convo->convo_id;
                DB::table('conversations')
                    ->where('convo_id', $convoId)
                    ->update([
                        'last_message_at' => now(),
                        'deleted_by_a' => 0,
                        'deleted_by_b' => 0
                    ]);
            }

            $messagesToInsert = [];
            $notificationBody = 'Sent a message';

            if ($request->hasFile('documents')) {
                $docCount = 0;
                foreach ($request->file('documents') as $doc) {
                    $originalName = $doc->getClientOriginalName();
                    $path = $doc->store('chat_files', 'public');
                    $messagesToInsert[] = "[FILE]:" . $path . "|" . $originalName;
                    $docCount++;
                }
                $notificationBody = $docCount > 1 ? 'Sent multiple files' : 'Sent a file';
            }

            if ($request->hasFile('images')) {
                $imageCount = 0;
                foreach ($request->file('images') as $image) {
                    $path = $image->store('chat_images', 'public');
                    $messagesToInsert[] = "[IMAGE]:" . $path;
                    $imageCount++;
                }
                $notificationBody = $imageCount > 1 ? 'Sent multiple photos' : 'Sent a photo';
            }

            if (!empty($request->message)) {
                $messagesToInsert[] = $request->message;
                if (!$request->hasFile('images') && !$request->hasFile('documents')) {
                    $notificationBody = Str::limit($request->message, 60);
                }
            }

            foreach ($messagesToInsert as $msgBody) {
                DB::table('messages')->insert([
                    'convo_id'  => $convoId,
                    'sender_id' => $userId,
                    'body'      => $msgBody,
                    'is_read'   => 0,
                    'sent_at'   => now()
                ]);
            }

            DB::table('notifications')->insert([
                'user_id' => $receiverId,
                'type' => 'new_message',
                'title' => '💬 New message from ' . Auth::user()->full_name,
                'body' => $notificationBody,
                'is_read' => 0,
                'sent_at' => now()
            ]);

            // 🟢 Send background Web Push to receiver (pops up even if receiver's tab is closed)
            \App\Services\WebPushService::sendToUser(
                (int) $receiverId,
                '💬 ' . Auth::user()->full_name,
                $notificationBody,
                url('/messages?user=' . $userId),
                'chat'
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteMessage($id)
    {
        $message = DB::table('messages')->where('message_id', $id)->first();

        if ($message && $message->sender_id == Auth::id()) {
            if (Str::startsWith($message->body, '[IMAGE]:')) {
                $path = str_replace('[IMAGE]:', '', $message->body);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            } elseif (Str::startsWith($message->body, '[FILE]:')) {
                $fData = explode('|', str_replace('[FILE]:', '', $message->body));
                $path = $fData[0];
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            DB::table('messages')->where('message_id', $id)->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    public function deleteConversation($userId)
    {
        $myId = Auth::id();

        $convo = DB::table('conversations')
            ->where(function($q) use ($myId, $userId) {
                $q->where('participant_a', $myId)->where('participant_b', $userId);
            })->orWhere(function($q) use ($myId, $userId) {
                $q->where('participant_b', $myId)->where('participant_a', $userId);
            })->first();

        if ($convo) {
            $updateData = [];
            if ($convo->participant_a == $myId) {
                $updateData['deleted_by_a'] = 1;
                $convo->deleted_by_a = 1;
            } else {
                $updateData['deleted_by_b'] = 1;
                $convo->deleted_by_b = 1;
            }
            
            DB::table('conversations')->where('convo_id', $convo->convo_id)->update($updateData);

            if ($convo->deleted_by_a == 1 && $convo->deleted_by_b == 1) {
                $messages = DB::table('messages')->where('convo_id', $convo->convo_id)->get();
                foreach ($messages as $message) {
                    if (Str::startsWith($message->body, '[IMAGE]:')) {
                        $path = str_replace('[IMAGE]:', '', $message->body);
                        if (Storage::disk('public')->exists($path)) { Storage::disk('public')->delete($path); }
                    } elseif (Str::startsWith($message->body, '[FILE]:')) {
                        $fData = explode('|', str_replace('[FILE]:', '', $message->body));
                        if (Storage::disk('public')->exists($fData[0])) { Storage::disk('public')->delete($fData[0]); }
                    }
                }
                DB::table('conversations')->where('convo_id', $convo->convo_id)->delete();
            }

            return redirect()->route('messages.index')->with('success', 'Conversation removed from your view.');
        }

        return back()->with('error', 'Conversation not found.');
    }

    public function logs()
    {
        $navData = $this->getNavbarData();
        $logs = $this->getAuditLogQuery()->orderByDesc('logged_at')->get();
        return view('admin.logs', array_merge($navData, ['logs' => $logs]));
    }

    public function alerts()
    {
        $navData = $this->getNavbarData();

        $intrusions = DB::table('intrusion_alerts')
            ->select(
                'alert_id as id',
                'alert_type',
                'severity',
                'description',
                'is_resolved',
                'triggered_at'
            )->get();

        $doorbells = DB::table('doorbell_events')
            ->select(
                'event_id as id',
                DB::raw("'doorbell_rung' as alert_type"), 
                DB::raw("'info' as severity"),            
                DB::raw("'A visitor rang the doorbell.' as description"),
                DB::raw("1 as is_resolved"),              
                'pressed_at as triggered_at'
            )->get();

        $allAlerts = $intrusions->concat($doorbells)->sortByDesc('triggered_at')->values();

        return view('admin.alerts', array_merge($navData, ['allAlerts' => $allAlerts]));
    }

    // =========================================================================
    // MY ROOMS
    // =========================================================================
    public function myRooms()
    {
        $navData = $this->getNavbarData();
        $myRooms = DB::table('room_assignments as ra')
            ->join('users as u', 'ra.user_id', '=', 'u.user_id')
            ->join('rooms as r', 'ra.room_id', '=', 'r.room_id')
            ->join('users as assigner', 'ra.assigned_by', '=', 'assigner.user_id')
            ->where('ra.user_id', Auth::id())
            ->where('ra.is_active', 1)
            ->select(
                'u.user_id', 'u.full_name', 'u.role',
                'r.room_code', 'r.room_name', 'r.room_id',
                'r.location', 'r.capacity', 'r.is_air_conditioned', 'r.backup_ssid',
                'r.is_active as room_is_active', 
                DB::raw("IFNULL(r.door_state, 'locked') as door_state"),
                'r.occupancy_status',
                'ra.access_level', 'ra.valid_from', 'ra.valid_until', 'ra.is_active',
                'ra.pin_code',
                'assigner.full_name as assigned_by_name'
            )
            ->get();

        $myFingerprints = DB::table('fingerprints')
            ->where('user_id', Auth::id())
            ->where('is_active', 1)
            ->get()
            ->keyBy('room_id');

        foreach ($myRooms as $room) {
            $room->fingerprint = $myFingerprints->get($room->room_id); 
            $isExpired = $room->valid_until && \Carbon\Carbon::parse($room->valid_until)->isPast();
            $room->access_verified = $room->is_active && $room->room_is_active && !$isExpired;
            $room->access_expired = (bool) $isExpired;
        }

        return view('admin.my_rooms', array_merge($navData, ['myRooms' => $myRooms]));
    }

    public function setPin(Request $request)
    {
        $request->validate([
            'room_id' => 'required|integer',
            'pin_code' => 'required|digits:4'
        ], [
            'pin_code.digits' => 'The PIN code must be exactly 4 numbers.'
        ]);

        $assignment = DB::table('room_assignments')
            ->where('user_id', Auth::id())
            ->where('room_id', $request->room_id)
            ->where('is_active', 1)
            ->first();

        if (!$assignment) {
            return back()->with('error', 'Failed to save PIN. Ensure you are authorized for this laboratory.');
        }

        if ($assignment->access_level === 'readonly') {
            return back()->with('error', 'Action Denied: You only have View/Read-Only access to this laboratory.');
        }

        if ($assignment->valid_until && \Carbon\Carbon::parse($assignment->valid_until)->isPast()) {
            return back()->with('error', 'Your access to this laboratory has expired. Please contact your Admin.');
        }

        DB::table('room_assignments')
            ->where('user_id', Auth::id())
            ->where('room_id', $request->room_id)
            ->update([
                'pin_code' => $request->pin_code,
                'updated_at' => now()
            ]);

        return back()->with('success', 'Your access PIN has been successfully saved. You can now use it on the keypad.');
    }

    public function startEnrollment(Request $request)
    {
        $request->validate([
            'room_id' => 'required|integer',
            'enroll_id' => 'nullable|integer|min:1|max:127'
        ]);

        $assignment = DB::table('room_assignments')
            ->where('user_id', Auth::id())
            ->where('room_id', $request->room_id)
            ->where('is_active', 1)
            ->first();

        if (!$assignment) {
            return back()->with('error', 'You are not authorized to enroll a fingerprint for this laboratory.');
        }

        if ($assignment->access_level === 'readonly') {
            return back()->with('error', 'Action Denied: You only have View/Read-Only access to this laboratory.');
        }

        if ($assignment->valid_until && \Carbon\Carbon::parse($assignment->valid_until)->isPast()) {
            return back()->with('error', 'Your access to this laboratory has expired. Please contact your Admin.');
        }

        $roomExists = DB::table('rooms')->where('room_id', $request->room_id)->where('is_active', 1)->exists();

        if (!$roomExists) {
            return back()->with('error', 'Failed to connect to the hardware. Device might be offline.');
        }

        if ($request->filled('enroll_id')) {
            $idTaken = DB::table('fingerprints')
                ->where('room_id', $request->room_id)
                ->where('template_slot', $request->enroll_id)
                ->where('is_active', 1)
                ->exists();

            if ($idTaken) {
                return back()->with('error', 'ID ' . $request->enroll_id . ' is already in use. Please select a different number.');
            }
        }

        DB::table('rooms')
            ->where('room_id', $request->room_id)
            ->update([
                'enrollment_mode' => 1,
                'enroll_user_id' => Auth::id(),
                'enroll_fp_id' => $request->enroll_id,
                'updated_at' => now()
            ]);

        return back()->with('success', 'Hardware sensor activated! Please place your finger on the scanner now.');
    }

    public function deleteFingerprint(Request $request)
    {
        $request->validate([
            'room_id' => 'required|integer'
        ]);

        $assignment = DB::table('room_assignments')
            ->where('user_id', Auth::id())
            ->where('room_id', $request->room_id)
            ->where('is_active', 1)
            ->first();

        if (!$assignment) {
            return back()->with('error', 'You are not authorized to manage a fingerprint for this laboratory.');
        }

        if ($assignment->access_level === 'readonly') {
            return back()->with('error', 'Action Denied: You only have View/Read-Only access to this laboratory.');
        }

        $fp = DB::table('fingerprints')
            ->where('user_id', Auth::id())
            ->where('room_id', $request->room_id)
            ->where('is_active', 1)
            ->first();

        if (!$fp) {
            return back()->with('error', 'No active fingerprint found to remove for this laboratory.');
        }

        $roomExists = DB::table('rooms')->where('room_id', $request->room_id)->exists();
        if (!$roomExists) {
            return back()->with('error', 'Failed to connect to the hardware. Device might be offline.');
        }

        DB::table('rooms')
            ->where('room_id', $request->room_id)
            ->update([
                'delete_mode' => 1,
                'delete_fp_id' => $fp->template_slot,
                'updated_at' => now()
            ]);

        return back()->with('success', 'Fingerprint removal requested! Please wait a few seconds — the hardware will automatically clear your fingerprint from the sensor.');
    }

    public function adminDeleteFingerprint(Request $request)
    {
        if (Auth::user()->role !== 'Admin') { abort(403); }

        $request->validate([
            'fp_id' => 'required|integer'
        ]);

        $fp = DB::table('fingerprints')
            ->where('fp_id', $request->fp_id)
            ->where('is_active', 1)
            ->first();

        if (!$fp) {
            return back()->with('error', 'Fingerprint record not found or already inactive.');
        }

        $roomExists = DB::table('rooms')->where('room_id', $fp->room_id)->exists();
        if (!$roomExists) {
            return back()->with('error', 'Failed to connect to the hardware. Device might be offline.');
        }

        DB::table('rooms')
            ->where('room_id', $fp->room_id)
            ->update([
                'delete_mode' => 1,
                'delete_fp_id' => $fp->template_slot,
                'updated_at' => now()
            ]);

        return back()->with('success', 'Fingerprint removal requested! The hardware will clear this slot within a few seconds.');
    }

    public function remoteControl(Request $request)
    {
        $request->validate([
            'room_id' => 'required|integer',
            'action' => 'required|in:lock,unlock'
        ]);

        if (Auth::user()->role !== 'Admin') {
            $assignment = DB::table('room_assignments')
                ->where('user_id', Auth::id())
                ->where('room_id', $request->room_id)
                ->where('is_active', 1)
                ->first();

            if (!$assignment) {
                return back()->with('error', 'Action Denied: You are not assigned to this laboratory.');
            }

            if ($assignment->access_level === 'readonly') {
                return back()->with('error', 'Action Denied: You only have View/Read-Only access to this laboratory.');
            }

            if ($assignment->valid_until && \Carbon\Carbon::parse($assignment->valid_until)->isPast()) {
                return back()->with('error', 'Action Denied: Your schedule for this laboratory has already expired.');
            }
        }

        $newState = $request->action === 'unlock' ? 'unlocked' : 'locked';
        $actionEnum = $request->action === 'unlock' ? 'remote_unlock' : 'remote_lock';

        $roomUpdates = [
            'door_state' => $newState,
            'updated_at' => now()
        ];

        if ($newState === 'unlocked') {
            $roomUpdates['occupancy_status'] = 'occupied';
            
            DB::table('audit_logs')
                ->where('room_id', $request->room_id)
                ->where('action', 'attempt_failed')
                ->update(['logged_at' => now()->subMinutes(5)]);
                
            DB::table('intrusion_alerts')
                ->where('room_id', $request->room_id)
                ->where('is_resolved', 0)
                ->update([
                    'is_resolved' => 1,
                    'resolved_by' => Auth::id(),
                    'resolved_at' => now()
                ]);
        }

        $updated = DB::table('rooms') 
            ->where('room_id', $request->room_id)
            ->update($roomUpdates);

        if ($updated) {
            DB::table('audit_logs')->insert([
                'room_id' => $request->room_id,
                'user_id' => Auth::id(),
                'action' => $actionEnum,
                'method' => 'remote_pwa',
                'door_state_after' => $newState,
                'ip_address' => $request->ip(),
                'notes' => 'User remotely ' . $newState . ' Room ID: ' . $request->room_id,
                'logged_at' => now()
            ]);

            return back()->with('success', 'The laboratory door has been ' . $newState . ' successfully.');
        }

        return back()->with('error', 'Failed to execute remote command. Device might be offline.');
    }

    public function resolveAlert($id)
    {
        if (Auth::user()->role !== 'Admin') { abort(403); }

        $alert = DB::table('intrusion_alerts')->where('alert_id', $id)->first();
        
        if ($alert) {
            DB::table('intrusion_alerts')->where('alert_id', $id)->update([
                'is_resolved' => 1,
                'resolved_by' => Auth::id(),
                'resolved_at' => now()
            ]);

            DB::table('audit_logs')
                ->where('room_id', $alert->room_id)
                ->where('action', 'attempt_failed')
                ->update(['logged_at' => now()->subMinutes(5)]);
        }

        return back()->with('success', 'Alert resolved! The hardware alarm has been silenced.');
    }

    public function registerPasskey(Request $request)
    {
        $request->validate([
            'credential_id' => 'required|string',
            'name'          => 'nullable|string'
        ]);

        // 1. Siguraduhing hindi nagagamit ng ibang user ang credential na ito
        $existing = DB::table('passkeys')->where('credential_id', $request->credential_id)->first();
        
        if ($existing && $existing->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'This passkey is already registered to another account.']);
        }

        // 2. Linisin muna ang lumang passkey para sa device name na ito ng user para hindi mag-double
        DB::table('passkeys')
            ->where('user_id', Auth::id())
            ->where('name', $request->name ?? 'Authorized Device')
            ->delete();

        // 3. I-save ang bagong passkey credential para sa device
        DB::table('passkeys')->insert([
            'user_id' => Auth::id(),
            'credential_id' => $request->credential_id,
            'credential' => 'native_webauthn_bypass', 
            'name' => $request->name ?? 'Authorized Device',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Device successfully registered for Passkey login!']);
    }

    public function toggleTheme(Request $request)
    {
        DB::table('users')->where('user_id', Auth::id())->update([
            'dark_mode' => $request->dark_mode ? 1 : 0
        ]);
        return response()->json(['success' => true]);
    }

    public function profile()
    {
        $navData = $this->getNavbarData();
        return view('admin.profile', $navData);
    }

    public function viewProfile($id)
    {
        if (Auth::id() == $id) {
            return redirect()->route('profile.index');
        }

        $navData = $this->getNavbarData();
        $viewUser = DB::table('users')->where('user_id', $id)->first();

        if (!$viewUser) {
            return redirect()->route('messages.index')->with('error', 'User not found.');
        }

        return view('admin.viewprofile', array_merge($navData, [
            'viewUser' => $viewUser
        ]));
    }

    public function updateProfileInfo(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'birthdate' => 'nullable|date',
            'address' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        $user = \App\Models\User::find(Auth::id());
        $user->full_name = $request->full_name;
        $user->contact_number = $request->contact_number;
        $user->birthdate = $request->birthdate;
        $user->address = $request->address;

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo = $path;
        }

        $user->save();

        return back()->with('success', 'Your profile information has been successfully updated!');
    }

    public function updateProfilePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = \App\Models\User::find(Auth::id());

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'The current password you entered is incorrect.');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Your password has been successfully changed!');
    }

    public function removeProfilePhoto(Request $request)
    {
        $user = \App\Models\User::find(Auth::id());

        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
            $user->profile_photo = null;
            $user->save();

            return back()->with('success', 'Your profile photo has been successfully removed.');
        }

        return back()->with('error', 'No profile photo found to remove.');
    }

    public function removePasskey(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = \App\Models\User::find(Auth::id());

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Incorrect password. Passkey removal failed.');
        }

        DB::table('passkeys')->where('user_id', $user->user_id)->delete();

        return back()->with('success', 'Your device passkey has been successfully removed.');
    }

    public function settings()
    {
        $navData = $this->getNavbarData();

        $systemSettings = [
            'institution_name' => cache('set_institution_name', 'Talibon Polytechnic College'),
            'system_alias' => cache('set_system_alias', 'SecureLab v1.0'),
            'maintenance_mode' => cache('set_maintenance_mode', false), 
            'audit_alerts' => cache('set_audit_alerts', true), 
            'failed_threshold' => cache('set_failed_threshold', 3),
            'session_timeout' => cache('set_session_timeout', 120),
            'hardware_api_key' => cache('set_hardware_api_key', 'SECURELAB_77A8B92CC4'),
            'mqtt_broker' => cache('set_mqtt_broker', ''),
            'dark_mode' => cache('set_dark_mode', false),
        ];

        $allFingerprints = collect();

        if (Auth::user()->role === 'Admin') {
            $allFingerprints = DB::table('fingerprints as f')
                ->join('users as u', 'f.user_id', '=', 'u.user_id')
                ->join('rooms as r', 'f.room_id', '=', 'r.room_id')
                ->where('f.is_active', 1)
                ->select('f.fp_id', 'f.template_slot', 'f.finger_label', 'f.enrolled_at', 'u.full_name', 'r.room_name', 'r.room_code')
                ->orderBy('r.room_name')
                ->orderBy('f.template_slot')
                ->get();
        }

        return view('admin.settings', array_merge($navData, [
            'settings' => $systemSettings,
            'allFingerprints' => $allFingerprints
        ]));
    }

    public function updateSettings(Request $request)
    {
        if (Auth::user()->role !== 'Admin') { abort(403); }

        cache()->forever('set_institution_name', $request->institution_name);
        cache()->forever('set_system_alias', $request->system_alias);
        cache()->forever('set_failed_threshold', $request->failed_threshold);
        cache()->forever('set_session_timeout', $request->session_timeout);
        cache()->forever('set_hardware_api_key', $request->hardware_api_key);
        cache()->forever('set_mqtt_broker', $request->mqtt_broker);

        cache()->forever('set_maintenance_mode', $request->has('maintenance_mode'));
        cache()->forever('set_audit_alerts', $request->has('audit_alerts'));

        return back()->with('success', 'System settings have been successfully updated and saved!');
    }

    public function manageRooms()
    {
        if (Auth::user()->role !== 'Admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Only System Administrators can access Manage Rooms.');
        }
        $navData = $this->getNavbarData();
        $rooms = DB::table('rooms')->get();
        return view('admin.manage_rooms', array_merge($navData, ['rooms' => $rooms]));
    }

    public function storeRoom(Request $request)
    {
        if (Auth::user()->role !== 'Admin') { abort(403); }
        
        $request->validate([
            'room_name' => 'required|string|max:255',
            'room_code' => 'required|string|max:50|unique:rooms,room_code',
            'capacity'  => 'nullable|integer|min:1'
        ]);

        DB::table('rooms')->insert([
            'room_name' => $request->room_name,
            'room_code' => $request->room_code,
            'capacity'  => $request->capacity ?? 40,
            'is_active' => 1,
            'door_state' => 'locked',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return back()->with('success', 'New laboratory room successfully registered.');
    }

    public function updateRoom(Request $request)
    {
        if (Auth::user()->role !== 'Admin') { abort(403); }
        $request->validate([
            'room_id'   => 'required|integer',
            'room_name' => 'required|string|max:255',
            'room_code' => 'required|string|max:50',
            'capacity'  => 'nullable|integer|min:1',
            'is_active' => 'required|boolean'
        ]);

        DB::table('rooms')->where('room_id', $request->room_id)->update([
            'room_name' => $request->room_name,
            'room_code' => $request->room_code,
            'capacity'  => $request->capacity,
            'is_active' => $request->is_active,
            'updated_at' => now()
        ]);

        return back()->with('success', 'Laboratory configuration updated.');
    }

    public function deleteRoom(Request $request)
    {
        if (Auth::user()->role !== 'Admin') { abort(403); }

        $request->validate([
            'room_id' => 'required|exists:rooms,room_id'
        ]);

        try {
            DB::table('rooms')->where('room_id', $request->room_id)->delete();
            return redirect()->back()->with('success', 'Laboratory successfully deleted.');
        } catch (\Exception $e) {
            Log::error('Failed to delete room: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete laboratory. Please try again.');
        }
    }

    public function assignUsers()
    {
        if (Auth::user()->role !== 'Admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Only System Administrators can access Assign Users.');
        }
        $navData = $this->getNavbarData();
        
        $assignments = DB::table('room_assignments as ra')
            ->join('users as u', 'ra.user_id', '=', 'u.user_id')
            ->join('rooms as r', 'ra.room_id', '=', 'r.room_id')
            ->join('users as assigner', 'ra.assigned_by', '=', 'assigner.user_id')
            ->select(
                'u.user_id', 'u.full_name', 'u.role',
                'r.room_code', 'r.room_name', 'r.room_id',
                'ra.access_level', 'ra.valid_from', 'ra.valid_until', 'ra.is_active',
                'assigner.full_name as assigned_by_name'
            )
            ->get();
            
        $users = DB::table('users')->where('is_active', 1)->get();
        $rooms = DB::table('rooms')->where('is_active', 1)->get();

        return view('admin.assign_users', array_merge($navData, [
            'assignments' => $assignments,
            'users' => $users,
            'rooms' => $rooms
        ]));
    }

    public function saveAssignment(Request $request)
    {
        if (Auth::user()->role !== 'Admin') { abort(403); }
        
        $request->validate([
            'user_id'      => 'required|integer',
            'room_id'      => 'required|integer',
            'access_level' => 'required|string',
            'valid_until'  => 'nullable|date'
        ]);

        $existingAssignment = DB::table('room_assignments')
            ->where('user_id', $request->user_id)
            ->where('room_id', $request->room_id)
            ->first();

        if (!$existingAssignment) {
            $room = DB::table('rooms')->where('room_id', $request->room_id)->first();
            
            if ($room && $room->capacity !== null) {
                $currentAssignedCount = DB::table('room_assignments')
                    ->where('room_id', $request->room_id)
                    ->where('is_active', 1)
                    ->count();

                if ($currentAssignedCount >= $room->capacity) {
                    return back()->with('error', "The laboratory has reached its maximum capacity limit of {$room->capacity} users!");
                }
            }
        }

        DB::table('room_assignments')->updateOrInsert(
            [
                'user_id' => $request->user_id,
                'room_id' => $request->room_id
            ],
            [
                'assigned_by'  => Auth::id(),
                'access_level' => $request->access_level,
                'valid_from'   => now()->format('Y-m-d'),
                'valid_until'  => $request->valid_until,
                'is_active'    => 1,
                'updated_at'   => now()
            ]
        );

        return back()->with('success', 'User access successfully updated!');
    }

    public function revokeAssignment($userId, $roomId)
    {
        if (Auth::user()->role !== 'Admin') { abort(403); }

        DB::table('room_assignments')
            ->where('user_id', $userId)
            ->where('room_id', $roomId)
            ->delete();

        return back()->with('success', 'User laboratory permissions safely revoked.');
    }

    public function revokeAllAssignments($roomId)
    {
        if (Auth::user()->role !== 'Admin') { abort(403); }

        $assignedCount = DB::table('room_assignments')
            ->where('room_id', $roomId)
            ->count();

        if ($assignedCount > 0) {
            DB::table('room_assignments')->where('room_id', $roomId)->delete();
        }

        return back()->with('success', "Successfully revoked all {$assignedCount} user(s) from the selected laboratory.");
    }

    public function userDatabase()
    {
        if (Auth::user()->role !== 'Admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Only System Administrators can access the User Database.');
        }
        $navData = $this->getNavbarData();
        $users = DB::table('users')->get();
        return view('admin.users_list', array_merge($navData, ['users' => $users]));
    }

    // 🟢 NEW: PENDING USERS METHOD
    public function pendingUsers()
    {
        if (Auth::user()->role !== 'Admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Only System Administrators can access Pending Accounts.');
        }
        $navData = $this->getNavbarData();
        
        // Fetch users where is_active is 0
        $pendingUsers = DB::table('users')->where('is_active', 0)->get();
        
        return view('admin.pending_users', array_merge($navData, ['pendingUsers' => $pendingUsers]));
    }

    public function storeUser(Request $request)
    {
        if (Auth::user()->role !== 'Admin') { abort(403); }
        
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|in:Dean,Admin,Staff',
            'is_active' => 'required|boolean'
        ]);

        DB::table('users')->insert([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => $request->role,
            'is_active' => $request->is_active,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return back()->with('success', 'New user successfully added to the system.');
    }

    public function updateUser(Request $request)
    {
        if (Auth::user()->role !== 'Admin') { abort(403); }
        $request->validate([
            'user_id' => 'required|integer',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'role' => 'required|in:Dean,Admin,Staff',
            'is_active' => 'required|boolean'
        ]);

        DB::table('users')->where('user_id', $request->user_id)->update([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'role' => $request->role,
            'is_active' => $request->is_active,
            'updated_at' => now()
        ]);

        return back()->with('success', 'User updated successfully.');
    }

    public function deleteUser(Request $request)
    {
        if (Auth::user()->role !== 'Admin') { abort(403); }
        if ($request->user_id == Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        DB::table('users')->where('user_id', $request->user_id)->delete();
        return back()->with('success', 'User removed.');
    }

    public function deviceManagement()
    {
        if (Auth::user()->role !== 'Admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Only System Administrators can access IoT Devices.');
        }
        $navData = $this->getNavbarData();
        $devices = $this->getDeviceHealthLatestQuery()->get();
        return view('admin.devices', array_merge($navData, ['devices' => $devices]));
    }

    public function analytics()
    {
        $navData = $this->getNavbarData();
        $stats = DB::table('audit_logs')
            ->select('action', DB::raw('count(*) as total'))
            ->groupBy('action')
            ->get();
        return view('admin.analytics', array_merge($navData, ['stats' => $stats]));
    }

    public function reports()
    {
        $navData = $this->getNavbarData();
        $reports = $this->getAuditLogQuery()->orderByDesc('logged_at')->limit(100)->get();
        return view('admin.reports', array_merge($navData, ['reports' => $reports]));
    }

    public function clearChatBadgesLocally($userId)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'error' => 'Unauthenticated'], 401);
        }

        try {
            $myId = Auth::id();

            $convo = DB::table('conversations')
                ->where(function($q) use ($myId, $userId) {
                    $q->where('participant_a', $myId)->where('participant_b', $userId);
                })->orWhere(function($q) use ($myId, $userId) {
                    $q->where('participant_b', $myId)->where('participant_a', $userId);
                })->first();

            if ($convo) {
                DB::table('messages')
                    ->where('convo_id', $convo->convo_id)
                    ->where('sender_id', $userId)
                    ->where('is_read', 0)
                    ->update([
                        'is_read' => 1
                    ]);

                DB::table('notifications')
                    ->where('user_id', $myId)
                    ->where('type', 'new_message')
                    ->where('is_read', 0)
                    ->update([
                        'is_read' => 1,
                        'read_at' => now()
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Thread badges synchronized cleanly.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'error' => 'SQL Execution Fault: ' . $e->getMessage()
            ], 500);
        }
    }

    public function testHardwareAlert()
    {
        if (!cache('set_audit_alerts', true)) {
            return response()->json([
                'success' => false, 
                'message' => 'Emails will not be sent because Instant Email Alerts is OFF in System Settings.'
            ]);
        }

        $alertMessage = "SIMULATION TEST: Forced entry attempt detected at the main laboratory door.";
        
        $roomId = 1; 
        
        try {
            DB::table('intrusion_alerts')->insert([
                'room_id'      => $roomId,
                'alert_type'   => 'forced_entry',
                'severity'     => 'critical',
                'description'  => $alertMessage,
                'is_resolved'  => 0,
                'triggered_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Simulation Database Record Failed: ' . $e->getMessage());
        }

        $adminUsers = DB::table('users')->where('role', 'Admin')->get();
        
        try {
            foreach ($adminUsers as $admin) {
                DB::table('notifications')->insert([
                    'user_id' => $admin->user_id,
                    'room_id' => $roomId,
                    'type'    => 'intrusion_alert', 
                    'title'   => '🚨 CRITICAL: Forced Entry',
                    'body'    => 'A simulated forced entry was recorded at Room ID: ' . $roomId,
                    'is_read' => 0,
                    'sent_at' => now()
                ]);
            }

            // 🟢 Send background Web Push to Admins (pops up even if browser tab is closed)
            \App\Services\WebPushService::sendToAdmins(
                '🚨 CRITICAL: Forced Entry',
                'A simulated forced entry was recorded at Room ID: ' . $roomId,
                url('/alerts?popup_alert=1&title=' . urlencode('🚨 CRITICAL: Forced Entry') . '&body=' . urlencode('A simulated forced entry was recorded at Room ID: ' . $roomId) . '&type=intrusion_alert'),
                'intrusion_alert'
            );
        } catch (\Exception $e) {
            Log::error('Simulation Notification Alert Failed: ' . $e->getMessage());
        }

        $adminEmails = $adminUsers->pluck('email');

        if ($adminEmails->isEmpty()) {
            return response()->json([
                'success' => false, 
                'message' => 'Recorded in Alerts table but no Admin found in the database to send email to.'
            ]);
        }

        try {
            foreach ($adminEmails as $email) {
                \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\IntrusionAlertMail($alertMessage));
            }
            return response()->json([
                'success' => true, 
                'message' => 'SUCCESS! Alert recorded in database and email sent to Admins.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Recorded in Alerts table but THERE IS AN ERROR IN EMAIL SETTINGS: ' . $e->getMessage()
            ]);
        }
    }

    public function createBackup()
    {
        if (Auth::user()->role !== 'Admin') { abort(403); }

        try {
            $tables = DB::select('SHOW TABLES');
            $dbName = env('DB_DATABASE');
            
            $sql = "-- SecureLab Database Backup\n-- Generated: " . now()->toDateTimeString() . "\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

            foreach ($tables as $table) {
                $tableName = $table->{'Tables_in_' . $dbName};
                if (Str::startsWith($tableName, 'vw_')) continue;

                $createTable = DB::select("SHOW CREATE TABLE {$tableName}");
                $sql .= "-- Structure for {$tableName}\n";
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    $sql .= "-- Data for {$tableName}\n";
                    foreach ($rows as $row) {
                        $values = array_map(function($val) {
                            return is_null($val) ? 'NULL' : "'" . addslashes($val) . "'";
                        }, (array)$row);
                        
                        $sql .= "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $sql .= "\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

            $filename = 'SecureLab_Backup_' . now()->format('Y_m_d_His') . '.sql';
            
            return new StreamedResponse(function () use ($sql) {
                echo $sql;
            }, 200, [
                'Content-Type' => 'application/sql',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

        } catch (\Exception $e) {
            Log::error('Backup Creation Failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate backup: ' . $e->getMessage());
        }
    }

    public function restoreBackup(Request $request)
    {
        if (Auth::user()->role !== 'Admin') { abort(403); }

        $request->validate([
            'backup_file' => 'required|file'
        ]);

        try {
            $sql = file_get_contents($request->file('backup_file')->getRealPath());
            
            DB::unprepared("SET FOREIGN_KEY_CHECKS = 0;");
            DB::unprepared($sql);
            DB::unprepared("SET FOREIGN_KEY_CHECKS = 1;");
            
            DB::table('audit_logs')->insert([
                'room_id' => 1,
                'user_id' => Auth::id(),
                'action' => 'remote_unlock', 
                'method' => 'remote_pwa',
                'door_state_after' => 'unlocked',
                'ip_address' => $request->ip(),
                'notes' => 'Admin initiated a full database restoration.',
                'logged_at' => now()
            ]);

            return back()->with('success', 'Database has been successfully restored from the backup file.');
        } catch (\Exception $e) {
            Log::error('Backup Restoration Failed: ' . $e->getMessage());
            return back()->with('error', 'Restoration failed. Please ensure the uploaded file is a valid SecureLab SQL backup.');
        }
    }

    public function cleanup(Request $request)
    {
        if (Auth::user()->role !== 'Admin') { abort(403); }

        try {
            Log::info('Deep System Cleanup initiated by Admin ID: ' . Auth::id());

            DB::statement("SET FOREIGN_KEY_CHECKS = 0;");
            
            DB::table('audit_logs')->delete();
            DB::table('backup_battery_logs')->delete();
            DB::table('conversations')->delete();
            DB::table('device_health_logs')->delete();
            DB::table('device_response_logs')->delete();
            DB::table('doorbell_events')->delete();
            DB::table('intrusion_alerts')->delete();
            DB::table('messages')->delete();
            DB::table('notifications')->delete();
            DB::table('room_assignments')->delete();
            DB::table('fingerprints')->delete();
            DB::table('passkeys')->delete();
            DB::table('push_subscriptions')->delete();
            DB::table('sessions')->delete();
            
            DB::statement("SET FOREIGN_KEY_CHECKS = 1;");

            DB::table('audit_logs')->insert([
                'room_id' => 1,
                'user_id' => Auth::id(),
                'action' => 'remote_lock',
                'method' => 'remote_pwa',
                'door_state_after' => 'locked',
                'ip_address' => $request->ip(),
                'notes' => 'Deep system cleanup protocol executed successfully. All logs, passkeys, and room assignments cleared.',
                'logged_at' => now()
            ]);

            return back()->with('success', 'System Cleanup Successful! All logs, alerts, passkeys, and room assignments have been cleared for a fresh start.');
        } catch (\Exception $e) {
            Log::error('System Cleanup Failed: ' . $e->getMessage());
            return back()->with('error', 'Cleanup protocol failed: ' . $e->getMessage());
        }
    }

    public function toggleOccupancy(Request $request)
    {
        $roomId = $request->input('room_id');
        $status = $request->input('status'); 

        DB::table('rooms')->where('room_id', $roomId)->update([
            'occupancy_status' => $status,
            'updated_at' => now()
        ]);

        DB::table('audit_logs')->insert([
            'room_id' => $roomId,
            'user_id' => Auth::id(),
            'action' => $status === 'occupied' ? 'remote_unlock' : 'remote_lock', 
            'method' => 'remote_pwa',
            'door_state_after' => DB::table('rooms')->where('room_id', $roomId)->value('door_state'),
            'notes' => 'Instructor marked the room as ' . strtoupper($status),
            'logged_at' => now()
        ]);

        return response()->json([
            'success' => true, 
            'new_status' => $status
        ]);
    }

    public function setHotspot(Request $request)
    {
        $request->validate([
            'room_id' => 'required|integer',
            'hotspot_ssid' => 'required|string|max:255',
            'hotspot_password' => 'required|string|max:255'
        ]);

        $assignment = DB::table('room_assignments')
            ->where('user_id', Auth::id())
            ->where('room_id', $request->room_id)
            ->where('is_active', 1)
            ->first();

        if (!$assignment) {
            return back()->with('error', 'You are not authorized to configure this laboratory.');
        }

        DB::table('rooms')->where('room_id', $request->room_id)->update([
            'backup_ssid' => $request->hotspot_ssid,
            'backup_password' => $request->hotspot_password,
            'updated_at' => now()
        ]);

        DB::table('audit_logs')->insert([
            'room_id' => $request->room_id,
            'user_id' => Auth::id(),
            'action' => 'remote_unlock', 
            'method' => 'remote_pwa',
            'door_state_after' => DB::table('rooms')->where('room_id', $request->room_id)->value('door_state'),
            'notes' => 'User configured a new Backup Mobile Hotspot.',
            'logged_at' => now()
        ]);

        return back()->with('success', 'Backup Hotspot credentials saved! The hardware will receive and use this network if the main WiFi fails.');
    }

    // =========================================================================
    // 🟢 HARDWARE WIFI CONFIGURATION (ADMIN ONLY)
    // =========================================================================
    
    /**
     * Display the Hardware WiFi Setup Page.
     */
    public function showWifiPage()
    {
        if (Auth::user()->role !== 'Admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Only System Administrators can configure hardware network settings.');
        }

        $navData = $this->getNavbarData();
        return view('admin.wifi', $navData);
    }

    /**
     * Process the updated WiFi credentials.
     */
    public function updateHardwareWifi(Request $request)
    {
        if (Auth::user()->role !== 'Admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'ssid' => 'required|string|max:32',
            'password' => 'required|string|max:64',
        ]);

        try {
            // 🟢 FIXED: We must save the new credentials to the cache 
            // so the /api/poll-commands route can send it to the ESP32!
            cache()->forever('hardware_main_wifi_ssid', $request->ssid);
            cache()->forever('hardware_main_wifi_pass', $request->password);

            // Save to Audit Logs
            DB::table('audit_logs')->insert([
                'room_id' => 1, 
                'user_id' => Auth::id(),
                'action' => 'remote_unlock', 
                'method' => 'remote_pwa',
                'door_state_after' => 'locked',
                'ip_address' => $request->ip(),
                'notes' => 'Admin updated global hardware WiFi credentials to SSID: ' . $request->ssid,
                'logged_at' => now()
            ]);

            return redirect()->route('hardware.wifi')->with('success', 'WiFi credentials successfully synced! The hardware will restart and attempt to connect to the new network shortly.');

        } catch (\Exception $e) {
            \Log::error('WiFi Sync Error: ' . $e->getMessage());
            return redirect()->route('hardware.wifi')->with('error', 'Failed to synchronize credentials to hardware.');
        }
    }
}