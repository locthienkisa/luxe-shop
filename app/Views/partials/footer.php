<footer class="footer-lx mt-5 pt-5 pb-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h5 class="text-white"><?= e(setting('site_name', 'Luxe Shop')) ?></h5>
                <p class="mb-1"><?= e(setting('address', '123 Nguyễn Huệ, Q.1, TP.HCM')) ?></p>
                <p class="mb-1">Hotline: <?= e(setting('hotline', '1900 0000')) ?></p>
                <p><?= e(setting('email_contact', 'hello@luxe-shop.local')) ?></p>
            </div>
            <div class="col-md-4">
                <h6 class="text-white">Hỗ trợ</h6>
                <ul class="list-unstyled">
                    <li><a href="<?= e(url('/san-pham')) ?>">Sản phẩm</a></li>
                    <li><a href="<?= e(url('/don-hang')) ?>">Theo dõi đơn hàng</a></li>
                    <li><a href="<?= e(url('/tai-khoan')) ?>">Tài khoản</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="text-white">Cam kết</h6>
                <p>Giao hàng toàn quốc · Đổi trả 7 ngày · Thanh toán COD an toàn.</p>
            </div>
        </div>
        <hr class="border-secondary">
        <p class="small mb-0">&copy; <?= date('Y') ?> Luxe Shop. Demo e-commerce.</p>
    </div>
</footer>
