<?php
require_once __DIR__ . '/includes/auth.php';
checkLogin();

$pageTitle = 'Quản Lý Hội Viên - GYMFIT PRO';
$pdo = getDB();
$keyword = trim($_GET['keyword'] ?? '');
$status  = trim($_GET['status'] ?? 'ALL');

$members = HoiVien::getAll($pdo, $keyword, $status);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- MAIN CONTENT -->
<main class="flex-1 min-w-0 space-y-5">
  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
    <div>
      <h1 class="text-2xl font-bold text-slate-900">Quản Lý Hội Viên</h1>
      <p class="text-xs text-slate-500 mt-0.5">Theo dõi hồ sơ khách hàng, tình trạng gói tập và gia hạn</p>
    </div>
    <div class="flex items-center gap-2">
      <button onclick="openModal('modal-add-member')" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-1.5 shadow-sm">
        <i data-lucide="plus-circle" class="w-4 h-4"></i>
        <span>Thêm Hội Viên Mới</span>
      </button>
    </div>
  </div>

  <!-- Filter & Search Bar -->
  <form method="GET" action="members.php" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-3">
    <div class="relative flex-1">
      <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
      <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="Tìm theo tên, SĐT hoặc mã hội viên..." 
        class="w-full pl-10 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500">
    </div>
    <div class="flex items-center gap-2">
      <select name="status" onchange="this.form.submit()" class="py-2 px-3 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
        <option value="ALL" <?= $status === 'ALL' ? 'selected' : '' ?>>Tất cả trạng thái</option>
        <option value="ACTIVE" <?= $status === 'ACTIVE' ? 'selected' : '' ?>>Đang hoạt động (Còn hạn)</option>
        <option value="EXPIRED" <?= $status === 'EXPIRED' ? 'selected' : '' ?>>Đã hết hạn gói</option>
        <option value="PENDING" <?= $status === 'PENDING' ? 'selected' : '' ?>>Chờ đăng ký gói</option>
      </select>
      <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition">
        Lọc
      </button>
      <?php if (!empty($keyword) || $status !== 'ALL'): ?>
        <a href="members.php" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100" title="Xóa bộ lọc">
          <i data-lucide="x" class="w-4 h-4"></i>
        </a>
      <?php endif; ?>
    </div>
  </form>

  <!-- Members Table -->
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
            <th class="py-3 px-4">Hội Viên</th>
            <th class="py-3 px-4">Liên Hệ</th>
            <th class="py-3 px-4">Gói Tập</th>
            <th class="py-3 px-4">Hạn Sử Dụng</th>
            <th class="py-3 px-4">Trạng Thái</th>
            <th class="py-3 px-4 text-right">Thao Tác</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php if (empty($members)): ?>
            <tr>
              <td colspan="6" class="text-center py-8 text-slate-400">
                <i data-lucide="users" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                Không tìm thấy hội viên nào phù hợp
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($members as $m): 
              $isConHan = $m->isGoiConHan();
              $daysLeft = $m->soNgayConLai();
              $st = $m->getTrangThai();
            ?>
              <tr class="hover:bg-slate-50 transition-colors">
                <td class="py-3 px-4">
                  <div class="flex items-center gap-3">
                    <img src="<?= htmlspecialchars($m->getAvatar()) ?>" class="w-8 h-8 rounded-full border border-slate-200" alt="Avatar">
                    <div>
                      <div class="font-bold text-slate-800"><?= htmlspecialchars($m->getHoTen()) ?></div>
                      <div class="text-[11px] text-slate-400 font-mono"><?= $m->getMaHV() ?> • <?= $m->getGioiTinh() ?></div>
                    </div>
                  </div>
                </td>
                <td class="py-3 px-4">
                  <div class="font-medium text-slate-700"><?= htmlspecialchars($m->getSoDienThoai()) ?></div>
                  <div class="text-[11px] text-slate-400"><?= htmlspecialchars($m->getEmail() ?: 'Chưa có email') ?></div>
                </td>
                <td class="py-3 px-4 font-medium text-slate-800">
                  <?= htmlspecialchars($m->getTenGoiTap()) ?>
                </td>
                <td class="py-3 px-4 text-slate-600">
                  <?= $m->getNgayHetHan() ? date('d/m/Y', strtotime($m->getNgayHetHan())) : '--/--/----' ?>
                </td>
                <td class="py-3 px-4">
                  <?php if ($st === 'ACTIVE'): ?>
                    <span class="badge-active px-2.5 py-1 rounded-full font-bold text-[10px] inline-flex items-center gap-1">
                      <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Còn <?= $daysLeft ?> ngày
                    </span>
                  <?php elseif ($st === 'EXPIRED'): ?>
                    <span class="badge-expired px-2.5 py-1 rounded-full font-bold text-[10px] inline-flex items-center gap-1">
                      <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Đã hết hạn
                    </span>
                  <?php else: ?>
                    <span class="badge-pending px-2.5 py-1 rounded-full font-bold text-[10px] inline-flex items-center gap-1">
                      <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Chờ đăng ký
                    </span>
                  <?php endif; ?>
                </td>
                <td class="py-3 px-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <!-- Nút Gia Hạn Gói -->
                    <button onclick="openRegisterPackageModal(<?= $m->getId() ?>)" 
                      class="px-2.5 py-1 rounded-lg bg-sky-50 text-sky-700 hover:bg-sky-100 font-semibold text-[11px] flex items-center gap-1 transition" title="Đăng ký hoặc gia hạn gói tập">
                      <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
                      <span>Gói</span>
                    </button>
                    <!-- Nút Check-in nhanh -->
                    <form action="action.php?act=quick_checkin" method="POST" class="inline">
                      <input type="hidden" name="hoi_vien_id" value="<?= $m->getId() ?>">
                      <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-semibold text-[11px] flex items-center gap-1 transition" title="Check-in ngay">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        <span>Check-in</span>
                      </button>
                    </form>
                    <!-- Nút Sửa -->
                    <button onclick="openEditMemberModal(<?= htmlspecialchars(json_encode([
                      'id' => $m->getId(),
                      'ho_ten' => $m->getHoTen(),
                      'so_dien_thoai' => $m->getSoDienThoai(),
                      'email' => $m->getEmail(),
                      'gioi_tinh' => $m->getGioiTinh()
                    ])) ?>)" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition" title="Chỉnh sửa thông tin">
                      <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</main>

