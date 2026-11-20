<?php
/**
 * TOP NAVBAR COMPONENT
 */
$currentUser = getCurrentUser();
$isManager = $currentUser ? $currentUser->isManager() : false;
$pageTitle = $pageTitle ?? 'GYMFIT PRO - Quản Lý Phòng Gym';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Lucide Icons CDN -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <!-- Custom Styles -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col antialiased">

  <!-- TOP NAVBAR -->
  <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        
        <!-- Brand Logo -->
        <a href="<?= $isManager ? 'dashboard.php' : 'checkin.php' ?>" class="flex items-center space-x-3 group">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-sky-600 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-sky-200 group-hover:scale-105 transition-transform">
            <i data-lucide="dumbbell" class="w-6 h-6"></i>
          </div>
          <div>
            <span class="text-xl font-bold tracking-tight text-slate-900 flex items-center gap-1.5">
              GYMFIT <span class="text-sky-600 font-extrabold">PRO</span>
            </span>
            <p class="text-xs text-slate-400 font-medium">
              <?= $isManager ? 'Quản Trị Hệ Thống Toàn Quyền' : 'Cổng Nhân Viên Lễ Tân' ?>
            </p>
          </div>
        </a>

        <!-- User Profile & Action -->
        <div class="flex items-center space-x-3">
          
          <!-- User Profile Card -->
          <div class="flex items-center space-x-3 px-3 py-1.5 bg-slate-50 rounded-xl border border-slate-200">
            <div class="w-8 h-8 rounded-full <?= $isManager ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700' ?> flex items-center justify-center font-bold text-xs shadow-inner">
              <?= $isManager ? 'QL' : 'NV' ?>
            </div>
            <div class="text-left text-xs">
              <div class="font-bold text-slate-800 flex items-center gap-1.5">
                <span><?= htmlspecialchars($currentUser->getHoTen()) ?></span>
                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold border <?= $isManager ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200' ?>">
                  <?= $isManager ? 'QUẢN LÝ TỔNG' : 'NHÂN VIÊN' ?>
                </span>
              </div>
              <div class="text-[11px] text-slate-400">
                <?= $isManager ? 'Toàn quyền điều hành phòng gym' : htmlspecialchars($currentUser->getCaLamViec()) ?>
              </div>
            </div>
          </div>

          <!-- Logout Button -->
          <a href="logout.php" onclick="return confirm('Bạn có chắc muốn đăng xuất khỏi hệ thống không?')" title="Đăng xuất khỏi tài khoản" 
            class="px-3 py-1.5 text-xs font-bold text-rose-600 hover:text-white bg-rose-50 hover:bg-rose-600 border border-rose-200 hover:border-rose-600 rounded-xl transition flex items-center gap-1.5 shadow-sm">
            <i data-lucide="log-out" class="w-4 h-4"></i>
            <span class="hidden sm:inline">Đăng Xuất</span>
          </a>

        </div>

      </div>
    </div>
  </header>

  <!-- MAIN APP CONTAINER -->
  <div class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col md:flex-row gap-6">
