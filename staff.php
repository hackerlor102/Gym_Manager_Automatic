<?php
require_once __DIR__ . '/includes/auth.php';
// Phân quyền: Chỉ Quản Lý mới được vào trang Quản Lý Nhân Viên
checkRole('MANAGER');

$pageTitle = 'Quản Lý Nhân Viên - GYMFIT PRO';
$pdo = getDB();
$staffList = QuanLy::getAllStaff($pdo);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- MAIN CONTENT -->
<main class="flex-1 min-w-0 space-y-5">
  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
    <div>
      <div class="flex items-center gap-2">
        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">DÀNH RIÊNG CHO QUẢN LÝ</span>
      </div>
      <h1 class="text-2xl font-bold text-slate-900 mt-1">Quản Lý Đội Ngũ Nhân Viên</h1>
      <p class="text-xs text-slate-500 mt-0.5">Phân quyền, xếp ca trực và thiết lập mức lương nhân sự</p>
    </div>
    <div class="flex items-center gap-2">
      <button onclick="openModal('modal-add-staff')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-1.5 shadow-sm">
        <i data-lucide="user-plus" class="w-4 h-4"></i>
        <span>Thêm Nhân Viên</span>
      </button>
    </div>
  </div>

  <!-- Staff Table -->
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
            <th class="py-3 px-4">Nhân Viên</th>
            <th class="py-3 px-4">Vai Trò</th>
            <th class="py-3 px-4">Ca Làm Việc</th>
            <th class="py-3 px-4">Lương Cơ Bản</th>
            <th class="py-3 px-4">Ngày Vào Làm</th>
            <th class="py-3 px-4">Trạng Thái</th>
            <th class="py-3 px-4 text-right">Thao Tác</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($staffList as $st): 
            $isMgr = ($st['vai_tro'] === 'MANAGER');
            $isActive = ($st['trang_thai'] === 'ACTIVE');
          ?>
            <tr class="hover:bg-slate-50 transition-colors">
              <td class="py-3 px-4">
                <div class="font-bold text-slate-800"><?= htmlspecialchars($st['ho_ten']) ?></div>
                <div class="text-[11px] text-slate-400 font-mono"><?= $st['ma_nv'] ?> • <?= $st['so_dien_thoai'] ?> (<?= $st['username'] ?>)</div>
              </td>
              <td class="py-3 px-4">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold border <?= $isMgr ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200' ?>">
                  <?= $isMgr ? 'Quản Lý' : 'Nhân Viên' ?>
                </span>
              </td>
              <td class="py-3 px-4 text-slate-700"><?= htmlspecialchars($st['ca_lam_viec']) ?></td>
              <td class="py-3 px-4 font-semibold text-slate-900"><?= number_format($st['luong_co_ban'], 0, ',', '.') ?> đ</td>
              <td class="py-3 px-4 text-slate-500"><?= $st['ngay_vao_lam'] ? date('d/m/Y', strtotime($st['ngay_vao_lam'])) : '--' ?></td>
              <td class="py-3 px-4">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $isActive ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400' ?>">
                  <?= $isActive ? 'Đang làm việc' : 'Tạm khóa' ?>
                </span>
              </td>
              <td class="py-3 px-4 text-right">
                <?php if (!$isMgr): ?>
                  <div class="flex items-center justify-end gap-1.5">
                    <!-- Toggle Status Form -->
                    <form action="action.php?act=toggle_staff" method="POST" class="inline">
                      <input type="hidden" name="id" value="<?= $st['id'] ?>">
                      <input type="hidden" name="trang_thai" value="<?= $isActive ? 'INACTIVE' : 'ACTIVE' ?>">
                      <button type="submit" class="text-[11px] px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 font-semibold text-slate-600 transition">
                        <?= $isActive ? 'Khóa' : 'Mở' ?>
                      </button>
                    </form>
                    <!-- Edit Button -->
                    <button onclick="openEditStaffModal(<?= htmlspecialchars(json_encode([
                      'id' => $st['id'],
                      'ho_ten' => $st['ho_ten'],
                      'so_dien_thoai' => $st['so_dien_thoai'],
                      'ca_lam_viec' => $st['ca_lam_viec'],
                      'luong_co_ban' => $st['luong_co_ban']
                    ])) ?>)" class="p-1 text-slate-400 hover:text-slate-600 rounded hover:bg-slate-100 transition" title="Chỉnh sửa">
                      <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                    </button>
                  </div>
                <?php else: ?>
                  <span class="text-[11px] text-slate-400 italic">Quản trị viên</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</main>

