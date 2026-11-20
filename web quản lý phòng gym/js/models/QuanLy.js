/**
 * LỚP: QuanLy (Quản lý phòng gym)
 * Kế thừa từ NhanVien (và User).
 * Quản lý có toàn bộ quyền của Nhân viên cộng thêm:
 * - Quản lý nhân viên (Thêm, sửa, đổi ca, trạng thái)
 * - Quản lý danh mục gói tập (Thêm mới, sửa giá, quyền lợi, dừng bán)
 * - Thống kê doanh thu, gói tập, báo cáo hiệu suất
 */
class QuanLy extends NhanVien {
  /**
   * @param {Object} data 
   * @param {string} [data.phongBan='Ban Quản Lý'] - Bộ phận trực thuộc
   * @param {number} [data.phuCapQuanLy=3000000] - Phụ cấp quản lý
   */
  constructor(data) {
    super({
      ...data,
      vaiTro: 'MANAGER'
    });
    this.phongBan = data.phongBan || 'Ban Quản Lý';
    this.phuCapQuanLy = data.phuCapQuanLy || 3000000;
  }

  // ============================================
  // CÁC CHỨC NĂNG DÀNH RIÊNG CHO QUẢN LÝ
  // ============================================

  /**
   * Thêm nhân viên mới vào danh sách
   * @param {Array<NhanVien>} danhSachNhanVien 
   * @param {Object} thongTinNV 
   * @returns {NhanVien}
   */
  themNhanVien(danhSachNhanVien, thongTinNV) {
    const nvMoi = new NhanVien(thongTinNV);
    danhSachNhanVien.push(nvMoi);
    return nvMoi;
  }

  /**
   * Cập nhật thông tin nhân viên
   * @param {NhanVien} nhanVien 
   * @param {Object} thongTinMoi 
   * @returns {NhanVien}
   */
  capNhatNhanVien(nhanVien, thongTinMoi) {
    Object.assign(nhanVien, thongTinMoi);
    return nhanVien;
  }

  /**
   * Thay đổi trạng thái nhân viên (Nghỉ việc / Tạm khóa)
   * @param {NhanVien} nhanVien 
   * @param {'ACTIVE' | 'INACTIVE'} trangThaiMoi 
   */
  doiTrangThaiNhanVien(nhanVien, trangThaiMoi) {
    nhanVien.trangThai = trangThaiMoi;
    return nhanVien;
  }

  /**
   * Thêm gói tập mới
   * @param {Array<GoiTap>} danhSachGoiTap 
   * @param {Object} thongTinGoi 
   * @returns {GoiTap}
   */
  themGoiTap(danhSachGoiTap, thongTinGoi) {
    const goiMoi = new GoiTap(thongTinGoi);
    danhSachGoiTap.push(goiMoi);
    return goiMoi;
  }

  /**
   * Cập nhật thông tin gói tập
   * @param {GoiTap} goiTap 
   * @param {Object} thongTinMoi 
   * @returns {GoiTap}
   */
  capNhatGoiTap(goiTap, thongTinMoi) {
    Object.assign(goiTap, thongTinMoi);
    return goiTap;
  }

  /**
   * Bật hoặc tắt trạng thái kinh doanh của gói tập
   * @param {GoiTap} goiTap 
   * @param {boolean} kichHoat 
   */
  thayDoiTrangThaiGoi(goiTap, kichHoat) {
    goiTap.trangThai = kichHoat ? 'ACTIVE' : 'DISABLED';
    return goiTap;
  }

  /**
   * Thống kê tổng hợp doanh thu & số liệu kinh doanh
   * @param {Array<HoaDon>} danhSachHoaDon 
   * @param {Array<HoiVien>} danhSachHoiVien 
   * @param {Array<CheckIn>} danhSachCheckIn 
   * @returns {Object}
   */
  thongKeTongHop(danhSachHoaDon = [], danhSachHoiVien = [], danhSachCheckIn = []) {
    // 1. Tổng doanh thu
    const tongDoanhThu = danhSachHoaDon.reduce((sum, hd) => sum + (Number(hd.soTien) || 0), 0);

    // 2. Thống kê theo phương thức thanh toán
    const theoPhuongThuc = {
      TIEN_MAT: 0,
      CHUYEN_KHOAN: 0,
      THE: 0
    };
    danhSachHoaDon.forEach(hd => {
      if (theoPhuongThuc[hd.phuongThuc] !== undefined) {
        theoPhuongThuc[hd.phuongThuc] += Number(hd.soTien) || 0;
      }
    });

    // 3. Thống kê hội viên
    const tongHoiVien = danhSachHoiVien.length;
    const hoiVienConHan = danhSachHoiVien.filter(hv => hv.isGoiConHan()).length;
    const hoiVienHetHan = tongHoiVien - hoiVienConHan;

    // 4. Lượt check-in hôm nay
    const todayStr = new Date().toISOString().split('T')[0];
    const checkInHomNay = danhSachCheckIn.filter(c => c.thoiGianCheckIn.startsWith(todayStr)).length;

    // 5. Thống kê gói tập bán chạy
    const demGoiTap = {};
    danhSachHoaDon.forEach(hd => {
      const ten = hd.tenGoiTap || 'Khác';
      demGoiTap[ten] = (demGoiTap[ten] || 0) + 1;
    });

    return {
      tongDoanhThu,
      theoPhuongThuc,
      tongHoiVien,
      hoiVienConHan,
      hoiVienHetHan,
      checkInHomNay,
      demGoiTap,
      tongSoHoaDon: danhSachHoaDon.length
    };
  }
}

if (typeof window !== 'undefined') {
  window.QuanLy = QuanLy;
}
if (typeof module !== 'undefined' && module.exports) {
  module.exports = QuanLy;
}
