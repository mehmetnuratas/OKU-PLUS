<?php
// İzin verilen giriş sayfalarının URL'leri
$allowed_referers = [
    'http://localhost/seslikitapAdmin.php',
    'http://localhost/giris.php'
];

// Referans URL'yi al
$referer = $_SERVER['HTTP_REFERER'] ?? '';

// Eğer geçerli referans URL listede yoksa, erişimi engelle
if (!in_array($referer, $allowed_referers)) {
    die('Bu sayfaya doğrudan erişim yasak.');
}
// Veritabanı bağlantısı
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'okuplus';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Kitap silme işlemi
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql_delete = "DELETE FROM kitaplar WHERE kitap_id = $delete_id";
    if ($conn->query($sql_delete) === TRUE) {
        echo "<script>alert('Kitap başarıyla silindi.'); window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
    } else {
        echo "Hata: " . $conn->error;
    }
}

// Kitap düzenleme işlemi
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $sql_edit = "SELECT * FROM kitaplar WHERE kitap_id = $edit_id";
    $result_edit = $conn->query($sql_edit);
    $book = $result_edit->fetch_assoc();
}

// Kitap güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $update_id = intval($_POST['update_id']);
    $kitap_adi = $conn->real_escape_string($_POST['kitap_adi']);
    $yazar = $conn->real_escape_string($_POST['yazar']);
    $yayin_tarihi = $conn->real_escape_string($_POST['yayin_tarihi']);
    $fiyat = floatval($_POST['fiyat']);
    $indirim_orani = floatval($_POST['indirim_orani']);
    $stok_sayisi = intval($_POST['stok_sayisi']);
    $kategori_id = intval($_POST['kategori_id']);
    $yayin_evi = $conn->real_escape_string($_POST['yayin_evi']);
    $sayfa_sayisi = intval($_POST['sayfa_sayisi']);
    $dil = $conn->real_escape_string($_POST['dil']);
    $aciklama = $conn->real_escape_string($_POST['aciklama']);

    // Kapak resmi dosyasını yükleme
    $kapak_resmi = $conn->real_escape_string($_POST['current_kapak_resmi']);
    if (isset($_FILES['kapak_resmi']) && $_FILES['kapak_resmi']['error'] === 0) {
        $kapak_resmi = 'uploads/' . basename($_FILES['kapak_resmi']['name']);
        move_uploaded_file($_FILES['kapak_resmi']['tmp_name'], $kapak_resmi);
    }

    $sql_update = "UPDATE kitaplar SET 
                    kitap_adi = '$kitap_adi', 
                    yazar = '$yazar',
                    yayin_tarihi = '$yayin_tarihi', 
                    fiyat = '$fiyat', 
                    indirim_orani = '$indirim_orani', 
                    stok_sayisi = '$stok_sayisi', 
                    kategori_id = '$kategori_id', 
                    yayin_evi = '$yayin_evi', 
                    sayfa_sayisi = '$sayfa_sayisi', 
                    dil = '$dil', 
                    aciklama = '$aciklama', 
                    kapak_resmi = '$kapak_resmi' 
                    WHERE kitap_id = $update_id";
    if ($conn->query($sql_update) === TRUE) {
        echo "<script>alert('Kitap başarıyla güncellendi.'); window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
    } else {
        echo "Hata: " . $conn->error;
    }
}

