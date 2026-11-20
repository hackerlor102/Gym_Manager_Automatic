<?php
require_once __DIR__ . '/includes/auth.php';

// Nếu đã đăng nhập thì chuyển hướng vào hệ thống
if (!empty($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$pdo = getDB();

// Xử lý khi submit form đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    try {
        if (empty($username) || empty($password)) {
            throw new Exception("Vui lòng điền đầy đủ tên đăng nhập và mật khẩu!");
        }

        $user = User::login($username, $password, $pdo);
        $_SESSION['user_id']   = $user->getId();
        $_SESSION['user_role'] = $user->getVaiTro();
        $_SESSION['user_name'] = $user->getHoTen();

        setFlash("Đăng nhập thành công! Chào mừng " . $user->getHoTen());
        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng Nhập - GYMFIT PRO (PHP & XAMPP)</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Lucide Icons CDN -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden font-sans">
    
    <!-- Background Glow Effects -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-xl relative z-10 space-y-6">
      
      <!-- Brand Logo Header -->
      <div class="text-center space-y-2">
        <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-sky-500 to-indigo-600 items-center justify-center text-white shadow-xl shadow-sky-500/20 mb-2">
          <i data-lucide="dumbbell" class="w-8 h-8"></i>
        </div>
        <h1 class="text-3xl font-extrabold tracking-tight text-white flex items-center justify-center gap-2">
          GYMFIT <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-indigo-400">PRO</span>
        </h1>
        <p class="text-xs text-slate-400 max-w-sm mx-auto">
          Hệ thống quản lý phòng tập chạy trên <strong>PHP & CSDL XAMPP (MySQL)</strong>. Vui lòng đăng nhập để truy cập đúng phân quyền.
        </p>
      </div>

      <!-- Main Login Card -->
      <div class="bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
        
        <!-- Error Alert -->
        <?php if (!empty($error)): ?>
          <div class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs flex items-center gap-2.5">
            <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 text-rose-400"></i>
            <span><?= htmlspecialchars($error) ?></span>
          </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form id="form-login" action="login.php" method="POST" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Tên đăng nhập</label>
            <div class="relative">
              <i data-lucide="user" class="w-4 h-4 text-slate-500 absolute left-3.5 top-3"></i>
              <input type="text" name="username" id="login-username" required placeholder="Nhập tên đăng nhập (admin / lananh)..." 
                class="w-full pl-10 pr-4 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition">
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Mật khẩu</label>
            <div class="relative">
              <i data-lucide="lock" class="w-4 h-4 text-slate-500 absolute left-3.5 top-3"></i>
              <input type="password" name="password" id="login-password" required placeholder="Nhập mật khẩu (mặc định: 123456)..." 
                class="w-full pl-10 pr-10 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition">
              <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3.5 top-3 text-slate-500 hover:text-slate-300 transition">
                <i data-lucide="eye" class="w-4 h-4" id="icon-toggle-password"></i>
              </button>
            </div>
          </div>

          <button type="submit" class="w-full py-2.5 px-4 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 text-white font-bold text-xs rounded-xl transition shadow-lg shadow-sky-600/20 flex items-center justify-center gap-2">
            <span>Đăng Nhập Vào Hệ Thống</span>
            <i data-lucide="arrow-right" class="w-4 h-4"></i>
          </button>
        </form>

        <!-- Divider -->
        <div class="relative flex py-1 items-center">
          <div class="flex-grow border-t border-slate-800"></div>
          <span class="flex-shrink mx-3 text-[10px] uppercase font-bold tracking-wider text-slate-500">Hoặc chọn tài khoản thử nghiệm nhanh</span>
          <div class="flex-grow border-t border-slate-800"></div>
        </div>

        <!-- Quick Demo Login Selection Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          
          <!-- Quick Manager Card -->
          <button type="button" onclick="quickFill('admin', '123456')" class="group p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800 hover:border-indigo-500/50 hover:bg-indigo-950/20 transition-all text-left flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
              <div class="w-7 h-7 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center group-hover:bg-indigo-500 group-hover:text-white transition">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
              </div>
              <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">QUẢN LÝ</span>
            </div>
            <div>
              <div class="font-bold text-xs text-slate-200 group-hover:text-white">Nguyễn Hoàng Minh</div>
              <div class="text-[11px] text-slate-400">admin • 123456</div>
            </div>
            <div class="mt-2 text-[10px] text-indigo-400 font-medium flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
              <span>Đăng nhập quyền Quản lý</span>
              <i data-lucide="chevron-right" class="w-3 h-3"></i>
            </div>
          </button>

          <!-- Quick Staff Card -->
          <button type="button" onclick="quickFill('lananh', '123456')" class="group p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800 hover:border-emerald-500/50 hover:bg-emerald-950/20 transition-all text-left flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
              <div class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-white transition">
                <i data-lucide="user-check" class="w-4 h-4"></i>
              </div>
              <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">NHÂN VIÊN</span>
            </div>
            <div>
              <div class="font-bold text-xs text-slate-200 group-hover:text-white">Trần Thị Lan Anh</div>
              <div class="text-[11px] text-slate-400">lananh • 123456</div>
            </div>
            <div class="mt-2 text-[10px] text-emerald-400 font-medium flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
              <span>Đăng nhập quyền Nhân viên</span>
              <i data-lucide="chevron-right" class="w-3 h-3"></i>
            </div>
          </button>

        </div>

      </div>

      <div class="text-center text-[11px] text-slate-500">
        GYMFIT PRO &copy; 2026. Công nghệ PHP OOP & CSDL MySQL (XAMPP).
      </div>
    </div>

    <script>
      function togglePasswordVisibility() {
        const inp = document.getElementById('login-password');
        const icon = document.getElementById('icon-toggle-password');
        const isPass = inp.type === 'password';
        inp.type = isPass ? 'text' : 'password';
        icon.setAttribute('data-lucide', isPass ? 'eye-off' : 'eye');
        if (typeof lucide !== 'undefined') lucide.createIcons();
      }

      function quickFill(user, pass) {
        const uInp = document.getElementById('login-username');
        const pInp = document.getElementById('login-password');
        const form = document.getElementById('form-login');
        if (uInp) uInp.value = user;
        if (pInp) pInp.value = pass;
        if (form) form.submit();
      }

      document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') lucide.createIcons();
      });
    </script>
</body>
</html>
