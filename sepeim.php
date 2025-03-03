<?php
// Sepet siparişlerini işleyen ekran
if (isset($_GET['ids'])) {
    $kitapIds = explode(',', $_GET['ids']); // Gelen kitap ID'lerini al
    
    if (empty($kitapIds)) {
        die('Sepet boş!');
    }
    
    // Veritabanı bağlantısı
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=okuplus", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Bağlantı başarısız: " . $e->getMessage());
    }

    // Kitap bilgilerini al ve görüntüle
    $placeholders = rtrim(str_repeat('?,', count($kitapIds)), ','); // SQL için '?' placeholder oluştur
    $stmt = $pdo->prepare("SELECT * FROM sesli_kitaplar WHERE kitap_id IN ($placeholders)");
    $stmt->execute($kitapIds);
    $selectedBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    die('Hiçbir kitap seçilmedi!');
}

// Kredi kartı bilgilerini ve sipariş kaydını işle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kredi kartı ve sipariş bilgileri
    if (isset($_POST['kart_id'], $_POST['kartNumarasi'], $_POST['sonKullanmaTarihi'], $_POST['kartSahibiAdi'], $_POST['cvv'], $_POST['kullanici_id'])) {
        // Kredi kartı bilgileri
        $kart_id = $_POST['kart_id']; // Kart id'si
        $kartNumarasi = $_POST['kartNumarasi'];
        $sonKullanmaTarihi = $_POST['sonKullanmaTarihi'];  // 'sonKullanmaTarihi' int formatında gelmeli
        $kartSahibiAdi = $_POST['kartSahibiAdi'];
        $cvv = $_POST['cvv'];
        $kullanici_id = $_POST['kullanici_id'];

        // 'sonKullanmaTarihi' formatını kontrol et ve int formatına çevir
        if (!preg_match('/^\d{6}$/', $sonKullanmaTarihi)) {
            die('Geçerli bir son kullanma tarihi formatı girin (YYYYMM).');
        }

        // Eğer kart_id mevcut değilse, yeni bir kredi kartı kaydediyoruz
        if ($kart_id == '0') {  // '0' değeri yeni kart olduğunu belirtir
            $stmt = $pdo->prepare("INSERT INTO kredikartbilgileri (kartNumarasi, sonKullanmaTarihi, kartSahibiAdi, cvv, kullanici_id) 
                                   VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$kartNumarasi, $sonKullanmaTarihi, $kartSahibiAdi, $cvv, $kullanici_id]);
            // Yeni kart id'sini alıyoruz
            $kart_id = $pdo->lastInsertId();
        } else {
            // Kredi kartı bilgilerini güncelle
            $stmt = $pdo->prepare("UPDATE kredikartbilgileri SET kartNumarasi = ?, sonKullanmaTarihi = ?, kartSahibiAdi = ?, cvv = ? 
                                   WHERE kart_id = ? AND kullanici_id = ?");
            $stmt->execute([$kartNumarasi, $sonKullanmaTarihi, $kartSahibiAdi, $cvv, $kart_id, $kullanici_id]);
        }

        // Sipariş kaydını oluştur
        $totalPrice = 0;
        foreach ($selectedBooks as $kitap) {
            $totalPrice += $kitap['ucret'];

            // Siparişi 'sesli_kitaplarsiparisler' tablosuna ekle
            $stmt = $pdo->prepare("INSERT INTO sesli_kitaplarsiparisler (kullanici_id, kitap_id, kitap_adi, sure, ucret, siparis_tarihi) 
                VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([ 
                $kullanici_id,
                $kitap['kitap_id'],
                $kitap['kitap_adi'],
                $kitap['sure'],
                $kitap['ucret']
            ]);
        }

        // Sipariş ve ödeme başarılı
        echo "Sipariş ve ödeme işlemi başarıyla tamamlandı!";
        header("Location: index.php"); // Anasayfaya yönlendirme
        exit;
    } else {
        echo "Tüm kredi kartı bilgilerini doldurmalısınız.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sepet Onayı | OkuPlus</title>
    <link rel="stylesheet" href="./assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/styles.css">
</head>
<body>
    <div class="container my-5">
        <h1 class="mb-4">Sepetinizdeki Kitaplar</h1>
        <?php if (!empty($selectedBooks)): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kitap Adı</th>
                        <th>Süre (dk)</th>
                        <th>Ücret (TL)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalPrice = 0;
                    foreach ($selectedBooks as $index => $kitap): 
                        $totalPrice += $kitap['ucret'];
                    ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= htmlspecialchars($kitap['kitap_adi']); ?></td>
                            <td><?= htmlspecialchars($kitap['sure']); ?></td>
                            <td><?= htmlspecialchars($kitap['ucret']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Toplam Ücret:</strong></td>
                        <td><strong><?= $totalPrice; ?> TL</strong></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <p>Sepetiniz boş görünüyor!</p>
        <?php endif; ?>

       <!-- Kredi kartı bilgilerini alma formu -->
<h2 class="my-4">Kredi Kartı Bilgileri</h2>
<form method="POST" action=""> 
    <input type="hidden" name="kullanici_id" value="1"> <!-- Bu değeri oturumdan alabilirsiniz -->
    <input type="hidden" name="kart_id" value="0"> <!-- Yeni kart ekleniyorsa '0' -->
    
    <div class="form-group">
        <label for="kartNumarasi">Kart Numarası</label>
        <input type="text" class="form-control" id="kartNumarasi" name="kartNumarasi" required pattern="\d{16}" title="Kart numarası 16 haneli olmalıdır">
    </div>
    
    <div class="form-group">
        <label for="sonKullanmaTarihi">Son Kullanma Tarihi (YYYYMM)</label>
        <input type="text" class="form-control" id="sonKullanmaTarihi" name="sonKullanmaTarihi" required pattern="\d{6}" title="Son kullanma tarihi 'YYYYMM' formatında olmalıdır (örneğin: 012025)">
    </div>
    
    <div class="form-group">
        <label for="kartSahibiAdi">Kart Sahibi Adı</label>
        <input type="text" class="form-control" id="kartSahibiAdi" name="kartSahibiAdi" required pattern="[A-Za-z\s]+" title="Sadece harfler ve boşluklar geçerlidir">
    </div>
    
    <div class="form-group">
        <label for="cvv">CVV</label>
        <input type="text" class="form-control" id="cvv" name="cvv" required pattern="\d{3}" title="CVV 3 haneli olmalıdır">
    </div>
    
    <button type="submit" class="btn btn-success mt-3">Siparişi Tamamla ve Ödeme Yap</button>
</form>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
