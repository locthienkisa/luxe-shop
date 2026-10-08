<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\AdminLog;
use App\Models\Order;
use App\Models\User;

final class UserAdminController
{
    public function index(Request $r, array $p): void
    {
        $page = max(1, (int) ($r->query['page'] ?? 1));
        $q = (string) ($r->query['q'] ?? '');
        $role = $r->query['role_id'] ? (int) $r->query['role_id'] : null;
        $active = isset($r->query['active']) && $r->query['active'] !== '' ? (int) $r->query['active'] : null;
        $res = User::paginate($q, $role, $active, ($page - 1) * 20, 20);
        $roles = User::db()->query('SELECT * FROM roles ORDER BY id')->fetchAll();
        View::render('admin/users/index', [
            'title' => 'Khách hàng',
            'users' => $res['rows'],
            'meta' => paginate_meta($res['total'], $page, 20),
            'roles' => $roles,
            'q' => $q,
            'role_id' => $role,
            'active' => $active,
        ], 'layouts/admin');
    }

    public function show(Request $r, array $p): void
    {
        $user = User::find((int) $p['id']);
        if (!$user) {
            Response::abort(404);
        }
        $orders = Order::forUser((int) $user['id'], 0, 50);
        View::render('admin/users/show', ['title' => $user['email'], 'user' => $user, 'orders' => $orders['rows']], 'layouts/admin');
    }

    public function lock(Request $r, array $p): void
    {
        $id = (int) $r->input('id');
        if ($id === Auth::id()) {
            Session::flash('error', 'Không thể khóa chính mình.');
            redirect(url('/admin/nguoi-dung'));
        }
        $active = (int) $r->input('is_active');
        User::updateProfile($id, ['is_active' => $active]);
        AdminLog::record($active ? 'user.unlock' : 'user.lock', 'user', $id);
        redirect(url('/admin/nguoi-dung/' . $id));
    }

    public function role(Request $r, array $p): void
    {
        if (!Auth::hasRole('super_admin', 'admin')) {
            Response::abort(403);
        }
        $id = (int) $r->input('id');
        $roleId = (int) $r->input('role_id');
        $target = User::find($id);
        if (!$target) {
            Response::abort(404);
        }
        if ($id === Auth::id()) {
            Session::flash('error', 'Không tự đổi vai trò của mình.');
            redirect(url('/admin/nguoi-dung/' . $id));
        }
        User::updateProfile($id, ['role_id' => $roleId]);
        AdminLog::record('user.role', 'user', $id, ['role_id' => $roleId]);
        Session::flash('success', 'Đã đổi vai trò.');
        redirect(url('/admin/nguoi-dung/' . $id));
    }
}
