/**
 * LỚP: HoiVien (Hội viên phòng gym)
 * Đại diện cho khách hàng đăng ký tập luyện tại phòng gym
 */
class HoiVien {
  /**
   * @param {Object} data 
   * @param {string} data.id
   * @param {string} [data.maHoiVien]
   * @param {string} data.hoTen
   * @param {string} data.soDienThoai
   * @param {string} [data.email='']
   * @param {'Nam' | 'Nữ' | 'Khác'} [data.gioiTinh='Nam']
   * @param {string} [data.ngayThamGia]
   * @param {string} [data.goiTapId=null]
   * @param {string} [data.tenGoiTap='Chưa có']
   * @param {string} [data.ngayHetHan=null]
   * @param {'ACTIVE' | 'EXPIRED' | 'PENDING'} [data.trangThai='PENDING']
   * @param {string} [data.avatar='']
   */
  constructor(data) {
    this.id = data.id || Date.now().toString();
    this.maHoiVien = data.maHoiVien || `HV${String(this.id).slice(-4)}`;
    this.hoTen = data.hoTen;
    this.soDienThoai = data.soDienThoai;
    this.email = data.email || '';
    this.gioiTinh = data.gioiTinh || 'Nam';
    this.ngayThamGia = data.ngayThamGia || new Date().toISOString().split('T')[0];
    this.goiTapId = data.goiTapId || null;
    this.tenGoiTap = data.tenGoiTap || 'Chưa đăng ký gói';
    this.ngayHetHan = data.ngayHetHan || null;
    this.avatar = data.avatar || `https://api.dicebear.com/7.x/initials/svg?seed=${encodeURIComponent(this.hoTen)}`;
    this.trangThai = data.trangThai || this.tinhTrangThai();
  }

  /**
   * Kiểm tra xem gói tập hiện tại còn hạn sử dụng hay không
   * @returns {boolean}
   */
  isGoiConHan() {
    if (!this.ngayHetHan) return false;
    const now = new Date();
    now.setHours(0, 0, 0, 0);
    const expire = new Date(this.ngayHetHan);
    expire.setHours(23, 59, 59, 999);
    return expire >= now;
  }

  /**
   * Số ngày còn lại của gói tập
   * @returns {number}
   */
  soNgayConLai() {
    if (!this.ngayHetHan) return 0;
    const now = new Date();
    now.setHours(0, 0, 0, 0);
    const expire = new Date(this.ngayHetHan);
    expire.setHours(0, 0, 0, 0);
    const diffTime = expire - now;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays > 0 ? diffDays : 0;
  }

  /**
   * Tự động tính toán trạng thái dựa trên ngày hết hạn
   * @returns {'ACTIVE' | 'EXPIRED' | 'PENDING'}
   */
  tinhTrangThai() {
    if (!this.ngayHetHan) return 'PENDING';
    return this.isGoiConHan() ? 'ACTIVE' : 'EXPIRED';
  }

  /**
   * Gia hạn hoặc Đăng ký gói tập mới
   * @param {number} thoiHanThang - Số tháng cộng thêm
   * @param {string} tenGoi - Tên gói tập mới
   * @param {string} goiTapId - Mã định danh gói tập
   */
  giaHanGoi(thoiHanThang, tenGoi, goiTapId) {
    const today = new Date();
    let baseDate = today;

    // Nếu gói hiện tại vẫn còn hạn thì cộng dồn từ ngày hết hạn cũ
    if (this.isGoiConHan() && this.ngayHetHan) {
      baseDate = new Date(this.ngayHetHan);
    }

    const newExpire = new Date(baseDate);
    newExpire.setMonth(newExpire.getMonth() + Number(thoiHanThang));

    this.goiTapId = goiTapId;
    this.tenGoiTap = tenGoi;
    this.ngayHetHan = newExpire.toISOString().split('T')[0];
    this.trangThai = 'ACTIVE';
  }
}

if (typeof window !== 'undefined') {
  window.HoiVien = HoiVien;
}
if (typeof module !== 'undefined' && module.exports) {
  module.exports = HoiVien;
}
