<?php

declare(strict_types=1);

namespace App\Models;

final class Product extends Model
{
    protected static string $table = 'products';

    public static function findBySlug(string $slug): ?array
    {
        $st = self::db()->prepare('SELECT * FROM products WHERE slug = :s LIMIT 1');
        $st->execute(['s' => $slug]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function images(int $productId): array
    {
        $st = self::db()->prepare('SELECT * FROM product_images WHERE product_id = :id ORDER BY is_primary DESC, sort_order, id');
        $st->execute(['id' => $productId]);
        return $st->fetchAll();
    }

    public static function variants(int $productId): array
    {
        $st = self::db()->prepare('SELECT * FROM product_variants WHERE product_id = :id AND is_active = 1 ORDER BY id');
        $st->execute(['id' => $productId]);
        return $st->fetchAll();
    }

    public static function categoriesOf(int $productId): array
    {
        $st = self::db()->prepare(
            'SELECT c.* FROM categories c JOIN product_categories pc ON pc.category_id = c.id WHERE pc.product_id = :id'
        );
        $st->execute(['id' => $productId]);
        return $st->fetchAll();
    }

    public static function primaryImage(int $productId): ?string
    {
        $st = self::db()->prepare(
            'SELECT path FROM product_images WHERE product_id = :id ORDER BY is_primary DESC, sort_order LIMIT 1'
        );
        $st->execute(['id' => $productId]);
        $p = $st->fetchColumn();
        return $p ?: null;
    }

    public static function search(array $filters, int $offset, int $limit): array
    {
        $where = ['p.is_active = 1'];
        $params = [];
        $join = '';

        if (!empty($filters['q'])) {
            $where[] = '(p.name LIKE :q OR p.sku LIKE :q OR p.brand LIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['brand'])) {
            $where[] = 'p.brand = :brand';
            $params['brand'] = $filters['brand'];
        }
        if (!empty($filters['category'])) {
            $join .= ' JOIN product_categories pc ON pc.product_id = p.id JOIN categories c ON c.id = pc.category_id ';
            $where[] = '(c.slug = :cat OR c.id = :catid)';
            $params['cat'] = $filters['category'];
            $params['catid'] = (int) $filters['category'];
        }
        if (isset($filters['min_price']) && $filters['min_price'] !== '' && $filters['min_price'] !== null) {
            $where[] = 'COALESCE(NULLIF(p.sale_price,0), p.price) >= :minp';
            $params['minp'] = (float) $filters['min_price'];
        }
        if (isset($filters['max_price']) && $filters['max_price'] !== '' && $filters['max_price'] !== null) {
            $where[] = 'COALESCE(NULLIF(p.sale_price,0), p.price) <= :maxp';
            $params['maxp'] = (float) $filters['max_price'];
        }
        if (!empty($filters['rating'])) {
            $where[] = 'p.rating_avg >= :rating';
            $params['rating'] = (float) $filters['rating'];
        }
        if (!empty($filters['in_stock'])) {
            $where[] = 'p.stock > 0';
        }
        if (!empty($filters['sale'])) {
            $where[] = 'p.sale_price IS NOT NULL AND p.sale_price > 0 AND p.sale_price < p.price';
        }

        $order = match ($filters['sort'] ?? 'newest') {
            'price_asc' => 'COALESCE(NULLIF(p.sale_price,0), p.price) ASC',
            'price_desc' => 'COALESCE(NULLIF(p.sale_price,0), p.price) DESC',
            'best' => 'p.sold_count DESC',
            'rating' => 'p.rating_avg DESC',
            default => 'p.created_at DESC',
        };

        $sqlWhere = implode(' AND ', $where);
        $countSql = "SELECT COUNT(DISTINCT p.id) FROM products p $join WHERE $sqlWhere";
        $cst = self::db()->prepare($countSql);
        $cst->execute($params);
        $total = (int) $cst->fetchColumn();

        $sql = "SELECT DISTINCT p.*, (
                    SELECT path FROM product_images pi WHERE pi.product_id = p.id ORDER BY is_primary DESC, sort_order LIMIT 1
                ) AS image
                FROM products p $join WHERE $sqlWhere ORDER BY $order LIMIT $limit OFFSET $offset";
        $st = self::db()->prepare($sql);
        $st->execute($params);
        return ['total' => $total, 'rows' => $st->fetchAll()];
    }

    public static function collection(string $type, int $limit = 8): array
    {
        $where = 'p.is_active = 1';
        $order = 'p.created_at DESC';
        if ($type === 'featured') {
            $where .= ' AND p.is_featured = 1';
        } elseif ($type === 'sale') {
            $where .= ' AND p.sale_price IS NOT NULL AND p.sale_price > 0 AND p.sale_price < p.price';
            $order = '(p.price - p.sale_price) DESC';
        } elseif ($type === 'best') {
            $order = 'p.sold_count DESC';
        }
        $sql = "SELECT p.*, (
                    SELECT path FROM product_images pi WHERE pi.product_id = p.id ORDER BY is_primary DESC, sort_order LIMIT 1
                ) AS image
                FROM products p WHERE $where ORDER BY $order LIMIT " . (int) $limit;
        return self::db()->query($sql)->fetchAll();
    }

