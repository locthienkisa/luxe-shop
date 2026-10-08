<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Models\AdminLog;
use App\Models\Setting;

final class SettingAdminController
{
    public function index(Request $r, array $p): void
    {
        View::render('admin/settings/index', ['title' => 'Cài đặt', 'settings' => Setting::allKV()], 'layouts/admin');
    }

    public function save(Request $r, array $p): void
    {
        $allowed = ['site_name', 'meta_description', 'hotline', 'address', 'email_contact', 'low_stock_threshold'];
        foreach ($allowed as $k) {
            if (array_key_exists($k, $r->post)) {
                Setting::set($k, (string) $r->post[$k]);
            }
        }
        AdminLog::record('settings.update', 'settings', null);
        Session::flash('success', 'Đã lưu cài đặt.');
        redirect(url('/admin/cai-dat'));
    }
}
