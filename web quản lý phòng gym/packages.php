<?php
require_once __DIR__ . '/includes/auth.php';
// Phân quyền: Chỉ Quản Lý mới được vào trang Quản Lý Gói Tập
checkRole('MANAGER');

$pageTitle = 'Quản Lý Danh Mục Gói Tập - GYMFIT PRO';
$pdo = getDB();
$packages = GoiTap::getAll($pdo, false);

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
      <h1 class="text-2xl font-bold text-slate-900 mt-1">Quản Lý Danh Mục Gói Tập</h1>
      <p class="text-xs text-slate-500 mt-0.5">Cấu hình thời hạn, giá niêm yết và danh sách quyền lợi dịch vụ</p>
    </div>
    <div class="flex items-center gap-2">
      <button onclick="openModal('modal-add-package')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-1.5 shadow-sm">
        <i data-lucide="plus-circle" class="w-4 h-4"></i>
        <span>Tạo Gói Tập Mới</span>
      </button>
    </div>
  </div>

  <!-- Package Cards Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    <?php foreach ($packages as $g): 
      $isAct = $g->isDangKinhDoanh();
      $perks = $g->getQuyenLoiArray();
    ?>
      <div class="bg-white rounded-2xl border <?= $isAct ? 'border-slate-200 shadow-sm' : 'border-dashed border-slate-300 opacity-70' ?> p-5 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between mb-2">
            <span class="font-mono text-[10px] font-bold text-slate-400"><?= $g->getMaGoi() ?></span>
            <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $isAct ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
              <?= $isAct ? 'Đang mở bán' : 'Tạm dừng' ?>
            </span>
          </div>
          <h3 class="font-bold text-slate-800 text-sm mb-1"><?= htmlspecialchars($g->getTenGoi()) ?></h3>
          <p class="text-xs text-slate-500 mb-3"><?= htmlspecialchars($g->getMoTa()) ?></p>
          <div class="text-xl font-extrabold text-sky-600 mb-4">
            <?= $g->getGiaFormatted() ?> <span class="text-xs text-slate-400 font-normal">/ <?= $g->getThoiHanThang() ?> tháng</span>
          </div>

          <!-- Quyền lợi -->
          <div class="space-y-1.5 border-t border-slate-100 pt-3 mb-4 text-xs">
            <div class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Quyền lợi bao gồm:</div>
            <?php foreach ($perks as $p): ?>
              <div class="flex items-center gap-2 text-slate-600">
                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-500 shrink-0"></i>
                <span><?= htmlspecialchars($p) ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="border-t border-slate-100 pt-3 flex items-center justify-between text-xs">
          <!-- Toggle Status Form -->
          <form action="action.php?act=toggle_package" method="POST" class="inline">
            <input type="hidden" name="id" value="<?= $g->getId() ?>">
            <input type="hidden" name="trang_thai" value="<?= $isAct ? 'DISABLED' : 'ACTIVE' ?>">
            <button type="submit" class="font-semibold <?= $isAct ? 'text-amber-600 hover:text-amber-700' : 'text-emerald-600 hover:text-emerald-700' ?>">
              <?= $isAct ? 'Tạm dừng bán' : 'Kích hoạt lại' ?>
            </button>
          </form>

          <!-- Edit Button -->
          <button onclick="openEditPackageModal(<?= htmlspecialchars(json_encode([
            'id' => $g->getId(),
            'ten_goi' => $g->getTenGoi(),
            'thoi_han_thang' => $g->getThoiHanThang(),
            'gia_tien' => $g->getGiaTien(),
            'moTa' => $g->getMoTa(),
            'quyen_loi' => $g->getQuyenLoiRaw()
          ])) ?>)" class="font-semibold text-sky-600 hover:text-sky-700">
            Chỉnh sửa
          </button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</main>

