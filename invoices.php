<?php
require_once __DIR__ . '/includes/auth.php';
checkLogin();

$pageTitle = 'Quản Lý Hóa Đơn Thu Tiền - GYMFIT PRO';
$pdo = getDB();
$keyword = trim($_GET['keyword'] ?? '');
$method  = trim($_GET['method'] ?? 'ALL');

$invoices = HoaDon::getAll($pdo, $keyword, $method);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- MAIN CONTENT -->
<main class="flex-1 min-w-0 space-y-5">
  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
    <div>
      <h1 class="text-2xl font-bold text-slate-900">Quản Lý Hóa Đơn & Thu Tiền</h1>
      <p class="text-xs text-slate-500 mt-0.5">Danh sách phiếu thu, hóa đơn đăng ký và gia hạn gói tập</p>
    </div>
    <div class="flex items-center gap-2">
      <button onclick="openRegisterPackageModal()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-1.5 shadow-sm">
        <i data-lucide="plus-circle" class="w-4 h-4"></i>
        <span>Lập Hóa Đơn Mới</span>
      </button>
    </div>
  </div>

  <!-- Filter & Search Invoices -->
  <form method="GET" action="invoices.php" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-3">
    <div class="relative flex-1">
      <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
      <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="Tìm theo mã HĐ, tên hội viên, gói tập..." 
        class="w-full pl-10 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
    </div>
    <div class="flex items-center gap-2">
      <select name="method" onchange="this.form.submit()" class="py-2 px-3 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
        <option value="ALL" <?= $method === 'ALL' ? 'selected' : '' ?>>Mọi hình thức thanh toán</option>
        <option value="TIEN_MAT" <?= $method === 'TIEN_MAT' ? 'selected' : '' ?>>Tiền mặt</option>
        <option value="CHUYEN_KHOAN" <?= $method === 'CHUYEN_KHOAN' ? 'selected' : '' ?>>Chuyển khoản QR</option>
        <option value="THE" <?= $method === 'THE' ? 'selected' : '' ?>>Thẻ POS</option>
      </select>
      <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition">
        Lọc
      </button>
      <?php if (!empty($keyword) || $method !== 'ALL'): ?>
        <a href="invoices.php" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100" title="Xóa bộ lọc">
          <i data-lucide="x" class="w-4 h-4"></i>
        </a>
      <?php endif; ?>
    </div>
  </form>

  <!-- Invoices Table -->
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
            <th class="py-3 px-4">Mã Hóa Đơn</th>
            <th class="py-3 px-4">Hội Viên</th>
            <th class="py-3 px-4">Gói Tập</th>
            <th class="py-3 px-4">Số Tiền</th>
            <th class="py-3 px-4">Hình Thức</th>
            <th class="py-3 px-4">Nhân Viên Thu</th>
            <th class="py-3 px-4">Thời Gian</th>
            <th class="py-3 px-4 text-right">In / Xem</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php if (empty($invoices)): ?>
            <tr>
              <td colspan="8" class="text-center py-8 text-slate-400">
                <i data-lucide="receipt" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                Không có hóa đơn nào phù hợp
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($invoices as $hd): 
              $methodLabels = [
                'TIEN_MAT'     => ['text' => 'Tiền mặt', 'color' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                'CHUYEN_KHOAN' => ['text' => 'Chuyển khoản', 'color' => 'bg-sky-50 text-sky-700 border-sky-200'],
                'THE'          => ['text' => 'Thẻ POS', 'color' => 'bg-indigo-50 text-indigo-700 border-indigo-200']
              ];
              $mInfo = $methodLabels[$hd->getPhuongThuc()] ?? ['text' => $hd->getPhuongThuc(), 'color' => 'bg-slate-100 text-slate-700'];
            ?>
              <tr class="hover:bg-slate-50 transition-colors">
                <td class="py-3 px-4 font-mono font-bold text-slate-800"><?= $hd->getMaHD() ?></td>
                <td class="py-3 px-4 font-semibold text-slate-800"><?= htmlspecialchars($hd->getTenHoiVien()) ?></td>
                <td class="py-3 px-4 text-slate-600"><?= htmlspecialchars($hd->getTenGoiTap()) ?></td>
                <td class="py-3 px-4 font-bold text-slate-900"><?= $hd->getSoTienFormatted() ?></td>
                <td class="py-3 px-4">
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold border <?= $mInfo['color'] ?>"><?= $mInfo['text'] ?></span>
                </td>
                <td class="py-3 px-4 text-slate-600"><?= htmlspecialchars($hd->getTenNhanVien()) ?></td>
                <td class="py-3 px-4 text-slate-400 text-[11px]"><?= $hd->getNgayTaoFormatted() ?></td>
                <td class="py-3 px-4 text-right">
                  <button onclick="viewInvoiceReceipt(<?= htmlspecialchars(json_encode([
                    'ma_hd' => $hd->getMaHD(),
                    'ngay' => $hd->getNgayTaoFormatted(),
                    'hoi_vien' => $hd->getTenHoiVien(),
                    'goi_tap' => $hd->getTenGoiTap(),
                    'phuong_thuc' => $hd->getTenPhuongThuc(),
                    'nhan_vien' => $hd->getTenNhanVien(),
                    'so_tien' => $hd->getSoTienFormatted()
                  ])) ?>)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px] transition inline-flex items-center gap-1">
                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                    <span>Xem / In</span>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</main>

