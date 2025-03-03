<?php
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "okuplus";
    private $conn;

    // Veritabanı bağlantısını oluştur
    public function connect() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);
        if ($this->conn->connect_error) {
            die("Bağlantı başarısız: " . $this->conn->connect_error);
        }
    }

    // Veritabanı bağlantısını kapat
    public function close() {
        $this->conn->close();
    }

    // Veritabanına sorgu gönder
    public function query($sql) {
        return $this->conn->query($sql);
    }
}

class User {
    private $db;

    // Constructor ile veritabanı bağlantısını başlat
    public function __construct() {
        $this->db = new Database();
        $this->db->connect();
    }

    // Kullanıcı kaydı işlemi
    public function register($ad_soyad, $email, $telefon_no, $sifre, $sifre_tekrar) {
        if ($sifre !== $sifre_tekrar) {
            return "Şifreler uyuşmuyor!";
        }

        // Şifreyi güvenli bir şekilde saklamak için hashleme
        $hashed_sifre = password_hash($sifre, PASSWORD_DEFAULT);

        // SQL sorgusu
        $sql = "INSERT INTO kullanicilar (email, telefon_no, ad_soyad, sifre) 
                VALUES ('$email', '$telefon_no', '$ad_soyad', '$hashed_sifre')";

        if ($this->db->query($sql) === TRUE) {
            return "Kayıt başarılı! Giriş yapabilirsiniz.";
        } else {
            return "Hata: " . $this->db->query($sql)->error;
        }
    }

    // Kullanıcı giriş işlemi
    public function login($email, $sifre) {
        $sql = "SELECT * FROM kullanicilar WHERE email = '$email'";
        $result = $this->db->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Şifreyi doğrula
            if (password_verify($sifre, $row['sifre'])) {
                // Giriş başarılı, session başlat
                session_start();
                $_SESSION['user_id'] = $row['id'];

                // Kullanıcının admin olup olmadığını kontrol et
                if ($row['is_admin'] == 1) {
                    // Admin kullanıcı, admin paneline yönlendir
                    header("Location: http://localhost/kitapAdmin.php");
                    exit();
                } else {
                    // Normal kullanıcı, ana sayfaya yönlendir
                    header("Location: http://localhost/index.php");
                    exit();
                }
            } else {
                return "Hatalı şifre!";
            }
        } else {
            return "Hatalı email veya şifre!";
        }
    }

    // Veritabanı bağlantısını kapat
    public function __destruct() {
        $this->db->close();
    }
}

// Kullanıcı işlemleri
$user = new User();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['register'])) {
        // Kayıt işlemi
        $ad_soyad = $_POST['ad_soyad'];
        $email = $_POST['email'];
        $telefon_no = $_POST['telefon_no'];
        $sifre = $_POST['sifre'];
        $sifre_tekrar = $_POST['sifre_tekrar'];

        $message = $user->register($ad_soyad, $email, $telefon_no, $sifre, $sifre_tekrar);
    }

    if (isset($_POST['login'])) {
        // Giriş işlemi
        $email = $_POST['email'];
        $sifre = $_POST['sifre'];

        $result = $user->login($email, $sifre);
        if ($result === true) {
            // Giriş başarılı, yönlendirme yapılacak
            header("Location: http://localhost/index.php");
            exit();
        } else {
            $message = $result; // Hatalı giriş
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glassmorphism Giriş ve Kayıt</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            height: 100vh;
            overflow: hidden;
        }
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('./tasarım/arkaplan.jpg'); 
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            z-index: -1;
        }
        .form-container {
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            padding: 20px 40px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 350px;
            margin: auto;
            position: relative;
            top: 50%;
            transform: translateY(-50%);
        }
        h3 {
            margin-bottom: 20px;
            color: #333;
        }
        input[type="text"], input[type="password"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            width: 100%;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
        .links {
            margin-top: 15px;
        }
        .links a {
            color: #007BFF;
            text-decoration: none;
        }
        .links a:hover {
            text-decoration: underline;
        }
        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="background"></div>

<div class="form-container">
    <h3>Giriş Yapın</h3>
    <form action="" method="POST">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Email adresinizi girin" required>

        <label for="password">Şifre</label>
        <input type="password" id="password" name="sifre" placeholder="Şifrenizi girin" required>

        <button type="submit" name="login">Giriş Yap</button>
    </form>

    <!-- Giriş hatası mesajı -->
    <?php if (!empty($message)) { echo "<p class='error-message'>$message</p>"; } ?>

    <div class="links">
        <a href="UyeOlkitap.php">Hesabınız yok mu? Üye olun</a>
    </div>
</div>

</body>
</html>
