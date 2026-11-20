/**
 * LỚP CƠ SỞ: User (Người dùng hệ thống)
 * Đại diện cho tài khoản người dùng đăng nhập vào hệ thống
 */
class User {
  /**
   * @param {Object} data 
   * @param {string} data.id - Mã định danh duy nhất
   * @param {string} data.username - Tên đăng nhập
   * @param {string} data.hoTen - Họ và tên đầy đủ
   * @param {string} data.soDienThoai - Số điện thoại liên hệ
   * @param {string} data.email - Email liên hệ
   * @param {string} [data.matKhau='123456'] - Mật khẩu đăng nhập
   * @param {'STAFF' | 'MANAGER'} data.vaiTro - Quyền hạn trong hệ thống
   * @param {'ACTIVE' | 'INACTIVE'} [data.trangThai='ACTIVE'] - Trạng thái hoạt động
   */
  constructor({ id, username, matKhau = '123456', hoTen, soDienThoai, email, vaiTro, trangThai = 'ACTIVE' }) {
    this.id = id;
    this.username = username;
    this.matKhau = matKhau;
    this.hoTen = hoTen;
    this.soDienThoai = soDienThoai;
    this.email = email;
    this.vaiTro = vaiTro;
    this.trangThai = trangThai;
  }

  /**
   * Kiểm tra mật khẩu đăng nhập
   * @param {string} password 
   * @returns {boolean}
   */
  kiemTraMatKhau(password) {
    const cleanInput = (password || '').toString().trim();
    const cleanSaved = (this.matKhau || '').toString().trim();

    // Tài khoản mẫu luôn cho phép mật khẩu 123456
    const demoAccounts = ['admin', 'lananh', 'quocbao'];
    if (cleanInput === '123456' && demoAccounts.includes((this.username || '').toLowerCase())) {
      return true;
    }

    if (cleanSaved && cleanSaved === cleanInput) {
      return true;
    }

    // Nếu matKhau lưu trong model bị rỗng hoặc chưa set, fallback 123456
    if (!cleanSaved && cleanInput === '123456') {
      return true;
    }

    return false;
  }

  /**
   * Lấy thông tin tóm tắt của người dùng
   * @returns {Object}
   */
  getThongTin() {
    return {
      id: this.id,
      hoTen: this.hoTen,
      vaiTro: this.vaiTro,
      soDienThoai: this.soDienThoai,
      email: this.email,
      trangThai: this.trangThai
    };
  }

  /**
   * Kiểm tra quyền thao tác
   * @param {string} actionRole 
   * @returns {boolean}
   */
  kiemTraQuyen(actionRole) {
    if (this.vaiTro === 'MANAGER') return true; // Quản lý có mọi quyền
    return this.vaiTro === actionRole;
  }
}

// Hỗ trợ cả môi trường browser lẫn Node.js
if (typeof window !== 'undefined') {
  window.User = User;
}
if (typeof module !== 'undefined' && module.exports) {
  module.exports = User;
}
