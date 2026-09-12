-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3306
-- Üretim Zamanı: 12 Eyl 2026, 14:29:21
-- Sunucu sürümü: 10.5.29-MariaDB-cll-lve-log
-- PHP Sürümü: 8.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `myquran_myquran`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `tbl_meal`
--

CREATE TABLE `tbl_meal` (
  `mealID` bigint(20) NOT NULL,
  `Lisan` varchar(50) DEFAULT NULL,
  `mealAdi` varchar(100) DEFAULT NULL,
  `mealNo` int(8) DEFAULT NULL,
  `Tam` int(8) DEFAULT NULL,
  `Aktif` int(8) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `tbl_meal`
--

INSERT INTO `tbl_meal` (`mealID`, `Lisan`, `mealAdi`, `mealNo`, `Tam`, `Aktif`) VALUES
(1, 'Turkce', 'Abdulbaki Gölpınarlı', 2, 1, 1),
(2, 'Turkce', 'Abdullah Aydın', 35, 0, 1),
(3, 'Turkce', 'Adem Uğur', 3, 1, 1),
(4, 'Turkce', 'Ahmed Hulusi', 4, 0, 1),
(5, 'Turkce', 'Ahmet Davudoğlu', 36, 0, 1),
(6, 'Turkce', 'Ahmet Tekin', 5, 1, 1),
(7, 'Turkce', 'Ahmet Varol', 6, 1, 1),
(8, 'Turkce', 'Ali Arslan', 37, 0, 1),
(9, 'Turkce', 'Ali Bulaç', 7, 1, 1),
(10, 'Turkce', 'Ali Fikri Yavuz', 8, 1, 1),
(11, 'Arabic', 'Arabic1', 34, 1, 1),
(12, 'Turkce', 'Arif Pamuk', 38, 0, 1),
(13, 'Turkce', 'Ayntabî Mehmet Efendi', 39, 0, 1),
(14, 'Turkce', 'Bahaeddin Sağlam', 40, 0, 1),
(15, 'Turkce', 'Bekir Sadak', 9, 1, 1),
(16, 'Turkce', 'Bir Heyet', 41, 0, 1),
(17, 'Turkce', 'Celal Yıldırım', 10, 1, 1),
(18, 'Turkce', 'Diyanet İşleri', 1, 1, 0),
(19, 'Turkce', 'Diyanet İşleri (eski)', 11, 1, 1),
(20, 'Turkce', 'Diyanet Vakfi', 12, 1, 0),
(21, 'Turkce', 'Diyanet Vakfı (1993)', 42, 0, 1),
(22, 'Turkce', 'Edip Yüksel', 13, 1, 0),
(23, 'Turkce', 'Elmalılı (sadeleştirilmiş - 2)', 16, 1, 1),
(24, 'Turkce', 'Elmalılı (sadeleştirilmiş)', 15, 1, 1),
(25, 'Turkce', 'Elmalılı Hamdi Yazır', 14, 1, 0),
(26, 'Turkce', 'Fizilal-il Kuran', 17, 0, 1),
(27, 'Turkce', 'Gültekin Onan', 18, 1, 1),
(28, 'Turkce', 'Hasan Basri Çantay', 19, 1, 1),
(29, 'Turkce', 'Hasan Tahsin Feyizli', 43, 0, 1),
(30, 'Turkce', 'Hayrat Neşriyat', 20, 1, 1),
(31, 'Turkce', 'Hüseyin Atay, Yaşar Kutluay', 44, 0, 1),
(32, 'Turkce', 'Hüseyin Kaleli', 45, 0, 1),
(33, 'Turkce', 'İbni Kesir', 21, 1, 1),
(34, 'Turkce', 'İskender Evrenosoğlu', 31, 1, 1),
(35, 'Turkce', 'İsmail Mutlu, Şaban Döğen', 46, 0, 1),
(36, 'Turkce', 'Muhammed Esed', 22, 1, 1),
(37, 'Turkce', 'Mustafa İslamoğlu', 47, 0, 0),
(38, 'Turkce', 'Nedim Yılmaz', 48, 0, 1),
(39, 'Turkce', 'Ömer Nasuhi Bilmen', 23, 1, 1),
(40, 'Turkce', 'Ömer Öngüt', 24, 1, 1),
(41, 'Turkce', 'Ömer Rıza Doğrul', 49, 0, 1),
(42, 'Turkce', 'Şaban Piriş', 25, 1, 1),
(43, 'Dictionary', 'Sozluk1', 32, 1, 1),
(44, 'Turkce', 'Suat Yıldırım', 26, 1, 1),
(45, 'Turkce', 'Süleyman Ateş', 27, 1, 1),
(46, 'Turkce', 'Talat Koçyiğit', 50, 0, 1),
(47, 'Turkce', 'Tefhim-ul Kuran', 28, 1, 1),
(48, 'Transcript', 'Turkce1', 33, 1, 1),
(49, 'Turkce', 'Ümit Şimşek', 29, 1, 1),
(50, 'Turkce', 'Yaşar Nuri Öztürk', 30, 1, 1),
(51, 'Turkce', 'Ziya Kazıcı, Necip Taylan', 51, 0, 1),
(52, 'Transcript', 'Turkce2', 52, 0, 1),
(53, 'Turkce', 'Hakkı Yılmaz', 53, 0, 0),
(54, 'Mushaf', 'Mushaf', 54, 0, 1),
(100, 'Azerice', 'Əlixan Musayev', 102, 1, 0),
(101, 'Azerice', 'Bünyadov-Məmmədəliyev', 103, 1, 0),
(102, 'Azerice', 'Elmir Quliyev\n', 104, 1, 0),
(103, 'Azerice', 'Ələddin Sultanov', 105, 1, 0),
(104, 'Azerice', 'Erhan aktaş (Google Tərcümə)', 100, 0, 1),
(105, 'Turkce', 'Erhan aktaş\r\n', 101, 1, 0),
(106, 'Azerice', 'Kerbelayi Malik ağa', 106, 1, 0),
(107, 'Azerice', 'Ələsgər Musayev', 107, 1, 0),
(109, 'Azerice', 'A.Mehdiyev və D.Cəfərli', 108, 1, 0),
(110, 'Azerice', 'Kövsər Tağıyev', 109, 1, 0),
(111, 'Azerice', 'M.Qənioğlu və T.Bilaloğlu', 110, 1, 0),
(112, 'Azerice', 'Sabirə Dünyamalıyeva', 111, 1, 0),
(200, 'Turkce', 'Ahmed Hulusi (Türkçe Kur\'an Çözümü)', 55, 1, 1),
(201, 'Turkce', 'Ali Bulaç (Kur\'an-ı Kerim ve Türkçe Anlamı)', 56, 1, 1),
(202, 'Turkce', 'Bayraktar Bayraklı (Yeni Bir Anlayışın Işığında Kur\'an Meali)', 57, 1, 1),
(203, 'Turkce', 'Diyanet İşleri (Kur\'an-ı Kerim Türkçe Meali)', 58, 1, 1),
(204, 'Turkce', 'Edip Yüksel (Eski Baskı) (Mesaj: Kuran Çevirisi)', 59, 1, 1),
(205, 'Turkce', 'Elmalılı Hamdi Yazır (Kur\'an-ı Kerim ve Yüce Meali)', 60, 1, 1),
(206, 'Turkce', 'Elmalılı (sadeleştirilmiş)', 61, 1, 0),
(207, 'Turkce', 'Gültekin Onan', 62, 1, 1),
(208, 'Turkce', 'Hasan Basri Çantay (Kur\'an-ı Hakim ve Meal-i Kerim)', 63, 1, 1),
(209, 'Turkce', 'İbni Kesir', 64, 1, 0),
(210, 'Turkce', 'Muhammed Esed (Kur\'an Mesajı)', 65, 1, 1),
(211, 'Turkce', 'Şaban Piriş (Kur\'an-ı Kerim Türkçe Anlamı)', 66, 1, 1),
(212, 'Turkce', 'Suat Yıldırım (Kuran-ı Kerim ve Meali)', 67, 1, 1),
(213, 'Turkce', 'Süleyman Ateş (Kur\'an-ı Kerim ve Yüce Meali)', 68, 1, 1),
(214, 'Turkce', 'Yaşar Nuri Öztürk (Kur\'an-ı Kerim Meali)', 69, 1, 1),
(215, 'Turkce', 'Mustafa İslamoğlu (Hayat Kitabı Kur’an)', 70, 1, 1),
(216, 'Turkce', 'Erhan Aktaş (Eski Baskı) (Kerim Kur\'an)', 71, 1, 1),
(217, 'Turkce', 'Ali Rıza Safa (Kur\'an-ı Kerim Gerçek)', 72, 1, 1),
(218, 'Turkce', 'Süleymaniye Vakfı (Süleymaniye Vakfı Meali)', 73, 1, 1),
(219, 'Turkce', 'Edip Yüksel (Mesaj: Kuran Çevirisi)', 74, 1, 1),
(220, 'Turkce', 'Erhan Aktaş (Kerim Kur\'an)', 75, 1, 1),
(221, 'Turkce', 'Mehmet Okuyan (Kur’an Meal-Tefsir)', 76, 1, 1),
(300, 'English', 'Al-Hilali & Khan', 300, 1, 1),
(301, 'English', 'Abdullah Yusuf Ali', 301, 1, 1),
(302, 'English', 'Aisha Bewley', 302, 1, 1),
(303, 'English', 'Shabbir Ahmed', 303, 1, 1),
(304, 'English', 'Progressive Muslims', 304, 1, 1),
(305, 'English', 'Muhammad Asad', 305, 1, 1),
(306, 'English', 'Ali Quli Qarai', 306, 1, 1),
(307, 'English', 'Amatul Rahman Omar', 307, 1, 1),
(308, 'English', 'Arthur John Arberry', 308, 1, 1),
(309, 'English', 'E. Henry Palmer', 309, 1, 1),
(310, 'English', 'Hamid S. Aziz', 310, 1, 1),
(311, 'English', 'Mahmoud Ghali', 311, 1, 1),
(312, 'English', 'Abdel Khalek Himmat (Al- Muntakhab)', 312, 1, 1),
(313, 'English', 'Bijan Moeinian', 313, 1, 1),
(314, 'English', 'Mohamed Ahmed - Samira', 314, 1, 1),
(315, 'English', 'George Sale', 315, 1, 1),
(316, 'English', 'Sahih International ((Umm Muhammad, Mary Kennedy, Amatullah Bantley))', 316, 1, 1),
(317, 'English', 'Syed Vickar Ahamed', 317, 1, 1),
(318, 'English', 'Sam Gerrans (The Qur\'an: A Complete Revelation)', 318, 1, 1),
(319, 'English', 'Rashad Khalifa (The Final Testament)', 319, 1, 1),
(320, 'English', 'The Monotheist Group (The Quran: A Monotheist Translation)', 320, 0, 1),
(321, 'English', 'Edip-Layth (Quran: A Reformist Translation)', 321, 1, 1),
(322, 'English', 'Marmaduke Pickthall', 322, 1, 1),
(323, 'English', 'Abul A\'la Maududi (Tafhim commentary)', 323, 1, 1),
(324, 'English', 'Taqi Usmani', 324, 1, 1),
(325, 'English', 'Mustafa Khattab (The Clear Quran)', 325, 1, 1),
(326, 'English', 'Abdul Haleem', 326, 1, 1);

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `tbl_meal`
--
ALTER TABLE `tbl_meal`
  ADD PRIMARY KEY (`mealID`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `tbl_meal`
--
ALTER TABLE `tbl_meal`
  MODIFY `mealID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=327;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
