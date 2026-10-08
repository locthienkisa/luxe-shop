<?php

declare(strict_types=1);

namespace App\Core;

final class Uploader
{
    private const ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    private const ALLOWED_MIME = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    private const MAX_BYTES = 3_000_000;

    public static function image(array $file, string $subdir): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Tải tệp thất bại.');
        }
        if (($file['size'] ?? 0) > self::MAX_BYTES) {
            throw new \RuntimeException('Tệp vượt quá 3MB.');
        }

        $original = (string) ($file['name'] ?? '');
        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXT, true)) {
            throw new \RuntimeException('Định dạng tệp không được phép.');
        }

        $tmp = (string) $file['tmp_name'];
        if (!is_uploaded_file($tmp)) {
            throw new \RuntimeException('Tệp không hợp lệ.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmp) ?: '';
        if (!in_array($mime, self::ALLOWED_MIME, true)) {
            throw new \RuntimeException('MIME type không hợp lệ.');
        }

        $info = @getimagesize($tmp);
        if ($info === false) {
            throw new \RuntimeException('Không phải tệp hình ảnh.');
        }

        $name = bin2hex(random_bytes(16)) . '.' . $ext;
        $dir = dirname(__DIR__, 2) . '/public/uploads/' . $subdir;
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new \RuntimeException('Không tạo được thư mục upload.');
        }

        $dest = $dir . '/' . $name;
        if (!move_uploaded_file($tmp, $dest)) {
            throw new \RuntimeException('Không lưu được tệp.');
        }

        return 'uploads/' . $subdir . '/' . $name;
    }
}
