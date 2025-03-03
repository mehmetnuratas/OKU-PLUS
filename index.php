<?php
// Veritabanı Bağlantı Sınıfı
class Database {
    private $conn;

    // Constructor: Veritabanı bağlantısını başlatır
    public function __construct($servername, $username, $password, $dbname) {
        // MySQLi ile veritabanına bağlanıyoruz
        $this->conn = new mysqli($servername, $username, $password, $dbname);

        // Bağlantı hatası varsa işlem sonlandırılır
        if ($this->conn->connect_error) {
            die("Bağlantı hatası: " . $this->conn->connect_error);
        }
    }

    // Sorgu çalıştırır ve sonucu döner
    public function query($sql) {
        return $this->conn->query($sql);
    }

    // Veritabanı bağlantısını kapatır
    public function close() {
        $this->conn->close();
    }

    // Hazırlanmış ifadeyle veri sorgular
    public function prepareQuery($sql) {
        return $this->conn->prepare($sql);
    }
}

// Kitap Sınıfı
class Kitap {
    private $id;
    private $adi;
    private $yazar;
    private $fiyat;
    private $indirimOrani;
    private $kapakResmi;
    private $kategoriId;

    // Constructor: Kitap bilgilerini alır ve nesne oluşturur
    public function __construct($id, $adi, $yazar, $fiyat, $indirimOrani, $kapakResmi, $kategoriId) {
        $this->id = $id;
        $this->adi = $adi;
        $this->yazar = $yazar;
        $this->fiyat = $fiyat;
        $this->indirimOrani = $indirimOrani;
        $this->kapakResmi = $kapakResmi;
        $this->kategoriId = $kategoriId;
    }

    // İndirimli fiyatı hesaplar
    public function getIndirimliFiyat() {
        return $this->fiyat - ($this->fiyat * $this->indirimOrani / 100);
    }

    // Kitap kartını render eder (HTML çıktısı oluşturur)
    public function renderCard() {
        return '
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-img-wrapper">
                    <img src="' . htmlspecialchars($this->kapakResmi) . '" class="card-img-top" alt="' . htmlspecialchars($this->adi) . '">
                </div>
                <div class="card-body">
                    <h5 class="card-title">' . htmlspecialchars($this->adi) . '</h5>
                    <p class="card-text"><strong>Yazar:</strong> ' . htmlspecialchars($this->yazar) . '</p>
                    <p class="card-text"><strong>Fiyat:</strong> ₺' . number_format($this->getIndirimliFiyat(), 2) . '</p>
                    <button onclick="sepeteEkle(' . $this->id . ', \'' . addslashes($this->adi) . '\', ' . $this->getIndirimliFiyat() . ')" class="btn btn-success w-100">Sepete Ekle</button>
                    <a href="kitap_detay.php?kitap_id=' . $this->id . '" class="btn btn-primary w-100 mt-2">Devamını Oku</a>
                </div>
            </div>
        </div>';
    }

    // Kategori ID'sini döner
    public function getKategoriId() {
        return $this->kategoriId;
    }
}

// Kitap Yönetimi Sınıfı
class KitapManager {
    private $db;

    // Constructor: Veritabanı bağlantısı alır
    public function __construct($db) {
        $this->db = $db;
    }

    // Kategorilere göre kitapları gruplar
    public function getKitaplarByKategori() {
        $kitaplar = [];
        
        // "kitaplar" tablosundan tüm kitapları alır
        $result = $this->db->query("SELECT * FROM kitaplar");

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Her bir kitap için yeni bir Kitap nesnesi oluşturulur
                $kitap = new Kitap(
                    $row['kitap_id'], 
                    $row['kitap_adi'], 
                    $row['yazar'], 
                    $row['fiyat'], 
                    $row['indirim_orani'], 
                    $row['kapak_resmi'],
                    $row['kategori_id']
                );

                // Kitapları kategoriye göre gruplar
                $kategoriId = $kitap->getKategoriId();
                if (!isset($kitaplar[$kategoriId])) {
                    $kitaplar[$kategoriId] = [];
                }
                $kitaplar[$kategoriId][] = $kitap;
            }
        }

        return $kitaplar;
    }
}

// Kullanıcı sınıfı
class User {
    private $db;

    // Constructor: Veritabanı bağlantısı oluşturur
    public function __construct($servername = "localhost", $username = "root", $password = "", $dbname = "okuplus") {
        $this->db = new Database($servername, $username, $password, $dbname);
    }