<!-- MODAL: TẠO GÓI TẬP MỚI -->
<div id="modal-add-package" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
  <div class="modal-backdrop fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modal-add-package')"></div>
  <div class="modal-content relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl z-10 space-y-4">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
      <h3 class="font-bold text-slate-800 text-base">Tạo Gói Tập Mới</h3>
      <button type="button" onclick="closeModal('modal-add-package')" class="text-slate-400 hover:text-slate-600">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form action="action.php?act=add_package" method="POST" class="space-y-3 text-xs">
      <div>
        <label class="block font-semibold text-slate-700 mb-1">Tên gói tập <span class="text-red-500">*</span></label>
        <input type="text" name="ten_goi" required placeholder="Ví dụ: Gói Sinh Viên 3 Tháng" 
          class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block font-semibold text-slate-700 mb-1">Thời hạn (Tháng) <span class="text-red-500">*</span></label>
          <input type="number" name="thoi_han_thang" required min="1" max="60" value="1" 
            class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
          <label class="block font-semibold text-slate-700 mb-1">Giá tiền (VNĐ) <span class="text-red-500">*</span></label>
          <input type="number" name="gia_tien" required min="0" step="50000" placeholder="500000" 
            class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
      </div>

      <div>
        <label class="block font-semibold text-slate-700 mb-1">Mô tả ngắn</label>
        <textarea name="mo_ta" rows="2" placeholder="Tóm tắt điểm nổi bật..." 
          class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
      </div>

      <div>
        <label class="block font-semibold text-slate-700 mb-1">Quyền lợi (Mỗi dòng một quyền lợi)</label>
        <textarea name="quyen_loi" rows="3" placeholder="Sử dụng máy tập gym&#10;Tủ đồ cá nhân&#10;Phòng tắm nóng lạnh" 
          class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
      </div>

      <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
        <button type="button" onclick="closeModal('modal-add-package')" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-xl font-semibold">Hủy</button>
        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-sm">Lưu Gói Tập</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: SỬA GÓI TẬP -->
<div id="modal-edit-package" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
  <div class="modal-backdrop fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modal-edit-package')"></div>
  <div class="modal-content relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl z-10 space-y-4">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
      <h3 class="font-bold text-slate-800 text-base">Chỉnh Sửa Gói Tập</h3>
      <button type="button" onclick="closeModal('modal-edit-package')" class="text-slate-400 hover:text-slate-600">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form action="action.php?act=edit_package" method="POST" class="space-y-3 text-xs">
      <input type="hidden" name="id" id="edit-pack-id">

      <div>
        <label class="block font-semibold text-slate-700 mb-1">Tên gói tập <span class="text-red-500">*</span></label>
        <input type="text" name="ten_goi" id="edit-pack-name" required 
          class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block font-semibold text-slate-700 mb-1">Thời hạn (Tháng) <span class="text-red-500">*</span></label>
          <input type="number" name="thoi_han_thang" id="edit-pack-duration" required min="1" max="60" 
            class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
          <label class="block font-semibold text-slate-700 mb-1">Giá tiền (VNĐ) <span class="text-red-500">*</span></label>
          <input type="number" name="gia_tien" id="edit-pack-price" required min="0" step="50000" 
            class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
      </div>

      <div>
        <label class="block font-semibold text-slate-700 mb-1">Mô tả ngắn</label>
        <textarea name="mo_ta" id="edit-pack-desc" rows="2" 
          class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
      </div>

      <div>
        <label class="block font-semibold text-slate-700 mb-1">Quyền lợi (Mỗi dòng một quyền lợi)</label>
        <textarea name="quyen_loi" id="edit-pack-perks" rows="3" 
          class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
      </div>

      <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
        <button type="button" onclick="closeModal('modal-edit-package')" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-xl font-semibold">Hủy</button>
        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-sm">Lưu Thay Đổi</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openEditPackageModal(data) {
    document.getElementById('edit-pack-id').value = data.id;
    document.getElementById('edit-pack-name').value = data.ten_goi;
    document.getElementById('edit-pack-duration').value = data.thoi_han_thang;
    document.getElementById('edit-pack-price').value = data.gia_tien;
    document.getElementById('edit-pack-desc').value = data.moTa || '';
    document.getElementById('edit-pack-perks').value = data.quyen_loi || '';
    openModal('modal-edit-package');
  }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
