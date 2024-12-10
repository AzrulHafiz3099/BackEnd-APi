-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2024 at 05:45 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
-- Table structure for table `drug_header`
--

CREATE TABLE `drug_header` (
  `DrugHeaderID` int(200) NOT NULL,
  `CategoryName` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `drug_supply`
--

CREATE TABLE `drug_supply` (
  `SupplyID` int(200) NOT NULL,
  `DrugHeaderID` varchar(200) NOT NULL,
  `VendorID` varchar(200) NOT NULL,
  `Quantity` varchar(200) NOT NULL,
  `ExpiryDate` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
('US_0001', 'Azrul Hafiz', 'azrul', '$2y$10$sVqJpYSvYQnQGKNgkSFIsua534w5JD9BnJuYZb6Ie/F4Yhmmoxeem', '0172090464', '10/08/2002', 'pp.jpg', 'user'),
('US_0002', 'Amir Hamzah', 'amir@gmail.com', '$2y$10$fE7KsykJ8hXURDFiKXcQjeio87pCGxvO5/.9Cn.1rxOSrHKM1ruS2', '', '', '', 'user');

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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `drug_header`
--
ALTER TABLE `drug_header`
  ADD PRIMARY KEY (`DrugHeaderID`);

--
-- Indexes for table `drug_supply`
--
ALTER TABLE `drug_supply`
  ADD PRIMARY KEY (`SupplyID`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `drug_header`
--
ALTER TABLE `drug_header`
  MODIFY `DrugHeaderID` int(200) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `drug_supply`
--
ALTER TABLE `drug_supply`
  MODIFY `SupplyID` int(200) NOT NULL AUTO_INCREMENT;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