    // Kullanıcı ID'sine göre kullanıcı bilgilerini döner
    public function getUserById($id) {
        $sql = "SELECT * FROM kullanicilar WHERE id = ?";
        $stmt = $this->db->prepareQuery($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    // Kullanıcı profilini günceller
    public function updateProfile($id, $ad_soyad, $email, $telefon_no, $sifre) {
        $hashed_sifre = password_hash($sifre, PASSWORD_DEFAULT);
        $sql = "UPDATE kullanicilar SET ad_soyad = ?, email = ?, telefon_no = ?, sifre = ? WHERE id = ?";
        $stmt = $this->db->prepareQuery($sql);
        $stmt->bind_param("ssssi", $ad_soyad, $email, $telefon_no, $hashed_sifre, $id);
        return $stmt->execute();
    }

    // Destructor: Veritabanı bağlantısını kapatır
    public function __destruct() {
        $this->db->close();
    }
}

// Oturum başlatılıyor
session_start();

// Kullanıcı çıkışı işlemi
if (isset($_POST['logout'])) {
    session_unset(); // Oturumdaki tüm verileri temizler
    session_destroy(); // Oturumu sonlandırır
    header("Location: " . $_SERVER['PHP_SELF']); // Sayfayı yeniden yükler
    exit();
}

// Giriş kontrolü ve kullanıcı bilgilerini alma
if (!isset($_SESSION['user_id'])) {
    $userInfo = [
        'ad_soyad' => '',
        'email' => '',
        'telefon_no' => ''
    ]; // Giriş yapmayan kullanıcı için boş bilgi
} else {
    // Kullanıcı bilgilerini almak için User sınıfını kullanıyoruz
    $user = new User("localhost", "root", "", "okuplus");
    $userId = $_SESSION['user_id'];
    $userInfo = $user->getUserById($userId); // Giriş yapan kullanıcı bilgilerini al
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update'])) {
        // Profil güncelleniyor
        $ad_soyad = $_POST['ad_soyad'];
        $email = $_POST['email'];
        $telefon_no = $_POST['telefon_no'];
        $sifre = $_POST['sifre'];

        if ($user->updateProfile($userId, $ad_soyad, $email, $telefon_no, $sifre)) {
            $message = "Profil başarıyla güncellendi.";
        } else {
            $message = "Hata: Profil güncellenemedi.";
        }
        // Profil güncelleme sonrası kullanıcı bilgilerini yeniden alıyoruz
        $userInfo = $user->getUserById($userId);
    }
}

// Veritabanı bağlantısı
$db = new Database("localhost", "root", "", "okuplus");
$kitapManager = new KitapManager($db);
$kitaplarByKategori = $kitapManager->getKitaplarByKategori();
?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitaplar | OkuPlus</title>
    <link rel="stylesheet" href="./assets/bootstrap/css/bootstrap.min.css">
    <style>
        /* Kategoriler arası yatay kaydırma */
        .kategori-scroll {
            display: flex;
            overflow-x: auto;
            gap: 15px;
            padding: 10px 0;
        }
        .kategori-scroll .col-md-3 {
            flex: 0 0 auto;
        }