<!-- MODAL THÊM HỘI VIÊN -->
<div id="modal-add-member" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
  <div class="modal-backdrop fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modal-add-member')"></div>
  <div class="modal-content relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl z-10 space-y-4">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
      <h3 class="font-bold text-slate-800 text-base">Thêm Hội Viên Mới</h3>
      <button type="button" onclick="closeModal('modal-add-member')" class="text-slate-400 hover:text-slate-600">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form action="action.php?act=add_member" method="POST" class="space-y-3 text-xs">
      <div>
        <label class="block font-semibold text-slate-700 mb-1">Họ và tên <span class="text-red-500">*</span></label>
        <input type="text" name="ho_ten" required placeholder="Ví dụ: Nguyễn Văn A" 
          class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500">
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block font-semibold text-slate-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
          <input type="tel" name="so_dien_thoai" required placeholder="0987..." 
            class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500">
        </div>
        <div>
          <label class="block font-semibold text-slate-700 mb-1">Giới tính</label>
          <select name="gioi_tinh" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
            <option value="Nam">Nam</option>
            <option value="Nữ">Nữ</option>
            <option value="Khác">Khác</option>
          </select>
        </div>
      </div>

      <div>
        <label class="block font-semibold text-slate-700 mb-1">Email</label>
        <input type="email" name="email" placeholder="example@gmail.com" 
          class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500">
      </div>

      <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
        <button type="button" onclick="closeModal('modal-add-member')" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-xl font-semibold">Hủy</button>
        <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-bold shadow-sm">Lưu Thông Tin</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL SỬA HỘI VIÊN -->
<div id="modal-edit-member" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
  <div class="modal-backdrop fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modal-edit-member')"></div>
  <div class="modal-content relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl z-10 space-y-4">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
      <h3 class="font-bold text-slate-800 text-base">Chỉnh Sửa Thông Tin Hội Viên</h3>
      <button type="button" onclick="closeModal('modal-edit-member')" class="text-slate-400 hover:text-slate-600">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form action="action.php?act=edit_member" method="POST" class="space-y-3 text-xs">
      <input type="hidden" name="id" id="edit-member-id">

      <div>
        <label class="block font-semibold text-slate-700 mb-1">Họ và tên <span class="text-red-500">*</span></label>
        <input type="text" name="ho_ten" id="edit-member-name" required 
          class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500">
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block font-semibold text-slate-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
          <input type="tel" name="so_dien_thoai" id="edit-member-phone" required 
            class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500">
        </div>
        <div>
          <label class="block font-semibold text-slate-700 mb-1">Giới tính</label>
          <select name="gioi_tinh" id="edit-member-gender" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
            <option value="Nam">Nam</option>
            <option value="Nữ">Nữ</option>
            <option value="Khác">Khác</option>
          </select>
        </div>
      </div>

      <div>
        <label class="block font-semibold text-slate-700 mb-1">Email</label>
        <input type="email" name="email" id="edit-member-email" 
          class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500">
      </div>

      <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
        <button type="button" onclick="closeModal('modal-edit-member')" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-xl font-semibold">Hủy</button>
        <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-bold shadow-sm">Cập Nhật</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openEditMemberModal(data) {
    document.getElementById('edit-member-id').value = data.id;
    document.getElementById('edit-member-name').value = data.ho_ten;
    document.getElementById('edit-member-phone').value = data.so_dien_thoai;
    document.getElementById('edit-member-email').value = data.email || '';
    document.getElementById('edit-member-gender').value = data.gioi_tinh || 'Nam';
    openModal('modal-edit-member');
  }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
