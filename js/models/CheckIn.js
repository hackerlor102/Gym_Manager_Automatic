/**
 * LỚP: CheckIn (Lịch sử check-in vào phòng tập)
 * Ghi nhận mỗi lần hội viên đến điểm danh tập luyện
 */
class CheckIn {
  /**
   * @param {Object} data 
   * @param {string} data.id
   * @param {string} data.hoiVienId
   * @param {string} data.maHoiVien
   * @param {string} data.tenHoiVien
   * @param {string} data.soDienThoai
   * @param {string} [data.thoiGianCheckIn]
   * @param {string} data.nhanVienDuyetId
   * @param {string} data.tenNhanVienDuyet
   * @param {boolean} [data.hopLe=true]
   * @param {string} [data.ghiChu='']
   */
  constructor(data) {
    this.id = data.id || Date.now().toString();
    this.hoiVienId = data.hoiVienId;
    this.maHoiVien = data.maHoiVien;
    this.tenHoiVien = data.tenHoiVien;
    this.soDienThoai = data.soDienThoai;
    this.thoiGianCheckIn = data.thoiGianCheckIn || new Date().toISOString();
    this.nhanVienDuyetId = data.nhanVienDuyetId;
    this.tenNhanVienDuyet = data.tenNhanVienDuyet;
    this.hopLe = data.hopLe !== undefined ? data.hopLe : true;
    this.ghiChu = data.ghiChu || '';
  }

  /**
   * Định dạng thời gian check-in ra chuỗi dễ nhìn (HH:mm:ss DD/MM/YYYY)
   * @returns {string}
   */
  getThoiGianFormatted() {
    const d = new Date(this.thoiGianCheckIn);
    return d.toLocaleTimeString('vi-VN') + ' ' + d.toLocaleDateString('vi-VN');
  }
}

if (typeof window !== 'undefined') {
  window.CheckIn = CheckIn;
}
if (typeof module !== 'undefined' && module.exports) {
  module.exports = CheckIn;
}
