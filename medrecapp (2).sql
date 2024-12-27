-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 21, 2024 at 12:50 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `medrecapp`
--

DELIMITER $$
--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `GetNextNumericPart` () RETURNS INT(11) DETERMINISTIC BEGIN
    DECLARE next_id INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(UserID, 4) AS UNSIGNED)), 0) + 1 INTO next_id
    FROM user_account;
    RETURN next_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `drug_details`
--

CREATE TABLE `drug_details` (
  `DrugID` varchar(255) NOT NULL,
  `DrugHeaderID` varchar(255) DEFAULT NULL,
  `SupplyID` varchar(255) DEFAULT NULL,
  `BrandName` varchar(255) DEFAULT NULL,
  `GenericName` varchar(255) DEFAULT NULL,
  `Active_Ingredient` varchar(255) DEFAULT NULL,
  `Dosage` varchar(255) DEFAULT NULL,
  `Dosage_Form` varchar(255) DEFAULT NULL,
  `Manufacturer` varchar(255) DEFAULT NULL,
  `Manufacture_Date` date DEFAULT NULL,
  `SideEffects` text DEFAULT NULL,
  `Price` decimal(10,2) DEFAULT NULL,
  `DrugImage` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drug_details`
--

INSERT INTO `drug_details` (`DrugID`, `DrugHeaderID`, `SupplyID`, `BrandName`, `GenericName`, `Active_Ingredient`, `Dosage`, `Dosage_Form`, `Manufacturer`, `Manufacture_Date`, `SideEffects`, `Price`, `DrugImage`) VALUES
('D_0001', 'DH_0001', 'S_0001', 'Panadol', 'Paracetamol', 'ubat', '650', 'tablet', 'paracetamol ', '2024-12-12', 'Sleepy\r\n', 6.60, 'paracetamol.jpg'),
('D_0002', 'DH_0002', 'S_0002', 'Anti', 'Antibiotic', 'Venom', '1000', 'Liquid', 'Marvel', '2024-12-11', 'Buff', 10.20, 'venom.jpg'),
('D_0003', 'DH_0002', 'S_0003', 'test', 'tt', 'tt', 'tt', 'tt', 'tt', '2024-12-24', 'tt', 10.20, 'tt'),
('D_0004', 'DH_0001', 'S_0004', 'Test3', '32432432', '32432432', '432432432', '4324324', '34234242', '2024-12-11', '32423432', 6.60, '43223423');

-- --------------------------------------------------------

--
-- Table structure for table `drug_header`
--

CREATE TABLE `drug_header` (
  `DrugHeaderID` varchar(255) NOT NULL,
  `Category_Name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drug_header`
--

INSERT INTO `drug_header` (`DrugHeaderID`, `Category_Name`) VALUES
('DH_0001', 'Painkiller'),
('DH_0002', 'Antibiotics');

-- --------------------------------------------------------

--
-- Table structure for table `drug_inventory`
--

CREATE TABLE `drug_inventory` (
  `InventoryID` int(11) NOT NULL,
  `DrugID` varchar(255) NOT NULL,
  `CurrentStock` int(11) NOT NULL DEFAULT 0,
  `ReorderLevel` int(11) NOT NULL DEFAULT 0,
  `LastRestockDate` date DEFAULT NULL,
  `ExpirationDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drug_inventory`
--

INSERT INTO `drug_inventory` (`InventoryID`, `DrugID`, `CurrentStock`, `ReorderLevel`, `LastRestockDate`, `ExpirationDate`) VALUES
(1, 'D_0001', 50, 10, '2024-12-01', '2025-12-12'),
(2, 'D_0002', 30, 5, '2024-12-05', '2025-11-15'),
(3, 'D_0003', 100, 20, '2024-12-10', '2025-10-10'),
(4, 'D_0004', 20, 5, '2024-12-12', '2025-09-30');

-- --------------------------------------------------------

--
-- Table structure for table `drug_price`
--

CREATE TABLE `drug_price` (
  `PriceID` varchar(255) NOT NULL,
  `DrugID` varchar(255) DEFAULT NULL,
  `Vendor_Name` varchar(255) DEFAULT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `Price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `drug_supply`
--

CREATE TABLE `drug_supply` (
  `SupplyID` varchar(255) NOT NULL,
  `DrugHeaderID` varchar(255) DEFAULT NULL,
  `VendorID` varchar(255) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `Manufacture_Date` date DEFAULT NULL,
  `ExpiryDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drug_supply`
--

INSERT INTO `drug_supply` (`SupplyID`, `DrugHeaderID`, `VendorID`, `Quantity`, `Manufacture_Date`, `ExpiryDate`) VALUES
('S_0001', 'DH_0001', 'V_0001', 1, '2024-12-13', '2024-12-13'),
('S_0002', 'DH_0002', 'V_0001', 5, '2024-12-10', '2024-12-13'),
('S_0003', 'DH_0001', 'V_0002', 5, '2024-12-11', '2024-12-10'),
('S_0004', 'DH_0002', 'V_0001', 5, '2024-12-13', '2024-12-13');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `PatientID` int(11) NOT NULL,
  `UserID` varchar(200) NOT NULL,
  `Name` varchar(200) NOT NULL,
  `Age` varchar(200) NOT NULL,
  `Gender` varchar(200) NOT NULL,
  `Address` varchar(200) NOT NULL,
  `MedicalHistory` varchar(200) NOT NULL,
  `PhoneNumber` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reminder`
--

CREATE TABLE `reminder` (
  `ReminderID` int(200) NOT NULL,
  `PatientID` varchar(200) NOT NULL,
  `Title` varchar(200) NOT NULL,
  `Description` varchar(200) NOT NULL,
  `ReminderDate` varchar(200) NOT NULL,
  `isCompleted` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_account`
--

CREATE TABLE `user_account` (
  `UserID` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Fullname` varchar(200) NOT NULL,
  `Email` varchar(200) NOT NULL,
  `Password` varchar(200) NOT NULL,
  `Phonenumber` varchar(200) NOT NULL,
  `DateOfBirth` varchar(200) NOT NULL,
  `ProfilePicture` varchar(200) NOT NULL,
  `Role` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_account`
--

INSERT INTO `user_account` (`UserID`, `Fullname`, `Email`, `Password`, `Phonenumber`, `DateOfBirth`, `ProfilePicture`, `Role`) VALUES
('US_0003', 'Haikal Zabidi', 'haikal@gmail.com', '$2y$10$df/.iJh.BkHu0P90pDtRpOAjXhLKUeeNxxT0Yokx5tvRA.qQNQfki', '', '', '', 'user'),
('US_0004', 'Nik Daniel', 'nik@gmail.com', '$2y$10$N6HEYT3xFEsglmbPTWJ7iutLoc/GDn0y.8MYzzo.zq020cohi.niu', '', '', '', 'user'),
('US_0005', 'Azrul Hafiz', 'azrul', '$2y$10$sVqJpYSvYQnQGKNgkSFIsua534w5JD9BnJuYZb6Ie/F4Yhmmoxeem', '0172090464', '10/08/2002', 'pp.jpg', 'user'),
('US_0006', 'Amir Hamzah', 'amir@gmail.com', '$2y$10$fE7KsykJ8hXURDFiKXcQjeio87pCGxvO5/.9Cn.1rxOSrHKM1ruS2', '0123456789', '10/02/2021', 'pp2.jpg', 'user');

--
-- Triggers `user_account`
--
DELIMITER $$
CREATE TRIGGER `GenerateUserID` BEFORE INSERT ON `user_account` FOR EACH ROW BEGIN
    DECLARE numeric_part INT;
    SET numeric_part = GetNextNumericPart();
    SET NEW.UserID = CONCAT('US_', LPAD(numeric_part, 4, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

CREATE TABLE `vendor` (
  `VendorID` varchar(200) NOT NULL,
  `Fullname` varchar(255) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Address` text DEFAULT NULL,
  `ContactNumber` varchar(20) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Latitude` varchar(200) DEFAULT NULL,
  `Longitude` varchar(200) DEFAULT NULL,
  `ProfilePicture` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor`
--

INSERT INTO `vendor` (`VendorID`, `Fullname`, `Username`, `Password`, `Address`, `ContactNumber`, `Email`, `Latitude`, `Longitude`, `ProfilePicture`) VALUES
('V_0001', 'Farmasi Durian Tunggal', 'FDM', '1234', 'DT966 PEKAN, 76100 Durian Tunggal, Melaka', '06-553 3061', 'fdm@gmail.com', '2.3129343991111866', '102.28264782702448', 'pp.jpg'),
('V_0002', 'Farmasi NK Melaka', 'FNK', '1234', 'Farmasi NK, DT18, Jalan Pusat Perniagaan Durian Tunggal 2 Pusat Perniagaan Durian Tunggal Durian Tunggal, 76100 Alor Gajah, Melaka', '065490927', 'fnk@gmail.com', '2.312388173095135', '102.28001973178266', 'fnk.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `drug_details`
--
ALTER TABLE `drug_details`
  ADD PRIMARY KEY (`DrugID`),
  ADD KEY `DrugHeaderID` (`DrugHeaderID`),
  ADD KEY `SupplyID` (`SupplyID`);

--
-- Indexes for table `drug_header`
--
ALTER TABLE `drug_header`
  ADD PRIMARY KEY (`DrugHeaderID`);

--
-- Indexes for table `drug_inventory`
--
ALTER TABLE `drug_inventory`
  ADD PRIMARY KEY (`InventoryID`),
  ADD KEY `DrugID` (`DrugID`);

--
-- Indexes for table `drug_price`
--
ALTER TABLE `drug_price`
  ADD PRIMARY KEY (`PriceID`),
  ADD KEY `DrugID` (`DrugID`);

--
-- Indexes for table `drug_supply`
--
ALTER TABLE `drug_supply`
  ADD PRIMARY KEY (`SupplyID`),
  ADD KEY `DrugHeaderID` (`DrugHeaderID`),
  ADD KEY `VendorID` (`VendorID`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`PatientID`);

--
-- Indexes for table `reminder`
--
ALTER TABLE `reminder`
  ADD PRIMARY KEY (`ReminderID`);

--
-- Indexes for table `user_account`
--
ALTER TABLE `user_account`
  ADD PRIMARY KEY (`UserID`);

--
-- Indexes for table `vendor`
--
ALTER TABLE `vendor`
  ADD PRIMARY KEY (`VendorID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `drug_inventory`
--
ALTER TABLE `drug_inventory`
  MODIFY `InventoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `PatientID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reminder`
--
ALTER TABLE `reminder`
  MODIFY `ReminderID` int(200) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `drug_details`
--
ALTER TABLE `drug_details`
  ADD CONSTRAINT `drug_details_ibfk_1` FOREIGN KEY (`DrugHeaderID`) REFERENCES `drug_header` (`DrugHeaderID`),
  ADD CONSTRAINT `drug_details_ibfk_2` FOREIGN KEY (`SupplyID`) REFERENCES `drug_supply` (`SupplyID`);

--
-- Constraints for table `drug_inventory`
--
ALTER TABLE `drug_inventory`
  ADD CONSTRAINT `drug_inventory_ibfk_1` FOREIGN KEY (`DrugID`) REFERENCES `drug_details` (`DrugID`);

--
-- Constraints for table `drug_price`
--
ALTER TABLE `drug_price`
  ADD CONSTRAINT `drug_price_ibfk_1` FOREIGN KEY (`DrugID`) REFERENCES `drug_details` (`DrugID`);

--
-- Constraints for table `drug_supply`
--
ALTER TABLE `drug_supply`
  ADD CONSTRAINT `drug_supply_ibfk_1` FOREIGN KEY (`DrugHeaderID`) REFERENCES `drug_header` (`DrugHeaderID`),
  ADD CONSTRAINT `drug_supply_ibfk_2` FOREIGN KEY (`VendorID`) REFERENCES `vendor` (`VendorID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