// Yeni kitap ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book'])) {
    $kitap_adi = $conn->real_escape_string($_POST['kitap_adi']);
    $yazar = $conn->real_escape_string($_POST['yazar']);
    $yayin_tarihi = $conn->real_escape_string($_POST['yayin_tarihi']);
    $fiyat = floatval($_POST['fiyat']);
    $indirim_orani = floatval($_POST['indirim_orani']);
    $stok_sayisi = intval($_POST['stok_sayisi']);
    $kategori_id = intval($_POST['kategori_id']);
    $yayin_evi = $conn->real_escape_string($_POST['yayin_evi']);
    $sayfa_sayisi = intval($_POST['sayfa_sayisi']);
    $dil = $conn->real_escape_string($_POST['dil']);
    $aciklama = $conn->real_escape_string($_POST['aciklama']);

    // Kapak resmi dosyasını yükleme
    $kapak_resmi = null;
    if (isset($_FILES['kapak_resmi']) && $_FILES['kapak_resmi']['error'] === 0) {
        $kapak_resmi = 'uploads/' . basename($_FILES['kapak_resmi']['name']);
        move_uploaded_file($_FILES['kapak_resmi']['tmp_name'], $kapak_resmi);
    }

    $sql_add = "INSERT INTO kitaplar (kitap_adi, yazar, yayin_tarihi, fiyat, indirim_orani, stok_sayisi, kategori_id, yayin_evi, sayfa_sayisi, dil, aciklama, kapak_resmi) 
                VALUES ('$kitap_adi', '$yazar', '$yayin_tarihi', '$fiyat', '$indirim_orani', '$stok_sayisi', '$kategori_id', '$yayin_evi', '$sayfa_sayisi', '$dil', '$aciklama', '$kapak_resmi')";
    if ($conn->query($sql_add) === TRUE) {
        echo "<script>alert('Yeni kitap başarıyla eklendi.'); window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
    } else {
        echo "Hata: " . $conn->error;
    }
}

// Kitapları listeleme
$sql = "SELECT * FROM kitaplar";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitaplar Admin Paneli</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
       <style>
  /* Genel sayfa ayarları */
body {
    font-family: 'Roboto', sans-serif; /* Yazı tipi ailesi */
    background-color: #f4f7fc; /* Arka plan rengi */
    margin: 0; /* Dış boşlukları sıfırla */
    padding: 0; /* İç boşlukları sıfırla */
    color: #333; /* Metin rengi */
}

/* Ana kapsayıcı ayarları */
.container {
    max-width: 1200px; /* Maksimum genişlik */
    margin: 0 auto; /* Ortalamak için kenar boşlukları */
    padding: 20px; /* İç boşluk */
}

/* Sayfa başlığı (header) stilleri */
header {
    background-color: #4CAF50; /* Arka plan rengi */
    color: white; /* Metin rengi */
    text-align: center; /* Metni ortala */
    padding: 15px; /* İç boşluk */
    border-radius: 8px; /* Yuvarlatılmış köşeler */
    margin-bottom: 20px; /* Alt boşluk */
}

/* Kitap kartlarının bulunduğu konteyner */
.books-container {
    display: flex; /* Esnek kutu düzeni */
    flex-wrap: wrap; /* Kartları birden fazla satıra sığdır */
    gap: 20px; /* Kartlar arasındaki boşluk */
    justify-content: space-around; /* Kartları yatayda yay */
}

/* Kitap kartlarının genel stili */
.book-card {
    background-color: white; /* Arka plan rengi */
    border-radius: 8px; /* Yuvarlatılmış köşeler */
    padding: 15px; /* İç boşluk */
    width: 250px; /* Kart genişliği */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Hafif gölge efekti */
    transition: box-shadow 0.3s, transform 0.3s; /* Geçiş animasyonları */
    text-align: center; /* Metni ortala */
}

/* Kartın üzerine gelindiğinde oluşan efektler */
.book-card:hover {
    transform: translateY(-5px); /* Kartı hafifçe yukarı kaldır */
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1); /* Daha belirgin gölge efekti */
}

/* Kartın içindeki kitap resmi */
.book-card img {
    width: 100%; /* Resmin genişliği kartın tamamını kaplar */
    height: 300px; /* Sabit yükseklik */
    object-fit: cover; /* Resmin düzgün şekilde sığması */
    border-radius: 8px; /* Yuvarlatılmış köşeler */
    margin-bottom: 15px; /* Alt boşluk */
    border: 5px solid #555; /* Çerçeve rengi */
}

/* Kitap başlığı (h3) */
.book-card h3 {
    font-size: 18px; /* Yazı boyutu */
    margin: 10px 0; /* Üst ve alt boşluk */
}

/* Kitap açıklama metni */
.book-card p {
    font-size: 14px; /* Yazı boyutu */
    color: #555; /* Metin rengi */
    margin-bottom: 10px; /* Alt boşluk */
}