        .navbar {
    background-color: #5c6bc0; /* Hafif mavi tonları */
    padding: 20px 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.navbar .title {
    font-size: 1.8rem;
    color: white;
    margin-right: 40px;
    text-decoration: none;
    font-weight: 600;
}

.navbar .menu .nav-link {
    color: white;
    margin: 0 15px;
    font-size: 1.1rem;
    transition: color 0.3s, transform 0.3s;
}

.navbar .menu .nav-link:hover, 
.navbar .menu .active .nav-link {
    color: #ffd700; /* Altın sarısı */
    transform: scale(1.1); /* Hoverda buton büyür */
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
    margin: 20px;
    background-color: #ffffff;
}

.card:hover {
    transform: translateY(-15px);
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.2);
}

.card-img-top {
    height: 450px;
    object-fit: cover;
    border-radius: 10px;
    margin: 10px;
    border: 3px solid #eeeeee; /* Daha soft bir çerçeve rengi */
}

.card-title {
    font-size: 1.4rem;
    color: #212529;
    margin-top: 15px;
    font-weight: 700;
}

.card-text {
    font-size: 1rem;
    color: #6c757d;
    line-height: 1.5;
    margin-bottom: 15px;
}

.btn {
    background-color: #5c6bc0;
    color: white;
    border-radius: 20px;
    padding: 10px 20px;
    font-size: 1.1rem;
    transition: background-color 0.3s;
}

.btn:hover {
    background-color: #3f51b5;
}

.offcanvas {
    background-color: #ffffff;
    color: #212529;
}

.offcanvas-header {
    background-color: #3f51b5;
    color: white;
    padding: 15px 25px;
}

footer {
    background-color: #5c6bc0;
    color: white;
    padding: 30px 0;
    text-align: center;
}

footer p {
    margin: 0;
    font-size: 1rem;
}
/* Slider görüntüsünün tam ekran genişliğine uymasını sağlar */
.carousel-item img {
    width: 100%; 
    height: 100%; 
    object-fit: cover; /* Görsellerin en/boy oranlarını bozmadan tam ekran görünmesini sağlar */
}

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #profileForm {
            display: none;
        }
    </style>
    <script>
        function toggleProfileForm() {
            var form = document.getElementById('profileForm');
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">OKU PLUS</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
               <?php if (isset($_SESSION['user_id'])): ?>
    <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#sepetCanvas" aria-controls="sepetCanvas">
        Sepetiniz
    </button>
<?php else: ?>
    <button class="btn btn-outline-secondary" type="button" onclick="window.location.href='login.php'" disabled>
        Giriş Yap
    </button>
<?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" href="seslikitap.php">Sesli Kitaplar</a>
                </li>
				
                <li class="nav-item">
                    <a class="nav-link" href="iletişim.php">İletişim</a>
                </li>
				
<!-- Profil düzenleme formu -->
<form id="profileForm" action="" method="POST">
    <label for="ad_soyad">Ad Soyad</label>
    <input type="text" id="ad_soyad" name="ad_soyad" value="<?= isset($userInfo['ad_soyad']) ? $userInfo['ad_soyad'] : '' ?>" required><br>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="<?= isset($userInfo['email']) ? $userInfo['email'] : '' ?>" required><br>

    <label for="telefon_no">Telefon Numarası</label>
    <input type="tel" id="telefon_no" name="telefon_no" value="<?= isset($userInfo['telefon_no']) ? $userInfo['telefon_no'] : '' ?>" required><br>

    <label for="sifre">Şifre</label>
    <input type="password" id="sifre" name="sifre" required><br>

    <button type="submit" name="update">Güncelle</button>
</form>

<!-- Çıkış yap butonu -->
<form action="" method="POST">
    <button type="submit" name="logout" class="btn btn-danger">Çıkış Yap</button>
</form>
  <a class="nav-link" href="giris.php">GİRİŞ YAP</a>
            </ul>
        </div>
    </div>
</nav>

<section class="slider">
    <div id="carouselbookslider" class="carousel slide">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="./tasarım/3.jpg" class="d-block w-100" alt="">
            </div>
            <div class="carousel-item">
                <img src="./tasarım/2.jpg" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
                <img src="./tasarım/4.jpg" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
                <img src="./tasarım/5.jpg" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
                <img src="./tasarım/6.jpg" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
                <img src="./tasarım/7.jpg" class="d-block w-100" alt="...">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselbookslider" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselbookslider" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>

<div class="container my-5">
    <?php foreach ($kitaplarByKategori as $kategoriId => $kitaplar): ?>
        <h3>Kategori ID: <?= $kategoriId ?></h3>
        <div class="kategori-scroll">
            <?php foreach ($kitaplar as $kitap): ?>
                <?= $kitap->renderCard(); ?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>

<div class="container my-5">
    <div class="row">
        <?php foreach ($kitaplarByKategori as $kitaplar): ?>
            <?php foreach ($kitaplar as $kitap): ?>
                <?= $kitap->renderCard(); ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="sepetCanvas" aria-labelledby="sepetCanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="sepetCanvasLabel">Sepetiniz</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div id="sepetListesi">
            <p class="text-center">Sepetiniz şu an boş</p>
        </div>
        <form id="sepetForm" action="sepetim.php" method="POST">
            <input type="hidden" id="sepetData" name="sepetData" value="">
            <button type="submit" class="btn btn-primary w-100 mt-4">Ödemeye Geç</button>
        </form>
    </div>
</div>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


    <script src="./assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/bootstrap/js/bootstrap.bundle.min.js"></script>
	</style>
   
<script>



    let sepet = []; // Sepet başlatılır

    // Sepete kitap ekler
    function sepeteEkle(kitapId, kitapAdi, kitapFiyati) {
        sepet.push({ id: kitapId, adi: kitapAdi, fiyati: kitapFiyati });
        sepetiGuncelle();
    }

    // Sepetten kitap siler
    function sepettenCikar(kitapId) {
        sepet = sepet.filter(item => item.id !== kitapId); // Belirli kitap ID'sini siler
        sepetiGuncelle();
    }

    // Sepeti günceller
    function sepetiGuncelle() {
        const sepetListesi = document.getElementById('sepetListesi');
        const sepetDataInput = document.getElementById('sepetData');
        sepetListesi.innerHTML = '';

        if (sepet.length === 0) {
            sepetListesi.innerHTML = '<p class="text-center">Sepetiniz Şu an boş</p>';
        } else {
            sepet.forEach(item => {
                const listItem = document.createElement('div');
                listItem.classList.add('mb-2');
                listItem.innerHTML = `
                    <strong>${item.adi}</strong> - ₺${item.fiyati.toFixed(2)}
                    <button class="btn btn-danger btn-sm float-end" onclick="sepettenCikar(${item.id})">Sil</button>
                `;
                sepetListesi.appendChild(listItem);
            });

            // Sepetteki kitap ID'lerini JSON formatında sakla
            const sepetIds = sepet.map(item => item.id);
            sepetDataInput.value = JSON.stringify(sepetIds);
        }
    }
	
</script>
</body>
</html>
