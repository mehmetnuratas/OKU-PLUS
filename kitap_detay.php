<?php
// Database sınıfı - Veritabanı işlemleriyle ilgilenen sınıf
class Database {
    private $connection;

    // Constructor: Veritabanı bağlantısını kurar
    public function __construct($servername, $username, $password, $dbname) {
        // MySQL veritabanına bağlantıyı sağlar
        $this->connection = new mysqli($servername, $username, $password, $dbname);
        // Bağlantı hatası kontrolü
        if ($this->connection->connect_error) {
            die("Bağlantı hatası: " . $this->connection->connect_error);
        }
    }

    // SQL sorgusu çalıştıran metod
    public function query($sql) {
        return $this->connection->query($sql); // Sorgu çalıştırılır ve sonucu döner
    }

    // SQL enjeksiyonundan korunmak için veri kaçışını sağlar
    public function escape($value) {
        return $this->connection->real_escape_string($value); // Değeri güvenli hale getirir
    }

    // Veritabanı bağlantısını kapatan metod
    public function close() {
        $this->connection->close(); // Bağlantıyı kapatır
    }
}

// Book sınıfı - Kitaplarla ilgili işlemleri yöneten sınıf
class Book {
    private $db;

    // Constructor: Database sınıfının nesnesini alır
    public function __construct($db) {
        $this->db = $db; // Database nesnesini depolar
    }

    // Kitap ID'sine göre kitap bilgisini getirir
    public function getBookById($bookId) {
        $bookId = intval($bookId); // Kitap ID'si sayısal değere dönüştürülür
        $sql = "SELECT * FROM kitaplar WHERE kitap_id = $bookId"; // SQL sorgusu
        $result = $this->db->query($sql); // Sorgu çalıştırılır

        // Kitap bulunursa, veriyi döndürür
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc(); // Veriyi döndürür
        } else {
            throw new Exception("Kitap bulunamadı."); // Kitap bulunamazsa hata fırlatılır
        }
    }
}

// Cart sınıfı - Sepet işlemleriyle ilgilenen sınıf
class Cart {
    // Constructor: Oturumda sepeti başlatır
    public function __construct() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = []; // Sepet boşsa, sepet dizisini başlatır
        }
    }

    // Sepete ürün ekler
    public function addToCart($item) {
        $exists = false;

        // Sepette mevcut olan ürünü kontrol eder
        foreach ($_SESSION['cart'] as &$cartItem) {
            if ($cartItem['kitap_id'] === $item['kitap_id']) {
                $cartItem['miktar'] += 1; // Miktarı artırır
                $exists = true;
                break;
            }
        }

        // Eğer ürün sepetin içinde değilse, yeni ürün ekler
        if (!$exists) {
            $item['miktar'] = 1; // Yeni ürünün miktarı 1 olarak eklenir
            $_SESSION['cart'][] = $item; // Sepete ekler
        }
    }

    // Sepetteki tüm ürünleri döndüren metod
    public function getCartItems() {
        return $_SESSION['cart']; // Sepetteki ürünleri döndürür
    }

    // Sepetten ürün kaldırır
    public function removeFromCart($bookId) {
        // Sepetteki ürünleri dolaşarak, belirtilen kitap ID'sine sahip olanı bulur
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['kitap_id'] == $bookId) {
                unset($_SESSION['cart'][$key]); // Ürünü sepetten siler
                break;
            }
        }
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Anahtarları yeniden sıralar
    }

    // Sepeti tamamen temizler
    public function clearCart() {
        $_SESSION['cart'] = []; // Sepeti temizler
    }
}

// Oturumu başlat
session_start();

// Veritabanı bağlantısı oluşturuluyor
$db = new Database("localhost", "root", "", "okuplus"); // Database sınıfından nesne oluşturuluyor
$bookManager = new Book($db); // Book sınıfından nesne oluşturuluyor
$cart = new Cart(); // Cart sınıfından nesne oluşturuluyor

try {
    // Kitap ID'si URL parametresi olarak alınıyor
    if (isset($_GET["kitap_id"])) {
        $kitap = $bookManager->getBookById($_GET["kitap_id"]); // Kitap bilgisi alınır
    } else {
        throw new Exception("Geçersiz kitap ID."); // Kitap ID'si geçerli değilse hata fırlatılır
    }

    // Sepete ürün ekleme işlemi
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
        $item = [
            'kitap_id' => intval($_POST['kitap_id']), // Kitap ID'si sayısal değere dönüştürülür
            'kitap_adi' => $_POST['kitap_adi'], // Kitap adı
            'fiyat' => floatval($_POST['fiyat']) // Fiyat sayısal değere dönüştürülür
        ];
        $cart->addToCart($item); // Ürün sepete eklenir

        header('Location: sepet.php'); // Sepet sayfasına yönlendirilir
        exit;
    }
} catch (Exception $e) {
    die($e->getMessage()); // Hata meydana geldiğinde mesaj gösterilir
}
?> 


