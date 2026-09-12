-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3306
-- Üretim Zamanı: 12 Eyl 2026, 14:29:42
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
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `google_id` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `ad_soyad` varchar(255) DEFAULT NULL,
  `show_arabic` tinyint(1) DEFAULT 1,
  `show_transcription` tinyint(1) DEFAULT 1,
  `show_translation` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `google_id`, `email`, `ad_soyad`, `show_arabic`, `show_transcription`, `show_translation`, `created_at`) VALUES
(1, '114085402715377434960', 'memmedovelxan649@gmail.com', 'Elxan Məmmədov', 1, 1, 1, '2026-02-27 16:56:59'),
(2, '109107955507026774818', 'bybrawe5@gmail.com', 'bybrawe bbrt', 1, 1, 1, '2026-02-28 07:51:28'),
(3, '101381714218121148941', 'lxnmmmdv80@gmail.com', 'Lxn Mmmdv', 1, 1, 1, '2026-02-28 10:54:11'),
(4, '111539366795261569610', 'bybrawetr@gmail.com', 'Ömer', 1, 1, 1, '2026-02-28 11:27:16'),
(5, '117017487186144348951', 'elxanmmmdv2000@gmail.com', 'Elxan Məmmədov', 1, 1, 1, '2026-04-23 08:24:42'),
(6, '104641876217479314593', 'jeyhung571@gmail.com', 'Ceyhun', 0, 0, 1, '2026-05-11 15:50:37');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `google_id` (`google_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
