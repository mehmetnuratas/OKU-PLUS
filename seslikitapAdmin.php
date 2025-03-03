<?php
// İzin verilen giriş sayfalarının URL'leri
$allowed_referers = [
    'http://localhost/kitapAdmin.php',
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
    $sql_delete = "DELETE FROM sesli_kitaplar WHERE kitap_id = $delete_id";
    if ($conn->query($sql_delete) === TRUE) {
        echo "<script>alert('Kitap başarıyla silindi.'); window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
    } else {
        echo "Hata: " . $conn->error;
    }
}

// Kitap düzenleme işlemi
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $sql_edit = "SELECT * FROM sesli_kitaplar WHERE kitap_id = $edit_id";
    $result_edit = $conn->query($sql_edit);
    $book = $result_edit->fetch_assoc();
}

// Kitap güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $update_id = intval($_POST['update_id']);
    $kitap_adi = $conn->real_escape_string($_POST['kitap_adi']);
    $seslendiren = $conn->real_escape_string($_POST['seslendiren']);
    $sure = $conn->real_escape_string($_POST['sure']);
    $dosya_yolu = $conn->real_escape_string($_POST['dosya_yolu']);
    $format = $conn->real_escape_string($_POST['format']);
    $boyut = $conn->real_escape_string($_POST['boyut']);
    $kitap_ozet = $conn->real_escape_string($_POST['kitap_ozet']);
    $ucret = floatval($_POST['ucret']);

    // Kapak resmi dosyasını yükleme
    $kapak_resmi = $conn->real_escape_string($_POST['current_kapak_resmi']);
    if (isset($_FILES['kapak_resmi']) && $_FILES['kapak_resmi']['error'] === 0) {
        $kapak_resmi = 'uploads/' . basename($_FILES['kapak_resmi']['name']);
        move_uploaded_file($_FILES['kapak_resmi']['tmp_name'], $kapak_resmi);
    }

    $sql_update = "UPDATE sesli_kitaplar SET 
                    kitap_adi = '$kitap_adi', 
                    seslendiren = '$seslendiren',
                    sure = '$sure', 
                    dosya_yolu = '$dosya_yolu', 
                    format = '$format', 
                    boyut = '$boyut', 
                    kitap_ozet = '$kitap_ozet', 
                    kapak_resmi = '$kapak_resmi', 
                    ucret = '$ucret' 
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
    $seslendiren = $conn->real_escape_string($_POST['seslendiren']);
    $sure = $conn->real_escape_string($_POST['sure']);
    $dosya_yolu = $conn->real_escape_string($_POST['dosya_yolu']);
    $format = $conn->real_escape_string($_POST['format']);
    $boyut = $conn->real_escape_string($_POST['boyut']);
    $kitap_ozet = $conn->real_escape_string($_POST['kitap_ozet']);
    $ucret = floatval($_POST['ucret']);

    // Kapak resmi dosyasını yükleme
    $kapak_resmi = null;
    if (isset($_FILES['kapak_resmi']) && $_FILES['kapak_resmi']['error'] === 0) {
        $kapak_resmi = 'uploads/' . basename($_FILES['kapak_resmi']['name']);
        move_uploaded_file($_FILES['kapak_resmi']['tmp_name'], $kapak_resmi);
    }

    $sql_add = "INSERT INTO sesli_kitaplar (kitap_adi, seslendiren, sure, dosya_yolu, format, boyut, kitap_ozet, kapak_resmi, ucret) 
                VALUES ('$kitap_adi', '$seslendiren', '$sure', '$dosya_yolu', '$format', '$boyut', '$kitap_ozet', '$kapak_resmi', '$ucret')";
    if ($conn->query($sql_add) === TRUE) {
        echo "<script>alert('Yeni kitap başarıyla eklendi.'); window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
    } else {
        echo "Hata: " . $conn->error;
    }
}

// Kitapları listeleme
$sql = "SELECT * FROM sesli_kitaplar";
$result = $conn->query($sql);
?>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>body {
    font-family: 'Roboto', sans-serif;
    background-color: #f4f7fc;
    margin: 0;
    padding: 0;
    color: #333;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

header {
    background-color: #4CAF50;
    color: white;
    text-align: center;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.books-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: space-around;
}

.book-card {
    background-color: white;
    border-radius: 8px;
    padding: 15px;
    width: 250px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s, transform 0.3s;
}

.book-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
}

.book-card img {
    width: 100%;
    height: 300px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 15px;
}

.book-card h3 {
    font-size: 18px;
    margin: 10px 0;
}

.book-card p {
    font-size: 14px;
    color: #555;
    margin-bottom: 10px;
}

.book-card .details-button, .book-card .edit-button {
    display: inline-block;
    padding: 8px 15px;
    background-color: #28a745;
    color: white;
    border-radius: 5px;
    text-decoration: none;
    margin-top: 10px;
}

.book-card .details-button:hover, .book-card .edit-button:hover {
    background-color: #218838;
}

