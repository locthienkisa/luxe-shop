<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Session;
use App\Core\Uploader;
use App\Core\Validator;
use App\Core\View;
use App\Models\Address;
use App\Models\Notification;
use App\Models\User;
use App\Core\Logger;

final class AccountController
{
    public function index(Request $r, array $p): void
    {
        View::render('account/index', ['title' => 'Tài khoản', 'user' => Auth::user()]);
    }

    public function update(Request $r, array $p): void
    {
        $errors = Validator::validate($r->post, ['full_name' => 'required|min:2|max:120']);
        if ($errors) {
            Session::flash('errors', $errors);
            redirect(url('/tai-khoan'));
        }
        $data = ['full_name' => trim((string) $r->input('full_name')), 'phone' => $r->input('phone')];
        if (!empty($r->files['avatar']['name'])) {
            try {
                $data['avatar'] = Uploader::image($r->files['avatar'], 'avatars');
            } catch (\Throwable $e) {
                Session::flash('error', $e->getMessage());
                redirect(url('/tai-khoan'));
            }
        }
        User::updateProfile(Auth::id(), $data);
        Session::flash('success', 'Đã cập nhật hồ sơ.');
        redirect(url('/tai-khoan'));
    }

    public function showPassword(Request $r, array $p): void
    {
        View::render('account/password', ['title' => 'Đổi mật khẩu']);
    }

    public function password(Request $r, array $p): void
    {
        $errors = Validator::validate($r->post, ['password' => 'required|min:8|confirmed']);
        $user = Auth::user();
        if ((int) ($user['must_change_password'] ?? 0) !== 1) {
            if (!password_verify((string) $r->input('current_password'), $user['password_hash'])) {
                $errors['current_password'] = 'Mật khẩu hiện tại không đúng.';
            }
        }
        if ($errors) {
            Session::flash('errors', $errors);
            redirect(url('/tai-khoan/doi-mat-khau'));
        }
        User::updateProfile(Auth::id(), [
            'password_hash' => password_hash((string) $r->input('password'), PASSWORD_DEFAULT),
            'must_change_password' => 0,
        ]);
        Logger::security('password_changed', ['user_id' => Auth::id()]);
        Session::flash('success', 'Đã đổi mật khẩu.');
        redirect(url('/tai-khoan'));
    }

    public function addresses(Request $r, array $p): void
    {
        View::render('account/addresses', ['title' => 'Địa chỉ', 'addresses' => Address::forUser(Auth::id())]);
    }

    public function storeAddress(Request $r, array $p): void
    {
        $errors = Validator::validate($r->post, [
            'full_name' => 'required', 'phone' => 'required|phone', 'line1' => 'required', 'district' => 'required', 'city' => 'required',
        ]);
        if ($errors) {
            Session::flash('errors', $errors);
            redirect(url('/tai-khoan/dia-chi'));
        }
        Address::create([
            'user_id' => Auth::id(),
            'label' => $r->input('label'),
            'full_name' => $r->input('full_name'),
            'phone' => $r->input('phone'),
            'line1' => $r->input('line1'),
            'line2' => $r->input('line2'),
            'ward' => $r->input('ward'),
            'district' => $r->input('district'),
            'city' => $r->input('city'),
            'postal_code' => $r->input('postal_code'),
            'is_default' => $r->input('is_default') ? 1 : 0,
        ]);
        Session::flash('success', 'Đã thêm địa chỉ.');
        redirect(url('/tai-khoan/dia-chi'));
    }

    public function deleteAddress(Request $r, array $p): void
    {
        Address::deleteOwned((int) $r->input('id'), Auth::id());
        Session::flash('success', 'Đã xóa địa chỉ.');
        redirect(url('/tai-khoan/dia-chi'));
    }

    public function notifications(Request $r, array $p): void
    {
        View::render('account/notifications', ['title' => 'Thông báo', 'items' => Notification::forUser(Auth::id())]);
    }

    public function readNotification(Request $r, array $p): void
    {
        Notification::markRead((int) $r->input('id'), Auth::id());
        redirect(url('/thong-bao'));
    }

    public function readAll(Request $r, array $p): void
    {
        Notification::markAll(Auth::id());
        redirect(url('/thong-bao'));
    }
}