<!-- MODAL: THÊM NHÂN VIÊN MỚI -->
<div id="modal-add-staff" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
  <div class="modal-backdrop fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modal-add-staff')"></div>
  <div class="modal-content relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl z-10 space-y-4">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
      <h3 class="font-bold text-slate-800 text-base">Thêm Nhân Viên Mới</h3>
      <button type="button" onclick="closeModal('modal-add-staff')" class="text-slate-400 hover:text-slate-600">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form action="action.php?act=add_staff" method="POST" class="space-y-3 text-xs">
      <div>
        <label class="block font-semibold text-slate-700 mb-1">Họ và tên nhân viên <span class="text-red-500">*</span></label>
        <input type="text" name="ho_ten" required placeholder="Ví dụ: Lê Văn Nam" 
          class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block font-semibold text-slate-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
          <input type="tel" name="so_dien_thoai" required placeholder="0912..." 
            class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
          <label class="block font-semibold text-slate-700 mb-1">Tên đăng nhập <span class="text-red-500">*</span></label>
          <input type="text" name="username" required placeholder="nv_nam" 
            class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
      </div>

      <div>
        <label class="block font-semibold text-slate-700 mb-1">Mật khẩu ban đầu</label>
        <input type="password" name="password" placeholder="Mặc định: 123456" 
          class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
      </div>

      <div>
        <label class="block font-semibold text-slate-700 mb-1">Ca làm việc</label>
        <select name="ca_lam_viec" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
          <option value="Ca Sáng (06:00 - 14:00)">Ca Sáng (06:00 - 14:00)</option>
          <option value="Ca Chiều - Tối (14:00 - 22:00)">Ca Chiều - Tối (14:00 - 22:00)</option>
          <option value="Ca Hành Chính (08:00 - 17:00)">Ca Hành Chính (08:00 - 17:00)</option>
          <option value="Xoay ca linh hoạt">Xoay ca linh hoạt</option>
        </select>
      </div>

      <div>
        <label class="block font-semibold text-slate-700 mb-1">Lương cơ bản (VNĐ)</label>
        <input type="number" name="luong_co_ban" value="7500000" step="500000" 
          class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
      </div>

      <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
        <button type="button" onclick="closeModal('modal-add-staff')" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-xl font-semibold">Hủy</button>
        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-sm">Lưu Nhân Viên</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: SỬA NHÂN VIÊN -->
<div id="modal-edit-staff" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
  <div class="modal-backdrop fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modal-edit-staff')"></div>
  <div class="modal-content relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl z-10 space-y-4">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
      <h3 class="font-bold text-slate-800 text-base">Chỉnh Sửa Thông Tin Nhân Viên</h3>
      <button type="button" onclick="closeModal('modal-edit-staff')" class="text-slate-400 hover:text-slate-600">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form action="action.php?act=edit_staff" method="POST" class="space-y-3 text-xs">
      <input type="hidden" name="id" id="edit-staff-id">

      <div>
        <label class="block font-semibold text-slate-700 mb-1">Họ và tên nhân viên <span class="text-red-500">*</span></label>
        <input type="text" name="ho_ten" id="edit-staff-name" required 
          class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
      </div>

      <div>
        <label class="block font-semibold text-slate-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
        <input type="tel" name="so_dien_thoai" id="edit-staff-phone" required 
          class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
      </div>

      <div>
        <label class="block font-semibold text-slate-700 mb-1">Ca làm việc</label>
        <select name="ca_lam_viec" id="edit-staff-shift" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
          <option value="Ca Sáng (06:00 - 14:00)">Ca Sáng (06:00 - 14:00)</option>
          <option value="Ca Chiều - Tối (14:00 - 22:00)">Ca Chiều - Tối (14:00 - 22:00)</option>
          <option value="Ca Hành Chính (08:00 - 17:00)">Ca Hành Chính (08:00 - 17:00)</option>
          <option value="Xoay ca linh hoạt">Xoay ca linh hoạt</option>
        </select>
      </div>

      <div>
        <label class="block font-semibold text-slate-700 mb-1">Lương cơ bản (VNĐ)</label>
        <input type="number" name="luong_co_ban" id="edit-staff-salary" step="500000" 
          class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
      </div>

      <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
        <button type="button" onclick="closeModal('modal-edit-staff')" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-xl font-semibold">Hủy</button>
        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-sm">Lưu Thay Đổi</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openEditStaffModal(data) {
    document.getElementById('edit-staff-id').value = data.id;
    document.getElementById('edit-staff-name').value = data.ho_ten;
    document.getElementById('edit-staff-phone').value = data.so_dien_thoai;
    document.getElementById('edit-staff-shift').value = data.ca_lam_viec;
    document.getElementById('edit-staff-salary').value = data.luong_co_ban;
    openModal('modal-edit-staff');
  }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
