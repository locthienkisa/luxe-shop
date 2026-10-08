<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;
use App\Models\AdminLog;

final class Auth
{
    private static ?array $user = null;

    public static function boot(): void
    {
        $id = Session::get('user_id');
        if ($id) {
            $user = User::find((int) $id);
            if ($user && (int) $user['is_active'] === 1) {
                self::$user = $user;
            } else {
                Session::forget('user_id');
            }
        }
    }

    public static function user(): ?array
    {
        return self::$user;
    }

    public static function id(): ?int
    {
        return self::$user ? (int) self::$user['id'] : null;
    }

    public static function check(): bool
    {
        return self::$user !== null;
    }

    public static function role(): string
    {
        return (string) (self::$user['role_slug'] ?? 'guest');
    }

    public static function hasRole(string ...$roles): bool
    {
        return in_array(self::role(), $roles, true);
    }

    public static function isStaff(): bool
    {
        return self::hasRole('staff', 'admin', 'super_admin');
    }

    public static function isAdmin(): bool
    {
        return self::hasRole('admin', 'super_admin');
    }

    public static function login(array $user): void
    {
        $oldSid = Session::id();
        Session::regenerate(true);
        Session::set('user_id', (int) $user['id']);
        self::$user = $user;
        if ($oldSid !== '' && class_exists(\App\Services\CartService::class)) {
            try {
                \App\Services\CartService::claimGuestCart($oldSid);
            } catch (\Throwable) {
            }
        }
        Logger::security('login', ['user_id' => $user['id'], 'email' => $user['email']]);
        AdminLog::record('login', 'user', (int) $user['id']);
    }

    public static function roleRank(?string $slug = null): int
    {
        return match ($slug ?? self::role()) {
            'super_admin' => 3,
            'admin' => 2,
            'staff' => 1,
            default => 0,
        };
    }

    public static function logout(): void
    {
        if (self::$user) {
            Logger::security('logout', ['user_id' => self::$user['id']]);
            AdminLog::record('logout', 'user', (int) self::$user['id']);
        }
        self::$user = null;
        Session::forget('user_id');
        Session::regenerate(true);
    }
}