    public static function brands(): array
    {
        return self::db()->query('SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND brand <> \'\' ORDER BY brand')->fetchAll();
    }

    public static function create(array $data): int
    {
        $st = self::db()->prepare(
            'INSERT INTO products (name, slug, sku, brand, short_description, description, price, sale_price, stock, has_variants, is_featured, is_active, meta_title, meta_description)
             VALUES (:name,:slug,:sku,:brand,:short_description,:description,:price,:sale_price,:stock,:has_variants,:is_featured,:is_active,:meta_title,:meta_description)'
        );
        $st->execute($data);
        return (int) self::db()->lastInsertId();
    }

    public static function updateRow(int $id, array $data): void
    {
        $data['id'] = $id;
        $st = self::db()->prepare(
            'UPDATE products SET name=:name, slug=:slug, sku=:sku, brand=:brand, short_description=:short_description,
             description=:description, price=:price, sale_price=:sale_price, stock=:stock, has_variants=:has_variants,
             is_featured=:is_featured, is_active=:is_active, meta_title=:meta_title, meta_description=:meta_description
             WHERE id=:id'
        );
        $st->execute($data);
    }

    public static function delete(int $id): void
    {
        self::db()->prepare('DELETE FROM products WHERE id = :id')->execute(['id' => $id]);
    }

    public static function syncCategories(int $productId, array $categoryIds): void
    {
        self::db()->prepare('DELETE FROM product_categories WHERE product_id = :id')->execute(['id' => $productId]);
        $st = self::db()->prepare('INSERT INTO product_categories (product_id, category_id) VALUES (:p, :c)');
        foreach ($categoryIds as $cid) {
            $st->execute(['p' => $productId, 'c' => (int) $cid]);
        }
    }

    public static function addImage(int $productId, string $path, bool $primary = false): void
    {
        self::db()->prepare(
            'INSERT INTO product_images (product_id, path, is_primary) VALUES (:p, :path, :pri)'
        )->execute(['p' => $productId, 'path' => $path, 'pri' => $primary ? 1 : 0]);
    }

    public static function availableStock(int $productId, ?int $variantId): int
    {
        if ($variantId) {
            $st = self::db()->prepare('SELECT stock FROM product_variants WHERE id = :id AND product_id = :p');
            $st->execute(['id' => $variantId, 'p' => $productId]);
            return (int) $st->fetchColumn();
        }
        $st = self::db()->prepare('SELECT stock FROM products WHERE id = :id');
        $st->execute(['id' => $productId]);
        return (int) $st->fetchColumn();
    }

    public static function adjustStock(int $productId, ?int $variantId, int $delta): void
    {
        if ($variantId) {
            self::db()->prepare('UPDATE product_variants SET stock = stock + :d WHERE id = :id')->execute(['d' => $delta, 'id' => $variantId]);
        }
        self::db()->prepare('UPDATE products SET stock = stock + :d WHERE id = :id')->execute(['d' => $delta, 'id' => $productId]);
    }

    public static function recountRating(int $productId): void
    {
        self::db()->prepare(
            'UPDATE products SET rating_avg = COALESCE((SELECT AVG(rating) FROM reviews WHERE product_id = :id AND status = \'approved\'),0),
             rating_count = (SELECT COUNT(*) FROM reviews WHERE product_id = :id2 AND status = \'approved\')
             WHERE id = :id3'
        )->execute(['id' => $productId, 'id2' => $productId, 'id3' => $productId]);
    }

    public static function lowStock(int $threshold = 5): array
    {
        $st = self::db()->prepare(
            'SELECT p.*, (SELECT path FROM product_images pi WHERE pi.product_id=p.id ORDER BY is_primary DESC LIMIT 1) AS image
             FROM products p WHERE p.stock <= :t ORDER BY p.stock ASC LIMIT 50'
        );
        $st->execute(['t' => $threshold]);
        return $st->fetchAll();
    }
}
