<?php
require_once __DIR__ . '/includes/auth.php';
checkLogin();

$pageTitle = 'Kiểm Tra Check-In Hội Viên - GYMFIT PRO';
$pdo = getDB();
$searchQuery = trim($_GET['search'] ?? '');
$foundMember = null;

if (!empty($searchQuery)) {
    $foundMember = HoiVien::findByPhoneOrCode($searchQuery, $pdo);
}

$todayLogs = CheckIn::getTodayLogs($pdo);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- MAIN CONTENT -->
<main class="flex-1 min-w-0 space-y-6">
  
  <div class="pb-2 border-b border-slate-200">
    <h1 class="text-2xl font-bold text-slate-900">Kiểm Tra Check-In Hội Viên</h1>
    <p class="text-xs text-slate-500 mt-0.5">Xác thực thời hạn gói tập để duyệt hội viên vào phòng tập</p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    
    <!-- Left: Lookup Card (5 Cols) -->
    <div class="lg:col-span-5 space-y-4">
      <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
          <i data-lucide="scan-barcode" class="w-4 h-4 text-sky-600"></i>
          <span>Tra Cứu Thông Tin Check-In</span>
        </h3>
        
        <form method="GET" action="checkin.php" class="space-y-2">
          <label class="block text-xs font-semibold text-slate-600">Số điện thoại hoặc Mã hội viên</label>
          <div class="flex gap-2">
            <input type="text" name="search" id="input-checkin-search" value="<?= htmlspecialchars($searchQuery) ?>" required placeholder="Ví dụ: 0987654321..." 
              class="flex-1 px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500">
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition">
              Tìm
            </button>
          </div>
        </form>

        <!-- Quick sample numbers -->
        <div class="text-[11px] text-slate-400">
          Mẫu thử nhanh: 
          <a href="checkin.php?search=0987654321" class="text-sky-600 underline">0987654321</a> (còn hạn), 
          <a href="checkin.php?search=0943999888" class="text-sky-600 underline">0943999888</a> (hết hạn)
        </div>

        <!-- Result Card -->
        <?php if (!empty($searchQuery)): ?>
          <?php if ($foundMember): 
            $isConHan = $foundMember->isGoiConHan();
            $daysLeft = $foundMember->soNgayConLai();
          ?>
            <div class="border rounded-xl p-4 space-y-3 <?= $isConHan ? 'border-emerald-200 bg-emerald-50/50' : 'border-rose-200 bg-rose-50/50' ?>">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <img src="<?= htmlspecialchars($foundMember->getAvatar()) ?>" class="w-10 h-10 rounded-full border border-slate-200" alt="Avatar">
                  <div>
                    <h4 class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($foundMember->getHoTen()) ?></h4>
                    <p class="text-[11px] text-slate-500 font-mono"><?= $foundMember->getMaHV() ?> • <?= $foundMember->getSoDienThoai() ?></p>
                  </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold <?= $isConHan ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' ?>">
                  <?= $isConHan ? "HỢP LỆ (Còn $daysLeft ngày)" : "GÓI ĐÃ HẾT HẠN" ?>
                </span>
              </div>

              <div class="bg-white p-3 rounded-lg border border-slate-200 text-xs space-y-1">
                <div class="flex justify-between">
                  <span class="text-slate-500">Gói tập:</span>
                  <span class="font-bold text-slate-800"><?= htmlspecialchars($foundMember->getTenGoiTap()) ?></span>
                </div>
                <div class="flex justify-between">
                  <span class="text-slate-500">Hạn sử dụng:</span>
                  <span class="font-bold <?= $isConHan ? 'text-emerald-700' : 'text-rose-600' ?>">
                    <?= $foundMember->getNgayHetHan() ? date('d/m/Y', strtotime($foundMember->getNgayHetHan())) : 'Chưa đăng ký' ?>
                  </span>
                </div>
              </div>

              <div class="flex gap-2">
                <?php if ($isConHan): ?>
                  <form action="action.php?act=do_checkin" method="POST" class="flex-1">
                    <input type="hidden" name="hoi_vien_id" value="<?= $foundMember->getId() ?>">
                    <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 rounded-xl font-bold text-xs text-white shadow-sm flex items-center justify-center gap-1.5 transition">
                      <i data-lucide="check" class="w-4 h-4"></i>
                      <span>Xác Nhận Check-In</span>
                    </button>
                  </form>
                <?php else: ?>
                  <button type="button" disabled class="flex-1 py-2.5 bg-slate-400 rounded-xl font-bold text-xs text-white cursor-not-allowed">
                    Không Thể Check-In
                  </button>
                  <button type="button" onclick="openRegisterPackageModal(<?= $foundMember->getId() ?>)" class="px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-bold text-xs flex items-center gap-1 shadow-sm transition">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                    <span>Gia Hạn Gói</span>
                  </button>
                <?php endif; ?>
              </div>
            </div>
          <?php else: ?>
            <div class="border border-rose-200 bg-rose-50 rounded-xl p-4 text-xs text-rose-700 space-y-1">
              <div class="flex items-center gap-2 font-bold">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                <span>Không tìm thấy hội viên!</span>
              </div>
              <p class="text-[11px]">Không có khách hàng nào khớp với số điện thoại hoặc mã <strong><?= htmlspecialchars($searchQuery) ?></strong>.</p>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>

      <!-- Rule Box -->
      <div class="p-4 rounded-xl bg-sky-50 border border-sky-100 text-xs text-sky-800 flex items-start gap-3">
        <i data-lucide="help-circle" class="w-5 h-5 text-sky-600 shrink-0 mt-0.5"></i>
        <div>
          <span class="font-bold">Quy tắc check-in:</span> Chỉ hội viên có gói tập <strong class="text-emerald-700">còn hạn</strong> mới được check-in. Nếu gói đã hết hạn, nhân viên có thể bấm "Gia hạn gói" ngay lập tức để lập hóa đơn và kích hoạt tiếp.
        </div>
      </div>
    </div>

    <!-- Right: Today's Check-in Log (7 Cols) -->
    <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
          <i data-lucide="history" class="w-4 h-4 text-purple-600"></i>
          <span>Nhật Ký Check-in Hôm Nay</span>
        </h3>
        <span class="text-xs bg-purple-50 text-purple-700 font-bold px-2 py-0.5 rounded-full">
          <?= count($todayLogs) ?> lượt
        </span>
      </div>

      <div class="overflow-hidden">
        <div class="overflow-y-auto max-h-[460px] divide-y divide-slate-100">
          <?php if (empty($todayLogs)): ?>
            <div class="py-8 text-center text-slate-400 text-xs">
              Chưa có lượt check-in nào trong hôm nay
            </div>
          <?php else: ?>
            <?php foreach ($todayLogs as $log): 
              $isHopLe = $log->isHopLe();
            ?>
              <div class="py-3 flex items-center justify-between text-xs">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 <?= $isHopLe ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' ?>">
                    <i data-lucide="<?= $isHopLe ? 'check-circle' : 'x-circle' ?>" class="w-4 h-4"></i>
                  </div>
                  <div>
                    <div class="font-bold text-slate-800">
                      <?= htmlspecialchars($log->getTenHoiVien()) ?> 
                      <span class="font-normal text-slate-400 font-mono text-[11px]">(<?= $log->getMaHV() ?>)</span>
                    </div>
                    <div class="text-[11px] text-slate-500"><?= htmlspecialchars($log->getGhiChu()) ?></div>
                  </div>
                </div>
                <div class="text-right">
                  <div class="font-medium text-slate-700"><?= $log->getThoiGianFormatted() ?></div>
                  <div class="text-[10px] text-slate-400">Duyệt bởi: <?= htmlspecialchars($log->getTenNhanVien()) ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
