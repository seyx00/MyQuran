-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3306
-- Üretim Zamanı: 12 Eyl 2026, 14:29:35
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
-- Tablo için tablo yapısı `tbl_sureler`
--

CREATE TABLE `tbl_sureler` (
  `sureId` int(11) NOT NULL,
  `SureNo` int(8) DEFAULT NULL,
  `SureAdi` text DEFAULT NULL,
  `SureAdiTR` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `tbl_sureler`
--

INSERT INTO `tbl_sureler` (`sureId`, `SureNo`, `SureAdi`, `SureAdiTR`) VALUES
(1, 80, 'Əbəsə', 'Abese'),
(2, 100, 'Adiyat', 'Âdiyât'),
(3, 46, 'Əhqaf', 'Ahkâf'),
(4, 33, 'Əhzab', 'Ahzâb'),
(5, 3, 'Ali İmran', 'Âl-i İmrân'),
(6, 96, 'Ələq', 'Alak'),
(7, 29, 'Ənkəbut', 'Ankebût'),
(8, 103, 'Əsr', 'Asr'),
(9, 87, 'Əla', 'A’lâ'),
(10, 7, 'Əraf', 'A’râf'),
(11, 2, 'Bəqərə', 'Bakara'),
(12, 90, 'Bələd', 'Beled'),
(13, 98, 'Beyyinə', 'Beyyine'),
(14, 85, 'Buruc', 'Bürûc'),
(15, 45, 'Casiyə', 'Câsiye'),
(16, 72, 'Cin', 'Cin'),
(17, 62, 'Cümə', 'Cuma'),
(18, 44, 'Duhan', 'Duhân'),
(19, 93, 'Duha', 'Duhâ'),
(20, 21, 'Ənbiya', 'Enbiyâ'),
(21, 8, 'Ənfal', 'Enfâl'),
(22, 6, 'Ənam', 'En’âm'),
(23, 1, 'Fatihə', 'Fâtiha'),
(24, 35, 'Fatir', 'Fâtır'),
(25, 89, 'Fəcr', 'Fecr'),
(26, 113, 'Fələq', 'Felak'),
(27, 48, 'Fəth', 'Fetih'),
(28, 105, 'Fil', 'Fîl'),
(29, 25, 'Furqan', 'Furkân'),
(30, 41, 'Fussilət', 'Fussilet'),
(31, 88, 'Ğaşiyə', 'Ğâşiye'),
(32, 22, 'Həcc', 'Hac'),
(33, 57, 'Hədid', 'Hadîd'),
(34, 69, 'Həqqə', 'Hâkka'),
(35, 59, 'Həşr', 'Haşr'),
(36, 15, 'Hicr', 'Hicr'),
(37, 49, 'Hucurat', 'Hucurât'),
(38, 11, 'Hud', 'Hûd'),
(39, 104, 'Huməzə', 'Hümeze'),
(40, 14, 'İbrahim', 'İbrâhîm'),
(41, 112, 'İxlas', 'İhlâs'),
(42, 82, 'İnfitar', 'İnfitâr'),
(43, 76, 'İnsan', 'İnsân'),
(44, 84, 'İnşiqaq', 'İnşikâk'),
(45, 94, 'İnşirah', 'İnşirah'),
(46, 17, 'İsra', 'İsrâ'),
(47, 97, 'Qədr', 'Kadr'),
(48, 109, 'Kafirun', 'Kâfirûn'),
(49, 50, 'Qaf', 'Kâf'),
(50, 68, 'Qələm', 'Kalem'),
(51, 54, 'Qəmər', 'Kamer'),
(52, 101, 'Qariə', 'Kâria'),
(53, 28, 'Qasas', 'Kasas'),
(54, 18, 'Kəhf', 'Kehf'),
(55, 108, 'Kəvsər', 'Kevser'),
(56, 75, 'Qiyamə', 'Kıyâmet'),
(57, 106, 'Qureyş', 'Kureyş'),
(58, 92, 'Leyl', 'Leyl'),
(59, 31, 'Loğman', 'Lokmân'),
(60, 5, 'Maidə', 'Mâide'),
(61, 107, 'Maun', 'Mâûn'),
(62, 19, 'Məryəm', 'Meryem'),
(63, 70, 'Məaric', 'Meâric'),
(64, 58, 'Mücadilə', 'Mücâdele'),
(65, 74, 'Müddəssir', 'Müddessir'),
(66, 47, 'Muhəmməd', 'Muhammed'),
(67, 67, 'Mülk', 'Mülk'),
(68, 60, 'Mumtəhinə', 'Mümtehine'),
(69, 63, 'Munafiqun', 'Münâfikûn'),
(70, 77, 'Mursəlat', 'Mürselât'),
(71, 83, 'Mutəffifin', 'Mutaffifîn'),
(72, 73, 'Muzzəmmil', 'Müzzemmil'),
(73, 23, 'Muminun', 'Mü’minûn'),
(74, 40, 'Mumin', 'Mü’min'),
(75, 16, 'Nəhl', 'Nahl'),
(76, 110, 'Nəsr', 'Nasr'),
(77, 114, 'Nas', 'Nâs'),
(78, 79, 'Naziat', 'Nâziât'),
(79, 78, 'Nəbə', 'Nebe'),
(80, 53, 'Nəcm', 'Necm'),
(81, 27, 'Nəml', 'Neml'),
(82, 4, 'Nisa', 'Nisâ'),
(83, 71, 'Nuh', 'Nûh'),
(84, 24, 'Nur', 'Nûr'),
(85, 55, 'Rəhman', 'Rahmân'),
(86, 13, 'Rəd', 'Ra’d'),
(87, 30, 'Rum', 'Rûm'),
(88, 38, 'Sad', 'Sâd'),
(89, 37, 'Səffat', 'Sâffât'),
(90, 61, 'Səff', 'Saff'),
(91, 34, 'Səba', 'Sebe'),
(92, 32, 'Səcdə', 'Secde'),
(93, 91, 'Şəms', 'Şems'),
(94, 42, 'Şura', 'Şûrâ'),
(95, 26, 'Şuara', 'Şuarâ'),
(96, 20, 'Taha', 'Tâ-Hâ'),
(97, 66, 'Təhrim', 'Tahrîm'),
(98, 65, 'Talaq', 'Talâk'),
(99, 86, 'Tariq', 'Târık'),
(100, 111, 'Təbbət', 'Leheb'),
(101, 64, 'Təğabun', 'Teğâbün'),
(102, 102, 'Təkəsur', 'Tekâsür'),
(103, 81, 'Təkvir', 'Tekvîr'),
(104, 9, 'Tövbə', 'Tevbe'),
(105, 95, 'Tin', 'Tîn'),
(106, 52, 'Tur', 'Tûr'),
(107, 56, 'Vaqiə', 'Vâkıa'),
(108, 36, 'Yasin', 'Yâ-Sîn'),
(109, 10, 'Yunus', 'Yûnus'),
(110, 12, 'Yusif', 'Yûsuf'),
(111, 51, 'Zariyat', 'Zâriyât'),
(112, 99, 'Zilzal', 'Zilzâl'),
(113, 43, 'Zuxruf', 'Zuhruf'),
(114, 39, 'Zumər', 'Zümer');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `tbl_sureler`
--
ALTER TABLE `tbl_sureler`
  ADD PRIMARY KEY (`sureId`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `tbl_sureler`
--
ALTER TABLE `tbl_sureler`
  MODIFY `sureId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=209;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
