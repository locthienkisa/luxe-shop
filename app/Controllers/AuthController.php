<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Models\LoginAttempt;
use App\Models\PasswordReset;
use App\Models\User;
use App\Core\Logger;

final class AuthController
{
    public function showLogin(Request $r, array $p): void
    {
        View::render('auth/login', ['title' => 'Đăng nhập']);
    }

    public function login(Request $r, array $p): void
    {
        $email = strtolower(trim((string) $r->input('email')));
        $password = (string) $r->input('password');
        $errors = Validator::validate($r->post, ['email' => 'required|email', 'password' => 'required']);
        if ($errors) {
            Session::set('_old', ['email' => $email]);
            Session::flash('errors', $errors);
            redirect(url('/dang-nhap'));
        }

        $max = (int) config('app.login_max_attempts', 5);
        $mins = (int) config('app.login_lockout_minutes', 15);
        if (LoginAttempt::tooMany($email, $r->ip(), $max, $mins)) {
            Logger::security('login_lockout', ['email' => $email, 'ip' => $r->ip()]);
            Session::flash('error', 'Quá nhiều lần đăng nhập sai. Thử lại sau ' . $mins . ' phút.');
            redirect(url('/dang-nhap'));
        }

        $user = User::findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            LoginAttempt::record($email, $r->ip(), false);
            Logger::security('login_failed', ['email' => $email, 'ip' => $r->ip()]);
            Session::flash('error', 'Email hoặc mật khẩu không đúng.');
            redirect(url('/dang-nhap'));
        }
        if (!(int) $user['is_active']) {
            Session::flash('error', 'Tài khoản đã bị khóa.');
            redirect(url('/dang-nhap'));
        }

        LoginAttempt::record($email, $r->ip(), true);
        User::updateProfile((int) $user['id'], ['last_login_at' => date('Y-m-d H:i:s'), 'last_login_ip' => $r->ip()]);
        Auth::login($user);
        $intended = Session::get('intended');
        Session::forget('intended');
        if (Auth::isStaff()) {
            redirect($intended && str_starts_with((string) $intended, '/admin') ? url((string) $intended) : url('/admin'));
        }
        redirect($intended ? url((string) $intended) : url('/tai-khoan'));
    }

    public function showRegister(Request $r, array $p): void
    {
        View::render('auth/register', ['title' => 'Đăng ký']);
    }

    public function register(Request $r, array $p): void
    {
        $errors = Validator::validate($r->post, [
            'full_name' => 'required|min:2|max:120',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
        $email = strtolower(trim((string) $r->input('email')));
        if (User::findByEmail($email)) {
            $errors['email'] = 'Email đã được sử dụng.';
        }
        if ($errors) {
            Session::set('_old', $r->post);
            Session::flash('errors', $errors);
            redirect(url('/dang-ky'));
        }
        $id = User::create([
            'role_id' => User::roleIdBySlug('customer'),
            'email' => $email,
            'password_hash' => password_hash((string) $r->input('password'), PASSWORD_DEFAULT),
            'full_name' => trim((string) $r->input('full_name')),
            'phone' => $r->input('phone'),
        ]);
        Logger::security('register', ['user_id' => $id, 'email' => $email]);
        $user = User::find($id);
        Auth::login($user);
        Session::flash('success', 'Đăng ký thành công.');
        redirect(url('/tai-khoan'));
    }

    public function logout(Request $r, array $p): void
    {
        Auth::logout();
        Session::flash('success', 'Đã đăng xuất.');
        redirect(url('/'));
    }

    public function showForgot(Request $r, array $p): void
    {
        View::render('auth/forgot', ['title' => 'Quên mật khẩu']);
    }

    public function forgot(Request $r, array $p): void
    {
        $email = strtolower(trim((string) $r->input('email')));
        $user = User::findByEmail($email);
        if ($user) {
            $token = PasswordReset::createToken((int) $user['id']);
            $link = url('/dat-lai-mat-khau?token=' . $token);
            $dir = dirname(__DIR__, 2) . '/storage/logs';
            file_put_contents($dir . '/password_resets.log', date('c') . " reset for {$email}: {$link}\n", FILE_APPEND);
            Logger::security('password_reset_requested', ['user_id' => $user['id']]);
        }
        Session::flash('success', 'Nếu email tồn tại, liên kết đặt lại mật khẩu đã được tạo (môi trường local: xem storage/logs/password_resets.log).');
        redirect(url('/quen-mat-khau'));
    }

    public function showReset(Request $r, array $p): void
    {
        View::render('auth/reset', ['title' => 'Đặt lại mật khẩu', 'token' => $r->query['token'] ?? '']);
    }

    public function reset(Request $r, array $p): void
    {
        $errors = Validator::validate($r->post, ['token' => 'required', 'password' => 'required|min:8|confirmed']);
        if ($errors) {
            Session::flash('errors', $errors);
            redirect(url('/dat-lai-mat-khau?token=' . urlencode((string) $r->input('token'))));
        }
        $row = PasswordReset::consume((string) $r->input('token'));
        if (!$row) {
            Session::flash('error', 'Liên kết không hợp lệ hoặc đã hết hạn.');
            redirect(url('/quen-mat-khau'));
        }
        User::updateProfile((int) $row['user_id'], [
            'password_hash' => password_hash((string) $r->input('password'), PASSWORD_DEFAULT),
            'must_change_password' => 0,
        ]);
        Logger::security('password_changed', ['user_id' => $row['user_id']]);
        Session::flash('success', 'Đã đổi mật khẩu. Hãy đăng nhập.');
        redirect(url('/dang-nhap'));
    }
}
