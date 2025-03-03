<?php
// Veritabanı bağlantısı
$host = "localhost";
$username = "root"; // Veritabanı kullanıcı adı
$password = ""; // Veritabanı şifresi
$dbname = "okuplus"; // Veritabanı adı

// Bağlantı oluşturuluyor
$conn = new mysqli($host, $username, $password, $dbname);

// Bağlantı kontrolü
if ($conn->connect_error) {
    die("Bağlantı başarısız: " . $conn->connect_error);
}

// Kullanıcı kaydı işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad_soyad = $_POST['ad_soyad'];
    $email = $_POST['email'];
    $telefon_no = $_POST['telefon_no'];
    $sifre = $_POST['sifre'];
    $sifre_tekrar = $_POST['sifre_tekrar'];

    // Şifrelerin uyuşup uyuşmadığını kontrol et
    if ($sifre !== $sifre_tekrar) {
        echo "Şifreler uyuşmuyor!";
    } else {
        // Şifreyi güvenli bir şekilde saklamak için hashleme
        $hashed_sifre = password_hash($sifre, PASSWORD_DEFAULT);

        // SQL sorgusu
        $sql = "INSERT INTO kullanicilar (email, telefon_no, ad_soyad, sifre) 
                VALUES ('$email', '$telefon_no', '$ad_soyad', '$hashed_sifre')";

        if ($conn->query($sql) === TRUE) {
            echo "Kayıt başarılı! Giriş yapabilirsiniz.";
            // Kayıttan sonra giriş sayfasına yönlendir
            header("Location: Giris.php");
            exit();
        } else {
            echo "Hata: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>

<!doctype html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Üye Ol</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

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
        background-image: url('./tasarım/arkaplan.jpg'); /* Arka plan resmi için doğru yolu verdik */
        background-size: cover;
        background-position: center;
        filter: blur(8px);
        z-index: -1;
    }

    .container {
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

    h1 {
        margin-bottom: 20px;
        color: #333;
    }

    .inputBox {
        margin-bottom: 15px;
        position: relative;
    }

    .inputBox input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
    }

    .inputBox label {
        position: absolute;
        top: 50%;
        left: 10px;
        transform: translateY(-50%);
        color: #666;
        pointer-events: none;
        font-size: 16px;
        transition: 0.3s;
    }

    .inputBox input:focus ~ label,
    .inputBox input:not(:placeholder-shown) ~ label {
        top: -10px;
        left: 10px;
        font-size: 12px;
        color: #4CAF50;
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

    .link {
        margin-top: 15px;
        display: block;
        color: #007BFF;
        text-decoration: none;
    }

    .link:hover {
        text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="background"></div>

  <div class="container">
    <h1>Üye Ol</h1>
    <form action="" method="POST">
      <div class="inputBox">
        <input type="text" name="ad_soyad" required> 
        <label>Ad Soyad</label>
      </div> 
      <div class="inputBox">
        <input type="email" name="email" required> 
        <label>Email</label>
      </div> 
      <div class="inputBox">
        <input type="text" name="telefon_no" required> 
        <label>Telefon Numarası</label>
      </div>
      <div class="inputBox">
        <input type="password" name="sifre" required> 
        <label>Şifre</label>
      </div>
      <div class="inputBox">
        <input type="password" name="sifre_tekrar" required> 
        <label>Şifre Tekrarı</label>
      </div> 
      <button type="submit">Kayıt Ol</button>
    </form>
  </div>

</body>
</html>
