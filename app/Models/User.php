<?php

declare(strict_types=1);

namespace App\Models;

final class User extends Model
{
    protected static string $table = 'users';

    public static function find(int $id): ?array
    {
        $st = self::db()->prepare(
            'SELECT u.*, r.slug AS role_slug, r.name AS role_name
             FROM users u JOIN roles r ON r.id = u.role_id WHERE u.id = :id LIMIT 1'
        );
        $st->execute(['id' => $id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $st = self::db()->prepare(
            'SELECT u.*, r.slug AS role_slug, r.name AS role_name
             FROM users u JOIN roles r ON r.id = u.role_id WHERE u.email = :email LIMIT 1'
        );
        $st->execute(['email' => $email]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $st = self::db()->prepare(
            'INSERT INTO users (role_id, email, password_hash, full_name, phone, is_active, must_change_password)
             VALUES (:role_id, :email, :password_hash, :full_name, :phone, :is_active, :must_change_password)'
        );
        $st->execute([
            'role_id' => $data['role_id'],
            'email' => $data['email'],
            'password_hash' => $data['password_hash'],
            'full_name' => $data['full_name'],
            'phone' => $data['phone'] ?? null,
            'is_active' => $data['is_active'] ?? 1,
            'must_change_password' => $data['must_change_password'] ?? 0,
        ]);
        return (int) self::db()->lastInsertId();
    }

    public static function updateProfile(int $id, array $data): void
    {
        $fields = [];
        $params = ['id' => $id];
        foreach (['full_name', 'phone', 'avatar', 'password_hash', 'role_id', 'is_active', 'must_change_password', 'last_login_at', 'last_login_ip'] as $f) {
            if (array_key_exists($f, $data)) {
                $fields[] = "`$f` = :$f";
                $params[$f] = $data[$f];
            }
        }
        if (!$fields) {
            return;
        }
        self::db()->prepare('UPDATE users SET ' . implode(',', $fields) . ' WHERE id = :id')->execute($params);
    }

    public static function paginate(string $q, ?int $roleId, ?int $active, int $offset, int $limit): array
    {
        $where = ['1=1'];
        $params = [];
        if ($q !== '') {
            $where[] = '(u.email LIKE :q OR u.full_name LIKE :q)';
            $params['q'] = '%' . $q . '%';
        }
        if ($roleId) {
            $where[] = 'u.role_id = :role_id';
            $params['role_id'] = $roleId;
        }
        if ($active !== null) {
            $where[] = 'u.is_active = :active';
            $params['active'] = $active;
        }
        $sqlWhere = implode(' AND ', $where);
        $count = self::db()->prepare("SELECT COUNT(*) FROM users u WHERE $sqlWhere");
        $count->execute($params);
        $total = (int) $count->fetchColumn();
        $st = self::db()->prepare(
            "SELECT u.*, r.slug AS role_slug, r.name AS role_name FROM users u
             JOIN roles r ON r.id = u.role_id WHERE $sqlWhere ORDER BY u.id DESC LIMIT $limit OFFSET $offset"
        );
        $st->execute($params);
        return ['total' => $total, 'rows' => $st->fetchAll()];
    }

    public static function roleIdBySlug(string $slug): int
    {
        $st = self::db()->prepare('SELECT id FROM roles WHERE slug = :s');
        $st->execute(['s' => $slug]);
        return (int) $st->fetchColumn();
    }
}
