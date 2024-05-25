-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Maj 25, 2024 at 03:06 AM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tester`
--

DELIMITER $$
--
-- Procedury
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTestDetails` (IN `user_id` INT)   BEGIN
    SELECT 
        test.id_test, 
        test.name, 
        test.time, 
        COUNT(questions.id_question) AS question_count, 
        SUM(questions.points) AS total_points
    FROM 
        test 
        LEFT JOIN link_test_groups ON test.id_test = link_test_groups.test_id
        LEFT JOIN groups ON link_test_groups.group_id = groups.id
        LEFT JOIN link_group_questions ON groups.id = link_group_questions.group_id
        LEFT JOIN questions ON link_group_questions.question_id = questions.id_question
    WHERE 
        test.account_id = user_id
    GROUP BY 
        test.id_test, 
        test.name, 
        test.time;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `accounts`
--

CREATE TABLE `accounts` (
  `account_id` int(11) NOT NULL,
  `Login` varchar(60) NOT NULL,
  `Password_hash` varchar(100) NOT NULL,
  `Salt` varchar(60) NOT NULL,
  `Type` int(11) NOT NULL COMMENT 'Typ konta\r\n1 = Administrator szkolny\r\n2 = Nauczyciel\r\n3 = Uczen'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`account_id`, `Login`, `Password_hash`, `Salt`, `Type`) VALUES
(1, 'artur.ograbek', '01829da8bc6c5d8365cb06b808f5675cdef9b17dbeafe9cd06ccc04a0f100c0e', 'a0e3b231687b3d0ff3901c52643216b4', 1),
(2, 'Patryk.Orzechowski', '5c027691a1c8af0f69d769df91ccd9a11b4f0a6eb129d80b861e2232d2b2d198', 'b24db6b35f2d5ed7f6abb31871bd9efd', 2);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `account_data`
--

