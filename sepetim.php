<?php
// Veritabanı bağlantı bilgileri
$servername = "localhost";
$username = "root";
$password = ""; // Şifreyi kendi yapılandırmanıza göre değiştirin
$dbname = "okuplus";

// Veritabanına bağlantı kuruluyor
$conn = new mysqli($servername, $username, $password, $dbname); // mysqli nesnesi oluşturuluyor
if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error); // OOP kullanarak hata kontrolü yapılır
}

$sepetData = isset($_POST['sepetData']) ? json_decode($_POST['sepetData'], true) : []; // Sepet verilerini alır
$kitaplar = [];
$toplamTutar = 0;
$kullaniciId = 1; // Kullanıcı ID'si sabit verilmiş, dinamik hale getirilebilir

if (!empty($sepetData)) {
    $kitapIds = implode(",", array_map('intval', $sepetData)); // Sepet verilerindeki kitap ID'lerini alır ve birleştirir
    $sql = "SELECT kitap_id, kitap_adi, yazar, fiyat FROM kitaplar WHERE kitap_id IN ($kitapIds)"; // SQL sorgusu oluşturulur
    $result = $conn->query($sql); // OOP tarzında sorgu çalıştırılır

    if ($result->num_rows > 0) {
        // Sorgu sonuçları döngüye alınır ve kitaplar ile toplam tutar hesaplanır
        while ($row = $result->fetch_assoc()) {
            $kitaplar[] = $row; // Kitaplar dizisine veri eklenir
            $toplamTutar += $row['fiyat']; // Toplam tutar hesaplanır
        }
    }
}

