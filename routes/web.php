<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes - SecureLab Smart Access System
|--------------------------------------------------------------------------
*/

// --- SYSTEM MAINTENANCE ROUTES ---
Route::get('/clear-cache', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    return 'Laravel Cache Cleared Successfully!';
});

Route::get('/setup-storage', function () {
    if (!Auth::check() || Auth::user()->role !== 'Admin') {
        abort(403, 'Unauthorized maintenance request.');
    }
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    return 'Storage linked successfully!';
});

Route::get('/run-push-migration', function () {
    if (!Auth::check() || Auth::user()->role !== 'Admin') {
        abort(403, 'Unauthorized maintenance request.');
    }
    \App\Services\WebPushService::ensureTableExists();
    return 'Push subscriptions table verified and ready!';
});

// ROUTE TO DOWNLOAD ANDROID APK PACKAGE
Route::get('/download-apk', function () {
    $path = public_path('downloads/SecureLab.apk');
    if (!file_exists($path)) {
        abort(404, 'SecureLab APK package not found on server.');
    }
    return response()->download($path, 'SecureLab.apk', [
        'Content-Type' => 'application/vnd.android.package-archive',
        'Content-Disposition' => 'attachment; filename="SecureLab.apk"',
    ]);
})->name('app.download.apk');

// Public Auth Status Probe (Clean 200 JSON for guest/auth check without 401 console error)
Route::get('/auth-status', function () {
    return response()->json([
        'authenticated' => Auth::check(),
        'role' => Auth::check() ? Auth::user()->role : null,
    ]);
})->name('auth.status');

// 1. Landing Page (Public Access)
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('index');
})->name('index')->middleware('prevent-back');

// Redirect login view requests back to index portal
Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('index')->with('error', 'Please login through the main portal.');
})->name('login')->middleware('prevent-back');

// 2. Authentication Routes (with Rate Limiting / Brute-Force Protection)
Route::post('/register', [LoginController::class, 'register'])->middleware('throttle:6,1')->name('register');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:6,1')->name('login.post'); 
Route::post('/login/passkey', [LoginController::class, 'loginWithPasskey'])->middleware('throttle:10,1')->name('login.passkey');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// OTP & Forgot Password (with Rate Limiting)
Route::get('/verify-otp', [LoginController::class, 'showOtpForm'])->name('otp.verify');
Route::post('/verify-otp', [LoginController::class, 'verifyOtp'])->middleware('throttle:6,1')->name('otp.verify.post');
Route::post('/resend-otp', [LoginController::class, 'resendOtp'])->middleware('throttle:3,1')->name('otp.resend');

Route::get('/forgot-password', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [LoginController::class, 'sendResetOtp'])->middleware('throttle:3,1')->name('password.email');
Route::get('/reset-password', [LoginController::class, 'showResetPasswordForm'])->name('password.reset.form');
Route::post('/reset-password', [LoginController::class, 'updatePassword'])->middleware('throttle:5,1')->name('password.update');

