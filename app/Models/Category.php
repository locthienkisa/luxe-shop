<?php

declare(strict_types=1);

namespace App\Models;

final class Category extends Model
{
    protected static string $table = 'categories';

    public static function active(): array
    {
        return self::db()->query('SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order, name')->fetchAll();
    }

    public static function findBySlug(string $slug): ?array
    {
        $st = self::db()->prepare('SELECT * FROM categories WHERE slug = :s LIMIT 1');
        $st->execute(['s' => $slug]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $st = self::db()->prepare(
            'INSERT INTO categories (parent_id, name, slug, description, image, sort_order, is_active, meta_title, meta_description)
             VALUES (:parent_id, :name, :slug, :description, :image, :sort_order, :is_active, :meta_title, :meta_description)'
        );
        $st->execute($data);
        return (int) self::db()->lastInsertId();
    }

    public static function updateRow(int $id, array $data): void
    {
        $st = self::db()->prepare(
            'UPDATE categories SET parent_id=:parent_id, name=:name, slug=:slug, description=:description,
             image=:image, sort_order=:sort_order, is_active=:is_active, meta_title=:meta_title, meta_description=:meta_description
             WHERE id=:id'
        );
        $data['id'] = $id;
        $st->execute($data);
    }

    public static function delete(int $id): void
    {
        $st = self::db()->prepare('DELETE FROM categories WHERE id = :id');
        $st->execute(['id' => $id]);
    }
}
