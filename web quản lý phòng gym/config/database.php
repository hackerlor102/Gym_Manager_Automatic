<?php
/**
 * =========================================================================
 * FILE KẾT NỐI RIÊNG BIỆT VỚI DATABASE TRÊN XAMPP (MySQL / MariaDB)
 * =========================================================================
 * File: config/database.php
 * Chức năng:
 * - Khởi tạo kết nối PDO an toàn với MySQL trong XAMPP
 * - Tự động tạo Database & Import bảng nếu lần đầu chạy trên XAMPP
 * - Cung cấp đối tượng kết nối $pdo dùng chung cho toàn bộ dự án
 */

// Cấu hình kết nối mặc định của XAMPP
define('DB_HOST', '127.0.0.1');      // hoặc 'localhost'
define('DB_USER', 'root');           // Tài khoản mặc định của XAMPP là root
define('DB_PASS', '');               // Mật khẩu mặc định của XAMPP là để trống
define('DB_NAME', 'gym_management'); // Tên cơ sở dữ liệu
define('DB_PORT', '3306');           // Cổng MySQL mặc định

class Database {
    private static $instance = null;
    private $conn = null;

    private function __construct() {
        try {
            // Bước 1: Thử kết nối trực tiếp đến database
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Nếu lỗi là do chưa có Database (Error code 1049: Unknown database)
            // Tự động kết nối tới MySQL server để tạo Database & nạp file database.sql
            if ($e->getCode() == 1049 || strpos($e->getMessage(), 'Unknown database') !== false) {
                $this->autoInitDatabase();
            } else {
                $this->renderConnectionError($e->getMessage());
            }
        }
    }

    /**
     * Tự động khởi tạo CSDL và nạp dữ liệu mẫu từ database.sql
     */
    private function autoInitDatabase() {
        try {
            // Kết nối MySQL server không chọn database
            $serverDsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4";
            $serverConn = new PDO($serverDsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // Tạo database
            $serverConn->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

            // Kết nối lại vào database vừa tạo
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);

            // Đọc và thực thi file database.sql nếu có
            $sqlFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'database.sql';
            if (file_exists($sqlFile)) {
                $sqlContent = file_get_contents($sqlFile);
                if ($sqlContent) {
                    $this->conn->exec($sqlContent);
                }
            }
        } catch (PDOException $ex) {
            $this->renderConnectionError($ex->getMessage());
        }
    }

    /**
     * Hiển thị thông báo hướng dẫn đẹp mắt nếu kết nối XAMPP thất bại
     */
    private function renderConnectionError($errorDetails) {
        die("
        <!DOCTYPE html>
        <html lang='vi'>
        <head>
            <meta charset='UTF-8'>
            <title>Lỗi Kết Nối Cơ Sở Dữ Liệu XAMPP</title>
            <script src='https://cdn.tailwindcss.com'></script>
        </head>
        <body class='bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4 font-sans'>
            <div class='max-w-xl w-full bg-slate-800 border border-slate-700 rounded-2xl p-6 sm:p-8 shadow-2xl space-y-4'>
                <div class='flex items-center gap-3 text-rose-400'>
                    <svg class='w-8 h-8 shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'></path>
                    </svg>
                    <h2 class='text-xl font-bold text-white'>Chưa Thể Kết Nối Đến MySQL (XAMPP)</h2>
                </div>
                <p class='text-xs text-slate-300 leading-relaxed'>
                    Hệ thống không thể kết nối tới cơ sở dữ liệu MySQL tại <code>" . DB_HOST . ":" . DB_PORT . "</code>.
                </p>
                <div class='bg-slate-900/80 p-4 rounded-xl border border-slate-800 space-y-2 text-xs text-slate-300'>
                    <p class='font-bold text-sky-400 uppercase text-[11px] tracking-wider'>👉 Các bước khắc phục nhanh:</p>
                    <ol class='list-decimal list-inside space-y-1.5 text-slate-400'>
                        <li>Mở phần mềm <strong>XAMPP Control Panel</strong> trên máy tính của bạn.</li>
                        <li>Bấm nút <strong class='text-emerald-400'>Start</strong> tại cả 2 dòng <strong>Apache</strong> và <strong>MySQL</strong>.</li>
                        <li>Truy cập <a href='http://localhost/phpmyadmin' target='_blank' class='text-sky-400 underline'>http://localhost/phpmyadmin</a>, tạo database tên: <code>" . DB_NAME . "</code>.</li>
                        <li>Import file <code>database.sql</code> có sẵn trong thư mục dự án này.</li>
                        <li>Sau đó tải lại (F5) trang web này.</li>
                    </ol>
                </div>
                <div class='text-[11px] text-slate-500 font-mono break-all border-t border-slate-700/60 pt-3'>
                    Chi tiết lỗi kỹ thuật: " . htmlspecialchars($errorDetails) . "
                </div>
            </div>
        </body>
        </html>");
    }

    /**
     * Singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Lấy đối tượng kết nối PDO
     * @return PDO
     */
    public function getConnection() {
        return $this->conn;
    }
}

/**
 * Hàm helper toàn cục để truy xuất nhanh kết nối Database
 * @return PDO
 */
function getDB() {
    return Database::getInstance()->getConnection();
}