// 3. Protected Dashboard Routes 
Route::middleware(['auth', 'prevent-back'])->group(function () {
    
    // --- MAIN MENU ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/audit-logs', [DashboardController::class, 'logs'])->name('audit.logs');
    Route::get('/alerts', [DashboardController::class, 'alerts'])->name('dashboard.alerts');
    Route::get('/test-hardware-alert', [DashboardController::class, 'testHardwareAlert'])->name('test.hardware-alert');

    // --- MESSAGES / INBOX ---
    Route::get('/messages', [DashboardController::class, 'inbox'])->name('messages.index');
    Route::get('/messages/inbox', [DashboardController::class, 'inbox'])->name('messages.inbox');
    Route::post('/messages/send', [DashboardController::class, 'sendMessage'])->name('messages.send');
    Route::post('/messages/react/{id}', [DashboardController::class, 'reactMessage'])->name('messages.react');
    Route::post('/messages/delete/{id}', [DashboardController::class, 'deleteMessage'])->name('messages.delete');
    Route::post('/messages/delete-conversation/{user}', [DashboardController::class, 'deleteConversation'])->name('messages.deleteConversation');
    Route::post('/messages/read-status', [DashboardController::class, 'readStatus'])->name('messages.readStatus');
    Route::get('/messages/unread-count', [DashboardController::class, 'getUnreadCount'])->name('messages.unreadCount');
    Route::post('/messages/read', [DashboardController::class, 'markAsRead'])->name('messages.read');
    Route::post('/messages/read-status/{user}', [DashboardController::class, 'clearChatBadgesLocally'])->name('messages.readStatusLive');
    Route::get('/messages/thread/{user}', [DashboardController::class, 'getConversationMessages'])->name('messages.thread');

    // --- WEB PUSH NOTIFICATIONS ---
    Route::post('/api/push-subscribe', function (\Illuminate\Http\Request $request) {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $endpoint = $request->input('endpoint');
        $key = $request->input('keys.p256dh');
        $token = $request->input('keys.auth');
        $deviceType = $request->input('device_type', 'browser');

        if (!$endpoint) {
            return response()->json(['success' => false, 'message' => 'Endpoint missing'], 422);
        }

        \App\Services\WebPushService::ensureTableExists();

        DB::table('push_subscriptions')->updateOrInsert(
            ['endpoint' => $endpoint],
            [
                'user_id' => $user->user_id,
                'public_key' => $key,
                'auth_token' => $token,
                'device_type' => $deviceType,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Push subscription saved.']);
    })->name('push.subscribe');

    Route::post('/api/push-unsubscribe', function (\Illuminate\Http\Request $request) {
        $endpoint = $request->input('endpoint');
        if ($endpoint) {
            \App\Services\WebPushService::ensureTableExists();
            DB::table('push_subscriptions')->where('endpoint', $endpoint)->delete();
        }
        return response()->json(['success' => true]);
    })->name('push.unsubscribe');

    Route::get('/test-push', function () {
        $user = Auth::user();
        if (!$user) return redirect('/login');

        $sent = \App\Services\WebPushService::sendToUser(
            $user->user_id,
            '🔔 Test Background Alert',
            'SecureLab Background Push is working! You will receive alerts even when your browser tab is closed.',
            route('dashboard.alerts') . '?popup_alert=1&title=Test%20Background%20Alert&body=SecureLab%20Background%20Push%20works!&type=doorbell',
            'doorbell'
        );

        return response()->json([
            'success' => true,
            'sent_count' => $sent,
            'message' => $sent > 0 
                ? "Background push sent to {$sent} registered device(s)!" 
                : "No active push subscription found for this browser. Please allow notifications on this site first."
        ]);
    })->name('push.test');

    // --- AUTHORIZED ACCESS & REMOTE CONTROL ---
    Route::get('/my-assigned-rooms', [DashboardController::class, 'myRooms'])->name('rooms.my');
    Route::post('/my-rooms/remote-control', [DashboardController::class, 'remoteControl'])->name('rooms.remote-control');
    Route::post('/my-rooms/enroll', [DashboardController::class, 'startEnrollment'])->name('rooms.enroll');
    Route::post('/rooms/unenroll', [DashboardController::class, 'deleteFingerprint'])->name('rooms.unenroll');
    Route::post('/my-rooms/set-pin', [DashboardController::class, 'setPin'])->name('rooms.set-pin');
    Route::post('/my-rooms/toggle-occupancy', [DashboardController::class, 'toggleOccupancy'])->name('rooms.toggle-occupancy');
    Route::post('/my-rooms/set-hotspot', [DashboardController::class, 'setHotspot'])->name('rooms.set-hotspot');

    // --- MY PROFILE & USER PROFILE ---
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile.index');
    Route::post('/profile/update-info', [DashboardController::class, 'updateProfileInfo'])->name('profile.update.info');
    Route::post('/profile/update-password', [DashboardController::class, 'updateProfilePassword'])->name('profile.update.password');
    Route::post('/profile/remove-passkey', [DashboardController::class, 'removePasskey'])->name('profile.remove.passkey');
    Route::post('/profile/remove-photo', [DashboardController::class, 'removeProfilePhoto'])->name('profile.remove.photo');
    Route::get('/user/{id}/profile', [DashboardController::class, 'viewProfile'])->name('profile.view');
    
    // PASSKEY REGISTRATION AND DARK MODE
    Route::post('/profile/passkey/register', [DashboardController::class, 'registerPasskey'])->name('profile.passkey.register');
    Route::post('/profile/toggle-theme', [DashboardController::class, 'toggleTheme'])->name('profile.toggle.theme');

    // --- SYSTEM SETTINGS ---
    Route::get('/system-settings', [DashboardController::class, 'settings'])->name('system.settings');
    Route::post('/system-settings', [DashboardController::class, 'updateSettings'])->name('system.settings.update');
    Route::post('/settings/fingerprints/admin-delete', [DashboardController::class, 'adminDeleteFingerprint'])->name('settings.fingerprints.delete');

    // --- MASTER CONFIGURATION & ADMIN TOOLS ---
    Route::get('/manage-rooms', [DashboardController::class, 'manageRooms'])->name('rooms.manage');
    Route::post('/manage-rooms/store', [DashboardController::class, 'storeRoom'])->name('rooms.store');
    Route::post('/manage-rooms/update', [DashboardController::class, 'updateRoom'])->name('rooms.update');
    
    // DELETE ROUTE FOR ROOMS
    Route::post('/manage-rooms/delete', [DashboardController::class, 'deleteRoom'])->name('rooms.delete');
    
    Route::get('/assign-users', [DashboardController::class, 'assignUsers'])->name('users.assign');
    Route::post('/assign-users/save', [DashboardController::class, 'saveAssignment'])->name('users.assign.save');
    Route::delete('/assign-users/revoke/{user_id}/{room_id}', [DashboardController::class, 'revokeAssignment'])->name('users.assign.revoke');
    Route::delete('/users/assign/revoke-all/{room_id}', [DashboardController::class, 'revokeAllAssignments'])->name('users.assign.revokeAll');
    
    // USER DATABASE ROUTES
    Route::get('/user-database', [DashboardController::class, 'userDatabase'])->name('users.database');
    Route::get('/users', [DashboardController::class, 'userDatabase'])->name('users.index');
    Route::post('/user-database/store', [DashboardController::class, 'storeUser'])->name('users.store'); 
    Route::post('/user-database/update', [DashboardController::class, 'updateUser'])->name('users.update');
    Route::delete('/user-database/delete', [DashboardController::class, 'deleteUser'])->name('users.delete');

    // PENDING ACCOUNTS APPROVAL ROUTE
    Route::get('/pending-accounts', [DashboardController::class, 'pendingUsers'])->name('users.pending');
    
    Route::get('/iot-devices', [DashboardController::class, 'deviceManagement'])->name('devices.manage');

    // 🟢 NEW: HARDWARE WIFI CONFIGURATION ROUTES
    Route::get('/settings/hardware-wifi', [DashboardController::class, 'showWifiPage'])->name('hardware.wifi');
    Route::post('/settings/hardware-wifi/update', [DashboardController::class, 'updateHardwareWifi'])->name('hardware.wifi.update');

    // --- DATA MANAGEMENT ---
    Route::post('/settings/backup/create', [DashboardController::class, 'createBackup'])->name('settings.backup.create');
    Route::post('/settings/backup/restore', [DashboardController::class, 'restoreBackup'])->name('settings.backup.restore');
    Route::post('/settings/cleanup', [DashboardController::class, 'cleanup'])->name('settings.cleanup');

    // --- ANALYTICS & REPORTS ---
    Route::get('/usage-analytics', [DashboardController::class, 'analytics'])->name('dean.analytics');
    Route::get('/monthly-reports', [DashboardController::class, 'reports'])->name('dean.reports');
});