.book-card .edit-button {
    background-color: #007bff;
}

.book-card .edit-button:hover {
    background-color: #0056b3;
}

.footer {
    text-align: center;
    padding: 20px;
    background-color: #4CAF50;
    color: white;
    margin-top: 30px;
    border-radius: 8px;
}

.edit-form {
    margin-top: 20px;
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.edit-form input, .edit-form textarea, .edit-form button {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.edit-form button {
    background-color: #4CAF50;
    color: white;
    cursor: pointer;
}

.edit-form button:hover {
    background-color: #45a049;
}

        /* Stil kodları aynı kalmıştır */
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>Sesli Kitaplar Admin Paneli</h1>
        <a href="#add-book-form" class="add-book-button">Yeni Kitap Ekle</a>
		  <a class="nav-link" href="kitapAdmin.php">KİTAPLAR ADMİN
</a>
    </header>

    <div class="books-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="book-card">
                    <div style="border: 3px solid #4CAF50; padding: 10px; border-radius: 8px; display: flex; justify-content: center; align-items: center;">
                        <img src="<?php echo $row['kapak_resmi']; ?>" alt="Kapak Resmi">
                    </div>
                    <h3><?php echo $row['kitap_adi']; ?></h3>
                    <p><strong>Seslendiren:</strong> <?php echo $row['seslendiren']; ?></p>
                    <p><strong>Ücret:</strong> <?php echo $row['ucret']; ?> ₺</p>
                    <p><strong>Süre:</strong> <?php echo $row['sure']; ?> dakika</p>
                    
                    <a href="?edit_id=<?php echo $row['kitap_id']; ?>" class="edit-button">Düzenle</a>
                    <a href="?delete_id=<?php echo $row['kitap_id']; ?>" class="details-button" onclick="return confirm('Bu kaydı silmek istediğinizden emin misiniz?')">Sil</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Hiçbir kayıt bulunamadı.</p>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['edit_id'])): ?>
        <div class="edit-form">
            <h2>Kitap Düzenle</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="update_id" value="<?php echo $book['kitap_id']; ?>">
                <input type="hidden" name="current_kapak_resmi" value="<?php echo $book['kapak_resmi']; ?>">
                <label for="kitap_adi">Kitap Adı:</label>
                <input type="text" name="kitap_adi" value="<?php echo $book['kitap_adi']; ?>" required><br>
                <label for="seslendiren">Seslendiren:</label>
                <input type="text" name="seslendiren" value="<?php echo $book['seslendiren']; ?>" required><br>
                <label for="sure">Süre:</label>
                <input type="text" name="sure" value="<?php echo $book['sure']; ?>" required><br>
                <label for="dosya_yolu">Dosya Yolu:</label>
                <input type="text" name="dosya_yolu" value="<?php echo $book['dosya_yolu']; ?>" required><br>
                <label for="format">Format:</label>
                <input type="text" name="format" value="<?php echo $book['format']; ?>" required><br>
                <label for="boyut">Boyut:</label>
                <input type="text" name="boyut" value="<?php echo $book['boyut']; ?>" required><br>
                <label for="kitap_ozet">Kitap Özeti:</label>
                <textarea name="kitap_ozet" rows="4" required><?php echo $book['kitap_ozet']; ?></textarea><br>
                <label for="ucret">Ücret:</label>
                <input type="number" name="ucret" step="0.01" value="<?php echo $book['ucret']; ?>" required><br>
                <label for="kapak_resmi">Kapak Resmi:</label>
                <input type="file" name="kapak_resmi"><br>
                <button type="submit">Güncelle</button>
            </form>
        </div>
    <?php endif; ?>

    <div class="edit-form" id="add-book-form">
        <h2>Yeni Kitap Ekle</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="add_book" value="1">
            <label for="kitap_adi">Kitap Adı:</label>
            <input type="text" name="kitap_adi" required><br>
            <label for="seslendiren">Seslendiren:</label>
            <input type="text" name="seslendiren" required><br>
            <label for="sure">Süre:</label>
            <input type="text" name="sure" required><br>
            <label for="dosya_yolu">Dosya Yolu:</label>
            <input type="text" name="dosya_yolu" required><br>
            <label for="format">Format:</label>
            <input type="text" name="format" required><br>
            <label for="boyut">Boyut:</label>
            <input type="text" name="boyut" required><br>
            <label for="kitap_ozet">Kitap Özeti:</label>
            <textarea name="kitap_ozet" rows="4" required></textarea><br>
            <label for="ucret">Ücret:</label>
            <input type="number" name="ucret" step="0.01" required><br>
            <label for="kapak_resmi">Kapak Resmi:</label>
            <input type="file" name="kapak_resmi" required><br>
            <button type="submit">Ekle</button>
        </form>
    </div>
</div>

<footer class="footer">
    &copy; 2025 Sesli Kitaplar Admin Paneli
</footer>
</body>
</html>