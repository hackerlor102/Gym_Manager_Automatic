/**
 * APP CONTROLLER: Quản lý giao diện, xác thực đăng nhập, phân quyền vai trò
 * Kết nối các lớp đối tượng OOP (User, NhanVien, QuanLy, HoiVien, GoiTap, HoaDon, CheckIn)
 * với giao diện HTML/Tailwind CSS
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Khởi tạo dịch vụ phòng gym
  const gymService = new GymService();

  // 2. Trạng thái giao diện
  let currentActiveTab = 'tab-dashboard';

  // Helper an toàn khi khởi tạo icon Lucide
  function safeCreateIcons() {
    if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
      lucide.createIcons();
    }
  }

  // ==========================================
  // HELPER: HIỂN THỊ THÔNG BÁO TOAST
  // ==========================================
  function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMsg = document.getElementById('toast-message');
    const toastIcon = document.getElementById('toast-icon');

    toastMsg.textContent = message;
    if (type === 'error') {
      toast.className = toast.className.replace('bg-slate-900', 'bg-rose-600');
    } else {
      toast.className = toast.className.replace('bg-rose-600', 'bg-slate-900');
    }

    toast.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
    setTimeout(() => {
      toast.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
    }, 2800);
  }

  // ==========================================
  // HELPER: MODAL HANDLERS
  // ==========================================
  function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.remove('hidden');
      safeCreateIcons();
    }
  }

  function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.add('hidden');
    }
  }

  document.querySelectorAll('.close-modal').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const modal = e.target.closest('[id^="modal-"]');
      if (modal) modal.classList.add('hidden');
    });
  });

  document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
    backdrop.addEventListener('click', (e) => {
      const modal = e.target.closest('[id^="modal-"]');
      if (modal) modal.classList.add('hidden');
    });
  });

  // ==========================================
  // XÁC THỰC ĐĂNG NHẬP & PHÂN QUYỀN
  // ==========================================
  const viewLogin = document.getElementById('view-login');
  const viewApp = document.getElementById('view-app');
  const formLogin = document.getElementById('form-login');
  const loginUsername = document.getElementById('login-username');
  const loginPassword = document.getElementById('login-password');
  const loginErrorAlert = document.getElementById('login-error-alert');
  const loginErrorText = document.getElementById('login-error-text');
  const btnTogglePassword = document.getElementById('btn-toggle-password');
  const iconTogglePassword = document.getElementById('icon-toggle-password');
  const btnLogout = document.getElementById('btn-logout');

  // Elements của App Workspace
  const headerWorkspaceLabel = document.getElementById('header-workspace-label');
  const currentUserName = document.getElementById('current-user-name');
  const currentUserBadge = document.getElementById('current-user-badge');
  const currentUserRoleDesc = document.getElementById('current-user-role-desc');
  const userAvatarText = document.getElementById('user-avatar-text');
  const roleAlertBox = document.getElementById('role-alert-box');
  const roleAlertTitle = document.getElementById('role-alert-title');
  const roleAlertDesc = document.getElementById('role-alert-desc');
  const tabManagerOnlyElements = document.querySelectorAll('.tab-manager-only');

  // Toggle ẩn/hiện mật khẩu trong form đăng nhập
  if (btnTogglePassword) {
    btnTogglePassword.addEventListener('click', () => {
      const isPass = loginPassword.type === 'password';
      loginPassword.type = isPass ? 'text' : 'password';
      btnTogglePassword.innerHTML = `<i data-lucide="${isPass ? 'eye-off' : 'eye'}" class="w-4 h-4 text-slate-400"></i>`;
      safeCreateIcons();
    });
  }

  // Kiểm tra trạng thái đăng nhập
  function checkAuthState() {
    if (gymService.isLoggedIn()) {
      viewLogin.classList.add('hidden');
      viewApp.classList.remove('hidden');
      renderWorkspaceForRole(gymService.currentUser);
    } else {
      viewLogin.classList.remove('hidden');
      viewApp.classList.add('hidden');
      loginErrorAlert.classList.add('hidden');
      safeCreateIcons();
    }
  }

  // Cấu hình không gian làm việc theo vai trò
  function renderWorkspaceForRole(user) {
    const isManager = user.vaiTro === 'MANAGER';

    currentUserName.textContent = user.hoTen;
    userAvatarText.textContent = isManager ? 'QL' : 'NV';

    if (isManager) {
      // 1. GIAO DIỆN QUẢN LÝ
      headerWorkspaceLabel.textContent = 'Quản Trị Hệ Thống Toàn Quyền';
      currentUserBadge.textContent = 'QUẢN LÝ TỔNG';
      currentUserBadge.className = 'px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200';
      currentUserRoleDesc.textContent = `${user.phongBan || 'Ban Quản Lý'} • Toàn quyền điều hành`;

      roleAlertBox.className = 'p-4 rounded-2xl bg-indigo-50 border border-indigo-100 text-xs text-indigo-900 flex items-start gap-3';
      roleAlertTitle.textContent = 'Không Gian Quản Trị: Quản Lý';
      roleAlertDesc.textContent = 'Toàn quyền: Giám sát doanh thu, quản lý nhân viên, cấu hình gói tập và quản lý hội viên.';

      // Hiện các tab chức năng quản lý
      tabManagerOnlyElements.forEach(el => el.style.display = 'flex');

      // Mặc định tab cho Quản lý: Thống kê doanh thu
      switchTab('tab-dashboard');
    } else {
      // 2. GIAO DIỆN NHÂN VIÊN
      headerWorkspaceLabel.textContent = 'Cổng Nhân Viên Lễ Tân';
      currentUserBadge.textContent = 'NHÂN VIÊN LỄ TÂN';
      currentUserBadge.className = 'px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200';
      currentUserRoleDesc.textContent = `${user.caLamViec} • Lễ tân quầy`;

      roleAlertBox.className = 'p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-xs text-emerald-900 flex items-start gap-3';
      roleAlertTitle.textContent = 'Không Gian Nghiệp Vụ: Nhân Viên';
      roleAlertDesc.textContent = 'Quyền hạn: Kiểm tra check-in, quản lý hội viên, đăng ký gói tập và xuất hóa đơn thu tiền.';

      // Ẩn hoàn toàn các chức năng của Quản lý
      tabManagerOnlyElements.forEach(el => el.style.display = 'none');

      // Mặc định tab cho Nhân viên: Kiểm tra check-in
      switchTab('tab-checkin');
    }

    // Cập nhật số lượng hội viên trên sidebar
    document.getElementById('badge-member-count').textContent = gymService.hoiVienList.length;
    safeCreateIcons();
  }

  // Xử lý Submit Form Đăng Nhập
  if (formLogin) {
    formLogin.addEventListener('submit', (e) => {
      e.preventDefault();
      const username = loginUsername.value.trim();
      const password = loginPassword.value.trim();

      try {
        const user = gymService.dangNhap(username, password);
        loginErrorAlert.classList.add('hidden');
        formLogin.reset();
        showToast(`Đăng nhập thành công! Xin chào ${user.hoTen}`);
        checkAuthState();
      } catch (err) {
        loginErrorText.textContent = err.message;
        loginErrorAlert.classList.remove('hidden');
      }
    });
  }

  // Nút đăng nhập nhanh bằng tài khoản mẫu
  document.querySelectorAll('.btn-quick-login-role').forEach(btn => {
    btn.addEventListener('click', () => {
      const username = btn.getAttribute('data-username');
      const password = btn.getAttribute('data-password');

      try {
        const user = gymService.dangNhap(username, password);
        loginErrorAlert.classList.add('hidden');
        showToast(`Đăng nhập thành công với tài khoản: ${user.hoTen} (${user.vaiTro === 'MANAGER' ? 'Quản Lý' : 'Nhân Viên'})`);
        checkAuthState();
      } catch (err) {
        showToast(err.message, 'error');
      }
    });
  });

  // Nút Đăng Xuất
  if (btnLogout) {
    btnLogout.addEventListener('click', () => {
      if (confirm('Bạn có chắc muốn đăng xuất khỏi hệ thống không?')) {
        gymService.dangXuat();
        showToast('Đã đăng xuất khỏi tài khoản thành công');
        checkAuthState();
      }
    });
  }

  // ==========================================
  // ĐIỀU HƯỚNG TAB
  // ==========================================
  const navButtons = document.querySelectorAll('.nav-tab-btn');
  const tabPanes = document.querySelectorAll('.tab-pane');

  function switchTab(tabId) {
    currentActiveTab = tabId;

    navButtons.forEach(btn => {
      if (btn.getAttribute('data-tab') === tabId) {
        btn.classList.add('bg-sky-50', 'text-sky-700');
        btn.classList.remove('text-slate-600', 'hover:bg-slate-100');
      } else {
        btn.classList.remove('bg-sky-50', 'text-sky-700');
        btn.classList.add('text-slate-600', 'hover:bg-slate-100');
      }
    });

    tabPanes.forEach(pane => {
      if (pane.id === tabId) {
        pane.classList.remove('hidden');
      } else {
        pane.classList.add('hidden');
      }
    });

    renderCurrentTab();
    safeCreateIcons();
  }

  navButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const tabId = btn.getAttribute('data-tab');
      switchTab(tabId);
    });
  });

  function renderCurrentTab() {
    switch (currentActiveTab) {
      case 'tab-dashboard':
        renderDashboard();
        break;
      case 'tab-members':
        renderMembers();
        break;
      case 'tab-checkin':
        renderCheckinHistory();
        break;
      case 'tab-invoices':
        renderInvoices();
        break;
      case 'tab-staff':
        renderStaff();
        break;
      case 'tab-packages':
        renderPackages();
        break;
    }
    document.getElementById('badge-member-count').textContent = gymService.hoiVienList.length;
    safeCreateIcons();
  }

  // ==========================================
  // 1. RENDER: THỐNG KÊ DOANH THU (QUẢN LÝ)
  // ==========================================
  function renderDashboard() {
    if (!gymService.currentUser || gymService.currentUser.vaiTro !== 'MANAGER') return;

    const stats = gymService.layBaoCaoThongKe();
    const formattedRevenue = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(stats.tongDoanhThu);

    document.getElementById('kpi-revenue').textContent = formattedRevenue;
    document.getElementById('kpi-members').textContent = stats.tongHoiVien;
    document.getElementById('kpi-active-members').textContent = stats.hoiVienConHan;
    document.getElementById('kpi-checkin').textContent = stats.checkInHomNay;
    document.getElementById('kpi-invoices').textContent = stats.tongSoHoaDon;
    document.getElementById('current-date-display').textContent = new Date().toLocaleDateString('vi-VN', {
      weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    });

    // Breakdown hình thức thanh toán
    const paymentMethodsContainer = document.getElementById('payment-methods-breakdown');
    const methods = [
      { key: 'CHUYEN_KHOAN', label: 'Chuyển khoản QR', amount: stats.theoPhuongThuc.CHUYEN_KHOAN, color: 'bg-sky-500' },
      { key: 'TIEN_MAT', label: 'Tiền mặt', amount: stats.theoPhuongThuc.TIEN_MAT, color: 'bg-emerald-500' },
      { key: 'THE', label: 'Thẻ POS', amount: stats.theoPhuongThuc.THE, color: 'bg-indigo-500' }
    ];

    paymentMethodsContainer.innerHTML = methods.map(m => {
      const pct = stats.tongDoanhThu > 0 ? Math.round((m.amount / stats.tongDoanhThu) * 100) : 0;
      const fmt = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(m.amount);
      return `
        <div>
          <div class="flex justify-between text-xs mb-1">
            <span class="font-medium text-slate-700">${m.label}</span>
            <span class="font-bold text-slate-900">${fmt} (${pct}%)</span>
          </div>
          <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
            <div class="${m.color} h-2 rounded-full" style="width: ${pct}%"></div>
          </div>
        </div>
      `;
    }).join('');

    // Gói tập bán chạy
    const popularContainer = document.getElementById('popular-packages-list');
    const packEntries = Object.entries(stats.demGoiTap);
    if (packEntries.length === 0) {
      popularContainer.innerHTML = '<div class="text-xs text-slate-400 py-3">Chưa có giao dịch nào</div>';
    } else {
      popularContainer.innerHTML = packEntries.map(([tenGoi, count]) => {
        return `
          <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center font-bold text-xs">
                <i data-lucide="package" class="w-4 h-4"></i>
              </div>
              <span class="font-semibold text-xs text-slate-800">${tenGoi}</span>
            </div>
            <span class="text-xs px-2.5 py-1 rounded-full bg-sky-100 text-sky-800 font-bold">${count} lượt đăng ký</span>
          </div>
        `;
      }).join('');
    }
  }

  // ==========================================
  // 2. RENDER: QUẢN LÝ HỘI VIÊN (CẢ 2 VAI TRÒ)
  // ==========================================
  const memberSearchInput = document.getElementById('member-search-input');
  const memberFilterStatus = document.getElementById('member-filter-status');
  const memberTableBody = document.getElementById('member-table-body');

  function renderMembers() {
    const keyword = memberSearchInput.value.trim();
    const status = memberFilterStatus.value;
    const members = gymService.timKiemHoiVien(keyword, status);

    if (members.length === 0) {
      memberTableBody.innerHTML = `
        <tr>
          <td colspan="6" class="text-center py-8 text-slate-400">
            <i data-lucide="users" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
            Không tìm thấy hội viên phù hợp
          </td>
        </tr>
      `;
      safeCreateIcons();
      return;
    }

    memberTableBody.innerHTML = members.map(m => {
      const isConHan = m.isGoiConHan();
      const daysLeft = m.soNgayConLai();

      let badgeHtml = '';
      if (m.trangThai === 'ACTIVE') {
        badgeHtml = `<span class="badge-active px-2.5 py-1 rounded-full font-bold text-[10px] inline-flex items-center gap-1">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Còn ${daysLeft} ngày
        </span>`;
      } else if (m.trangThai === 'EXPIRED') {
        badgeHtml = `<span class="badge-expired px-2.5 py-1 rounded-full font-bold text-[10px] inline-flex items-center gap-1">
          <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Đã hết hạn
        </span>`;
      } else {
        badgeHtml = `<span class="badge-pending px-2.5 py-1 rounded-full font-bold text-[10px] inline-flex items-center gap-1">
          <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Chờ đăng ký
        </span>`;
      }

      return `
        <tr class="hover:bg-slate-50 transition-colors">
          <td class="py-3 px-4">
            <div class="flex items-center gap-3">
              <img src="${m.avatar}" class="w-8 h-8 rounded-full border border-slate-200" alt="${m.hoTen}">
              <div>
                <div class="font-bold text-slate-800">${m.hoTen}</div>
                <div class="text-[11px] text-slate-400 font-mono">${m.maHoiVien} • ${m.gioiTinh}</div>
              </div>
            </div>
          </td>
          <td class="py-3 px-4">
            <div class="font-medium text-slate-700">${m.soDienThoai}</div>
            <div class="text-[11px] text-slate-400">${m.email || 'Chưa có email'}</div>
          </td>
          <td class="py-3 px-4">
            <span class="font-medium text-slate-800">${m.tenGoiTap}</span>
          </td>
          <td class="py-3 px-4 text-slate-600">
            ${m.ngayHetHan ? m.ngayHetHan.split('-').reverse().join('/') : '--/--/----'}
          </td>
          <td class="py-3 px-4">
            ${badgeHtml}
          </td>
          <td class="py-3 px-4 text-right">
            <div class="flex items-center justify-end gap-1.5">
              <button class="btn-renew-pack px-2.5 py-1 rounded-lg bg-sky-50 text-sky-700 hover:bg-sky-100 font-semibold text-[11px] flex items-center gap-1 transition"
                data-id="${m.id}" title="Đăng ký hoặc gia hạn gói tập">
                <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
                <span>Gói</span>
              </button>
              <button class="btn-quick-checkin px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-semibold text-[11px] flex items-center gap-1 transition"
                data-id="${m.id}" title="Check-in ngay">
                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                <span>Check-in</span>
              </button>
              <button class="btn-edit-member p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition"
                data-id="${m.id}" title="Chỉnh sửa thông tin">
                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
              </button>
            </div>
          </td>
        </tr>
      `;
    }).join('');

    attachMemberEvents();
    safeCreateIcons();
  }

  function attachMemberEvents() {
    document.querySelectorAll('.btn-renew-pack').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        openRegisterPackageModal(id);
      });
    });

    document.querySelectorAll('.btn-quick-checkin').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        try {
          const record = gymService.thucHienCheckIn(id);
          if (record.hopLe) {
            showToast(`Check-in thành công cho ${record.tenHoiVien}!`);
          } else {
            showToast(`Từ chối check-in: Gói của ${record.tenHoiVien} đã hết hạn!`, 'error');
          }
          renderMembers();
        } catch (e) {
          showToast(e.message, 'error');
        }
      });
    });

    document.querySelectorAll('.btn-edit-member').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        const m = gymService.hoiVienList.find(item => item.id === id);
        if (m) {
          document.getElementById('modal-member-title').textContent = 'Chỉnh Sửa Hội Viên';
          document.getElementById('member-form-id').value = m.id;
          document.getElementById('member-form-name').value = m.hoTen;
          document.getElementById('member-form-phone').value = m.soDienThoai;
          document.getElementById('member-form-gender').value = m.gioiTinh;
          document.getElementById('member-form-email').value = m.email;
          openModal('modal-member');
        }
      });
    });
  }

  memberSearchInput.addEventListener('input', renderMembers);
  memberFilterStatus.addEventListener('change', renderMembers);

  document.getElementById('btn-add-member').addEventListener('click', () => {
    document.getElementById('modal-member-title').textContent = 'Thêm Hội Viên Mới';
    document.getElementById('form-member').reset();
    document.getElementById('member-form-id').value = '';
    openModal('modal-member');
  });

  document.getElementById('form-member').addEventListener('submit', (e) => {
    e.preventDefault();
    const id = document.getElementById('member-form-id').value;
    const name = document.getElementById('member-form-name').value.trim();
    const phone = document.getElementById('member-form-phone').value.trim();
    const gender = document.getElementById('member-form-gender').value;
    const email = document.getElementById('member-form-email').value.trim();

    if (id) {
      gymService.capNhatHoiVien(id, { hoTen: name, soDienThoai: phone, gioiTinh: gender, email });
      showToast('Cập nhật thông tin hội viên thành công!');
    } else {
      gymService.themHoiVien({ hoTen: name, soDienThoai: phone, gioiTinh: gender, email });
      showToast('Thêm hội viên mới thành công!');
    }

    closeModal('modal-member');
    renderMembers();
  });

  // ==========================================
  // 3. RENDER: CHECK-IN HỘI VIÊN (CẢ 2 VAI TRÒ)
  // ==========================================
  const checkinSearchInput = document.getElementById('checkin-search-input');
  const btnCheckinSearch = document.getElementById('btn-checkin-search');
  const checkinResultCard = document.getElementById('checkin-result-card');
  const checkinHistoryList = document.getElementById('checkin-history-list');
  const checkinLogCount = document.getElementById('checkin-log-count');

  function renderCheckinHistory() {
    const list = gymService.checkInList;
    checkinLogCount.textContent = `${list.length} lượt`;

    if (list.length === 0) {
      checkinHistoryList.innerHTML = `
        <div class="py-8 text-center text-slate-400 text-xs">
          Chưa có lượt check-in nào trong hôm nay
        </div>
      `;
      return;
    }

    checkinHistoryList.innerHTML = list.map(item => {
      const isHopLe = item.hopLe;
      return `
        <div class="py-3 flex items-center justify-between text-xs">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 ${isHopLe ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'}">
              <i data-lucide="${isHopLe ? 'check-circle' : 'x-circle'}" class="w-4 h-4"></i>
            </div>
            <div>
              <div class="font-bold text-slate-800">${item.tenHoiVien} <span class="font-normal text-slate-400 font-mono text-[11px]">(${item.maHoiVien})</span></div>
              <div class="text-[11px] text-slate-500">${item.ghiChu}</div>
            </div>
          </div>
          <div class="text-right">
            <div class="font-medium text-slate-700">${item.getThoiGianFormatted ? item.getThoiGianFormatted() : new Date(item.thoiGianCheckIn).toLocaleTimeString('vi-VN')}</div>
            <div class="text-[10px] text-slate-400">Duyệt bởi: ${item.tenNhanVienDuyet}</div>
          </div>
        </div>
      `;
    }).join('');

    safeCreateIcons();
  }

  function doCheckinLookup() {
    const query = checkinSearchInput.value.trim().toLowerCase();
    if (!query) {
      checkinResultCard.classList.add('hidden');
      return;
    }

    const member = gymService.hoiVienList.find(m => 
      m.soDienThoai === query || 
      m.maHoiVien.toLowerCase() === query || 
      m.hoTen.toLowerCase().includes(query)
    );

    if (!member) {
      checkinResultCard.className = 'border border-rose-200 bg-rose-50 rounded-xl p-4 text-xs text-rose-700';
      checkinResultCard.innerHTML = `
        <div class="flex items-center gap-2 font-bold mb-1">
          <i data-lucide="alert-circle" class="w-4 h-4"></i>
          <span>Không tìm thấy hội viên</span>
        </div>
        <p class="text-[11px]">Vui lòng kiểm tra lại số điện thoại hoặc mã thẻ hội viên.</p>
      `;
      checkinResultCard.classList.remove('hidden');
      safeCreateIcons();
      return;
    }

    const isConHan = member.isGoiConHan();
    const daysLeft = member.soNgayConLai();

    checkinResultCard.className = `border rounded-xl p-4 space-y-3 ${isConHan ? 'border-emerald-200 bg-emerald-50/50' : 'border-rose-200 bg-rose-50/50'}`;
    checkinResultCard.innerHTML = `
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <img src="${member.avatar}" class="w-10 h-10 rounded-full border border-slate-200" alt="${member.hoTen}">
          <div>
            <h4 class="font-bold text-slate-800 text-sm">${member.hoTen}</h4>
            <p class="text-[11px] text-slate-500 font-mono">${member.maHoiVien} • ${member.soDienThoai}</p>
          </div>
        </div>
        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold ${isConHan ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'}">
          ${isConHan ? `HỢP LỆ (Còn ${daysLeft} ngày)` : 'GÓI ĐÃ HẾT HẠN'}
        </span>
      </div>

      <div class="bg-white p-3 rounded-lg border border-slate-200 text-xs space-y-1">
        <div class="flex justify-between">
          <span class="text-slate-500">Gói tập:</span>
          <span class="font-bold text-slate-800">${member.tenGoiTap}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-500">Hạn sử dụng:</span>
          <span class="font-bold ${isConHan ? 'text-emerald-700' : 'text-rose-600'}">
            ${member.ngayHetHan ? member.ngayHetHan.split('-').reverse().join('/') : 'Chưa đăng ký'}
          </span>
        </div>
      </div>

      <div class="flex gap-2">
        <button id="btn-confirm-checkin" class="flex-1 py-2.5 rounded-xl font-bold text-xs text-white shadow-sm flex items-center justify-center gap-1.5 transition ${isConHan ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-slate-400 cursor-not-allowed'}"
          ${!isConHan ? 'disabled' : ''}>
          <i data-lucide="check" class="w-4 h-4"></i>
          <span>Xác Nhận Check-In</span>
        </button>
        ${!isConHan ? `
          <button id="btn-checkin-renew" class="px-3 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-bold text-xs flex items-center gap-1 shadow-sm transition">
            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
            <span>Gia Hạn Gói</span>
          </button>
        ` : ''}
      </div>
    `;

    checkinResultCard.classList.remove('hidden');
    safeCreateIcons();

    const btnConfirm = document.getElementById('btn-confirm-checkin');
    if (btnConfirm && isConHan) {
      btnConfirm.addEventListener('click', () => {
        try {
          const rec = gymService.thucHienCheckIn(member.id);
          showToast(`Check-in thành công cho ${member.hoTen}!`);
          renderCheckinHistory();
          checkinSearchInput.value = '';
          checkinResultCard.classList.add('hidden');
        } catch (e) {
          showToast(e.message, 'error');
        }
      });
    }

    const btnRenew = document.getElementById('btn-checkin-renew');
    if (btnRenew) {
      btnRenew.addEventListener('click', () => {
        openRegisterPackageModal(member.id);
      });
    }
  }

  btnCheckinSearch.addEventListener('click', doCheckinLookup);
  checkinSearchInput.addEventListener('keyup', (e) => {
    if (e.key === 'Enter') doCheckinLookup();
  });

  document.querySelectorAll('.checkin-sample-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      checkinSearchInput.value = btn.getAttribute('data-phone');
      doCheckinLookup();
    });
  });

  // ==========================================
  // 4. RENDER: QUẢN LÝ HÓA ĐƠN (CẢ 2 VAI TRÒ)
  // ==========================================
  const invoiceSearchInput = document.getElementById('invoice-search-input');
  const invoiceFilterMethod = document.getElementById('invoice-filter-method');
  const invoiceTableBody = document.getElementById('invoice-table-body');

  function renderInvoices() {
    const keyword = invoiceSearchInput.value.trim().toLowerCase();
    const method = invoiceFilterMethod.value;

    const invoices = gymService.hoaDonList.filter(hd => {
      const matchKeyword = !keyword || 
        hd.maHoaDon.toLowerCase().includes(keyword) || 
        hd.tenHoiVien.toLowerCase().includes(keyword) ||
        hd.tenGoiTap.toLowerCase().includes(keyword);

      const matchMethod = method === 'ALL' || hd.phuongThuc === method;
      return matchKeyword && matchMethod;
    });

    if (invoices.length === 0) {
      invoiceTableBody.innerHTML = `
        <tr>
          <td colspan="8" class="text-center py-8 text-slate-400">
            <i data-lucide="receipt" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
            Không có hóa đơn nào phù hợp
          </td>
        </tr>
      `;
      safeCreateIcons();
      return;
    }

    invoiceTableBody.innerHTML = invoices.map(hd => {
      const fmtMoney = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(hd.soTien);
      const fmtDate = new Date(hd.ngayTao).toLocaleDateString('vi-VN') + ' ' + new Date(hd.ngayTao).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
      
      const methodLabels = {
        TIEN_MAT: { text: 'Tiền mặt', color: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
        CHUYEN_KHOAN: { text: 'Chuyển khoản', color: 'bg-sky-50 text-sky-700 border-sky-200' },
        THE: { text: 'Thẻ POS', color: 'bg-indigo-50 text-indigo-700 border-indigo-200' }
      };
      const mInfo = methodLabels[hd.phuongThuc] || { text: hd.phuongThuc, color: 'bg-slate-100 text-slate-700' };

      return `
        <tr class="hover:bg-slate-50 transition-colors">
          <td class="py-3 px-4 font-mono font-bold text-slate-800">${hd.maHoaDon}</td>
          <td class="py-3 px-4 font-semibold text-slate-800">${hd.tenHoiVien}</td>
          <td class="py-3 px-4 text-slate-600">${hd.tenGoiTap}</td>
          <td class="py-3 px-4 font-bold text-slate-900">${fmtMoney}</td>
          <td class="py-3 px-4">
            <span class="px-2 py-0.5 rounded text-[10px] font-bold border ${mInfo.color}">${mInfo.text}</span>
          </td>
          <td class="py-3 px-4 text-slate-600">${hd.tenNhanVienThu}</td>
          <td class="py-3 px-4 text-slate-400 text-[11px]">${fmtDate}</td>
          <td class="py-3 px-4 text-right">
            <button class="btn-view-invoice px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px] transition flex items-center gap-1 ml-auto"
              data-id="${hd.id}">
              <i data-lucide="eye" class="w-3.5 h-3.5"></i>
              <span>Xem / In</span>
            </button>
          </td>
        </tr>
      `;
    }).join('');

    document.querySelectorAll('.btn-view-invoice').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        const hd = gymService.hoaDonList.find(h => h.id === id);
        if (hd) {
          document.getElementById('inv-modal-code').textContent = hd.maHoaDon;
          document.getElementById('inv-modal-date').textContent = new Date(hd.ngayTao).toLocaleString('vi-VN');
          document.getElementById('inv-modal-member').textContent = hd.tenHoiVien;
          document.getElementById('inv-modal-package').textContent = hd.tenGoiTap;
          document.getElementById('inv-modal-method').textContent = hd.getTenPhuongThuc ? hd.getTenPhuongThuc() : hd.phuongThuc;
          document.getElementById('inv-modal-staff').textContent = hd.tenNhanVienThu;
          document.getElementById('inv-modal-total').textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(hd.soTien);
          openModal('modal-invoice-detail');
        }
      });
    });

    safeCreateIcons();
  }

  invoiceSearchInput.addEventListener('input', renderInvoices);
  invoiceFilterMethod.addEventListener('change', renderInvoices);

  document.getElementById('btn-create-invoice').addEventListener('click', () => {
    openRegisterPackageModal();
  });

  document.getElementById('btn-print-invoice').addEventListener('click', () => {
    window.print();
  });

  // ==========================================
  // MODAL ĐĂNG KÝ / GIA HẠN GÓI TẬP
  // ==========================================
  const selectRegMember = document.getElementById('reg-form-member-id');
  const selectRegPackage = document.getElementById('reg-form-package-id');
  const regSummaryDuration = document.getElementById('reg-summary-duration');
  const regSummaryPrice = document.getElementById('reg-summary-price');

  function openRegisterPackageModal(selectedMemberId = null) {
    selectRegMember.innerHTML = gymService.hoiVienList.map(m => `
      <option value="${m.id}" ${m.id === selectedMemberId ? 'selected' : ''}>
        ${m.hoTen} (${m.maHoiVien} - ${m.soDienThoai})
      </option>
    `).join('');

    const activePackages = gymService.goiTapList.filter(g => g.isDangKinhDoanh());
    selectRegPackage.innerHTML = activePackages.map(g => `
      <option value="${g.id}">
        ${g.tenGoi} - ${g.getGiaFormatted()} (${g.thoiHanThang} tháng)
      </option>
    `).join('');

    updateRegisterSummary();
    openModal('modal-register-package');
  }

  function updateRegisterSummary() {
    const pkgId = selectRegPackage.value;
    const pkg = gymService.goiTapList.find(g => g.id === pkgId);
    if (pkg) {
      regSummaryDuration.textContent = `${pkg.thoiHanThang} tháng`;
      regSummaryPrice.textContent = pkg.getGiaFormatted();
    }
  }

  selectRegPackage.addEventListener('change', updateRegisterSummary);

  document.getElementById('btn-quick-register').addEventListener('click', () => {
    openRegisterPackageModal();
  });

  document.getElementById('form-register-package').addEventListener('submit', (e) => {
    e.preventDefault();
    const memberId = selectRegMember.value;
    const packageId = selectRegPackage.value;
    const methodInput = document.querySelector('input[name="payment-method"]:checked');
    const paymentMethod = methodInput ? methodInput.value : 'TIEN_MAT';

    try {
      const { hoiVien, hoaDon } = gymService.dangKyGoiTap(memberId, packageId, paymentMethod);
      showToast(`Đã gia hạn thành công gói ${hoaDon.tenGoiTap} cho ${hoiVien.hoTen}!`);
      closeModal('modal-register-package');
      renderCurrentTab();
    } catch (err) {
      showToast(err.message, 'error');
    }
  });

  // ==========================================
  // 5. RENDER: QUẢN LÝ NHÂN VIÊN (CHỈ QUẢN LÝ)
  // ==========================================
  const staffTableBody = document.getElementById('staff-table-body');

  function renderStaff() {
    if (!gymService.currentUser || gymService.currentUser.vaiTro !== 'MANAGER') return;

    staffTableBody.innerHTML = gymService.nhanVienList.map(nv => {
      const isMgr = nv.vaiTro === 'MANAGER';
      const fmtSalary = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(nv.luongCoBan);

      return `
        <tr class="hover:bg-slate-50 transition-colors">
          <td class="py-3 px-4">
            <div class="font-bold text-slate-800">${nv.hoTen}</div>
            <div class="text-[11px] text-slate-400 font-mono">${nv.maNhanVien} • ${nv.soDienThoai}</div>
          </td>
          <td class="py-3 px-4">
            <span class="px-2 py-0.5 rounded text-[10px] font-bold border ${isMgr ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200'}">
              ${isMgr ? 'Quản Lý' : 'Nhân Viên'}
            </span>
          </td>
          <td class="py-3 px-4 text-slate-700">${nv.caLamViec}</td>
          <td class="py-3 px-4 font-semibold text-slate-900">${fmtSalary}</td>
          <td class="py-3 px-4 text-slate-500">${nv.ngayVaoLam}</td>
          <td class="py-3 px-4">
            <span class="px-2 py-0.5 rounded text-[10px] font-bold ${nv.trangThai === 'ACTIVE' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400'}">
              ${nv.trangThai === 'ACTIVE' ? 'Đang làm việc' : 'Tạm nghỉ'}
            </span>
          </td>
          <td class="py-3 px-4 text-right">
            ${!isMgr ? `
              <div class="flex items-center justify-end gap-1">
                <button class="btn-toggle-staff-status text-[11px] px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 font-semibold text-slate-600 transition"
                  data-id="${nv.id}" data-status="${nv.trangThai === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE'}">
                  ${nv.trangThai === 'ACTIVE' ? 'Khóa' : 'Mở'}
                </button>
                <button class="btn-edit-staff p-1 text-slate-400 hover:text-slate-600 rounded" data-id="${nv.id}">
                  <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                </button>
              </div>
            ` : '<span class="text-[11px] text-slate-400 italic">Quản trị viên</span>'}
          </td>
        </tr>
      `;
    }).join('');

    document.querySelectorAll('.btn-toggle-staff-status').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        const st = btn.getAttribute('data-status');
        gymService.doiTrangThaiNhanVien(id, st);
        showToast('Đã cập nhật trạng thái nhân viên');
        renderStaff();
      });
    });

    document.querySelectorAll('.btn-edit-staff').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        const nv = gymService.nhanVienList.find(n => n.id === id);
        if (nv) {
          document.getElementById('modal-staff-title').textContent = 'Chỉnh Sửa Nhân Viên';
          document.getElementById('staff-form-id').value = nv.id;
          document.getElementById('staff-form-name').value = nv.hoTen;
          document.getElementById('staff-form-phone').value = nv.soDienThoai;
          document.getElementById('staff-form-username').value = nv.username;
          document.getElementById('staff-form-shift').value = nv.caLamViec;
          document.getElementById('staff-form-salary').value = nv.luongCoBan;
          openModal('modal-staff');
        }
      });
    });

    safeCreateIcons();
  }

  document.getElementById('btn-add-staff').addEventListener('click', () => {
    document.getElementById('modal-staff-title').textContent = 'Thêm Nhân Viên Mới';
    document.getElementById('form-staff').reset();
    document.getElementById('staff-form-id').value = '';
    openModal('modal-staff');
  });

  document.getElementById('form-staff').addEventListener('submit', (e) => {
    e.preventDefault();
    const id = document.getElementById('staff-form-id').value;
    const name = document.getElementById('staff-form-name').value.trim();
    const phone = document.getElementById('staff-form-phone').value.trim();
    const username = document.getElementById('staff-form-username').value.trim() || phone;
    const shift = document.getElementById('staff-form-shift').value;
    const salary = Number(document.getElementById('staff-form-salary').value) || 7500000;

    if (id) {
      gymService.capNhatNhanVien(id, { hoTen: name, soDienThoai: phone, username, caLamViec: shift, luongCoBan: salary });
      showToast('Cập nhật thông tin nhân viên thành công!');
    } else {
      gymService.themNhanVien({ hoTen: name, soDienThoai: phone, username, caLamViec: shift, luongCoBan: salary });
      showToast('Thêm nhân viên mới thành công!');
    }

    closeModal('modal-staff');
    renderStaff();
  });

  // ==========================================
  // 6. RENDER: QUẢN LÝ GÓI TẬP (CHỈ QUẢN LÝ)
  // ==========================================
  const packagesCardGrid = document.getElementById('packages-card-grid');

  function renderPackages() {
    if (!gymService.currentUser || gymService.currentUser.vaiTro !== 'MANAGER') return;

    packagesCardGrid.innerHTML = gymService.goiTapList.map(g => {
      const isAct = g.isDangKinhDoanh();
      return `
        <div class="bg-white rounded-2xl border ${isAct ? 'border-slate-200 shadow-sm' : 'border-dashed border-slate-300 opacity-70'} p-5 flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between mb-2">
              <span class="font-mono text-[10px] font-bold text-slate-400">${g.maGoi}</span>
              <span class="px-2 py-0.5 rounded text-[10px] font-bold ${isAct ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500'}">
                ${isAct ? 'Đang mở bán' : 'Tạm dừng'}
              </span>
            </div>
            <h3 class="font-bold text-slate-800 text-sm mb-1">${g.tenGoi}</h3>
            <p class="text-xs text-slate-500 mb-3">${g.moTa}</p>
            <div class="text-xl font-extrabold text-sky-600 mb-4">
              ${g.getGiaFormatted()} <span class="text-xs text-slate-400 font-normal">/ ${g.thoiHanThang} tháng</span>
            </div>

            <!-- Quyền lợi -->
            <div class="space-y-1.5 border-t border-slate-100 pt-3 mb-4 text-xs">
              <div class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Quyền lợi bao gồm:</div>
              ${g.quyenLoi.map(q => `
                <div class="flex items-center gap-2 text-slate-600">
                  <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-500 shrink-0"></i>
                  <span>${q}</span>
                </div>
              `).join('')}
            </div>
          </div>

          <div class="border-t border-slate-100 pt-3 flex items-center justify-between text-xs">
            <button class="btn-toggle-pack font-semibold ${isAct ? 'text-amber-600 hover:text-amber-700' : 'text-emerald-600 hover:text-emerald-700'}"
              data-id="${g.id}" data-action="${isAct ? 'pause' : 'activate'}">
              ${isAct ? 'Tạm dừng bán' : 'Kích hoạt lại'}
            </button>
            <button class="btn-edit-pack font-semibold text-sky-600 hover:text-sky-700" data-id="${g.id}">
              Chỉnh sửa
            </button>
          </div>
        </div>
      `;
    }).join('');

    document.querySelectorAll('.btn-toggle-pack').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        const action = btn.getAttribute('data-action');
        gymService.thayDoiTrangThaiGoi(id, action === 'activate');
        showToast('Đã cập nhật trạng thái gói tập');
        renderPackages();
      });
    });

    document.querySelectorAll('.btn-edit-pack').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        const g = gymService.goiTapList.find(item => item.id === id);
        if (g) {
          document.getElementById('modal-package-title').textContent = 'Chỉnh Sửa Gói Tập';
          document.getElementById('package-form-id').value = g.id;
          document.getElementById('package-form-name').value = g.tenGoi;
          document.getElementById('package-form-duration').value = g.thoiHanThang;
          document.getElementById('package-form-price').value = g.giaTien;
          document.getElementById('package-form-desc').value = g.moTa;
          document.getElementById('package-form-perks').value = g.quyenLoi.join('\n');
          openModal('modal-package');
        }
      });
    });

    safeCreateIcons();
  }

  document.getElementById('btn-add-package').addEventListener('click', () => {
    document.getElementById('modal-package-title').textContent = 'Tạo Gói Tập Mới';
    document.getElementById('form-package').reset();
    document.getElementById('package-form-id').value = '';
    openModal('modal-package');
  });

  document.getElementById('form-package').addEventListener('submit', (e) => {
    e.preventDefault();
    const id = document.getElementById('package-form-id').value;
    const name = document.getElementById('package-form-name').value.trim();
    const duration = Number(document.getElementById('package-form-duration').value) || 1;
    const price = Number(document.getElementById('package-form-price').value) || 0;
    const desc = document.getElementById('package-form-desc').value.trim();
    const perks = document.getElementById('package-form-perks').value.split('\n').filter(p => p.trim());

    if (id) {
      gymService.capNhatGoiTap(id, { tenGoi: name, thoiHanThang: duration, giaTien: price, moTa: desc, quyenLoi: perks });
      showToast('Cập nhật gói tập thành công!');
    } else {
      gymService.themGoiTap({ tenGoi: name, thoiHanThang: duration, giaTien: price, moTa: desc, quyenLoi: perks });
      showToast('Thêm gói tập mới thành công!');
    }

    closeModal('modal-package');
    renderPackages();
  });

  // ==========================================
  // KHÔI PHỤC DỮ LIỆU MẪU (RESET DEMO)
  // ==========================================
  document.getElementById('btn-reset-demo').addEventListener('click', () => {
    if (confirm('Bạn có chắc muốn đặt lại dữ liệu mẫu ban đầu? Toàn bộ thay đổi thử nghiệm sẽ được làm mới.')) {
      gymService.resetToDefault();
      showToast('Đã khôi phục dữ liệu mẫu gốc!');
      checkAuthState();
    }
  });

  // ==========================================
  // KHỞI ĐỘNG ỨNG DỤNG LẦN ĐẦU (KIỂM TRA LOGIN)
  // ==========================================
  checkAuthState();
});
