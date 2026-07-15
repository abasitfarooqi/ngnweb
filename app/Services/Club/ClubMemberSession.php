<?php

namespace App\Services\Club;

use App\Models\ClubMember;
use Illuminate\Support\Facades\Cookie;

/**
 * Club membership login is separate from portal customer auth.
 * Persists via session + long-lived cookie so page navigations and
 * session()->regenerate() elsewhere do not force a club re-login.
 */
class ClubMemberSession
{
    public const SESSION_KEY = 'club_member_id';

    public const COOKIE = 'ngn_club_member';

    /** Keep club login for 30 days or until explicit logout. */
    public const COOKIE_MINUTES = 60 * 24 * 30;

    public static function login(ClubMember $member): void
    {
        session([self::SESSION_KEY => (int) $member->id]);
        self::queueCookie((int) $member->id);
    }

    public static function logout(): void
    {
        session()->forget([self::SESSION_KEY, 'user_session_id']);
        Cookie::queue(Cookie::forget(self::COOKIE));
    }

    public static function id(): ?int
    {
        $fromSession = session(self::SESSION_KEY);
        if ($fromSession) {
            return (int) $fromSession;
        }

        $fromCookie = self::idFromCookie();
        if ($fromCookie === null) {
            return null;
        }

        if (! ClubMember::query()->whereKey($fromCookie)->where('is_active', true)->exists()) {
            Cookie::queue(Cookie::forget(self::COOKIE));

            return null;
        }

        session([self::SESSION_KEY => $fromCookie]);

        return $fromCookie;
    }

    public static function member(): ?ClubMember
    {
        $id = self::id();
        if ($id === null) {
            return null;
        }

        return ClubMember::query()->find($id);
    }

    public static function check(): bool
    {
        return self::id() !== null;
    }

    /**
     * Rehydrate session from cookie after another part of the site regenerated the session.
     */
    public static function restoreIntoSession(): void
    {
        if (session()->has(self::SESSION_KEY)) {
            return;
        }

        self::id();
    }

    private static function idFromCookie(): ?int
    {
        $raw = request()->cookie(self::COOKIE);
        if ($raw === null || $raw === '') {
            return null;
        }

        $id = (int) $raw;

        return $id > 0 ? $id : null;
    }

    private static function queueCookie(int $memberId): void
    {
        Cookie::queue(cookie(
            self::COOKIE,
            (string) $memberId,
            self::COOKIE_MINUTES,
            '/',
            config('session.domain'),
            (bool) config('session.secure'),
            true,
            false,
            config('session.same_site', 'lax')
        ));
    }
}
