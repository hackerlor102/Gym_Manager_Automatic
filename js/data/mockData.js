/**
 * DỮ LIỆU MẪU BAN ĐẦU (MOCK DATA)
 * Giúp giao diện hoạt động tức thì mà không cần cài đặt Database
 */

const RAW_GOI_TAP = [
  {
    id: 'gt-1',
    maGoi: 'GT01',
    tenGoi: 'Gói Trải Nghiệm 1 Tháng',
    thoiHanThang: 1,
    giaTien: 450000,
    moTa: 'Phù hợp người mới bắt đầu tập thử, không ràng buộc lâu dài.',
    quyenLoi: ['Toàn bộ khu vực tạ & cardio', 'Tủ đồ miễn phí', 'Phòng tắm nóng lạnh'],
    trangThai: 'ACTIVE'
  },
  {
    id: 'gt-2',
    maGoi: 'GT02',
    tenGoi: 'Gói Tiêu Chuẩn 3 Tháng',
    thoiHanThang: 3,
    giaTien: 1200000,
    moTa: 'Tiết kiệm hơn, định hình thói quen tập luyện đều đặn.',
    quyenLoi: ['Toàn quyền tập gym & cardio', 'Tủ đồ miễn phí', 'Đo chỉ số InBody miễn phí 1 lần/tháng'],
    trangThai: 'ACTIVE'
  },
  {
    id: 'gt-3',
    maGoi: 'GT03',
    tenGoi: 'Gói Nâng Cao 6 Tháng',
    thoiHanThang: 6,
    giaTien: 2100000,
    moTa: 'Gói tập được yêu thích nhất, cam kết thay đổi vóc dáng.',
    quyenLoi: ['Full quyền gym, cardio', 'Đo InBody không giới hạn', 'Tặng 2 buổi hướng dẫn cùng HLV cá nhân'],
    trangThai: 'ACTIVE'
  },
  {
    id: 'gt-4',
    maGoi: 'GT04',
    tenGoi: 'Gói VIP Toàn Diện 1 Năm',
    thoiHanThang: 12,
    giaTien: 3600000,
    moTa: 'Tận hưởng trọn vẹn dịch vụ cao cấp nhất tại phòng tập.',
    quyenLoi: ['Toàn quyền phòng gym, yoga, xông hơi', 'Khăn tập & tủ đồ riêng', 'Tặng 5 buổi PT 1-1', 'Bảo lưu tối đa 30 ngày'],
    trangThai: 'ACTIVE'
  },
  {
    id: 'gt-5',
    maGoi: 'GT05',
    tenGoi: 'Gói PT Kèm Riêng 1-1',
    thoiHanThang: 2,
    giaTien: 6000000,
    moTa: '24 buổi tập chuyên sâu cùng Huấn Luyện Viên cá nhân.',
    quyenLoi: ['HLV thiết kế giáo án riêng', 'Lộ trình dinh dưỡng theo mục tiêu', 'Theo dõi sát sao từng buổi tập'],
    trangThai: 'ACTIVE'
  }
];

const RAW_NHAN_VIEN = [
  {
    id: 'usr-1',
    maNhanVien: 'QL001',
    username: 'admin',
    matKhau: '123456',
    hoTen: 'Nguyễn Hoàng Minh',
    soDienThoai: '0901234567',
    email: 'hoangminh.manager@gymfit.vn',
    vaiTro: 'MANAGER',
    caLamViec: 'Ca Hành Chính (08:00 - 17:00)',
    ngayVaoLam: '2023-01-15',
    luongCoBan: 15000000,
    trangThai: 'ACTIVE'
  },
  {
    id: 'usr-2',
    maNhanVien: 'NV001',
    username: 'lananh',
    matKhau: '123456',
    hoTen: 'Trần Thị Lan Anh',
    soDienThoai: '0912345678',
    email: 'lananh@gymfit.vn',
    vaiTro: 'STAFF',
    caLamViec: 'Ca Sáng (06:00 - 14:00)',
    ngayVaoLam: '2023-06-10',
    luongCoBan: 7500000,
    trangThai: 'ACTIVE'
  },
  {
    id: 'usr-3',
    maNhanVien: 'NV002',
    username: 'quocbao',
    matKhau: '123456',
    hoTen: 'Lê Quốc Bảo',
    soDienThoai: '0923456789',
    email: 'quocbao@gymfit.vn',
    vaiTro: 'STAFF',
    caLamViec: 'Ca Chiều - Tối (14:00 - 22:00)',
    ngayVaoLam: '2023-09-01',
    luongCoBan: 7500000,
    trangThai: 'ACTIVE'
  }
];

// Tạo ngày hết hạn động so với ngày hiện tại để dữ liệu luôn tươi mới
const getRelativeDate = (daysOffset) => {
  const d = new Date();
  d.setDate(d.getDate() + daysOffset);
  return d.toISOString().split('T')[0];
};

