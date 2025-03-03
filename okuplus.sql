-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1:3306
-- Üretim Zamanı: 03 Mar 2025, 10:02:40
-- Sunucu sürümü: 8.3.0
-- PHP Sürümü: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `okuplus`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `admin_kullanicilar`
--

DROP TABLE IF EXISTS `admin_kullanicilar`;
CREATE TABLE IF NOT EXISTS `admin_kullanicilar` (
  `admin_id` int NOT NULL AUTO_INCREMENT,
  `kullanici_adi` varchar(50) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `ad_soyad` varchar(255) DEFAULT NULL,
  `yetki_seviyesi` enum('standart','yonetici','superadmin') DEFAULT 'standart',
  `aktif` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `kullanici_adi` (`kullanici_adi`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `admin_kullanicilar`
--

INSERT INTO `admin_kullanicilar` (`admin_id`, `kullanici_adi`, `sifre`, `ad_soyad`, `yetki_seviyesi`, `aktif`) VALUES
(1, 'admin1', 'adminpass1', 'Mehmet Kaya', 'yonetici', 1),
(2, 'superadmin', 'superpass', 'Fatma Özkan', 'superadmin', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kitaplar`
--

DROP TABLE IF EXISTS `kitaplar`;
CREATE TABLE IF NOT EXISTS `kitaplar` (
  `kitap_id` int NOT NULL AUTO_INCREMENT,
  `kitap_adi` varchar(255) NOT NULL,
  `yazar` varchar(255) DEFAULT NULL,
  `yayin_tarihi` date DEFAULT NULL,
  `fiyat` decimal(10,2) DEFAULT NULL,
  `indirim_orani` decimal(5,2) DEFAULT '0.00',
  `stok_sayisi` int DEFAULT '0',
  `kategori_id` int DEFAULT NULL,
  `yayin_evi` varchar(255) DEFAULT NULL,
  `sayfa_sayisi` int DEFAULT NULL,
  `dil` varchar(50) DEFAULT 'Türkçe',
  `aciklama` text,
  `kapak_resmi` varchar(255) DEFAULT NULL,
  `eklenme_tarihi` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`kitap_id`),
  KEY `kategori_id` (`kategori_id`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `kitaplar`
--

INSERT INTO `kitaplar` (`kitap_id`, `kitap_adi`, `yazar`, `yayin_tarihi`, `fiyat`, `indirim_orani`, `stok_sayisi`, `kategori_id`, `yayin_evi`, `sayfa_sayisi`, `dil`, `aciklama`, `kapak_resmi`, `eklenme_tarihi`) VALUES
(4, 'Felsefenin Tesellisi', 'Boethius', '0524-01-01', 100.00, 0.00, 15, 4, 'Alfa Yayınları', 240, 'Türkçe', 'Felsefenin yaşamın zorluklarına etkisi.', 'Kitap Kapak Resimleri/felsefenin tesellisi.jpeg', '2024-12-21 12:14:05'),
(5, 'Savaş ve Barış', 'Lev Tolstoy', '1869-01-01', 50.00, 10.00, 100, 1, 'Penguin Books', 1225, 'Türkçe', 'Tolstoy\'un bu başyapıtı, Fransız işgali altındaki Rusya\'da geçen bir aşk ve savaş hikayesini anlatmaktadır.', 'Kitap Kapak Resimleri\\savaş ve barış.jpeg', '2024-12-20 21:00:00'),
(6, '1984', 'George Orwell', '1949-06-08', 39.99, 15.00, 120, 2, 'Harcourt', 328, 'İngilizce', 'Orwell\'in distopik romanı, totaliter bir rejim altındaki bireysel özgürlüklerin kaybını ele alır.', 'Kitap Kapak Resimleri/1984.jpeg', '2024-12-20 21:00:00'),
(7, 'Suç ve Ceza', 'Fyodor Dostoyevski', '1866-01-01', 100.00, 20.00, 150, 3, 'The Russian Messenger', 430, 'Türkçe', 'Dostoyevski’nin en önemli eserlerinden biri, suç ve cezanın felsefi boyutlarını derinlemesine inceler.', 'Kitap Kapak Resimleri\\suç ve ceza.jpeg', '2024-12-20 21:00:00'),
(8, 'Yüzüklerin Efendisi: Şövalye', 'J.R.R. Tolkien', '1954-07-29', 54.99, 10.00, 200, 1, 'George Allen & Unwin', 423, 'Türkçe', 'Orta Dünya\'da geçen destansı bir hikaye, dostluk, kahramanlık ve fedakarlığı anlatan bir macera.', 'Kitap Kapak Resimleri\\yüzüklerin efendisi.jpeg', '2024-12-20 21:00:00'),
(9, 'Harry Potter ve Felsefe Taşı', 'J.K. Rowling', '1997-06-26', 29.99, 30.00, 80, 2, 'Bloomsbury', 309, 'İngilizce', 'Harry Potter’ın Hogwarts’taki ilk yılına dair maceraları anlatan büyülü bir hikaye.', 'Kitap Kapak Resimleri\\Hp felsefe taşı.jpeg', '2024-12-20 21:00:00'),
(10, 'Kürk Mantolu Madonna', 'Sabahattin Ali', '1943-01-01', 25.99, 12.00, 250, 3, 'Varlık Yayınları', 144, 'Türkçe', 'Sabahattin Ali’nin en bilinen eserlerinden biri, aşk, yalnızlık ve insan ruhunun derinliklerini işler.', 'Kitap Kapak Resimleri\\kürk mantolu madonna.jpeg', '2024-12-20 21:00:00'),
(11, 'Don Kişot', 'Miguel de Cervantes', '1605-01-16', 41.99, 18.00, 150, 1, 'Francisco de Robles', 1056, 'İspanyolca', 'Don Kişot, bir şövalyenin hayallerinin peşinden sürükleyişini ve toplum eleştirisini konu alır.', 'Kitap Kapak Resimleri\\don kişot.jpeg', '2024-12-20 21:00:00'),
(12, 'Germinal', 'Émile Zola', '1885-03-25', 32.99, 25.00, 200, 2, 'Charpentier', 490, 'Fransızca', 'Zola, bu romanında işçi sınıfının yaşam koşullarını ve grevdeki mücadelelerini anlatır.', 'Kitap Kapak Resimleri\\germinal.jpeg', '2024-12-20 21:00:00'),
(13, 'Fahrenheit 451', 'Ray Bradbury', '1953-10-19', 28.99, 20.00, 100, 3, 'Ballantine Books', 249, 'İngilizce', 'Bradbury\'nin distopik romanı, kitapların yasaklandığı bir dünyada bireylerin özgürlük mücadelesini konu alır.', 'Kitap Kapak Resimleri\\fahrenheit 451.jpeg', '2024-12-20 21:00:00'),
(14, 'Bir Gün', 'David Nicholls', '2009-07-04', 39.99, 18.00, 150, 1, 'Hodder & Stoughton', 437, 'İngilizce', 'İki gencin yıllar süren dostluk ve aşk hikayesini anlatan romantik bir eser.', 'Kitap Kapak Resimleri\\bir gün.jpeg', '2024-12-20 21:00:00'),
(15, 'İstanbul Hatırası', 'Ahmet Ümit', '2014-04-01', 34.99, 15.00, 170, 2, 'Everest Yayınları', 508, 'Türkçe', 'Ahmet Ümit, İstanbul’da geçen bir cinayet ve kaybolan bir kadının hikayesini anlatıyor.', 'Kitap Kapak Resimleri\\istanbul hatırası.jpeg', '2024-12-20 21:00:00'),
(16, 'Küçük Prens', 'Antoine de Saint-Exupéry', '1943-04-06', 27.99, 10.00, 200, 3, 'Reynal & Hitchcock', 96, 'Fransızca', 'Küçük Prens, aşk, dostluk ve hayat üzerine öğretiler sunan bir masal.', 'Kitap Kapak Resimleri\\küçük prens.jpeg', '2024-12-20 21:00:00'),
(17, 'Meydan Okuma', 'Stephen King', '1987-05-08', 49.99, 5.00, 250, 1, 'Viking Penguin', 688, 'İngilizce', 'Stephen King’in romanı, çocukluğun ve korkuların iç içe geçtiği, doğaüstü bir hikayeyi anlatır.', 'Kitap Kapak Resimleri\\moby dick.jpeg', '2024-12-20 21:00:00'),
(18, 'Zalim İstanbul', 'Mehmet Eroğlu', '2002-05-01', 19.99, 20.00, 180, 2, 'Doğan Kitap', 452, 'Türkçe', 'Mehmet Eroğlu’nun İstanbul’un gece hayatını ve bireylerin yaşam mücadelelerini anlattığı romanı.', 'Kitap Kapak Resimleri\\zalim istanbul.jpeg', '2024-12-20 21:00:00'),
(19, 'Bülbülü Öldürmek', 'Harper Lee', '1960-07-11', 32.99, 10.00, 220, 3, 'J.B. Lippincott & Co.', 281, 'İngilizce', 'Amerikan Güney’inde ırkçılık ve adalet üzerine derinlemesine bir roman.', 'Kitap Kapak Resimleri\\bülbülü öldürmek.jpeg', '2024-12-20 21:00:00'),
(20, 'Simyacı', 'Paulo Coelho', '1988-11-01', 26.99, 5.00, 300, 1, 'HarperOne', 208, 'Portekizce', 'Paulo Coelho’nun en ünlü romanı, kişisel yolculuk ve kaderi keşfetmek üzerine bir hikaye.', 'Kitap Kapak Resimleri\\simyacı.jpeg', '2024-12-20 21:00:00'),
(21, 'Kötü Çocuk', 'Maria Goodin', '2012-09-01', 22.99, 12.00, 250, 2, 'HarperCollins', 367, 'İngilizce', 'Bir insanın en kötü yüzüyle yüzleşmesi ve kendini keşfetmesi üzerine bir hikaye.', 'Kitap Kapak Resimleri\\kötü çocuk.jpeg', '2024-12-20 21:00:00'),
(22, 'Gece Yarısı Kütüphanesi', 'Matt Haig', '2020-08-13', 35.99, 20.00, 150, 3, 'Canongate Books', 300, 'İngilizce', 'Gece yarısı kütüphanesinde geçen fantastik bir yolculuk hikayesi.', 'Kitap Kapak Resimleri\\gece yarısı kütüphanesi.jpeg', '2024-12-20 21:00:00'),
(23, 'Sapiens: A Brief History of Humankind', 'Yuval Noah Harari', '2011-01-01', 45.99, 15.00, 200, 1, 'Harvill Secker', 443, 'İngilizce', 'Harari\'nin, insan türünün evrimini ve tarihini anlatan ünlü eseri.', 'Kitap Kapak Resimleri\\sapiens.jpeg', '2024-12-20 21:00:00'),
(24, 'Şeker Portakalı', 'Jose Mauro de Vasconcelos', '1968-04-01', 23.99, 10.00, 180, 2, 'Ática', 160, 'Portekizce', 'Bir çocuğun yoksulluk ve masumiyet arasındaki yolculuğu.', 'Kitap Kapak Resimleri\\şeker portakalı.jpeg', '2024-12-20 21:00:00'),
(25, 'Moby Dick', 'Herman Melville', '1851-10-18', 41.99, 5.00, 100, 3, 'Harper & Brothers', 635, 'İngilizce', 'Büyük beyaz balina Moby Dick’i avlama hikayesi ve denizci bir adamın takıntısı.', 'Kitap Kapak Resimleri\\moby dick.jpeg', '2024-12-20 21:00:00'),
(26, 'Anna Karenina', 'Lev Tolstoy', '1878-04-01', 49.99, 10.00, 150, 1, 'The Russian Messenger', 864, 'Türkçe', 'Tolstoy\'un aşk, ihanet ve toplum üzerine derinlemesine bir romanı.', 'Kitap Kapak Resimleri\\anna karennina.jpeg', '2024-12-20 21:00:00'),
(27, 'Ölü Canlar', 'Albert Camus', '1947-01-01', 38.99, 15.00, 200, 2, 'Gallimard', 154, 'Fransızca', 'Camus\'nun felsefi bir drama eseri olan ve ölüm cezası üzerine yazılmış derin bir inceleme.', 'Kitap Kapak Resimleri\\Ölü Canlar.jpg', '2024-12-20 21:00:00'),
(28, 'Fahrenheit 451', 'Ray Bradbury', '1953-10-19', 24.99, 18.00, 90, 3, 'Ballantine Books', 249, 'İngilizce', 'Kitapların yasaklandığı bir dünyada, bir itfaiyecinin gerçeği arayış hikayesi.', 'Kitap Kapak Resimleri\\fahrenheit 451.jpeg', '2024-12-20 21:00:00'),
(29, 'Yüzyıllık Yalnızlık', 'Gabriel García Márquez', '1967-06-05', 53.99, 10.00, 130, 1, 'Editorial Sudamericana', 417, 'İspanyolca', 'Latin Amerika edebiyatının başyapıtlarından biri, aşk, yalnızlık ve kader üzerine bir roman.', 'Kitap Kapak Resimleri\\yüzyıllık yanlızlık.jpeg', '2024-12-20 21:00:00'),
(30, 'Arzu tramvayı', 'Tennessee Williams', '1955-09-01', 37.99, 25.00, 170, 2, 'New Directions', 331, 'İngilizce', 'Tennessee Williams’ın trajikomik eseri, toplumsal ve kişisel çöküşü anlatır.', 'Kitap Kapak Resimleri\\arzu tramvayı.jpg', '2024-12-20 21:00:00'),
(31, 'Kayıp Zamanın İzinde', 'Marcel Proust', '0000-00-00', 69.99, 5.00, 250, 3, 'Grasset', 4215, 'Fransızca', 'Proust’un modern edebiyatın en önemli eserlerinden biri olan ve zamanın geçişini ele alan devasa romanı.', 'Kitap Kapak Resimleri\\kayıp zamanın izinde.png', '2024-12-20 21:00:00'),
(32, 'Baba ve Piç', 'Elif Şafak', '2006-02-01', 29.99, 15.00, 180, 1, 'Doğan Kitap', 464, 'Türkçe', 'Elif Şafak’ın, aile ilişkileri ve kimlik arayışını ele alan romanı.', 'Kitap Kapak Resimleri\\baba ve piç.jpeg', '2024-12-20 21:00:00'),
(33, 'Kurtlar Vadisi', 'Tuncer Çakır', '2005-11-15', 22.99, 10.00, 150, 2, 'Epsilon Yayınları', 320, 'Türkçe', 'Türk mafyasının yer altı dünyasını anlatan sürükleyici bir hikaye.', 'Kitap Kapak Resimleri\\kurtlar vadisi.jpeg', '2024-12-20 21:00:00'),
(34, 'Felsefenin Kısa Tarihi', 'Nigel Warburton', '2004-05-01', 34.99, 20.00, 120, 3, 'Norton', 192, 'İngilizce', 'Felsefenin tarihini kısa ama öz bir şekilde ele alan bilgilendirici bir eser.', 'Kitap Kapak Resimleri\\felsefenin kısa tarihi.jpeg', '2024-12-20 21:00:00'),
(35, 'Bir Delinin Hatıra Defteri', 'Nikolay Gogol', '1835-01-01', 21.99, 15.00, 200, 1, 'Tyrrell & Foster', 160, 'Türkçe', 'Gogol\'un, deliliği ve toplumun bireye karşı tutumunu irdeleyen kısa romanı.', 'Kitap Kapak Resimleri\\Ölü Canlar.jpg', '2024-12-20 21:00:00'),
(36, 'Sinekler ve İnsanlar', 'John Steinbeck', '1937-02-06', 27.99, 10.00, 180, 2, 'Covici Friede', 112, 'İngilizce', 'İki işçinin Amerika’nın Büyük Buhran dönemindeki yaşam mücadelesini anlatan kısa bir roman.', 'Kitap Kapak Resimleri\\sinekler ve insanlar.jpeg', '2024-12-20 21:00:00'),
(37, 'Bütün İnsanlar Kardeştir', 'Albert Camus', '1948-09-01', 37.99, 5.00, 250, 3, 'Gallimard', 512, 'Fransızca', 'Camus\'nun, insanlık ve kardeşlik üzerine düşündürdüğü derin bir eser.', 'Kitap Kapak Resimleri\\Bütün İnsanlar Kardeştir.jpeg', '2024-12-20 21:00:00'),
(38, 'Sonsuz Aşk', 'Khaled Hosseini', '2013-05-21', 42.99, 15.00, 150, 1, 'Riverhead Books', 421, 'İngilizce', 'Afgan bir ailenin yıllar süren dramı ve aşk hikayesinin anlatıldığı etkileyici bir roman.', 'Kitap Kapak Resimleri\\sonsuz aşk.jpeg', '2024-12-20 21:00:00'),
(39, 'Frankenstein', 'Mary Shelley', '1818-01-01', 24.99, 10.00, 300, 2, 'Lackington, Hughes', 280, 'İngilizce', 'Shelley\'nin, bilim ve doğa üstü olayları bir araya getirdiği korku klasiği.', 'Kitap Kapak Resimleri\\frankenstein.jpeg', '2024-12-20 21:00:00'),
(40, 'Aşk', 'Elif Şafak', '2009-10-01', 39.99, 5.00, 220, 3, 'Doğan Kitap', 465, 'Türkçe', 'Aşkın farklı boyutlarını ve kültürlerarası farkları inceleyen derin bir roman.', 'Kitap Kapak Resimleri\\aşk.jpeg', '2024-12-20 21:00:00'),
(41, 'Yüzyılın Prensesi', 'Benedict Jacka', '2012-01-10', 33.99, 20.00, 170, 1, 'Ace', 320, 'İngilizce', 'Genç bir kadının büyü gücüyle mücadelesini anlatan fantastik bir roman.', 'Kitap Kapak Resimleri\\yüzyılın prensesi.jpeg', '2024-12-20 21:00:00'),
(42, 'İntikam', 'Patricia Highsmith', '1957-07-01', 29.99, 10.00, 200, 2, 'HarperCollins', 345, 'İngilizce', 'Bir intikam hikayesi ve suç psikolojisini derinlemesine işleyen bir gerilim romanı.', 'Kitap Kapak Resimleri\\intikam.jpeg', '2024-12-20 21:00:00'),
(43, 'Mavi Çocuk', 'Emine Işınsu', '1991-06-12', 21.99, 18.00, 150, 3, 'Yapı Kredi Yayınları', 312, 'Türkçe', 'İnsan ruhunun derinliklerine inen bir roman.', 'Kitap Kapak Resimleri\\mavi çocuk.jpeg', '2024-12-20 21:00:00'),
(44, 'Kayıp', 'Haruki Murakami', '2002-06-01', 43.99, 5.00, 100, 1, 'Knopf', 381, 'Japonca', 'Murakami\'nin, kaybolan bir insanın arayışını ve hayatın gizemli yönlerini ele alan romanı.', 'Kitap Kapak Resimleri\\kayıp.jpeg', '2024-12-20 21:00:00'),
(45, 'Şair', 'Rainer Maria Rilke', '1929-01-01', 25.99, 20.00, 200, 2, 'Kraft & Berthold', 368, 'Almanca', 'Rilke\'nin, şairlik ve yaratıcı süreç üzerine yazdığı derin bir düşünsel metin.', 'Kitap Kapak Resimleri\\şair.jpeg', '2024-12-20 21:00:00'),
(46, 'Büyü', 'Lev Grossman', '2009-08-11', 32.99, 10.00, 180, 3, 'Viking Press', 404, 'İngilizce', 'Bir grup gencin büyü yapma dünyasına girmeleri ve mücadeleleri üzerine fantastik bir roman.', 'Kitap Kapak Resimleri\\büyü.jpeg', '2024-12-20 21:00:00'),
(47, 'Uçurtma Avcısı', 'Khaled Hosseini', '2003-05-29', 39.99, 12.00, 250, 1, 'Riverhead Books', 371, 'İngilizce', 'Afganistan’daki savaşın etkileriyle büyüyen iki çocuğun hikayesi.', 'Kitap Kapak Resimleri\\uçurtma avcısı.jpeg', '2024-12-20 21:00:00'),
(48, 'Amerikan Tanrıları', 'Neil Gaiman', '2001-06-19', 49.99, 10.00, 150, 2, 'HarperCollins', 635, 'İngilizce', 'Mitoloji ve modern dünyayı harmanlayan, tanrıların savaşı üzerine fantastik bir roman.', 'Kitap Kapak Resimleri\\amerikan tanrıları.jpeg', '2024-12-20 21:00:00'),
(49, 'Zambaklar ve Güller', 'Brittany Cavallaro', '2016-04-15', 29.99, 15.00, 200, 3, 'Katherine Tegen Books', 356, 'İngilizce', 'Aşk, dram ve gizemi harmanlayan etkileyici bir gençlik romanı.', 'Kitap Kapak Resimleri\\zambaklar ve güller.jpeg', '2024-12-20 21:00:00'),
(52, 'BJK', 'mehmet', '2024-12-28', 1.00, 0.00, 1, 2, 'mehmet', 1, 'türükçe', 'beşiktaş tarihi', 'Kitap Kapak Resimleri/images.jpeg', '2024-12-28 11:04:47'),
(55, 'Galatasaray', 'metin oktay', '1905-05-05', 100.00, 0.00, 100, 1, 'Gsstore', 1905, 'türükçe', 'Galatasaray ın kuruluş hikayesi', 'Kitap Kapak Resimleri/wallpaperflare.com_wallpaper.jpg', '2024-12-28 11:13:26');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kredikartbilgileri`
--

DROP TABLE IF EXISTS `kredikartbilgileri`;
CREATE TABLE IF NOT EXISTS `kredikartbilgileri` (
  `kart_id` int NOT NULL AUTO_INCREMENT,
  `kartNumarasi` char(16) NOT NULL,
  `sonKullanmaTarihi` char(6) NOT NULL,
  `kartSahibiAdi` varchar(255) NOT NULL,
  `cvv` char(3) NOT NULL,
  `kullanici_id` int DEFAULT NULL,
  PRIMARY KEY (`kart_id`),
  KEY `fk_kullanici_id_kredi` (`kullanici_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `kredikartbilgileri`
--

INSERT INTO `kredikartbilgileri` (`kart_id`, `kartNumarasi`, `sonKullanmaTarihi`, `kartSahibiAdi`, `cvv`, `kullanici_id`) VALUES
(1, '1234567812345678', '200012', 'ljdfnhdjkfna', '322', NULL),
(2, '1234567812345678', '1212', 'fnkgkdzjfgbkdf', '123', NULL),
(3, '1234567812345678', '1212', 'fnkgkdzjfgbkdf', '123', NULL),
(4, '1234567812345678', '12221', 'mehe gs<hsj', '122', NULL),
(5, '1111111111111111', '11111', 'mehmet', '111', NULL),
(6, '1111111111111111', '11111', 'mehmet', '111', NULL),
(7, '2222222222222222', '2222', 'polat', '222', NULL),
(8, '2222222222222222', '2222', 'polat', '222', NULL),
(9, '3333333333333333', '333333', 'ataş', '333', NULL),
(10, '3333333333333333', '333333', 'ataş', '333', 1),
(11, '4444444444444444', '4444', 'nur', '444', 1),
(12, '3435 4365 4654 7', '12/25', 'mehmetnur ataş', '123', 1),
(13, '1234 4567 8766 5', '12/25', 'mehmet atas', '123', 1),
(14, '3212 2343 5346 4', '12/25', 'myyu can', '122', 1),
(15, '1234567812345678', '023211', 'mefnfsnkd jnenlwks', '322', NULL),
(16, '1234 4354 4655 4', '12/25', 'mehmet atas', '123', 1),
(17, '1234 5678 1234 5', '12/25', 'metin oktay', '123', 1),
(18, '1905190510905190', '202504', 'metin oktay', '123', 1),
(19, '1903190319031903', '203512', 'sergen yalcin', '193', 1),
(20, '4567 9809 0569 8', '11/27', 'kefsldngfd', '234', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanicilar`
--

DROP TABLE IF EXISTS `kullanicilar`;
CREATE TABLE IF NOT EXISTS `kullanicilar` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(191) NOT NULL,
  `telefon_no` varchar(15) NOT NULL,
  `ad_soyad` varchar(255) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_admin` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `kullanicilar`
--

INSERT INTO `kullanicilar` (`id`, `email`, `telefon_no`, `ad_soyad`, `sifre`, `created_at`, `is_admin`) VALUES
(1, 'admin1@gamil.com', '5312131131', 'mehmet', '$2y$10$aP98ZnpWrmTupX0usR0kQe4hMyHsEOd3gRDyY/GowlU3DYYphEacG', '2025-01-14 23:15:51', 1),
(2, 'preminyum@gmail.com', '5312131131', 'musa', '$2y$10$ZCVmLUV2wJ5.qRe3dAXjW.TzMJ39.vFmEecA22Uv3PwhNsHOYJG5O', '2025-01-14 23:39:15', 0),
(3, 'admin@gamil.com', '5312131131', 'ali', '$2y$10$SM1VIo4RsdBDvzxBlMemWuST6QrXS6D3YIem0olDje7akuDNRjDd2', '2025-01-15 00:37:30', 1),
(4, 'mehmet@gmail.com', '1235553524', 'mehmet', '$2y$10$eFj126xrm2kzJtc7uY2dEOC9QvV5pAW7B0zQKMdYXULuINEM.4vDS', '2025-02-20 08:11:28', 0),
(5, 'adminmehmet@gamil.com', '5312131131', 'mehmet', '$2y$10$lzl/vF5e5EI6IdI6RcPD0uwpP2gBxUPU7t5gBmGuEkGvV3FYMid3a', '2025-02-20 08:13:32', 0);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sesli_kitaplar`
--

DROP TABLE IF EXISTS `sesli_kitaplar`;
CREATE TABLE IF NOT EXISTS `sesli_kitaplar` (
  `kitap_id` int NOT NULL AUTO_INCREMENT,
  `sure` varchar(20) DEFAULT NULL,
  `seslendiren` varchar(255) DEFAULT NULL,
  `dosya_yolu` varchar(255) DEFAULT NULL,
  `format` varchar(50) DEFAULT 'MP3',
  `boyut` decimal(10,2) DEFAULT NULL,
  `kitap_ozet` text,
  `kitap_adi` varchar(255) DEFAULT NULL,
  `kapak_resmi` varchar(255) DEFAULT NULL,
  `eklenme_tarihi` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ucret` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`kitap_id`),
  KEY `kitap_id` (`kitap_id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `sesli_kitaplar`
--

INSERT INTO `sesli_kitaplar` (`kitap_id`, `sure`, `seslendiren`, `dosya_yolu`, `format`, `boyut`, `kitap_ozet`, `kitap_adi`, `kapak_resmi`, `eklenme_tarihi`, `ucret`) VALUES
(2, '10 saat', 'Funda Eryiğit', '/audio/1984.mp3', 'MP3', 120.00, NULL, 'deneme2', 'Kitap Kapak Resimleri\\Savaş ve Barış.jpeg', '2025-01-05 17:35:04', 0.00),
(1, '13', 'mehmet polat', '/audio/santıranc.jpg', 'djosnk', 123.00, 'SANTIRANÇ KURALARI', 'SANTIRANÇ', 'Kitap Kapak Resimleri/satranc.jpeg', '2025-01-05 17:35:04', 0.00),
(3, '130', 'mehmet polat', 'mp3', '12', 123.00, 'k.snf b.lsc', 'kafamda bir tuhaflık', 'uploads/kafamda bir tuhaflık.jpeg', '2025-01-05 17:35:04', 100.00),
(4, '34', 'mehmet polat', '21', '12', 32.00, 'gece', 'gece', 'uploads/bir delinin hatıra defteri.jpeg', '2025-01-05 17:35:04', 0.00),
(5, '1907', 'alpcan Şen', '21', '12', 19.07, 'gece\r\nVolkan Demirel: Kaleden Liderliğe Uzanan Bir Kariyer\r\nVolkan Demirel, 27 Ekim 1981\'de İstanbul\'da dünyaya geldi. Türk futbolunun en muhteşem kalecilerinden biri olan Volkan, kariyerinin büyük bir kısmı Fenerbahçe\'de geçirerek sarı-lacivertli kulüp efsaneleri arasında yer aldı. Uzun yıllar boyunca hem kulüp hem de milli takım düzeyinde başarılar elde eden Volkan,\r\n\r\n', 'Volkan Demirel', 'uploads/volkan.jpeg', '2025-01-05 17:35:04', 50.00),
(6, '1903', 'mehmet polat', 'mp3', '12', 193.00, 'Ricardo Quaresma\'nı\r\nİlk Transfer: Qua\r\nTeknik Özellikler:Netrivela vuruşları\r\nBaşarılar: Beşiktaş forması\r\nTaraftarla İlişkisi: Quaresma,\r\nDönüş: İlk ayrılığın ardından 2015\'te tekrar Beşiktaş\'a döndü ve ikinci dönemde ilk kez etkileyici bir performans sergiledi.\r\nUnutulmaz Anılar\r\nTrivela Golleri: UEFA Avrupa Ligi\'ndeki Club Brugge ve Porto gibi maçlarda atılan trivela golleri, futbolda ilerledi.\r\nSahanın İçindeki Tutkusu: Sahada hem oyunuyla hem de zaman zaman rakiplerle olan sert mücadelesiyle dikkat çekti. Bu durum bazen kart görmesine neden olsa da tutkusunun bir göstergesi olarak görülüyordu.', 'Ricardo Quaresma', 'uploads/q7.jpeg', '2025-01-11 19:02:14', 19.03),
(7, '34', 'mehmet nur ataş', 'mp3', '12', 1905.00, 'Galatasaray Dönemi: Yeni Bir Efsanenin Doğuşu\r\n2011 yılında ise Galatasaray teknik olarak Fatih Terim\'in iradesiyle Fernando Muslera, Lazio\'dan Galatasaray\'a transfer oldu. Muslera\'nın Galatasaray kariyeri Türk futbolunda yeni bir dönemin başlangıcını temsil ediyordu.\r\nGalatasaray formasıyla ilk sezonunda Süper Lig şampiyonluğu yaşayan Muslera, bu yıl içinde birçok kupa ile taçlandırıldı. Özellikle kritik maçlardaki kurtarışları ve sakinleşmesi, onu taraftarların gözünde bir efsane haline getirdi.\r\n\r\nGalatasaray formasıyla birçok başarıya imza atan Muslera\'nın kariyerindeki en dikkat çekici anlardan bazıları:\r\n\r\nSüper Lig Şampiyonlukları (2012, 2013, 2015, 2018, 2019): Muslera, Galatasaray\'la toplamda beş Süper Lig şampiyonluğu yaşadı. Takımının onun zaferindeki başarılarıyla ön plana çıktı.\r\nTürkiye Kupası ve Süper Kupa Şampiyonlukları: Muslera, Türkiye Kupası ve Süper Kupa şampiyonluklarında yaptığı kurtarışlarla takıma birçok zafer kazandırdı.\r\nDerbilerdeki Performansı: F.Bahçe ve Beşiktaş gibi güçlü rakiplere karşı oynanan kritik maçlarda, Galatasaray kalesini koruma performansını muhteşem yaptı.\r\n', 'Muslera', 'uploads/muslera.jpg', '2025-01-05 17:35:04', 19.05),
(8, '13', 'mehmet polat', 'fjnkj', '12', 13.00, 'jdbasJH', 'Galatasaray', 'Kitap Kapak Resimleri/wallpaperflare.com_wallpaper.jpg', '2025-01-05 17:35:04', 0.00),
(9, '1905', 'mauro icardi', '', 'MP3', 1905.00, 'GALATASARA ŞANLI TARİHİ', 'Galatasaray', 'Kitap Kapak Resimleri/wallpaperflare.com_wallpaper.jpg', '2025-01-05 14:42:44', 100.00),
(10, '13', 'mehmet polat', 'MP3', '12', 1905.00, 'deneme', '1984', 'Kitap Kapak Resimleri/1984.jpeg', '2025-01-11 12:19:54', 100.00),
(16, '10 saat', 'Funda Eryiğit', '/audio/1984.mp3', 'MP3', 120.00, NULL, 'deneme2', 'Kitap Kapak Resimleri\\Savaş ve Barış.jpeg', '2025-01-05 17:35:04', 0.00),
(17, '130', 'mehmet polat', 'mp3', '12', 123.00, 'k.snf b.lsc', 'kafamda bir tuhaflık', 'uploads/kafamda bir tuhaflık.jpeg', '2025-01-05 17:35:04', 100.00),
(15, '13', 'mehmet polat', '/audio/santıranc.jpg', 'djosnk', 123.00, 'SANTIRANÇ KURALARI', 'SANTIRANÇ', 'Kitap Kapak Resimleri/satranc.jpeg', '2025-01-05 17:35:04', 0.00),
(18, '34', 'mehmet nur ataş', 'mp3', '12', 1905.00, 'Galatasaray Dönemi: Yeni Bir Efsanenin Doğuşu\r\n2011 yılında ise Galatasaray teknik olarak Fatih Terim\'in iradesiyle Fernando Muslera, Lazio\'dan Galatasaray\'a transfer oldu. Muslera\'nın Galatasaray kariyeri Türk futbolunda yeni bir dönemin başlangıcını temsil ediyordu.\r\nGalatasaray formasıyla ilk sezonunda Süper Lig şampiyonluğu yaşayan Muslera, bu yıl içinde birçok kupa ile taçlandırıldı. Özellikle kritik maçlardaki kurtarışları ve sakinleşmesi, onu taraftarların gözünde bir efsane haline getirdi.\r\n\r\nGalatasaray formasıyla birçok başarıya imza atan Muslera\'nın kariyerindeki en dikkat çekici anlardan bazıları:\r\n\r\nSüper Lig Şampiyonlukları (2012, 2013, 2015, 2018, 2019): Muslera, Galatasaray\'la toplamda beş Süper Lig şampiyonluğu yaşadı. Takımının onun zaferindeki başarılarıyla ön plana çıktı.\r\nTürkiye Kupası ve Süper Kupa Şampiyonlukları: Muslera, Türkiye Kupası ve Süper Kupa şampiyonluklarında yaptığı kurtarışlarla takıma birçok zafer kazandırdı.\r\nDerbilerdeki Performansı: F.Bahçe ve Beşiktaş gibi güçlü rakiplere karşı oynanan kritik maçlarda, Galatasaray kalesini koruma performansını muhteşem yaptı.\r\n', 'Muslera', 'uploads/muslera.jpg', '2025-01-05 17:35:04', 19.05),
(19, '1905', 'mauro icardi', '', 'MP3', 1905.00, 'GALATASARA ŞANLI TARİHİ', 'Galatasaray', 'Kitap Kapak Resimleri/wallpaperflare.com_wallpaper.jpg', '2025-01-05 14:42:44', 100.00),
(20, '13', 'mehmet polat', 'MP3', '12', 1905.00, 'deneme', '1984', 'Kitap Kapak Resimleri/1984.jpeg', '2025-01-11 12:19:54', 100.00),
(21, '13', 'mehmet polat', '/audio/santıranc.jpg', 'djosnk', 123.00, 'SANTIRANÇ KURALARI', 'SANTIRANÇ', 'Kitap Kapak Resimleri/satranc.jpeg', '2025-01-05 17:35:04', 0.00),
(22, '10 saat', 'Funda Eryiğit', '/audio/1984.mp3', 'MP3', 120.00, NULL, 'deneme2', 'Kitap Kapak Resimleri\\Savaş ve Barış.jpeg', '2025-01-05 17:35:04', 0.00),
(23, '130', 'mehmet polat', 'mp3', '12', 123.00, 'k.snf b.lsc', 'kafamda bir tuhaflık', 'uploads/kafamda bir tuhaflık.jpeg', '2025-01-05 17:35:04', 100.00),
(24, '34', 'mehmet nur ataş', 'mp3', '12', 1905.00, 'Galatasaray Dönemi: Yeni Bir Efsanenin Doğuşu\r\n2011 yılında ise Galatasaray teknik olarak Fatih Terim\'in iradesiyle Fernando Muslera, Lazio\'dan Galatasaray\'a transfer oldu. Muslera\'nın Galatasaray kariyeri Türk futbolunda yeni bir dönemin başlangıcını temsil ediyordu.\r\nGalatasaray formasıyla ilk sezonunda Süper Lig şampiyonluğu yaşayan Muslera, bu yıl içinde birçok kupa ile taçlandırıldı. Özellikle kritik maçlardaki kurtarışları ve sakinleşmesi, onu taraftarların gözünde bir efsane haline getirdi.\r\n\r\nGalatasaray formasıyla birçok başarıya imza atan Muslera\'nın kariyerindeki en dikkat çekici anlardan bazıları:\r\n\r\nSüper Lig Şampiyonlukları (2012, 2013, 2015, 2018, 2019): Muslera, Galatasaray\'la toplamda beş Süper Lig şampiyonluğu yaşadı. Takımının onun zaferindeki başarılarıyla ön plana çıktı.\r\nTürkiye Kupası ve Süper Kupa Şampiyonlukları: Muslera, Türkiye Kupası ve Süper Kupa şampiyonluklarında yaptığı kurtarışlarla takıma birçok zafer kazandırdı.\r\nDerbilerdeki Performansı: F.Bahçe ve Beşiktaş gibi güçlü rakiplere karşı oynanan kritik maçlarda, Galatasaray kalesini koruma performansını muhteşem yaptı.\r\n', 'Muslera', 'uploads/muslera.jpg', '2025-01-05 17:35:04', 19.05),
(25, '1905', 'mauro icardi', '', 'MP3', 1905.00, 'GALATASARA ŞANLI TARİHİ', 'Galatasaray', 'Kitap Kapak Resimleri/wallpaperflare.com_wallpaper.jpg', '2025-01-05 14:42:44', 100.00),
(26, '13', 'mehmet polat', 'MP3', '12', 1905.00, 'deneme', '1984', 'Kitap Kapak Resimleri/1984.jpeg', '2025-01-11 12:19:54', 100.00);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sesli_kitaplarsiparisler`
--

DROP TABLE IF EXISTS `sesli_kitaplarsiparisler`;
CREATE TABLE IF NOT EXISTS `sesli_kitaplarsiparisler` (
  `siparis_id` int NOT NULL AUTO_INCREMENT,
  `kullanici_id` int NOT NULL,
  `kitap_id` int NOT NULL,
  `kitap_adi` varchar(255) NOT NULL,
  `sure` int NOT NULL,
  `ucret` decimal(10,2) NOT NULL,
  `siparis_tarihi` datetime NOT NULL,
  PRIMARY KEY (`siparis_id`),
  KEY `kitap_id` (`kitap_id`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `sesli_kitaplarsiparisler`
--

INSERT INTO `sesli_kitaplarsiparisler` (`siparis_id`, `kullanici_id`, `kitap_id`, `kitap_adi`, `sure`, `ucret`, `siparis_tarihi`) VALUES
(1, 1, 1, 'SANTIRANÇ', 13, 0.00, '2025-01-15 00:25:17'),
(2, 1, 2, 'deneme2', 10, 0.00, '2025-01-15 00:25:17'),
(3, 1, 6, 'Ricardo Quaresma', 1903, 19.03, '2025-01-15 00:25:17'),
(4, 1, 7, 'Muslera', 34, 19.05, '2025-01-15 00:25:17'),
(5, 1, 1, 'SANTIRANÇ', 13, 0.00, '2025-01-15 01:44:16'),
(6, 1, 2, 'deneme2', 10, 0.00, '2025-01-15 01:44:16'),
(7, 1, 5, 'Volkan Demirel', 1907, 50.00, '2025-01-15 01:44:16'),
(8, 1, 6, 'Ricardo Quaresma', 1903, 19.03, '2025-01-15 01:44:16'),
(9, 1, 7, 'Muslera', 34, 19.05, '2025-01-15 01:44:16'),
(10, 1, 9, 'Galatasaray', 1905, 100.00, '2025-01-15 01:44:16'),
(11, 1, 10, '1984', 13, 100.00, '2025-01-15 01:44:16'),
(12, 1, 1, 'SANTIRANÇ', 13, 0.00, '2025-01-15 01:47:44'),
(13, 1, 2, 'deneme2', 10, 0.00, '2025-01-15 01:47:44'),
(14, 1, 5, 'Volkan Demirel', 1907, 50.00, '2025-01-15 01:47:44'),
(15, 1, 6, 'Ricardo Quaresma', 1903, 19.03, '2025-01-15 01:47:44'),
(16, 1, 7, 'Muslera', 34, 19.05, '2025-01-15 01:47:44'),
(17, 1, 9, 'Galatasaray', 1905, 100.00, '2025-01-15 01:47:44'),
(18, 1, 10, '1984', 13, 100.00, '2025-01-15 01:47:44'),
(19, 1, 2, 'deneme2', 10, 0.00, '2025-01-15 11:05:28'),
(20, 1, 3, 'kafamda bir tuhaflık', 130, 100.00, '2025-01-15 11:05:28'),
(21, 1, 3, 'kafamda bir tuhaflık', 130, 100.00, '2025-01-15 11:10:47'),
(22, 1, 3, 'kafamda bir tuhaflık', 130, 100.00, '2025-01-15 11:13:51'),
(23, 1, 3, 'kafamda bir tuhaflık', 130, 100.00, '2025-01-15 11:15:47'),
(24, 1, 3, 'kafamda bir tuhaflık', 130, 100.00, '2025-01-15 11:19:21'),
(25, 1, 2, 'deneme2', 10, 0.00, '2025-01-15 11:22:13'),
(26, 1, 3, 'kafamda bir tuhaflık', 130, 100.00, '2025-01-15 11:22:13'),
(27, 1, 2, 'deneme2', 10, 0.00, '2025-01-15 11:25:51'),
(28, 1, 3, 'kafamda bir tuhaflık', 130, 100.00, '2025-01-15 11:25:51'),
(29, 1, 2, 'deneme2', 10, 0.00, '2025-01-15 11:28:11'),
(30, 1, 3, 'kafamda bir tuhaflık', 130, 100.00, '2025-01-15 11:28:11'),
(31, 1, 2, 'deneme2', 10, 0.00, '2025-01-15 11:35:11'),
(32, 1, 3, 'kafamda bir tuhaflık', 130, 100.00, '2025-01-15 11:35:11'),
(33, 1, 4, 'gece', 34, 0.00, '2025-01-15 11:35:11'),
(34, 1, 2, 'deneme2', 10, 0.00, '2025-01-15 11:37:36'),
(35, 1, 3, 'kafamda bir tuhaflık', 130, 100.00, '2025-01-15 11:37:36'),
(36, 1, 4, 'gece', 34, 0.00, '2025-01-15 11:37:36'),
(37, 1, 2, 'deneme2', 10, 0.00, '2025-01-15 11:39:54'),
(38, 1, 3, 'kafamda bir tuhaflık', 130, 100.00, '2025-01-15 11:39:54'),
(39, 1, 4, 'gece', 34, 0.00, '2025-01-15 11:39:54'),
(40, 1, 2, 'deneme2', 10, 0.00, '2025-01-15 11:45:51'),
(41, 1, 3, 'kafamda bir tuhaflık', 130, 100.00, '2025-01-15 11:45:51'),
(42, 1, 4, 'gece', 34, 0.00, '2025-01-15 11:45:51'),
(43, 1, 2, 'deneme2', 10, 0.00, '2025-01-15 11:54:51'),
(44, 1, 3, 'kafamda bir tuhaflık', 130, 100.00, '2025-01-15 11:54:51'),
(45, 1, 4, 'gece', 34, 0.00, '2025-01-15 11:54:51'),
(46, 1, 2, 'deneme2', 10, 0.00, '2025-01-15 11:57:33'),
(47, 1, 3, 'kafamda bir tuhaflık', 130, 100.00, '2025-01-15 11:57:33'),
(48, 1, 4, 'gece', 34, 0.00, '2025-01-15 11:57:33');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `siparisler`
--

DROP TABLE IF EXISTS `siparisler`;
CREATE TABLE IF NOT EXISTS `siparisler` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kitap_id` int NOT NULL,
  `toplam_tutar` decimal(10,2) NOT NULL,
  `tarih` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `kullanici_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kitap_id` (`kitap_id`),
  KEY `fk_kullanici_id` (`kullanici_id`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `siparisler`
--

INSERT INTO `siparisler` (`id`, `kitap_id`, `toplam_tutar`, `tarih`, `kullanici_id`) VALUES
(1, 4, 196.98, '2025-01-14 18:57:41', NULL),
(2, 8, 196.98, '2025-01-14 18:57:41', NULL),
(3, 11, 196.98, '2025-01-14 18:57:41', NULL),
(4, 4, 196.98, '2025-01-14 18:58:03', NULL),
(5, 8, 196.98, '2025-01-14 18:58:03', NULL),
(6, 11, 196.98, '2025-01-14 18:58:03', NULL),
(7, 4, 196.98, '2025-01-14 19:01:51', NULL),
(8, 8, 196.98, '2025-01-14 19:01:51', NULL),
(9, 11, 196.98, '2025-01-14 19:01:51', NULL),
(10, 4, 196.98, '2025-01-14 19:02:18', NULL),
(11, 8, 196.98, '2025-01-14 19:02:18', NULL),
(12, 11, 196.98, '2025-01-14 19:02:18', NULL),
(13, 44, 183.98, '2025-01-14 19:04:17', NULL),
(14, 47, 183.98, '2025-01-14 19:04:17', NULL),
(15, 55, 183.98, '2025-01-14 19:04:17', NULL),
(16, 44, 183.98, '2025-01-14 19:10:48', 1),
(17, 47, 183.98, '2025-01-14 19:10:48', 1),
(18, 55, 183.98, '2025-01-14 19:10:48', 1),
(19, 44, 183.98, '2025-01-14 19:11:19', 1),
(20, 47, 183.98, '2025-01-14 19:11:19', 1),
(21, 55, 183.98, '2025-01-14 19:11:19', 1),
(22, 44, 183.98, '2025-01-14 19:13:11', 1),
(23, 47, 183.98, '2025-01-14 19:13:11', 1),
(24, 55, 183.98, '2025-01-14 19:13:11', 1),
(25, 44, 183.98, '2025-01-14 19:13:43', 1),
(26, 47, 183.98, '2025-01-14 19:13:43', 1),
(27, 55, 183.98, '2025-01-14 19:13:43', 1),
(28, 4, 150.00, '2025-01-14 19:39:00', 1),
(29, 5, 150.00, '2025-01-14 19:39:00', 1),
(30, 4, 100.00, '2025-01-14 19:41:13', 1),
(31, 37, 37.99, '2025-01-14 19:42:47', 1),
(32, 4, 398.95, '2025-01-15 04:52:50', 1),
(33, 5, 398.95, '2025-01-15 04:52:50', 1),
(34, 6, 398.95, '2025-01-15 04:52:50', 1),
(35, 7, 398.95, '2025-01-15 04:52:50', 1),
(36, 9, 398.95, '2025-01-15 04:52:50', 1),
(37, 10, 398.95, '2025-01-15 04:52:50', 1),
(38, 18, 398.95, '2025-01-15 04:52:50', 1),
(39, 19, 398.95, '2025-01-15 04:52:50', 1),
(40, 8, 54.99, '2025-01-15 05:51:56', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urun_kategorileri`
--

DROP TABLE IF EXISTS `urun_kategorileri`;
CREATE TABLE IF NOT EXISTS `urun_kategorileri` (
  `kategori_id` int NOT NULL AUTO_INCREMENT,
  `kategori_adi` varchar(100) NOT NULL,
  `ust_kategori_id` int DEFAULT NULL,
  PRIMARY KEY (`kategori_id`),
  KEY `ust_kategori_id` (`ust_kategori_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `urun_kategorileri`
--

INSERT INTO `urun_kategorileri` (`kategori_id`, `kategori_adi`, `ust_kategori_id`) VALUES
(1, 'Roman', NULL),
(2, 'Bilim', NULL),
(3, 'Tarih', NULL),
(4, 'Felsefe', NULL),
(5, 'Çocuk Kitapları', NULL),
(6, 'Klasikler', 1),
(7, 'Bilim Kurgu', 2);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