CREATE TABLE `account_data` (
  `account_id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `surname` varchar(20) NOT NULL,
  `number` int(20) DEFAULT NULL,
  `email` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account_data`
--

INSERT INTO `account_data` (`account_id`, `name`, `surname`, `number`, `email`) VALUES
(2, 'Patryk', 'Orzechowski', NULL, 'patryk@cos.com');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `activeted_test`
--

CREATE TABLE `activeted_test` (
  `id` int(11) NOT NULL,
  `activation_time` datetime NOT NULL,
  `test_code` varchar(6) DEFAULT NULL,
  `test_id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `code_generated` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activeted_test`
--

INSERT INTO `activeted_test` (`id`, `activation_time`, `test_code`, `test_id`, `account_id`, `code_generated`) VALUES
(4, '2024-05-25 04:39:00', 'SMJGHP', 4, 2, 1),
(5, '2024-05-25 02:41:00', NULL, 4, 2, 1),
(6, '2024-05-25 02:56:00', NULL, 4, 2, 1);

--
-- Wyzwalacze `activeted_test`
--
DELIMITER $$
CREATE TRIGGER `generate_test_code` BEFORE INSERT ON `activeted_test` FOR EACH ROW BEGIN
    DECLARE test_code_generated VARCHAR(6);
    DECLARE characters VARCHAR(36) DEFAULT 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    DECLARE i INT DEFAULT 1;

    IF NEW.code_generated = 0 THEN
        SET test_code_generated = '';
        WHILE i <= 6 DO
            SET test_code_generated = CONCAT(test_code_generated, SUBSTRING(characters, FLOOR(1 + RAND() * 36), 1));
            SET i = i + 1;
        END WHILE;
        SET NEW.test_code = test_code_generated;
        SET NEW.code_generated = 1; -- Ustawiamy flagę, że kod został wygenerowany
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `answers`
--

CREATE TABLE `answers` (
  `answer_id` int(11) NOT NULL,
  `text` text DEFAULT NULL,
  `correct` tinyint(1) DEFAULT NULL,
  `question_id` int(11) NOT NULL,
  `points` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`answer_id`, `text`, `correct`, `question_id`, `points`) VALUES
(17, 'Tak', 0, 2, NULL),
(18, 'Nie', 0, 2, NULL),
(80, 'Nie', 0, 1, NULL),
(81, 'Super', 1, 1, NULL);

--
-- Wyzwalacze `answers`
--
DELIMITER $$
CREATE TRIGGER `after_update_answers_points` AFTER UPDATE ON `answers` FOR EACH ROW BEGIN
  IF OLD.points <> NEW.points THEN
    UPDATE link_account_activated_test laatt
    JOIN link_account_activated_test_answer laata ON laatt.id = laata.link_account_activated_test_id
    JOIN questions q ON NEW.question_id = q.id_question
    SET laatt.points = q.points + NEW.points
    WHERE laata.answer_id = NEW.answer_id;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `account_id`) VALUES
(9, 'Grupa 1', NULL),
(12, 'Grupa 2', NULL),
(13, 'Grupa 2', NULL),
(14, 'Grupa 2', NULL),
(15, 'Grupa 2', 2),
(16, 'Grupa 1', 2);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `link_account_activated_test`
--

CREATE TABLE `link_account_activated_test` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `activated_test_id` int(11) NOT NULL,
  `points` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `link_account_activated_test_answer`
--

CREATE TABLE `link_account_activated_test_answer` (
  `id` int(11) NOT NULL,
  `link_account_activated_test_id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Wyzwalacze `link_account_activated_test_answer`
--
DELIMITER $$
CREATE TRIGGER `after_insert_link_account_activated_test_answer` AFTER INSERT ON `link_account_activated_test_answer` FOR EACH ROW BEGIN
  UPDATE link_account_activated_test laatt
  JOIN answers a ON NEW.answer_id = a.answer_id
  JOIN questions q ON a.question_id = q.id_question
  SET laatt.points = q.points + a.points
  WHERE laatt.id = NEW.link_account_activated_test_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `link_group_questions`
--

CREATE TABLE `link_group_questions` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `link_group_questions`
--

INSERT INTO `link_group_questions` (`id`, `question_id`, `group_id`) VALUES
(38, 1, 15),
(39, 1, 16);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `link_organisations_accounts`
--

CREATE TABLE `link_organisations_accounts` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `organisation_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `link_organisations_accounts`
--

INSERT INTO `link_organisations_accounts` (`id`, `account_id`, `organisation_id`) VALUES
(0, 2, 1),
(1, 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `link_test_groups`
--

CREATE TABLE `link_test_groups` (
  `id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `question_count` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `link_test_groups`
--

INSERT INTO `link_test_groups` (`id`, `test_id`, `group_id`, `question_count`) VALUES
(5, 7, 15, 0),
(6, 7, 16, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `organisations`
--

CREATE TABLE `organisations` (
  `organisation_id` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `initials` varchar(4) NOT NULL,
  `Address` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organisations`
--

INSERT INTO `organisations` (`organisation_id`, `Name`, `initials`, `Address`) VALUES
(1, 'Uniwersytet Łódzki', 'UL', 'Narutowicz 68');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `questions`
--

CREATE TABLE `questions` (
  `id_question` int(11) NOT NULL,
  `opened` tinyint(1) NOT NULL,
  `text` text NOT NULL,
  `points` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id_question`, `opened`, `text`, `points`, `account_id`) VALUES
(1, 0, 'Czy się udało?', 1000, 2),
(2, 0, 'Czy się udało?', 1000, NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `question_image`
--

CREATE TABLE `question_image` (
  `image_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `test`
--

CREATE TABLE `test` (
  `id_test` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `time` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `test`
--

INSERT INTO `test` (`id_test`, `name`, `time`, `account_id`) VALUES
(1, 'Test', 10, NULL),
(3, 'Test', 10, NULL),
(4, 'Uniwersytet Łódzki', 1, 2),
(5, 'Test', 10, NULL),
(6, 'Test3', 10, NULL),
(7, 'Test', 10, 2);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `UNIQUE` (`Login`) USING BTREE;

--
-- Indeksy dla tabeli `account_data`
--
ALTER TABLE `account_data`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `number` (`number`);

--
-- Indeksy dla tabeli `activeted_test`
--
ALTER TABLE `activeted_test`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_id` (`test_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indeksy dla tabeli `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indeksy dla tabeli `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indeksy dla tabeli `link_account_activated_test`
--
ALTER TABLE `link_account_activated_test`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `activated_test_id` (`activated_test_id`);

--
-- Indeksy dla tabeli `link_account_activated_test_answer`
--
ALTER TABLE `link_account_activated_test_answer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `answer_id` (`answer_id`),
  ADD KEY `link_account_activated_test_id` (`link_account_activated_test_id`);

--
-- Indeksy dla tabeli `link_group_questions`
--
ALTER TABLE `link_group_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indeksy dla tabeli `link_organisations_accounts`
--
ALTER TABLE `link_organisations_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `school_id` (`organisation_id`);

--
-- Indeksy dla tabeli `link_test_groups`
--
ALTER TABLE `link_test_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_id` (`test_id`),
  ADD KEY `link_test_question_ibfk_2` (`group_id`);

--
-- Indeksy dla tabeli `organisations`
--
ALTER TABLE `organisations`
  ADD PRIMARY KEY (`organisation_id`),
  ADD UNIQUE KEY `initials` (`initials`);

--
-- Indeksy dla tabeli `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id_question`),
  ADD KEY `account_id` (`account_id`);

--
-- Indeksy dla tabeli `question_image`
--
ALTER TABLE `question_image`
  ADD PRIMARY KEY (`image_id`),
  ADD UNIQUE KEY `image` (`image`),
  ADD KEY `question_id` (`question_id`);

--
-- Indeksy dla tabeli `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`id_test`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `name` (`name`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `activeted_test`
--
ALTER TABLE `activeted_test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `link_account_activated_test`
--
ALTER TABLE `link_account_activated_test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `link_account_activated_test_answer`
--
ALTER TABLE `link_account_activated_test_answer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `link_group_questions`
--
ALTER TABLE `link_group_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `link_test_groups`
--
ALTER TABLE `link_test_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id_question` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `test`
--
ALTER TABLE `test`
  MODIFY `id_test` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account_data`
--
ALTER TABLE `account_data`
  ADD CONSTRAINT `account_data_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`);

--
-- Constraints for table `activeted_test`
--
ALTER TABLE `activeted_test`
  ADD CONSTRAINT `activeted_test_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `test` (`id_test`),
  ADD CONSTRAINT `activeted_test_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`);

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id_question`);

--
-- Constraints for table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`);

--
-- Constraints for table `link_account_activated_test`
--
ALTER TABLE `link_account_activated_test`
  ADD CONSTRAINT `link_account_activated_test_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`),
  ADD CONSTRAINT `link_account_activated_test_ibfk_2` FOREIGN KEY (`activated_test_id`) REFERENCES `activeted_test` (`id`);

--
-- Constraints for table `link_account_activated_test_answer`
--
ALTER TABLE `link_account_activated_test_answer`
  ADD CONSTRAINT `link_account_activated_test_answer_ibfk_1` FOREIGN KEY (`answer_id`) REFERENCES `answers` (`answer_id`),
  ADD CONSTRAINT `link_account_activated_test_answer_ibfk_2` FOREIGN KEY (`link_account_activated_test_id`) REFERENCES `link_account_activated_test` (`id`);

--
-- Constraints for table `link_group_questions`
--
ALTER TABLE `link_group_questions`
  ADD CONSTRAINT `link_group_questions_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `link_group_questions_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id_question`);

--
-- Constraints for table `link_organisations_accounts`
--
ALTER TABLE `link_organisations_accounts`
  ADD CONSTRAINT `link_organisations_accounts_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`),
  ADD CONSTRAINT `link_organisations_accounts_ibfk_2` FOREIGN KEY (`organisation_id`) REFERENCES `organisations` (`organisation_id`);

--
-- Constraints for table `link_test_groups`
--
ALTER TABLE `link_test_groups`
  ADD CONSTRAINT `link_test_groups_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `test` (`id_test`),
  ADD CONSTRAINT `link_test_groups_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`);

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`);

--
-- Constraints for table `question_image`
--
ALTER TABLE `question_image`
  ADD CONSTRAINT `question_image_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id_question`);

--
-- Constraints for table `test`
--
ALTER TABLE `test`
  ADD CONSTRAINT `test_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`);

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `update_test_code_to_null` ON SCHEDULE EVERY 1 MINUTE STARTS '2024-05-25 02:54:55' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE activeted_test at
  JOIN test t ON at.test_id = t.id_test
  SET at.test_code = NULL
  WHERE at.test_code IS NOT NULL
  AND at.activation_time <= DATE_SUB(NOW(), INTERVAL t.time MINUTE)$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
