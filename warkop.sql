SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `warkop`
--

-- --------------------------------------------------------

--
-- Table structure for table `kasir`
--

CREATE TABLE `kasir` (
  `id_kasir` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_lengkap` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kasir`
--

INSERT INTO `kasir` (`id_kasir`, `username`, `password`, `nama_lengkap`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Bos Besar'),
(2, 'kasir1', '29c748d4d8f4bd5cbc0f3f60cb7ed3d0', 'unasil maftuh'),
(5, 'kasir2', '8c86013d8ba23d9b5ade4d6463f81c45', 'Budiono Siregar');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id_menu` int NOT NULL,
  `nama_menu` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `harga` int NOT NULL,
  `stok` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id_menu`, `nama_menu`, `harga`, `stok`) VALUES
(1, 'Indomie Goreng Telur', 10000, 47),
(2, 'Indomie Rebus Telur', 10000, 50),
(3, 'Indomie Nyemek Spesial', 12000, 48),
(4, 'Nasi Telur Kecap', 10000, 30),
(5, 'Magelangan', 13000, 30),
(6, 'Roti Bakar Coklat Keju', 10000, 30),
(7, 'Pisang Goreng Coklat', 10000, 30),
(8, 'Kentang Goreng', 8000, 37),
(9, 'Gorengan Campur', 5000, 100),
(10, 'Kue Pancong Lumer', 8000, 30),
(11, 'Kopi Hitam Tubruk', 4000, 100),
(12, 'Kopi Good Day Cappuccino', 5000, 100),
(13, 'Es Kopi Susu Gula Aren', 10000, 50),
(14, 'Es Mochaccino', 6000, 50),
(15, 'Kopi Jahe Panas', 5000, 48),
(16, 'Es Teh Manis', 3000, 100),
(17, 'Es Jeruk Peras', 4000, 49),
(18, 'Es Milo Dinosaurus', 8000, 47),
(19, 'Es Susu Soda', 7000, 50),
(20, 'Nutrisari Dingin', 4000, 100);

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int NOT NULL,
  `tanggal_waktu` datetime DEFAULT NULL,
  `nama_kasir` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_pembeli` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `menu` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `jumlah` int NOT NULL,
  `total_harga` int NOT NULL,
  `metode_pembayaran` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Tunai',
  `uang_bayar` int NOT NULL DEFAULT '0',
  `kembalian` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kasir`
--
ALTER TABLE `kasir`
  ADD PRIMARY KEY (`id_kasir`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kasir`
--
ALTER TABLE `kasir`
  MODIFY `id_kasir` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