<!-- MODAL: XEM & IN HÓA ĐƠN -->
<div id="modal-invoice-detail" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
  <div class="modal-backdrop fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modal-invoice-detail')"></div>
  <div class="modal-content relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl z-10 space-y-4">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100 no-print">
      <h3 class="font-bold text-slate-800 text-base">Hóa Đơn Thu Tiền</h3>
      <button type="button" onclick="closeModal('modal-invoice-detail')" class="text-slate-400 hover:text-slate-600">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <!-- INVOICE PRINTABLE AREA -->
    <div id="invoice-print-area" class="bg-white border border-slate-200 rounded-xl p-5 text-xs space-y-4">
      <div class="text-center border-b border-dashed border-slate-300 pb-3">
        <h2 class="text-base font-extrabold text-slate-900 tracking-wider">PHÒNG TẬP GYMFIT PRO</h2>
        <p class="text-[11px] text-slate-500">123 Đường Thể Thao, Quận 1, TP. Hồ Chí Minh</p>
        <p class="text-[11px] text-slate-500">Hotline: 1900 8888 - 0901 234 567</p>
        <div class="mt-2 text-xs font-bold text-slate-800 uppercase tracking-widest">PHIẾU THU TIỀN GÓI TẬP</div>
        <div class="text-[11px] text-slate-400 font-mono" id="inv-modal-code">HD000000</div>
      </div>

      <div class="space-y-1.5 text-slate-600">
        <div class="flex justify-between">
          <span>Ngày lập phiếu:</span>
          <span id="inv-modal-date" class="font-medium text-slate-800">--/--/----</span>
        </div>
        <div class="flex justify-between">
          <span>Hội viên:</span>
          <span id="inv-modal-member" class="font-bold text-slate-800">Nguyễn Văn A</span>
        </div>
        <div class="flex justify-between">
          <span>Gói đăng ký:</span>
          <span id="inv-modal-package" class="font-semibold text-slate-800">Gói 1 Tháng</span>
        </div>
        <div class="flex justify-between">
          <span>Hình thức:</span>
          <span id="inv-modal-method" class="font-medium text-slate-800">Tiền mặt</span>
        </div>
        <div class="flex justify-between">
          <span>Nhân viên thu:</span>
          <span id="inv-modal-staff" class="font-medium text-slate-800">Lê Quốc Bảo</span>
        </div>
      </div>

      <div class="border-t border-b border-dashed border-slate-300 py-2.5 flex justify-between items-center text-sm font-bold">
        <span class="text-slate-800">TỔNG THANH TOÁN:</span>
        <span id="inv-modal-total" class="text-sky-600 text-base">0 đ</span>
      </div>

      <div class="text-center text-[10px] text-slate-400 italic">
        Cảm ơn quý khách đã tin tưởng và đồng hành cùng GYMFIT PRO!<br>
        (Vui lòng giữ lại hóa đơn để đối chiếu khi cần)
      </div>
    </div>

    <div class="pt-2 flex justify-end gap-2 no-print">
      <button type="button" onclick="closeModal('modal-invoice-detail')" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-xl font-semibold text-xs">Đóng</button>
      <button type="button" onclick="window.print()" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-xs flex items-center gap-1.5 shadow-sm">
        <i data-lucide="printer" class="w-4 h-4"></i>
        <span>In Hóa Đơn</span>
      </button>
    </div>
  </div>
</div>

<script>
  function viewInvoiceReceipt(data) {
    document.getElementById('inv-modal-code').textContent = data.ma_hd;
    document.getElementById('inv-modal-date').textContent = data.ngay;
    document.getElementById('inv-modal-member').textContent = data.hoi_vien;
    document.getElementById('inv-modal-package').textContent = data.goi_tap;
    document.getElementById('inv-modal-method').textContent = data.phuong_thuc;
    document.getElementById('inv-modal-staff').textContent = data.nhan_vien;
    document.getElementById('inv-modal-total').textContent = data.so_tien;
    openModal('modal-invoice-detail');
  }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
