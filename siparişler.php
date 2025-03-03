<?php
// İzin verilen giriş sayfalarının URL'leri
$allowed_referers = [
    'http://localhost/kitapAdmin.php',
];

// Referans URL'yi al
$referer = $_SERVER['HTTP_REFERER'] ?? '';

// Eğer geçerli referans URL listede yoksa, erişimi engelle
if (!in_array($referer, $allowed_referers)) {
    die('Bu sayfaya doğrudan erişim yasak.');
}

class Database {
    private $pdo;

    public function __construct($host, $dbname, $username, $password) {
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Veritabanı bağlantı hatası: " . $e->getMessage());
        }
    }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}

class Order {
    private $id;
    private $userId;
    private $bookId;
    private $totalAmount;
    private $orderDate;

    public function __construct($data) {
        $this->id = $data['id'];
        $this->userId = $data['kullanici_id'];
        $this->bookId = $data['kitap_id'];
        $this->totalAmount = $data['toplam_tutar'];
        $this->orderDate = $data['tarih'];
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'bookId' => $this->bookId,
            'totalAmount' => $this->totalAmount,
            'orderDate' => $this->orderDate,
        ];
    }
}

class AudioBookOrder {
    private $orderId;
    private $userId;
    private $bookId;
    private $bookTitle;
    private $duration;
    private $price;
    private $orderDate;

    public function __construct($data) {
        $this->orderId = $data['siparis_id'];
        $this->userId = $data['kullanici_id'];
        $this->bookId = $data['kitap_id'];
        $this->bookTitle = $data['kitap_adi'];
        $this->duration = $data['sure'];
        $this->price = $data['ucret'];
        $this->orderDate = $data['siparis_tarihi'];
    }

    public function toArray() {
        return [
            'orderId' => $this->orderId,
            'userId' => $this->userId,
            'bookId' => $this->bookId,
            'bookTitle' => $this->bookTitle,
            'duration' => $this->duration,
            'price' => $this->price,
            'orderDate' => $this->orderDate,
        ];
    }
}

class OrderManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllOrders() {
        $query = "SELECT id, kitap_id, toplam_tutar, tarih, kullanici_id FROM siparisler";
        $stmt = $this->db->query($query);
        $ordersData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($data) => new Order($data), $ordersData);
    }

    public function getAllAudioBookOrders() {
        $query = "SELECT siparis_id, kullanici_id, kitap_id, kitap_adi, sure, ucret, siparis_tarihi FROM sesli_kitaplarsiparisler";
        $stmt = $this->db->query($query);
        $audioBookOrdersData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($data) => new AudioBookOrder($data), $audioBookOrdersData);
    }
}

class View {
    // Güvenli htmlspecialchars fonksiyonu
    public static function safeHtml($value) {
        return htmlspecialchars($value ?? '');
    }

    public static function renderOrdersTable($orders) {
        if (empty($orders)) {
            echo "<p>Sistemde henüz bir kitap siparişi bulunmamaktadır.</p>";
            return;
        }

        echo "<h2>Kitap Siparişleri</h2>";
        echo "<table border='1'>";
        echo "<thead><tr><th>Sipariş ID</th><th>Kullanıcı ID</th><th>Kitap ID</th><th>Toplam Tutar</th><th>Sipariş Tarihi</th></tr></thead>";
        echo "<tbody>";

        foreach ($orders as $order) {
            $data = $order->toArray();
            echo "<tr>";
            echo "<td>" . self::safeHtml($data['id']) . "</td>";
            echo "<td>" . self::safeHtml($data['userId']) . "</td>";
            echo "<td>" . self::safeHtml($data['bookId']) . "</td>";
            echo "<td>" . self::safeHtml($data['totalAmount']) . "</td>";
            echo "<td>" . self::safeHtml($data['orderDate']) . "</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    }

    public static function renderAudioBookOrdersTable($audioBookOrders) {
        if (empty($audioBookOrders)) {
            echo "<p>Sistemde henüz bir sesli kitap siparişi bulunmamaktadır.</p>";
            return;
        }

        echo "<h2>Sesli Kitap Siparişleri</h2>";
        echo "<table border='1'>";
        echo "<thead><tr><th>Sipariş ID</th><th>Kullanıcı ID</th><th>Kitap ID</th><th>Kitap Adı</th><th>Süre</th><th>Ücret</th><th>Sipariş Tarihi</th></tr></thead>";
        echo "<tbody>";

        foreach ($audioBookOrders as $audioBookOrder) {
            $data = $audioBookOrder->toArray();
            echo "<tr>";
            echo "<td>" . self::safeHtml($data['orderId']) . "</td>";
            echo "<td>" . self::safeHtml($data['userId']) . "</td>";
            echo "<td>" . self::safeHtml($data['bookId']) . "</td>";
            echo "<td>" . self::safeHtml($data['bookTitle']) . "</td>";
            echo "<td>" . self::safeHtml($data['duration']) . "</td>";
            echo "<td>" . self::safeHtml($data['price']) . "</td>";
            echo "<td>" . self::safeHtml($data['orderDate']) . "</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    }
}

// Ana işlem
session_start();

try {
    $db = new Database('localhost', 'okuplus', 'root', '');
    $orderManager = new OrderManager($db);

    $orders = $orderManager->getAllOrders();
    $audioBookOrders = $orderManager->getAllAudioBookOrders();

    echo "<!DOCTYPE html>";
    echo "<html lang='tr'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>Siparişler Listesi</title>";
    echo "<style>";
    echo "body { font-family: Arial, sans-serif; line-height: 1.6; background-color: #f4f4f9; color: #333; }";
    echo "h1, h2 { text-align: center; color: #444; }";
    echo "table { width: 90%; margin: 20px auto; border-collapse: collapse; background-color: #fff; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); }";
    echo "table thead { background-color: #007BFF; color: #fff; }";
    echo "table th, table td { padding: 12px 15px; border: 1px solid #ddd; text-align: left; }";
    echo "table tbody tr:nth-child(even) { background-color: #f9f9f9; }";
    echo "table tbody tr:hover { background-color: #f1f1f1; }";
    echo "p { text-align: center; font-size: 1.1em; }";
    echo "</style>";
    echo "</head>";
    echo "<body>";
    echo "<h1>Siparişler</h1>";

    // Kitap siparişlerini görüntüle
    View::renderOrdersTable($orders);

    // Sesli kitap siparişlerini görüntüle
    View::renderAudioBookOrdersTable($audioBookOrders);

    echo "</body>";
    echo "</html>";

} catch (Exception $e) {
    echo "<p>Bir hata oluştu: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
