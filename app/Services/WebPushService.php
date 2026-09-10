<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class WebPushService
{
    /**
     * Get or build WebPush instance
     */
    public static function getWebPushInstance(): ?WebPush
    {
        $publicKey = env('VAPID_PUBLIC_KEY', 'BKxeNffS8w9m0gPbapKT59yDxq4iEHLHKodzvwH9vylCPqXw7MNhesdBtnPjEhefvKs99b6CTLv5bv0wMBv4Zzo');
        $privateKey = env('VAPID_PRIVATE_KEY', 'PJlmfLjRXvnzmLlwcKrPorcomcMNW2JpeZGau73wdn8');
        $subject = env('VAPID_SUBJECT', 'mailto:admin@securelab-talibon.xyz');

        if (!$publicKey || !$privateKey) {
            Log::warning('WebPush VAPID keys are missing.');
            return null;
        }

        try {
            return new WebPush([
                'VAPID' => [
                    'subject' => $subject,
                    'publicKey' => $publicKey,
                    'privateKey' => $privateKey,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('WebPush initialization error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Ensure push_subscriptions table exists
     */
    public static function ensureTableExists(): void
    {
        try {
            if (!Schema::hasTable('push_subscriptions')) {
                Schema::create('push_subscriptions', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id')->index();
                    $table->text('endpoint');
                    $table->string('public_key', 255)->nullable();
                    $table->string('auth_token', 255)->nullable();
                    $table->string('device_type', 50)->nullable()->default('browser');
                    $table->timestamps();
                });
            }
        } catch (\Exception $e) {
            Log::warning('Could not ensure push_subscriptions table: ' . $e->getMessage());
        }
    }

    /**
     * Send Web Push notification to a specific user
     */
    public static function sendToUser(int $userId, string $title, string $body, string $targetUrl = '/', string $type = 'general'): int
    {
        return self::sendToUsers([$userId], $title, $body, $targetUrl, $type);
    }

    /**
     * Send Web Push notification to all Admin & Dean users
     */
    public static function sendToAdmins(string $title, string $body, string $targetUrl = '/', string $type = 'alert'): int
    {
        try {
            $adminIds = DB::table('users')
                ->whereIn('role', ['Admin', 'Dean'])
                ->where('is_active', 1)
                ->pluck('user_id')
                ->toArray();

            if (empty($adminIds)) {
                return 0;
            }

            return self::sendToUsers($adminIds, $title, $body, $targetUrl, $type);
        } catch (\Exception $e) {
            Log::error('WebPush sendToAdmins error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Send Web Push notification to multiple user IDs
     */
    public static function sendToUsers(array $userIds, string $title, string $body, string $targetUrl = '/', string $type = 'general'): int
    {
        self::ensureTableExists();

        $webPush = self::getWebPushInstance();
        if (!$webPush) {
            return 0;
        }

        try {
            $subscriptions = DB::table('push_subscriptions')
                ->whereIn('user_id', $userIds)
                ->get();

            if ($subscriptions->isEmpty()) {
                return 0;
            }

            $payload = json_encode([
                'title' => $title,
                'body' => $body,
                'icon' => asset('assets/img/tpc-logo.jpg'),
                'badge' => asset('assets/img/tpc-logo.jpg'),
                'url' => $targetUrl,
                'type' => $type,
                'vibrate' => [200, 100, 200, 100, 200],
                'tag' => 'securelab-' . $type . '-' . time(),
                'timestamp' => round(microtime(true) * 1000)
            ]);

            $queuedCount = 0;
            foreach ($subscriptions as $sub) {
                if (empty($sub->endpoint)) continue;

                $subscription = Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->public_key,
                    'authToken' => $sub->auth_token,
                ]);

                $webPush->queueNotification($subscription, $payload);
                $queuedCount++;
            }

            if ($queuedCount === 0) {
                return 0;
            }

            $successCount = 0;
            $expiredEndpoints = [];

            foreach ($webPush->flush() as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                if ($report->isSuccess()) {
                    $successCount++;
                } else {
                    $statusCode = $report->getResponse() ? $report->getResponse()->getStatusCode() : null;
                    // 410 Gone or 404 Not Found indicates expired or revoked subscription
                    if (in_array($statusCode, [404, 410])) {
                        $expiredEndpoints[] = $endpoint;
                    }
                    Log::warning("WebPush failed to {$endpoint}: " . $report->getReason());
                }
            }

            // Remove expired/invalid subscriptions
            if (!empty($expiredEndpoints)) {
                DB::table('push_subscriptions')->whereIn('endpoint', $expiredEndpoints)->delete();
            }

            return $successCount;
        } catch (\Exception $e) {
            Log::error('WebPush dispatch error: ' . $e->getMessage());
            return 0;
        }
    }
}
