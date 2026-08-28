<?php

namespace App\Support;

use App\Models\Communication;
use App\Models\CommunicationReply;
use App\Models\SupportMessage;
use App\Services\Communications\CommunicationSchema;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

final class FluxAdminUnreadBadges
{
    public static function counts(?Authenticatable $user = null): array
    {
        $user ??= FluxAdminAccess::user();
        $id = (int) ($user?->getAuthIdentifier() ?? 0);
        $remember = function () use ($user): array {
            return [
                'inbox' => self::inboxUnread(),
                'notifications' => self::notificationsUnread($user),
            ];
        };

        if ($id <= 0) {
            return $remember();
        }

        return Cache::remember('flux-admin.unread.counts.'.$id, 3, $remember);
    }

    public static function inboxUnread(): int
    {
        if (! Schema::hasTable('support_messages')) {
            return 0;
        }

        return (int) SupportMessage::query()
            ->where('sender_type', 'customer')
            ->whereNull('read_at_staff')
            ->count();
    }

    public static function notificationsUnread(?Authenticatable $user = null): int
    {
        if (! app(CommunicationSchema::class)->ready()) {
            return 0;
        }

        $since = self::notificationsSeenAt($user);
        $count = (int) Communication::query()
            ->when(\Illuminate\Support\Facades\Schema::hasColumn('communications', 'staff_hidden_at'), fn ($q) => $q->whereNull('staff_hidden_at'))
            ->where('created_at', '>', $since)
            ->count();

        if (app(CommunicationSchema::class)->repliesReady()) {
            $count += (int) CommunicationReply::query()
                ->where('author_type', 'customer')
                ->where('created_at', '>', $since)
                ->count();
        }

        return $count;
    }

    public static function markNotificationsSeen(?Authenticatable $user = null): void
    {
        $user ??= FluxAdminAccess::user();
        $id = (int) ($user?->getAuthIdentifier() ?? 0);
        if ($id <= 0) {
            return;
        }

        Cache::put(self::cacheKey($id), now()->toIso8601String(), now()->addDays(30));
    }

    private static function notificationsSeenAt(?Authenticatable $user): string
    {
        $id = (int) ($user?->getAuthIdentifier() ?? 0);
        if ($id <= 0) {
            return now()->toIso8601String();
        }

        $stored = Cache::get(self::cacheKey($id));
        if (is_string($stored) && $stored !== '') {
            return $stored;
        }

        $initial = now()->toIso8601String();
        Cache::put(self::cacheKey($id), $initial, now()->addDays(30));

        return $initial;
    }

    private static function cacheKey(int $userId): string
    {
        return 'flux-admin.unread.notifications.seen.'.$userId;
    }
}
