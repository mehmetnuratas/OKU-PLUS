<?php
// Veritabanı bağlantısı ve kitap sınıfı (değişmedi)
class Database {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "okuplus";
    public $conn;

    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Bağlantı başarısız: " . $e->getMessage());
        }
    }

    public function query($sql) {
        return $this->conn->query($sql);
    }

    public function close() {
        $this->conn = null;
    }
}

// Kitap sınıfında ücret bilgisi dahil
class Book {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllBooks() {
        $sql = "SELECT * FROM sesli_kitaplar";
        $stmt = $this->db->query($sql);
        $books = [];

        if ($stmt) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $books[] = $row;
            }
        }

        return $books;
    }

    public function getBookById($kitapId) {
        $sql = "SELECT * FROM sesli_kitaplar WHERE kitap_id = :kitapId";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(':kitapId', $kitapId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Kullanıcı sınıfı (Tek bir tanesi kaldı)
class User {
    private $db;

    public function __construct($servername = "localhost", $username = "root", $password = "", $dbname = "okuplus") {
        $this->db = new Database($servername, $username, $password, $dbname);
    }

    public function getUserById($id) {
        $sql = "SELECT * FROM kullanicilar WHERE id = ?";
        // PDO'yu kullanarak sorgu hazırlıyoruz
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(1, $id, PDO::PARAM_INT); // Parametreyi bağla
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Sonuçları al ve döndür
    }

    public function updateProfile($id, $ad_soyad, $email, $telefon_no, $sifre) {
        $hashed_sifre = password_hash($sifre, PASSWORD_DEFAULT);
        $sql = "UPDATE kullanicilar SET ad_soyad = ?, email = ?, telefon_no = ?, sifre = ? WHERE id = ?";
        // PDO'yu kullanarak sorgu hazırlıyoruz
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(1, $ad_soyad);
        $stmt->bindParam(2, $email);
        $stmt->bindParam(3, $telefon_no);
        $stmt->bindParam(4, $hashed_sifre);
        $stmt->bindParam(5, $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

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
    header("Location: index.php"); // Sayfayı yeniden yükler
    exit();
}

// Kullanıcı giriş kontrolü ve bilgilerini alma
if (isset($_SESSION['user_id'])) {
    $user = new User("localhost", "root", "", "okuplus");
    $userId = $_SESSION['user_id'];
    $userInfo = $user->getUserById($userId); // Giriş yapan kullanıcı bilgilerini al
} else {
    $userInfo = null; // Giriş yapmayan kullanıcı için boş bilgi
}

// Profil güncelleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $ad_soyad = $_POST['ad_soyad'];
    $email = $_POST['email'];
    $telefon_no = $_POST['telefon_no'];
    $sifre = $_POST['sifre'];

    if ($user->updateProfile($userId, $ad_soyad, $email, $telefon_no, $sifre)) {
        echo "Profil başarıyla güncellendi.";
        // Profil güncelleme sonrası kullanıcı bilgilerini yeniden alıyoruz
        $userInfo = $user->getUserById($userId);
    } else {
        echo "Hata: Profil güncellenemedi.";
    }
}

// Veritabanı bağlantısını oluştur
$db = new Database();
$book = new Book($db);

// Kitapları al
$kitaplar = $book->getAllBooks();

// Veritabanı bağlantısını kapat
$db->close();
?><!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitaplar | OkuPlus</title>
    <link rel="stylesheet" href="./assets/bootstrap/css/bootstrap.min.css">
    <style>
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
            height: 250px;
            object-fit: cover;
            border-radius: 10px;
            margin: 10px;
            border: 3px solid #eeeeee;
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: black;">
        <div class="container">
            <a class="navbar-brand" href="#">OKU PLUS</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ml-auto">
                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#sepetCanvas" aria-controls="sepetCanvas">
                        Sepetiniz
                    </button>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Kitaplar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="iletişim.php">İletişim</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
	<br>

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
</br>
				
    <!-- Kitaplar Listesi -->
    <section class="container my-5">
        <div class="row">
            <?php foreach ($kitaplar as $index => $kitap): ?>
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <img src="<?= htmlspecialchars($kitap['kapak_resmi']); ?>" class="card-img-top" alt="<?= htmlspecialchars($kitap['kitap_adi']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($kitap['kitap_adi']); ?></h5>
                            <p class="card-text">Seslendiren: <?= htmlspecialchars($kitap['seslendiren']); ?></p>
                            <p class="card-text">Süre: <?= htmlspecialchars($kitap['sure']); ?> dakika</p>
                            <p class="card-text">Ücret: <?= htmlspecialchars($kitap['ucret']); ?> TL</p>
                            <button class="btn w-100" onclick="redirectToDetail(<?= $kitap['kitap_id'] ?>)">Detay</button>
                            <button class="btn btn-primary w-100 mt-2" onclick="addToCartButton(<?= $kitap['kitap_id'] ?>, '<?= htmlspecialchars($kitap['kitap_adi']); ?>', '<?= htmlspecialchars($kitap['sure']); ?>', '<?= htmlspecialchars($kitap['ucret']); ?>')">Sepete Ekle</button>
                        </div>
                    </div>
                </div>
                <?php if (($index + 1) % 4 === 0): ?>
                    </div><div class="row">
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Sepetiniz -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="sepetCanvas" aria-labelledby="sepetCanvasLabel">
        <div class="offcanvas-header">
            <h5 id="sepetCanvasLabel">Sepetiniz</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul id="cartItems" class="list-group">
                <!-- Sepet Elemanları -->
            </ul>
            <button class="btn btn-success w-100 mt-3" onclick="submitCart()">Siparişi Onayla</button>
        </div>
    </div>

    <script>
        // Sepete kitap ekleme fonksiyonu
        function addToCartButton(bookId, bookName, bookDuration, bookPrice) {
            const book = { id: bookId, name: bookName, duration: bookDuration, price: bookPrice };
            let sepet = JSON.parse(localStorage.getItem('cart')) || [];
        
            // Aynı kitap zaten sepette varsa, kullanıcıya bildirim
            if (sepet.find(item => item.id === bookId)) {
                alert("Bu kitap zaten sepete eklendi.");
                return;
            }
        
            // Kitabı sepete ekleyin
            sepet.push(book);
            localStorage.setItem('cart', JSON.stringify(sepet));
            updateCart();  // Sepet görünümünü güncelle
        }

        // Sepet içeriğini güncelleyen fonksiyon
        function updateCart() {
            const sepet = JSON.parse(localStorage.getItem('cart')) || [];
            const cartItems = document.getElementById('cartItems');
            cartItems.innerHTML = '';  // Sepet içeriğini temizle
        
            // Sepetteki kitapları listeye ekle
            sepet.forEach((item, index) => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `
                    ${item.name} - ${item.duration} dk - ${item.price} TL
                    <button class="btn btn-danger btn-sm" onclick="removeFromCart(${index})">Sil</button>
                `;
                cartItems.appendChild(li);
            });
        }

        // Sepetteki bir kitabı silme fonksiyonu
        function removeFromCart(index) {
            let sepet = JSON.parse(localStorage.getItem('cart')) || [];
            sepet.splice(index, 1);  // Kitapları sil
            localStorage.setItem('cart', JSON.stringify(sepet));
            updateCart();  // Sepet görünümünü güncelle
        }

        // Sepeti onaylama ve yönlendirme işlemi
        function submitCart() {
            const sepet = JSON.parse(localStorage.getItem('cart')) || [];
            if (sepet.length === 0) {
                alert("Sepetiniz boş!");
                return;
            }
            const kitapIds = sepet.map(item => item.id).join(',');  // Sepet kitaplarını ID'ler olarak al
            window.location.href = `sepeim.php?ids=${kitapIds}`;  // Sipariş sayfasına yönlendir
        }

        // Sayfa yüklendiğinde sepeti güncelle
        document.addEventListener('DOMContentLoaded', function () {
            updateCart();
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="./assets/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
