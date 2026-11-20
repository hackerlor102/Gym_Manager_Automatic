/**
 * SERVICE: GymService (Quản lý trạng thái và dữ liệu phòng Gym)
 * Điều phối dữ liệu tập trung, khởi tạo các thực thể OOP từ mock data hoặc localStorage.
 */
class GymService {
  constructor() {
    this.STORAGE_KEY = 'GYM_MANAGEMENT_DATA_V1';
    this.SESSION_KEY = 'GYM_LOGGED_IN_USER_ID';
    this.hoiVienList = [];
    this.nhanVienList = [];
    this.goiTapList = [];
    this.hoaDonList = [];
    this.checkInList = [];
    this.currentUser = null;

    this.initData();
  }

  /**
   * Khởi tạo dữ liệu ban đầu
   */
  initData() {
    const saved = localStorage.getItem(this.STORAGE_KEY);
    if (saved) {
      try {
        const parsed = JSON.parse(saved);
        this.loadFromRaw(parsed);
        return;
      } catch (e) {
        console.warn('Lỗi đọc localStorage, sử dụng dữ liệu mặc định:', e);
      }
    }
    // Dùng dữ liệu mẫu
    this.loadFromRaw(window.MockData);
  }

  /**
   * Chuyển đổi dữ liệu thô thành các Object OOP tương ứng
   */
  loadFromRaw(raw) {
    // 1. Gói tập
    this.goiTapList = (raw.RAW_GOI_TAP || []).map(item => new GoiTap(item));

    // 2. Nhân viên & Quản lý
    this.nhanVienList = (raw.RAW_NHAN_VIEN || []).map(item => {
      if (item.vaiTro === 'MANAGER') {
        return new QuanLy(item);
      }
      return new NhanVien(item);
    });

    // 3. Hội viên
    this.hoiVienList = (raw.RAW_HOI_VIEN || []).map(item => new HoiVien(item));

    // 4. Hóa đơn
    this.hoaDonList = (raw.RAW_HOA_DON || []).map(item => new HoaDon(item));

    // 5. Check-in
    this.checkInList = (raw.RAW_CHECK_IN || []).map(item => new CheckIn(item));

    // Kiểm tra phiên đăng nhập đã lưu trong sessionStorage
    const savedUserId = sessionStorage.getItem(this.SESSION_KEY);
    if (savedUserId) {
      this.currentUser = this.nhanVienList.find(u => u.id === savedUserId && u.trangThai === 'ACTIVE') || null;
    } else {
      this.currentUser = null;
    }
  }

  /**
   * Đăng nhập hệ thống bằng username & password
   * @param {string} username 
   * @param {string} password 
   * @returns {User}
   */
  dangNhap(username, password) {
    const cleanUser = (username || '').toString().trim().toLowerCase();
    const cleanPass = (password || '').toString().trim();

    let user = this.nhanVienList.find(u => (u.username || '').toLowerCase() === cleanUser);
    
    // Nếu không tìm thấy và là tài khoản demo, nạp lại mock data để tự phục hồi
    if (!user && ['admin', 'lananh', 'quocbao'].includes(cleanUser)) {
      if (window.MockData && window.MockData.RAW_NHAN_VIEN) {
        this.nhanVienList = window.MockData.RAW_NHAN_VIEN.map(item => {
          if (item.vaiTro === 'MANAGER') return new QuanLy(item);
          return new NhanVien(item);
        });
        user = this.nhanVienList.find(u => (u.username || '').toLowerCase() === cleanUser);
      }
    }

    if (!user) {
      throw new Error('Tên đăng nhập không tồn tại');
    }
    if (user.trangThai !== 'ACTIVE') {
      throw new Error('Tài khoản này đã bị khóa hoặc tạm dừng');
    }
    if (!user.kiemTraMatKhau(cleanPass)) {
      throw new Error('Mật khẩu không chính xác');
    }
    this.currentUser = user;
    sessionStorage.setItem(this.SESSION_KEY, user.id);
    return user;
  }

  /**
   * Đăng xuất khỏi hệ thống
   */
  dangXuat() {
    this.currentUser = null;
    sessionStorage.removeItem(this.SESSION_KEY);
  }

  /**
   * Kiểm tra đã đăng nhập chưa
   * @returns {boolean}
   */
  isLoggedIn() {
    return this.currentUser !== null;
  }

  /**
   * Đăng nhập nhanh bằng vai trò (dành cho thử nghiệm demo)
   * @param {'MANAGER' | 'STAFF'} role 
   */
  loginDemoRole(role) {
    const user = this.nhanVienList.find(u => u.vaiTro === role && u.trangThai === 'ACTIVE');
    if (user) {
      this.currentUser = user;
      sessionStorage.setItem(this.SESSION_KEY, user.id);
    }
    return this.currentUser;
  }

  /**
   * Lưu trạng thái hiện tại vào localStorage
   */
  saveToStorage() {
    try {
      const raw = {
        RAW_GOI_TAP: this.goiTapList,
        RAW_NHAN_VIEN: this.nhanVienList,
        RAW_HOI_VIEN: this.hoiVienList,
        RAW_HOA_DON: this.hoaDonList,
        RAW_CHECK_IN: this.checkInList
      };
      localStorage.setItem(this.STORAGE_KEY, JSON.stringify(raw));
    } catch (e) {
      console.error('Không thể lưu localStorage:', e);
    }
  }

  /**
   * Đặt lại dữ liệu mẫu gốc
   */
  resetToDefault() {
    localStorage.removeItem(this.STORAGE_KEY);
    this.loadFromRaw(window.MockData);
  }