/* Detay ve düzenleme butonlarının ortak stili */
.book-card .details-button, 
.book-card .edit-button {
    display: inline-block; /* Butonları satır içi blok yap */
    padding: 8px 15px; /* İç boşluk */
    background-color: #28a745; /* Arka plan rengi */
    color: white; /* Yazı rengi */
    border-radius: 5px; /* Yuvarlatılmış köşeler */
    text-decoration: none; /* Alt çizgiyi kaldır */
    margin-top: 10px; /* Üst boşluk */
    transition: background-color 0.3s; /* Renk geçiş animasyonu */
}

/* Detay ve düzenleme butonlarının üzerine gelindiğinde */
.book-card .details-button:hover, 
.book-card .edit-button:hover {
    background-color: #218838; /* Daha koyu yeşil ton */
}

/* Düzenleme butonunun özel stili */
.book-card .edit-button {
    background-color: #007bff; /* Mavi arka plan */
}

/* Düzenleme butonunun hover durumu */
.book-card .edit-button:hover {
    background-color: #0056b3; /* Daha koyu mavi */
}

/* Sayfa alt kısmı (footer) */
.footer {
    text-align: center; /* Metni ortala */
    padding: 20px; /* İç boşluk */
    background-color: #4CAF50; /* Arka plan rengi */
    color: white; /* Yazı rengi */
    margin-top: 30px; /* Üst boşluk */
    border-radius: 8px; /* Yuvarlatılmış köşeler */
}

/* Düzenleme formunun genel stili */
.edit-form {
    margin-top: 20px; /* Üst boşluk */
    background-color: white; /* Arka plan rengi */
    padding: 20px; /* İç boşluk */
    border-radius: 8px; /* Yuvarlatılmış köşeler */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Hafif gölge efekti */
}

/* Form elemanlarının ortak stili */
.edit-form input, 
.edit-form textarea, 
.edit-form button {
    width: 100%; /* Elemanların genişliği konteynırı kaplar */
    padding: 10px; /* İç boşluk */
    margin-bottom: 10px; /* Alt boşluk */
    border-radius: 5px; /* Yuvarlatılmış köşeler */
    border: 1px solid #ccc; /* Çerçeve rengi */
}

/* Formdaki butonun genel stili */
.edit-form button {
    background-color: #4CAF50; /* Arka plan rengi */
    color: white; /* Yazı rengi */
    cursor: pointer; /* Tıklanabilir imleç */
    transition: background-color 0.3s; /* Renk geçiş animasyonu */
}

/* Form butonunun hover durumu */
.edit-form button:hover {
    background-color: #007bff; /* Daha koyu yeşil ton */
}

/* Özel kitap resim stili */
.book-image {
    border: 5px solid #28a745; /* Çerçeve rengi */
    border-radius: 10px; /* Yuvarlatılmış köşeler */
    padding: 5px; /* İç boşluk */
    width: 240px; /* Sabit genişlik */
    height: 300px; /* Sabit yükseklik */
    object-fit: cover; /* Resmin düzgün şekilde sığması */
    margin: 0 auto; /* Ortala */
}

/* Stok düşükse çerçeve kırmızıya dönsün */
.low-stock {
    border: 5px solid #FF0000 !important; /* Çerçeve rengi kırmızı */
}
.low-stock img {
    border-color: #FF0000 !important; /* Resim çerçevesi kırmızı */
}
/* Stok düşükse çerçeve kırmızıya dönsün */
.low-stock {
    border: 5px solid #FF0000 !important; /* Çerçeve rengi kırmızı */
}
.low-stock img {
    border-color: #FF0000 !important; /* Resim çerçevesi kırmızı */
}

</style>


    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center text-white bg-success py-3 rounded-3">
<div class="container mt-5">
    <h1 class="text-center text-white bg-success py-3 rounded-3 d-flex align-items-center justify-content-between">
        <span>Kitaplar Admin Paneli</span>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookModal">Yeni Kitap Ekle</button>
            <a class="btn btn-warning" href="siparişler.php">Siparişler</a>
			  <a class="nav-link" href="seslikitapAdmin.php">SESLİ KİTAPLAR
</a>
        </div>
    </h1>
