/**
 * LỚP: HoaDon (Hóa đơn thanh toán gói tập)
 * Đại diện cho giao dịch thu tiền khi hội viên đăng ký hoặc gia hạn gói tập
 */
class HoaDon {
  /**
   * @param {Object} data 
   * @param {string} data.id
   * @param {string} [data.maHoaDon]
   * @param {string} data.hoiVienId
   * @param {string} data.tenHoiVien
   * @param {string} data.goiTapId
   * @param {string} data.tenGoiTap
   * @param {number} data.soTien
   * @param {string} [data.ngayTao]
   * @param {string} data.nhanVienThuId
   * @param {string} data.tenNhanVienThu
   * @param {'TIEN_MAT' | 'CHUYEN_KHOAN' | 'THE'} [data.phuongThuc='TIEN_MAT']
   * @param {string} [data.ghiChu='']
   */
  constructor(data) {
    this.id = data.id || Date.now().toString();
    this.maHoaDon = data.maHoaDon || `HD${String(this.id).slice(-6)}`;
    this.hoiVienId = data.hoiVienId;
    this.tenHoiVien = data.tenHoiVien;
    this.goiTapId = data.goiTapId;
    this.tenGoiTap = data.tenGoiTap;
    this.soTien = Number(data.soTien) || 0;
    this.ngayTao = data.ngayTao || new Date().toISOString();
    this.nhanVienThuId = data.nhanVienThuId;
    this.tenNhanVienThu = data.tenNhanVienThu;
    this.phuongThuc = data.phuongThuc || 'TIEN_MAT';
    this.ghiChu = data.ghiChu || '';
  }

  /**
   * Định dạng số tiền
   * @returns {string}
   */
  getSoTienFormatted() {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(this.soTien);
  }

  /**
   * Tên hiển thị của phương thức thanh toán
   * @returns {string}
   */
  getTenPhuongThuc() {
    const map = {
      TIEN_MAT: 'Tiền mặt',
      CHUYEN_KHOAN: 'Chuyển khoản ngân hàng',
      THE: 'Thẻ POS'
    };
    return map[this.phuongThuc] || this.phuongThuc;
  }
}

if (typeof window !== 'undefined') {
  window.HoaDon = HoaDon;
}
if (typeof module !== 'undefined' && module.exports) {
  module.exports = HoaDon;
}
