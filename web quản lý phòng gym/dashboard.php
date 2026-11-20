<?php
require_once __DIR__ . '/includes/auth.php';
// Phân quyền: Chỉ Quản Lý mới được vào trang Thống Kê
checkRole('MANAGER');

$pageTitle = 'Thống Kê Doanh Thu & Báo Cáo - GYMFIT PRO';
$pdo = getDB();
$currentUser = getCurrentUser();
$stats = $currentUser->thongKeTongHop($pdo);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- MAIN CONTENT -->
<main class="flex-1 min-w-0 space-y-6">
  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
    <div>
      <div class="flex items-center gap-2">
        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">DÀNH RIÊNG CHO QUẢN LÝ</span>
      </div>
      <h1 class="text-2xl font-bold text-slate-900 mt-1">Thống Kê Doanh Thu & Báo Cáo Vận Hành</h1>
      <p class="text-xs text-slate-500 mt-0.5">Báo cáo thời gian thực từ cơ sở dữ liệu MySQL XAMPP</p>
    </div>
    <div class="flex items-center gap-2">
      <span class="text-xs text-slate-500 bg-white px-3 py-1.5 rounded-lg border border-slate-200 font-medium flex items-center gap-1.5">
        <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
        <span><?= date('l, d/m/Y') ?></span>
      </span>
    </div>
  </div>

  <!-- KPI Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- KPI 1: Doanh thu -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
        <i data-lucide="badge-dollar-sign" class="w-6 h-6"></i>
      </div>
      <div>
        <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Tổng Doanh Thu</p>
        <h3 class="text-xl font-extrabold text-slate-900 mt-0.5"><?= number_format($stats['tongDoanhThu'], 0, ',', '.') ?> đ</h3>
        <p class="text-[11px] text-emerald-600 font-medium mt-0.5">Từ hóa đơn gói tập</p>
      </div>
    </div>

    <!-- KPI 2: Hội viên -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
        <i data-lucide="users" class="w-6 h-6"></i>
      </div>
      <div>
        <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Tổng Hội Viên</p>
        <h3 class="text-xl font-extrabold text-slate-900 mt-0.5"><?= $stats['tongHoiVien'] ?></h3>
        <p class="text-[11px] text-slate-500 font-medium mt-0.5">
          <span class="text-sky-600 font-bold"><?= $stats['hoiVienConHan'] ?></span> đang hoạt động
        </p>
      </div>
    </div>

    <!-- KPI 3: Check-in hôm nay -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
        <i data-lucide="scan-line" class="w-6 h-6"></i>
      </div>
      <div>
        <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Check-in Hôm Nay</p>
        <h3 class="text-xl font-extrabold text-slate-900 mt-0.5"><?= $stats['checkInHomNay'] ?></h3>
        <p class="text-[11px] text-purple-600 font-medium mt-0.5">Lượt vào phòng tập</p>
      </div>
    </div>

    <!-- KPI 4: Số hóa đơn -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
        <i data-lucide="receipt-text" class="w-6 h-6"></i>
      </div>
      <div>
        <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Số Hóa Đơn</p>
        <h3 class="text-xl font-extrabold text-slate-900 mt-0.5"><?= $stats['soHoaDon'] ?></h3>
        <p class="text-[11px] text-amber-600 font-medium mt-0.5">Giao dịch thành công</p>
      </div>
    </div>
  </div>

  <!-- Charts & Revenue Breakdown -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Payment Methods -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-800 text-sm">Hình Thức Thanh Toán</h3>
        <i data-lucide="pie-chart" class="w-4 h-4 text-slate-400"></i>
      </div>
      <div class="space-y-4 pt-2">
        <?php
        $methods = [
          ['label' => 'Chuyển khoản QR', 'amount' => $stats['theoPhuongThuc']['CHUYEN_KHOAN'] ?? 0, 'color' => 'bg-sky-500'],
          ['label' => 'Tiền mặt', 'amount' => $stats['theoPhuongThuc']['TIEN_MAT'] ?? 0, 'color' => 'bg-emerald-500'],
          ['label' => 'Thẻ POS', 'amount' => $stats['theoPhuongThuc']['THE'] ?? 0, 'color' => 'bg-indigo-500']
        ];
        $totalRev = max(1, $stats['tongDoanhThu']);
        foreach ($methods as $m):
          $pct = round(($m['amount'] / $totalRev) * 100);
        ?>
          <div>
            <div class="flex justify-between text-xs mb-1">
              <span class="font-medium text-slate-700"><?= $m['label'] ?></span>
              <span class="font-bold text-slate-900"><?= number_format($m['amount'], 0, ',', '.') ?> đ (<?= $pct ?>%)</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
              <div class="<?= $m['color'] ?> h-2 rounded-full" style="width: <?= $pct ?>%"></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Popular Packages -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4 lg:col-span-2">
      <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-800 text-sm">Thống Kê Gói Tập Bán Chạy Nhất</h3>
        <span class="text-xs text-slate-400">Dữ liệu từ MySQL</span>
      </div>
      <div class="space-y-3 pt-2">
        <?php if (empty($stats['topPackages'])): ?>
          <div class="text-xs text-slate-400 py-4 text-center">Chưa có giao dịch hóa đơn nào</div>
        <?php else: ?>
          <?php foreach ($stats['topPackages'] as $tp): ?>
            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center font-bold text-xs">
                  <i data-lucide="package" class="w-4 h-4"></i>
                </div>
                <div>
                  <span class="font-semibold text-xs text-slate-800"><?= htmlspecialchars($tp['ten_goi']) ?></span>
                  <div class="text-[11px] text-slate-400 font-medium">Doanh thu: <?= number_format($tp['total_revenue'], 0, ',', '.') ?> đ</div>
                </div>
              </div>
              <span class="text-xs px-2.5 py-1 rounded-full bg-sky-100 text-sky-800 font-bold">
                <?= $tp['sales_count'] ?> lượt đăng ký
              </span>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

  </div>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
