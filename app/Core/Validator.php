<?php

declare(strict_types=1);

namespace App\Core;

final class Validator
{
    public static function validate(array $data, array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $ruleStr) {
            $value = $data[$field] ?? null;
            foreach (explode('|', $ruleStr) as $rule) {
                $param = null;
                if (str_contains($rule, ':')) {
                    [$rule, $param] = explode(':', $rule, 2);
                }
                $fail = match ($rule) {
                    'required' => $value === null || $value === '',
                    'email' => $value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL),
                    'min' => is_string($value) && mb_strlen($value) < (int) $param,
                    'max' => is_string($value) && mb_strlen($value) > (int) $param,
                    'numeric' => $value !== null && $value !== '' && !is_numeric($value),
                    'integer' => $value !== null && $value !== '' && filter_var($value, FILTER_VALIDATE_INT) === false,
                    'confirmed' => ($data[$field . '_confirmation'] ?? null) !== $value,
                    'in' => $value !== null && $value !== '' && !in_array($value, explode(',', (string) $param), true),
                    'phone' => $value !== null && $value !== '' && !preg_match('/^[0-9+\-\s]{8,20}$/', (string) $value),
                    default => false,
                };
                if ($fail) {
                    $errors[$field] = self::message($field, $rule);
                    break;
                }
            }
        }
        return $errors;
    }

    private static function message(string $field, string $rule): string
    {
        $labels = [
            'email' => 'Email',
            'password' => 'Mật khẩu',
            'full_name' => 'Họ tên',
            'phone' => 'Số điện thoại',
            'code' => 'Mã',
        ];
        $label = $labels[$field] ?? $field;
        return match ($rule) {
            'required' => "$label là bắt buộc.",
            'email' => 'Email không hợp lệ.',
            'min' => "$label quá ngắn.",
            'max' => "$label quá dài.",
            'confirmed' => 'Xác nhận mật khẩu không khớp.',
            'phone' => 'Số điện thoại không hợp lệ.',
            default => "$label không hợp lệ.",
        };
    }
}