// Ödeme işlemi yapılmışsa veritabanına kaydedilir
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['card_number'])) {
    $cardNumber = $_POST['card_number'];
    $expiryDate = $_POST['expiry_date'];
    $cvv = $_POST['cvv'];
    $cardHolder = $_POST['card_holder'];
    $tarih = date("Y-m-d H:i:s"); // Ödeme tarihi alınır

    // Kredi kartı bilgilerini veritabanına kaydetmek için OOP yöntemi kullanılarak hazırlanan sorgu
    $stmt = $conn->prepare("INSERT INTO kredikartbilgileri (kartNumarasi, sonKullanmaTarihi, cvv, kartSahibiAdi, kullanici_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $cardNumber, $expiryDate, $cvv, $cardHolder, $kullaniciId); // Parametreler bağlanır
    $stmt->execute(); // Sorgu çalıştırılır

    // Siparişleri veritabanına kaydetme işlemi
    foreach ($kitaplar as $kitap) {
        $stmt = $conn->prepare("INSERT INTO siparisler (kitap_id, toplam_tutar, tarih, kullanici_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("idss", $kitap['kitap_id'], $toplamTutar, $tarih, $kullaniciId); // Parametreler bağlanır
        $stmt->execute(); // Sipariş verileri kaydedilir
    }

    echo "<p>Ödeme başarıyla tamamlandı! Toplam Tutar: " . number_format($toplamTutar, 2) . " ₺</p>";
}

// Veritabanı bağlantısını kapatma işlemi
$conn->close(); // OOP kullanılarak bağlantı kapatılır
?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sepet ve Ödeme</title>
    <style>
       /* Genel Stil */
body {
    font-family: Arial, sans-serif;
    background-color: #f7f7f7;
    color: #333;
}

h2, h4 {
    color: #444;
    font-weight: bold;
}

/* Sepet Tablosu */
.sepet-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.sepet-table th, .sepet-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.sepet-table th {
    background-color: #f0f0f0;
}

/* Toplam Tutar */
.toplam-tutar {
    font-size: 18px;
    font-weight: bold;
    margin-top: 20px;
}

/* Kredi Kartı Formu */
.kredi-karti-form {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    width: 400px;
    margin: 0 auto;
}

.kredi-karti-form .form-group {
    margin-bottom: 15px;
}

.kredi-karti-form label {
    display: block;
    font-weight: bold;
}

.kredi-karti-form input[type="text"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    margin-top: 5px;
}

.kredi-karti-form input[type="submit"] {
    background-color: #4CAF50;
    color: #fff;
    padding: 12px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
    margin-top: 20px;
}

.kredi-karti-form input[type="submit"]:hover {
    background-color: #45a049;
}

.kredi-karti-form input[type="text"]:focus {
    border-color: #4CAF50;
}

.kredi-karti-baslik {
    font-size: 22px;
    margin-top: 30px;
    text-align: center;
}

.bos-sepet {
    text-align: center;
    font-size: 16px;
    color: #888;
}

    </style>
</head>
<body>
    <h2 class="sepet-baslik">Sepetinizdeki Kitaplar</h2>

<?php if (!empty($kitaplar)): ?>
    <table class="sepet-table">
        <thead>
            <tr>
                <th>Kitap ID</th>
                <th>Kitap Adı</th>
                <th>Yazar</th>
                <th>Fiyat (₺)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($kitaplar as $kitap): ?>
            <tr>
                <td><?php echo htmlspecialchars($kitap['kitap_id']); ?></td>
                <td><?php echo htmlspecialchars($kitap['kitap_adi']); ?></td>
                <td><?php echo htmlspecialchars($kitap['yazar']); ?></td>
                <td><?php echo number_format($kitap['fiyat'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <h4 class="toplam-tutar">Toplam Tutar: <?php echo number_format($toplamTutar, 2); ?> ₺</h4>
<?php else: ?>
    <p class="bos-sepet">Sepetiniz boş veya sepet verisi bulunamadı.</p>
<?php endif; ?>

<h2 class="kredi-karti-baslik">Kredi Kartı Bilgilerinizi Girin</h2>

<form action="" method="POST" class="kredi-karti-form" id="krediKartiForm">
    <div class="form-group">
        <label for="card_number">Kredi Kartı Numarası:</label>
        <input type="text" id="card_number" name="card_number" required placeholder="**** **** **** ****" 
               maxlength="19" oninput="formatCardNumber(event)">
    </div>

    <div class="form-group">
        <label for="expiry_date">Son Kullanma Tarihi (MM/YY):</label>
        <input type="text" id="expiry_date" name="expiry_date" required placeholder="MM/YY" 
               pattern="^(0[1-9]|1[0-2])\/(2[0-9])$" title="Geçerli bir tarih girin (Örn: 12/25)">
    </div>

    <div class="form-group">
        <label for="cvv">CVV:</label>
        <input type="text" id="cvv" name="cvv" required placeholder="***" 
               pattern="\d{3}" title="CVV numarası 3 haneli olmalıdır">
    </div>

    <div class="form-group">
        <label for="card_holder">Kart Sahibi Adı:</label>
        <input type="text" id="card_holder" name="card_holder" required placeholder="Ad ve Soyad" 
               pattern="[A-Za-zÇçĞğÖöŞşÜüİı\s]+" title="Kart sahibi adı Türk alfabesine uygun olmalıdır (sadece harfler ve boşluklar)">
    </div>

    <input type="hidden" name="sepetData" value='<?php echo json_encode($sepetData); ?>'>
    <input type="submit" value="Ödeme Yap" class="submit-btn">
</form>

<script>
    document.getElementById('krediKartiForm').addEventListener('submit', function(event) {
        const cardNumber = document.getElementById('card_number').value.replace(/\s/g, '');
        if (cardNumber.length !== 16 || isNaN(cardNumber)) {
            alert('Kredi kartı numarası 16 haneli olmalıdır.');
            event.preventDefault();
        }

        const expiryDate = document.getElementById('expiry_date').value;
        const [month, year] = expiryDate.split('/');
        const currentDate = new Date();
        const currentYear = currentDate.getFullYear() % 100;
        const currentMonth = currentDate.getMonth() + 1;

        if (parseInt(month) < currentMonth && parseInt(year) <= currentYear) {
            alert('Geçersiz son kullanma tarihi.');
            event.preventDefault();
        }
    });

    function formatCardNumber(event) {
        let value = event.target.value.replace(/\D/g, ''); // Remove non-digit characters
        if (value.length > 16) value = value.slice(0, 16); // Limit to 16 digits
        let formattedValue = value.replace(/(\d{4})(?=\d)/g, '$1 '); // Add space after every 4 digits
        event.target.value = formattedValue;
    }
</script>

</body>
</html>