  /**
   * Chuyển đổi vai trò người dùng hiện tại (để test giao diện)
   * @param {'MANAGER' | 'STAFF'} role 
   */
  switchRole(role) {
    const user = this.nhanVienList.find(u => u.vaiTro === role);
    if (user) {
      this.currentUser = user;
    }
    return this.currentUser;
  }

  // ==========================================
  // CÁC THAO TÁC HỘI VIÊN (Nhân viên & Quản lý)
  // ==========================================
  
  timKiemHoiVien(keyword = '', status = 'ALL') {
    return this.hoiVienList.filter(hv => {
      const matchKeyword = !keyword || 
        hv.hoTen.toLowerCase().includes(keyword.toLowerCase()) ||
        hv.soDienThoai.includes(keyword) ||
        hv.maHoiVien.toLowerCase().includes(keyword.toLowerCase());

      const matchStatus = status === 'ALL' || hv.trangThai === status;
      return matchKeyword && matchStatus;
    });
  }

  themHoiVien(data) {
    const hv = this.currentUser.themHoiVien(this.hoiVienList, data);
    this.saveToStorage();
    return hv;
  }

  capNhatHoiVien(id, data) {
    const hv = this.hoiVienList.find(h => h.id === id);
    if (hv) {
      this.currentUser.capNhatHoiVien(hv, data);
      hv.trangThai = hv.tinhTrangThai();
      this.saveToStorage();
    }
    return hv;
  }

  dangKyGoiTap(hoiVienId, goiTapId, phuongThuc) {
    const hoiVien = this.hoiVienList.find(h => h.id === hoiVienId);
    const goiTap = this.goiTapList.find(g => g.id === goiTapId);
    if (!hoiVien || !goiTap) throw new Error('Không tìm thấy hội viên hoặc gói tập');

    const hoaDon = this.currentUser.dangKyGoiTap(hoiVien, goiTap, phuongThuc, this.hoaDonList);
    this.saveToStorage();
    return { hoiVien, hoaDon };
  }

  // ==========================================
  // CHECK-IN (Nhân viên & Quản lý)
  // ==========================================

  thucHienCheckIn(hoiVienId) {
    const hoiVien = this.hoiVienList.find(h => h.id === hoiVienId);
    if (!hoiVien) throw new Error('Hội viên không tồn tại');

    const record = this.currentUser.kiemTraCheckIn(hoiVien, this.checkInList);
    this.saveToStorage();
    return record;
  }

  // ==========================================
  // QUẢN LÝ NHÂN VIÊN (Chỉ Quản lý)
  // ==========================================

  themNhanVien(data) {
    if (this.currentUser.vaiTro !== 'MANAGER') {
      throw new Error('Bạn không có quyền thêm nhân viên');
    }
    const nv = this.currentUser.themNhanVien(this.nhanVienList, data);
    this.saveToStorage();
    return nv;
  }

  capNhatNhanVien(id, data) {
    if (this.currentUser.vaiTro !== 'MANAGER') {
      throw new Error('Bạn không có quyền sửa thông tin nhân viên');
    }
    const nv = this.nhanVienList.find(n => n.id === id);
    if (nv) {
      this.currentUser.capNhatNhanVien(nv, data);
      this.saveToStorage();
    }
    return nv;
  }

  doiTrangThaiNhanVien(id, trangThai) {
    if (this.currentUser.vaiTro !== 'MANAGER') {
      throw new Error('Bạn không có quyền thay đổi trạng thái nhân viên');
    }
    const nv = this.nhanVienList.find(n => n.id === id);
    if (nv) {
      this.currentUser.doiTrangThaiNhanVien(nv, trangThai);
      this.saveToStorage();
    }
    return nv;
  }

  // ==========================================
  // QUẢN LÝ GÓI TẬP (Chỉ Quản lý)
  // ==========================================

  themGoiTap(data) {
    if (this.currentUser.vaiTro !== 'MANAGER') {
      throw new Error('Bạn không có quyền thêm gói tập');
    }
    const gt = this.currentUser.themGoiTap(this.goiTapList, data);
    this.saveToStorage();
    return gt;
  }

  capNhatGoiTap(id, data) {
    if (this.currentUser.vaiTro !== 'MANAGER') {
      throw new Error('Bạn không có quyền sửa gói tập');
    }
    const gt = this.goiTapList.find(g => g.id === id);
    if (gt) {
      this.currentUser.capNhatGoiTap(gt, data);
      this.saveToStorage();
    }
    return gt;
  }

  thayDoiTrangThaiGoi(id, kichHoat) {
    if (this.currentUser.vaiTro !== 'MANAGER') {
      throw new Error('Bạn không có quyền đổi trạng thái gói tập');
    }
    const gt = this.goiTapList.find(g => g.id === id);
    if (gt) {
      this.currentUser.thayDoiTrangThaiGoi(gt, kichHoat);
      this.saveToStorage();
    }
    return gt;
  }

  // ==========================================
  // THỐNG KÊ DOANH THU (Chỉ Quản lý)
  // ==========================================

  layBaoCaoThongKe() {
    if (this.currentUser.vaiTro !== 'MANAGER') {
      throw new Error('Chỉ Quản lý mới có quyền xem thống kê doanh thu');
    }
    return this.currentUser.thongKeTongHop(this.hoaDonList, this.hoiVienList, this.checkInList);
  }
}

if (typeof window !== 'undefined') {
  window.GymService = GymService;
}
