<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Auth;
use App\Core\Request;

final class AdminLog extends Model
{
    protected static string $table = 'admin_logs';

    public static function record(string $action, ?string $entity = null, ?int $entityId = null, array $meta = []): void
    {
        try {
            $st = self::db()->prepare(
                'INSERT INTO admin_logs (user_id, action, entity, entity_id, ip, user_agent, meta_json)
                 VALUES (:user_id, :action, :entity, :entity_id, :ip, :ua, :meta)'
            );
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
            $ua = substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
            $st->execute([
                'user_id' => Auth::id(),
                'action' => $action,
                'entity' => $entity,
                'entity_id' => $entityId,
                'ip' => $ip,
                'ua' => $ua,
                'meta' => $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
            ]);
        } catch (\Throwable) {
            // logging must never break the request
        }
    }
}
