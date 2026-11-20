<?php
/**
 * SIDEBAR NAVIGATION COMPONENT
 * Phân quyền hiển thị menu chính xác theo vai trò: Quản lý hoặc Nhân viên
 */
$currentScript = basename($_SERVER['PHP_SELF']);
$pdo = getDB();
$totalMembers = $pdo->query("SELECT COUNT(*) FROM hoi_vien")->fetchColumn();
?>
<aside class="w-full md:w-64 shrink-0 space-y-5">
  
  <!-- Role Banner / Notification -->
  <div class="p-4 rounded-2xl border text-xs flex items-start gap-3 <?= $isManager ? 'bg-indigo-50 border-indigo-100 text-indigo-900' : 'bg-emerald-50 border-emerald-100 text-emerald-900' ?>">
    <i data-lucide="info" class="w-5 h-5 shrink-0 mt-0.5 <?= $isManager ? 'text-indigo-600' : 'text-emerald-600' ?>"></i>
    <div>
      <div class="font-bold mb-0.5">
        <?= $isManager ? 'Không Gian Quản Trị: Quản Lý' : 'Không Gian Nghiệp Vụ: Nhân Viên' ?>
      </div>
      <div class="leading-relaxed opacity-90">
        <?= $isManager 
          ? 'Toàn quyền: Giám sát doanh thu, quản lý nhân viên, cấu hình gói tập và quản lý hội viên.' 
          : 'Quyền hạn: Kiểm tra check-in, quản lý hội viên, đăng ký gói tập và xuất hóa đơn.' ?>
      </div>
    </div>
  </div>

  <!-- Navigation Links -->
  <nav class="space-y-1.5" id="sidebar-nav">
    
    <?php if ($isManager): ?>
      <!-- 1. QUẢN LÝ: Thống Kê Doanh Thu -->
      <a href="dashboard.php" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-colors <?= $currentScript === 'dashboard.php' ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-100' ?>">
        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
        <span>Thống Kê Doanh Thu</span>
      </a>

      <!-- 2. QUẢN LÝ: Quản Lý Nhân Viên -->
      <a href="staff.php" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-colors <?= $currentScript === 'staff.php' ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-100' ?>">
        <i data-lucide="briefcase" class="w-5 h-5"></i>
        <span>Quản Lý Nhân Viên</span>
      </a>

      <!-- 3. QUẢN LÝ: Quản Lý Gói Tập -->
      <a href="packages.php" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-colors <?= $currentScript === 'packages.php' ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-100' ?>">
        <i data-lucide="sparkles" class="w-5 h-5"></i>
        <span>Quản Lý Gói Tập</span>
      </a>
    <?php endif; ?>

    <!-- 4. HỘI VIÊN (Cả 2 vai trò) -->
    <a href="members.php" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-colors <?= $currentScript === 'members.php' ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-100' ?>">
      <i data-lucide="users" class="w-5 h-5"></i>
      <span>Quản Lý Hội Viên</span>
      <span class="ml-auto text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold"><?= $totalMembers ?></span>
    </a>

    <!-- 5. CHECK-IN (Cả 2 vai trò) -->
    <a href="checkin.php" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-colors <?= $currentScript === 'checkin.php' ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-100' ?>">
      <i data-lucide="scan-barcode" class="w-5 h-5"></i>
      <span>Kiểm Tra Check-In</span>
    </a>

    <!-- 6. HÓA ĐƠN (Cả 2 vai trò) -->
    <a href="invoices.php" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-colors <?= $currentScript === 'invoices.php' ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-100' ?>">
      <i data-lucide="receipt" class="w-5 h-5"></i>
      <span>Quản Lý Hóa Đơn</span>
    </a>

  </nav>

  <!-- Quick Action Box -->
  <div class="bg-gradient-to-br from-slate-900 to-slate-800 p-5 rounded-2xl text-white shadow-lg shadow-slate-200 hidden md:block">
    <div class="flex items-center gap-2 mb-2 text-sky-400">
      <i data-lucide="zap" class="w-4 h-4"></i>
      <span class="text-xs font-bold uppercase tracking-wider">Nghiệp Vụ Nhanh</span>
    </div>
    <p class="text-xs text-slate-300 mb-4 leading-relaxed">Đăng ký hoặc gia hạn gói tập cho hội viên, xuất hóa đơn tức thì.</p>
    <button onclick="openRegisterPackageModal()" class="w-full py-2 px-3 text-xs font-bold bg-sky-500 hover:bg-sky-400 text-white rounded-xl transition-colors flex items-center justify-center gap-2 shadow-sm">
      <i data-lucide="credit-card" class="w-4 h-4"></i>
      <span>Đăng Ký Gói Tập</span>
    </button>
  </div>

</aside>
