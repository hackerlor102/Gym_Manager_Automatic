<?php
/**
 * FOOTER & MODAL COMPONENTS
 */
$flash = getFlash();
$pdo = getDB();
$allMembersForModal = HoiVien::getAll($pdo);
$activePackagesForModal = GoiTap::getAll($pdo, true);
?>
  </div> <!-- END MAIN APP CONTAINER -->

  <!-- MODAL DÙNG CHUNG: ĐĂNG KÝ / GIA HẠN GÓI TẬP -->
  <div id="modal-register-package" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="modal-backdrop fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modal-register-package')"></div>
    <div class="modal-content relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl z-10 space-y-4">
      <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div>
          <h3 class="font-bold text-slate-800 text-base">Đăng Ký / Gia Hạn Gói Tập</h3>
          <p class="text-xs text-slate-400">Tự động kích hoạt thời hạn và tạo hóa đơn thu tiền vào CSDL</p>
        </div>
        <button type="button" onclick="closeModal('modal-register-package')" class="text-slate-400 hover:text-slate-600">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <form action="action.php?act=register_package" method="POST" class="space-y-4 text-xs">
        <div>
          <label class="block font-semibold text-slate-700 mb-1">Chọn Hội Viên <span class="text-red-500">*</span></label>
          <select name="hoi_vien_id" id="reg-form-member-id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
            <?php foreach ($allMembersForModal as $m): ?>
              <option value="<?= $m->getId() ?>">
                <?= htmlspecialchars($m->getHoTen()) ?> (<?= $m->getMaHV() ?> - <?= $m->getSoDienThoai() ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block font-semibold text-slate-700 mb-1">Chọn Gói Tập <span class="text-red-500">*</span></label>
          <select name="goi_tap_id" id="reg-form-package-id" required onchange="updatePackageSummary()" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
            <?php foreach ($activePackagesForModal as $g): ?>
              <option value="<?= $g->getId() ?>" data-price="<?= $g->getGiaTien() ?>" data-duration="<?= $g->getThoiHanThang() ?>">
                <?= htmlspecialchars($g->getTenGoi()) ?> - <?= $g->getGiaFormatted() ?> (<?= $g->getThoiHanThang() ?> tháng)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
          <div class="flex justify-between text-slate-600">
            <span>Thời hạn cộng thêm:</span>
            <span id="reg-summary-duration" class="font-bold text-slate-800">1 tháng</span>
          </div>
          <div class="flex justify-between text-slate-600">
            <span>Số tiền thanh toán:</span>
            <span id="reg-summary-price" class="font-bold text-sky-600 text-sm">450.000 đ</span>
          </div>
        </div>

        <div>
          <label class="block font-semibold text-slate-700 mb-1">Hình thức thanh toán</label>
          <div class="grid grid-cols-3 gap-2">
            <label class="border border-slate-200 rounded-xl p-2.5 flex items-center justify-center gap-1.5 cursor-pointer hover:bg-slate-50 font-semibold has-[:checked]:bg-sky-50 has-[:checked]:border-sky-500 has-[:checked]:text-sky-700">
              <input type="radio" name="phuong_thuc" value="TIEN_MAT" checked class="hidden">
              <i data-lucide="banknote" class="w-4 h-4"></i>
              <span>Tiền mặt</span>
            </label>
            <label class="border border-slate-200 rounded-xl p-2.5 flex items-center justify-center gap-1.5 cursor-pointer hover:bg-slate-50 font-semibold has-[:checked]:bg-sky-50 has-[:checked]:border-sky-500 has-[:checked]:text-sky-700">
              <input type="radio" name="phuong_thuc" value="CHUYEN_KHOAN" class="hidden">
              <i data-lucide="smartphone" class="w-4 h-4"></i>
              <span>Chuyển khoản</span>
            </label>
            <label class="border border-slate-200 rounded-xl p-2.5 flex items-center justify-center gap-1.5 cursor-pointer hover:bg-slate-50 font-semibold has-[:checked]:bg-sky-50 has-[:checked]:border-sky-500 has-[:checked]:text-sky-700">
              <input type="radio" name="phuong_thuc" value="THE" class="hidden">
              <i data-lucide="credit-card" class="w-4 h-4"></i>
              <span>Thẻ POS</span>
            </label>
          </div>
        </div>

        <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
          <button type="button" onclick="closeModal('modal-register-package')" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-xl font-semibold">Hủy</button>
          <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-sm flex items-center gap-1.5">
            <i data-lucide="check" class="w-4 h-4"></i>
            <span>Xác Nhận & Xuất Hóa Đơn</span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- TOAST NOTIFICATION CONTAINER -->
  <div id="toast" class="fixed bottom-5 right-5 z-50 bg-slate-900 text-white px-4 py-3 rounded-xl shadow-2xl flex items-center gap-2.5 text-xs font-semibold transform transition-all duration-300 translate-y-20 opacity-0 pointer-events-none">
    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400" id="toast-icon"></i>
    <span id="toast-message">Thông báo thành công</span>
  </div>

  <script>
    // Khởi tạo icon Lucide an toàn
    function safeCreateIcons() {
      if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
        lucide.createIcons();
      }
    }

    function openModal(id) {
      const el = document.getElementById(id);
      if (el) {
        el.classList.remove('hidden');
        safeCreateIcons();
      }
    }

    function closeModal(id) {
      const el = document.getElementById(id);
      if (el) el.classList.add('hidden');
    }

    function openRegisterPackageModal(memberId = null) {
      if (memberId) {
        const sel = document.getElementById('reg-form-member-id');
        if (sel) sel.value = memberId;
      }
      updatePackageSummary();
      openModal('modal-register-package');
    }

    function updatePackageSummary() {
      const sel = document.getElementById('reg-form-package-id');
      if (!sel) return;
      const opt = sel.options[sel.selectedIndex];
      if (opt) {
        const duration = opt.getAttribute('data-duration') || '1';
        const price = Number(opt.getAttribute('data-price') || 0);
        document.getElementById('reg-summary-duration').textContent = duration + ' tháng';
        document.getElementById('reg-summary-price').textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
      }
    }

    function showToast(msg, type = 'success') {
      const t = document.getElementById('toast');
      const tm = document.getElementById('toast-message');
      const ti = document.getElementById('toast-icon');
      if (!t || !tm) return;

      tm.textContent = msg;
      t.className = t.className.replace('bg-rose-600', 'bg-slate-900');
      if (type === 'error') {
        t.className = t.className.replace('bg-slate-900', 'bg-rose-600');
      }
      t.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
      setTimeout(() => {
        t.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
      }, 3000);
    }

    document.addEventListener('DOMContentLoaded', () => {
      safeCreateIcons();
      updatePackageSummary();

      <?php if ($flash): ?>
        showToast(<?= json_encode($flash['message']) ?>, <?= json_encode($flash['type']) ?>);
      <?php endif; ?>
    });
  </script>
</body>
</html>