<!DOCTYPE html>
<html lang="tr">
<head>

   <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($kitap["kitap_adi"]); ?> | OkuPlus</title>
    <link rel="stylesheet" href="./assets/bootstrap/css/bootstrap.min.css">
    <style>
        /* Kitap resminin görünümü */
        .book-image-container {
            width: 100%;
            max-width: 400px;
            height: 600px;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f8f9fa;
            cursor: pointer;
            transition: transform 0.3s ease-in-out; /* Hover efektleri */
        }

        .book-image-container:hover {
            transform: scale(1.05); /* Hoverda biraz büyür */
        }

        .book-image {
            object-fit: cover;
            width: 100%;
            height: 100%;
            border-radius: 15px;
        }

        /* Işık kutusu stili */
        .lightbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 1000;
            animation: fadeIn 0.3s ease-in-out;
        }

        .lightbox img {
            max-width: 90%;
            max-height: 90%;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        /* Kitap detayları */
        .book-details {
            position: relative;
            top: 50px;
        }

        .book-details h1 {
            text-transform: uppercase;
            color: #FF5733;
            letter-spacing: 2px;
        }

        .book-details p {
            font-size: 16px;
            color: #555;
        }

        .book-details strong {
            color: #333;
        }

        .btn-primary {
            background-color: #FF5733;
            border: none;
        }

        .btn-primary:hover {
            background-color: #E84C2A;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">OKU PLUS</a>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="seslikitap.php">Sesli Kitaplar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Ana Sayfa</a>
                </li>
            </ul>
        </div>
    </nav>

<div class="container my-5">
    <div class="row">
        <div class="col-md-6 d-flex justify-content-center align-items-center">
            <div class="book-image-container" id="bookImageContainer">
                <img src="<?php echo htmlspecialchars($kitap["kapak_resmi"]); ?>" class="img-fluid book-image" alt="<?php echo htmlspecialchars($kitap["kitap_adi"]); ?>" id="bookImage">
            </div>
        </div>
        <div class="col-md-6 book-details">
            <h1><?php echo htmlspecialchars($kitap["kitap_adi"]); ?></h1>
            <p><strong>Yazar:</strong> <?php echo htmlspecialchars($kitap["yazar"]); ?></p>
            <p><strong>Yayın Evi:</strong> <?php echo htmlspecialchars($kitap["yayin_evi"]); ?></p>
            <p><strong>Sayfa Sayısı:</strong> <?php echo htmlspecialchars($kitap["sayfa_sayisi"]); ?></p>
            <p><strong>Fiyat:</strong> ₺<?php echo number_format($kitap["fiyat"], 2); ?></p>
            <p><strong>Açıklama:</strong> <?php echo htmlspecialchars($kitap["aciklama"]); ?></p>

            <form method="POST">
                <input type="hidden" name="kitap_id" value="<?php echo htmlspecialchars($kitap["kitap_id"]); ?>">
                <input type="hidden" name="kitap_adi" value="<?php echo htmlspecialchars($kitap["kitap_adi"]); ?>">
                <input type="hidden" name="fiyat" value="<?php echo htmlspecialchars($kitap["fiyat"]); ?>">
             
            </form>
            <a href="index.php" class="btn btn-secondary">Geri Dön</a>
        </div>
    </div>
</div>

<!-- Işık Kutusu -->
<div class="lightbox" id="lightbox">
    <img src="<?php echo htmlspecialchars($kitap["kapak_resmi"]); ?>" alt="Büyütülmüş Kitap Resmi">
</div>

<script>
    // Kitap resmine tıklanıldığında ışık kutusunu aç
    const bookImageContainer = document.getElementById('bookImageContainer');
    const lightbox = document.getElementById('lightbox');
    const lightboxImage = lightbox.querySelector('img');

    bookImageContainer.addEventListener('click', function() {
        lightbox.style.display = 'flex'; // Işık kutusunu göster
        lightboxImage.src = bookImageContainer.querySelector('img').src; // Resmi ışık kutusuna aktar
    });

    // Işık kutusuna tıklanıldığında gizle
    lightbox.addEventListener('click', function() {
        lightbox.style.display = 'none';
    });
</script>

<script src="./assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