const RAW_HOI_VIEN = [
  {
    id: 'hv-1',
    maHoiVien: 'HV1001',
    hoTen: 'Phạm Đức Anh',
    soDienThoai: '0987654321',
    email: 'ducanh@gmail.com',
    gioiTinh: 'Nam',
    ngayThamGia: '2024-01-10',
    goiTapId: 'gt-4',
    tenGoiTap: 'Gói VIP Toàn Diện 1 Năm',
    ngayHetHan: getRelativeDate(180), // Còn 180 ngày
    trangThai: 'ACTIVE'
  },
  {
    id: 'hv-2',
    maHoiVien: 'HV1002',
    hoTen: 'Vũ Hải Yến',
    soDienThoai: '0978123456',
    email: 'haiyen.vu@gmail.com',
    gioiTinh: 'Nữ',
    ngayThamGia: '2024-02-15',
    goiTapId: 'gt-3',
    tenGoiTap: 'Gói Nâng Cao 6 Tháng',
    ngayHetHan: getRelativeDate(45), // Còn 45 ngày
    trangThai: 'ACTIVE'
  },
  {
    id: 'hv-3',
    maHoiVien: 'HV1003',
    hoTen: 'Đỗ Tuấn Kiệt',
    soDienThoai: '0965333444',
    email: 'tuankiet@yahoo.com',
    gioiTinh: 'Nam',
    ngayThamGia: '2024-03-01',
    goiTapId: 'gt-1',
    tenGoiTap: 'Gói Trải Nghiệm 1 Tháng',
    ngayHetHan: getRelativeDate(3), // Sắp hết hạn (còn 3 ngày)
    trangThai: 'ACTIVE'
  },
  {
    id: 'hv-4',
    maHoiVien: 'HV1004',
    hoTen: 'Ngô Thu Thảo',
    soDienThoai: '0943999888',
    email: 'thuthao.ngo@gmail.com',
    gioiTinh: 'Nữ',
    ngayThamGia: '2023-11-20',
    goiTapId: 'gt-2',
    tenGoiTap: 'Gói Tiêu Chuẩn 3 Tháng',
    ngayHetHan: getRelativeDate(-10), // Đã hết hạn 10 ngày trước
    trangThai: 'EXPIRED'
  },
  {
    id: 'hv-5',
    maHoiVien: 'HV1005',
    hoTen: 'Trương Gia Huy',
    soDienThoai: '0918777666',
    email: 'giahuy@outlook.com',
    gioiTinh: 'Nam',
    ngayThamGia: '2024-04-01',
    goiTapId: null,
    tenGoiTap: 'Chưa đăng ký gói',
    ngayHetHan: null,
    trangThai: 'PENDING'
  }
];

const RAW_HOA_DON = [
  {
    id: 'hd-1',
    maHoaDon: 'HD892301',
    hoiVienId: 'hv-1',
    tenHoiVien: 'Phạm Đức Anh',
    goiTapId: 'gt-4',
    tenGoiTap: 'Gói VIP Toàn Diện 1 Năm',
    soTien: 3600000,
    ngayTao: new Date(Date.now() - 5 * 86400000).toISOString(),
    nhanVienThuId: 'usr-2',
    tenNhanVienThu: 'Trần Thị Lan Anh',
    phuongThuc: 'CHUYEN_KHOAN',
    ghiChu: 'Thanh toán qua quét mã QR ngân hàng'
  },
  {
    id: 'hd-2',
    maHoaDon: 'HD892302',
    hoiVienId: 'hv-2',
    tenHoiVien: 'Vũ Hải Yến',
    goiTapId: 'gt-3',
    tenGoiTap: 'Gói Nâng Cao 6 Tháng',
    soTien: 2100000,
    ngayTao: new Date(Date.now() - 3 * 86400000).toISOString(),
    nhanVienThuId: 'usr-3',
    tenNhanVienThu: 'Lê Quốc Bảo',
    phuongThuc: 'THE',
    ghiChu: 'Quẹt thẻ máy POS'
  },
  {
    id: 'hd-3',
    maHoaDon: 'HD892303',
    hoiVienId: 'hv-3',
    tenHoiVien: 'Đỗ Tuấn Kiệt',
    goiTapId: 'gt-1',
    tenGoiTap: 'Gói Trải Nghiệm 1 Tháng',
    soTien: 450000,
    ngayTao: new Date(Date.now() - 1 * 86400000).toISOString(),
    nhanVienThuId: 'usr-2',
    tenNhanVienThu: 'Trần Thị Lan Anh',
    phuongThuc: 'TIEN_MAT',
    ghiChu: 'Thu tiền mặt tại quầy lễ tân'
  }
];

const RAW_CHECK_IN = [
  {
    id: 'ck-1',
    hoiVienId: 'hv-1',
    maHoiVien: 'HV1001',
    tenHoiVien: 'Phạm Đức Anh',
    soDienThoai: '0987654321',
    thoiGianCheckIn: new Date(Date.now() - 3600000 * 2).toISOString(),
    nhanVienDuyetId: 'usr-2',
    tenNhanVienDuyet: 'Trần Thị Lan Anh',
    hopLe: true,
    ghiChu: 'Check-in thành công. Gói VIP còn 180 ngày.'
  },
  {
    id: 'ck-2',
    hoiVienId: 'hv-2',
    maHoiVien: 'HV1002',
    tenHoiVien: 'Vũ Hải Yến',
    soDienThoai: '0978123456',
    thoiGianCheckIn: new Date(Date.now() - 3600000 * 4).toISOString(),
    nhanVienDuyetId: 'usr-2',
    tenNhanVienDuyet: 'Trần Thị Lan Anh',
    hopLe: true,
    ghiChu: 'Check-in thành công. Gói 6 tháng còn 45 ngày.'
  }
];

if (typeof window !== 'undefined') {
  window.MockData = {
    RAW_GOI_TAP,
    RAW_NHAN_VIEN,
    RAW_HOI_VIEN,
    RAW_HOA_DON,
    RAW_CHECK_IN
  };
}
