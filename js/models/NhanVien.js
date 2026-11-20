/**
 * LỚP: NhanVien (Nhân viên phòng gym)
 * Kế thừa từ User, chịu trách nhiệm vận hành hàng ngày:
 * - Quản lý hội viên
 * - Đăng ký gói tập cho hội viên
 * - Kiểm tra check-in hội viên
 * - Quản lý hóa đơn thu tiền
 */
class NhanVien extends User {
  /**
   * @param {Object} data 
   * @param {string} data.maNhanVien - Mã nhân viên (ví dụ: NV001)
   * @param {string} data.caLamViec - Ca trực (ví dụ: 'Ca Sáng (06:00 - 14:00)', 'Ca Chiều (14:00 - 22:00)')
   * @param {string} data.ngayVaoLam - Ngày bắt đầu làm việc
   * @param {number} [data.luongCoBan=0] - Mức lương cơ bản
   */
  constructor(data) {
    super({
      ...data,
      vaiTro: data.vaiTro || 'STAFF'
    });
    this.maNhanVien = data.maNhanVien || `NV${String(data.id || Date.now()).slice(-4)}`;
    this.caLamViec = data.caLamViec || 'Ca Sáng';
    this.ngayVaoLam = data.ngayVaoLam || new Date().toISOString().split('T')[0];
    this.luongCoBan = data.luongCoBan || 7000000;
  }

  /**
   * Thêm hội viên mới
   * @param {Array<HoiVien>} danhSachHoiVien 
   * @param {Object} thongTinHoiVien 
   * @returns {HoiVien}
   */
  themHoiVien(danhSachHoiVien, thongTinHoiVien) {
    const hoiVienMoi = new HoiVien(thongTinHoiVien);
    danhSachHoiVien.push(hoiVienMoi);
    return hoiVienMoi;
  }

  /**
   * Cập nhật thông tin hội viên
   * @param {HoiVien} hoiVien 
   * @param {Object} thongTinMoi 
   * @returns {HoiVien}
   */
  capNhatHoiVien(hoiVien, thongTinMoi) {
    Object.assign(hoiVien, thongTinMoi);
    return hoiVien;
  }

  /**
   * Đăng ký hoặc Gia hạn gói tập cho hội viên & Lập hóa đơn
   * @param {HoiVien} hoiVien 
   * @param {GoiTap} goiTap 
   * @param {'TIEN_MAT' | 'CHUYEN_KHOAN' | 'THE'} [phuongThuc='TIEN_MAT'] 
   * @param {Array<HoaDon>} danhSachHoaDon 
   * @returns {HoaDon}
   */
  dangKyGoiTap(hoiVien, goiTap, phuongThuc = 'TIEN_MAT', danhSachHoaDon = []) {
    // Gia hạn gói cho hội viên
    hoiVien.giaHanGoi(goiTap.thoiHanThang, goiTap.tenGoi, goiTap.id);

    // Tạo hóa đơn mới
    const hoaDon = new HoaDon({
      id: Date.now().toString(),
      maHoaDon: `HD${Date.now().toString().slice(-6)}`,
      hoiVienId: hoiVien.id,
      tenHoiVien: hoiVien.hoTen,
      goiTapId: goiTap.id,
      tenGoiTap: goiTap.tenGoi,
      soTien: goiTap.giaTien,
      ngayTao: new Date().toISOString(),
      nhanVienThuId: this.id,
      tenNhanVienThu: this.hoTen,
      phuongThuc: phuongThuc,
      ghiChu: `Đăng ký gói ${goiTap.tenGoi} (${goiTap.thoiHanThang} tháng)`
    });

    if (Array.isArray(danhSachHoaDon)) {
      danhSachHoaDon.unshift(hoaDon);
    }
    return hoaDon;
  }

  /**
   * Kiểm tra check-in cho hội viên vào phòng tập
   * @param {HoiVien} hoiVien 
   * @param {Array<CheckIn>} [danhSachCheckIn=[]]
   * @returns {CheckIn}
   */
  kiemTraCheckIn(hoiVien, danhSachCheckIn = []) {
    const isHopLe = hoiVien.isGoiConHan();
    const ghiChu = isHopLe 
      ? `Check-in thành công. Gói còn lại ${hoiVien.soNgayConLai()} ngày.` 
      : `Từ chối: Gói tập của hội viên đã hết hạn hoặc chưa đăng ký.`;

    const record = new CheckIn({
      id: Date.now().toString(),
      hoiVienId: hoiVien.id,
      maHoiVien: hoiVien.maHoiVien,
      tenHoiVien: hoiVien.hoTen,
      soDienThoai: hoiVien.soDienThoai,
      thoiGianCheckIn: new Date().toISOString(),
      nhanVienDuyetId: this.id,
      tenNhanVienDuyet: this.hoTen,
      hopLe: isHopLe,
      ghiChu: ghiChu
    });

    if (Array.isArray(danhSachCheckIn)) {
      danhSachCheckIn.unshift(record);
    }
    return record;
  }
}

if (typeof window !== 'undefined') {
  window.NhanVien = NhanVien;
}
if (typeof module !== 'undefined' && module.exports) {
  module.exports = NhanVien;
}