</div>

    </h1>

    <!-- Kitapları Listeleme -->
    <div class="row">
        <?php while ($kitap = $result->fetch_assoc()): ?>
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100 border-success">
                    <!-- Çerçeveli Kapak Resmi -->
                    <img src="<?= $kitap['kapak_resmi'] ?>" class="card-img-top book-image" alt="Kitap Kapak Resmi">
                    <div class="card-body">
                        <h5 class="card-title"><?= $kitap['kitap_adi'] ?></h5>
                        <p class="card-text">Yazar: <?= $kitap['yazar'] ?></p>
                        <p class="card-text">Fiyat: <?= $kitap['fiyat'] ?> TL</p>
                        <p class="card-text">Stok: <?= $kitap['stok_sayisi'] ?> adet</p>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $kitap['kitap_id'] ?>">Düzenle</button>
                        <a href="?delete_id=<?= $kitap['kitap_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</a>
                    </div>
                </div>
            </div>

            <!-- Kitap Düzenleme Modalı -->
            <div class="modal fade" id="editModal<?= $kitap['kitap_id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $kitap['kitap_id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel<?= $kitap['kitap_id'] ?>">Kitap Düzenle</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="update_id" value="<?= $kitap['kitap_id'] ?>">
                                <input type="hidden" name="current_kapak_resmi" value="<?= $kitap['kapak_resmi'] ?>">
                                <div class="mb-3">
                                    <label>Kitap Adı</label>
                                    <input type="text" name="kitap_adi" value="<?= $kitap['kitap_adi'] ?>" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Yazar</label>
                                    <input type="text" name="yazar" value="<?= $kitap['yazar'] ?>" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Yayın Tarihi</label>
                                    <input type="date" name="yayin_tarihi" value="<?= $kitap['yayin_tarihi'] ?>" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Fiyat</label>
                                    <input type="number" name="fiyat" value="<?= $kitap['fiyat'] ?>" step="0.01" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>İndirim Oranı</label>
                                    <input type="number" name="indirim_orani" value="<?= $kitap['indirim_orani'] ?>" step="0.01" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Stok Sayısı</label>
                                    <input type="number" name="stok_sayisi" value="<?= $kitap['stok_sayisi'] ?>" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Yayın Evi</label>
                                    <input type="text" name="yayin_evi" value="<?= $kitap['yayin_evi'] ?>" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Sayfa Sayısı</label>
                                    <input type="number" name="sayfa_sayisi" value="<?= $kitap['sayfa_sayisi'] ?>" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Dil</label>
                                    <input type="text" name="dil" value="<?= $kitap['dil'] ?>" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Açıklama</label>
                                    <textarea name="aciklama" class="form-control"><?= $kitap['aciklama'] ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label>Kapak Resmi</label>
                                    <input type="file" name="kapak_resmi" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-success">Kitap Güncelle</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Modal: Yeni Kitap Ekle -->
    <div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBookModalLabel">Yeni Kitap Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label>Kitap Adı</label>
                            <input type="text" name="kitap_adi" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Yazar</label>
                            <input type="text" name="yazar" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Yayın Tarihi</label>
                            <input type="date" name="yayin_tarihi" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Fiyat</label>
                            <input type="number" name="fiyat" step="0.01" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>İndirim Oranı</label>
                            <input type="number" name="indirim_orani" step="0.01" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Stok Sayısı</label>
                            <input type="number" name="stok_sayisi" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Yayın Evi</label>
                            <input type="text" name="yayin_evi" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Sayfa Sayısı</label>
                            <input type="number" name="sayfa_sayisi" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Dil</label>
                            <input type="text" name="dil" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Açıklama</label>
                            <textarea name="aciklama" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Kapak Resmi</label>
                            <input type="file" name="kapak_resmi" class="form-control">
                        </div>
                        <button type="submit" name="add_book" class="btn btn-primary">Kitap Ekle</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const bookCards = document.querySelectorAll('.book-card'); // Tüm kitap kartlarını seç

    bookCards.forEach(card => {
        const stock = parseInt(card.getAttribute('data-stock')); // Stok bilgisini al
        if (stock < 50) {
            card.classList.add('low-stock'); // Stok azsa kırmızı çerçeve ekle
        }
    });
});

</script>
</body>
</html>
