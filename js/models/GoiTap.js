/**
 * LỚP: GoiTap (Gói tập luyện)
 * Đại diện cho các gói dịch vụ phòng gym cung cấp (1 tháng, 3 tháng, VIP, PT...)
 */
class GoiTap {
  /**
   * @param {Object} data 
   * @param {string} data.id
   * @param {string} [data.maGoi]
   * @param {string} data.tenGoi - Ví dụ: 'Gói Cơ Bản (1 Tháng)', 'Gói VIP 1 Năm'
   * @param {number} data.thoiHanThang - Thời hạn tính theo tháng (1, 3, 6, 12,...)
   * @param {number} data.giaTien - Giá niêm yết (VNĐ)
   * @param {string} [data.moTa='']
   * @param {Array<string>} [data.quyenLoi=[]] - Danh sách quyền lợi kèm theo
   * @param {'ACTIVE' | 'DISABLED'} [data.trangThai='ACTIVE']
   */
  constructor(data) {
    this.id = data.id || Date.now().toString();
    this.maGoi = data.maGoi || `GT${String(this.id).slice(-4)}`;
    this.tenGoi = data.tenGoi;
    this.thoiHanThang = Number(data.thoiHanThang) || 1;
    this.giaTien = Number(data.giaTien) || 0;
    this.moTa = data.moTa || '';
    this.quyenLoi = data.quyenLoi || ['Sử dụng toàn bộ máy tập', 'Tủ đồ miễn phí', 'Tắm nóng lạnh'];
    this.trangThai = data.trangThai || 'ACTIVE';
  }

  /**
   * Định dạng giá tiền ra chuỗi tiền tệ VNĐ (ví dụ: 500,000 đ)
   * @returns {string}
   */
  getGiaFormatted() {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(this.giaTien);
  }

  /**
   * Kiểm tra gói có đang được kinh doanh không
   * @returns {boolean}
   */
  isDangKinhDoanh() {
    return this.trangThai === 'ACTIVE';
  }
}

if (typeof window !== 'undefined') {
  window.GoiTap = GoiTap;
}
if (typeof module !== 'undefined' && module.exports) {
  module.exports = GoiTap;
}
