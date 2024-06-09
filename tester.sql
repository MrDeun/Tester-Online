-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Cze 09, 2024 at 08:49 PM
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddAccountAndOrganization` (IN `p_login` VARCHAR(50), IN `p_password` VARCHAR(255), IN `p_organization_name` VARCHAR(100), IN `p_organization_address` VARCHAR(255), IN `p_organization_initials` VARCHAR(10))   BEGIN
    DECLARE account_id INT;
    DECLARE organization_id INT;

    -- Dodanie nowego konta
    INSERT INTO accounts (Login, Password_hash, Salt, Type)
    VALUES (p_login, SHA2(CONCAT(p_password, HEX(RAND())), 256), HEX(RAND()), '1');

    -- Pobranie ID dodanego konta
    SET account_id = LAST_INSERT_ID();

    -- Dodanie nowej organizacji
    INSERT INTO organisations (Name, Address, initials)
    VALUES (p_organization_name, p_organization_address, p_organization_initials);

    -- Pobranie ID dodanej organizacji
    SET organization_id = LAST_INSERT_ID();

    -- Powiązanie konta z organizacją
    INSERT INTO link_organisations_accounts (account_id, organisation_id)
    VALUES (account_id, organization_id);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AddNewAccount` (IN `p_login` VARCHAR(255), IN `p_password_hash` VARCHAR(255), IN `p_salt` VARCHAR(255), IN `p_name` VARCHAR(255), IN `p_surname` VARCHAR(255), IN `p_email` VARCHAR(255), IN `p_number` VARCHAR(255), IN `p_type` INT, IN `p_user_id` INT)   BEGIN
    -- Dodanie nowego konta do tabeli accounts
    INSERT INTO accounts (Login, Password_hash, Salt, Type)
    VALUES (p_login, p_password_hash, p_salt, p_type);

    -- Pobranie ostatnio wstawionego account_id
    SET @last_account_id = LAST_INSERT_ID();

    -- Dodanie nowych danych do tabeli account_data
    INSERT INTO account_data (account_id, name, surname, email, number)
    VALUES (@last_account_id, p_name, p_surname, p_email, p_number);

    -- Pobranie ID z tabeli organisations
    SELECT organisations.id INTO @organisation_id
    FROM accounts
    JOIN link_organisations_accounts ON accounts.account_id = link_organisations_accounts.account_id
    JOIN organisations ON link_organisations_accounts.organisation_id = organisations.id
    WHERE accounts.account_id = p_user_id;

    -- Dodanie relacji między kontem a organizacją do tabeli link_organisations_accounts
    INSERT INTO link_organisations_accounts (account_id, organisation_id)
    VALUES (@last_account_id, @organisation_id);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AssignTestToStudent` (IN `p_activated_test_id` INT, IN `p_student_id` INT)   BEGIN
    DECLARE v_test_id INT;
    DECLARE v_group_id INT;
    DECLARE v_question_count INT;
    DECLARE v_question_id INT;
    DECLARE v_link_account_activated_test_id INT;
    DECLARE done INT DEFAULT FALSE;
    DECLARE record_exists INT DEFAULT 0;

    -- Deklaracja kursora dla grup
    DECLARE v_groups CURSOR FOR 
        SELECT group_id, question_count 
        FROM link_tests_groups 
        WHERE test_id = v_test_id;

    -- Deklaracja kursora dla pytań
    DECLARE question_cursor CURSOR FOR 
        SELECT question_id 
        FROM link_groups_questions 
        WHERE group_id = v_group_id 
        LIMIT v_question_count;

    -- Deklaracja warunku końca kursora
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Tymczasowa tabela do przechowywania wyników
    CREATE TEMPORARY TABLE IF NOT EXISTS temp_questions (
        question_id INT
    );
    
    -- Przypisanie wartości do v_test_id
    SELECT test_id INTO v_test_id 
    FROM activated_tests 
    WHERE id = p_activated_test_id;

	-- Sprawdzenie, czy istnieje już wiersz
    SELECT COUNT(*) INTO record_exists
    FROM link_account_activated_tests
    WHERE account_id = p_student_id AND activated_test_id = p_activated_test_id;

    -- Wstawienie wiersza, jeśli nie istnieje
    IF record_exists = 0 THEN
        INSERT INTO link_account_activated_tests (account_id, activated_test_id) 
        VALUES (p_student_id, p_activated_test_id);
    END IF;

    -- Pobranie ID z tabeli link_account_activated_tests
    SELECT id INTO v_link_account_activated_test_id
    FROM link_account_activated_tests
    WHERE account_id = p_student_id AND activated_test_id = p_activated_test_id;

    -- Otwarcie kursora dla grup
    OPEN v_groups;

    -- Pętla po v_groups
    read_loop: LOOP
        FETCH v_groups INTO v_group_id, v_question_count;
        IF done THEN
            SET done = FALSE;
            LEAVE read_loop;
        END IF;

        -- Otwarcie kursora dla pytań
        OPEN question_cursor;

        -- Pętla po pytaniach
        question_loop: LOOP
            FETCH question_cursor INTO v_question_id;
            IF done THEN
                SET done = FALSE;
                LEAVE question_loop;
            END IF;

            -- Wstawienie do tymczasowej tabeli
            INSERT INTO temp_questions (question_id) 
            VALUES (v_question_id);
        END LOOP question_loop;

        -- Zamknięcie kursora dla pytań
        CLOSE question_cursor;
    END LOOP read_loop;

    -- Zamknięcie kursora dla grup
    CLOSE v_groups;


    -- Zwrócenie wyników
    SELECT * FROM temp_questions;

    -- Usunięcie tymczasowej tabeli
    DROP TEMPORARY TABLE temp_questions;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ChangePassword` (IN `p_id` INT, IN `p_password_hash` VARCHAR(255), IN `p_salt` VARCHAR(32))   BEGIN
    UPDATE accounts
    SET Password_hash = p_password_hash, Salt = p_salt
    WHERE account_id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAccounts` (IN `user_id` INT)   BEGIN
    SELECT accounts.account_id AS id, accounts.Login AS Login, account_data.name AS Name, account_data.surname AS Surname, account_data.email AS Email, account_data.number AS Number 
    FROM (accounts INNER JOIN account_data ON accounts.account_id = account_data.account_id) 
    JOIN link_organisations_accounts ON accounts.account_id = link_organisations_accounts.account_id 
    JOIN organisations ON link_organisations_accounts.organisation_id = organisations.id 
    WHERE (accounts.Type = '2' OR accounts.Type='3') 
    AND organisations.id = 
    (SELECT organisations.id FROM accounts JOIN link_organisations_accounts ON accounts.account_id = link_organisations_accounts.account_id JOIN organisations ON link_organisations_accounts.organisation_id = organisations.id WHERE accounts.account_id = user_id);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAccountsByType` (IN `p_type` INT)   BEGIN
    SELECT accounts.account_id AS id, accounts.Login AS Login, organisations.Name AS Name, organisations.initials AS Initials, organisations.Address As Address 
    FROM accounts 
    JOIN link_organisations_accounts ON accounts.account_id = link_organisations_accounts.account_id 
    JOIN organisations ON link_organisations_accounts.organisation_id = organisations.id 
    WHERE accounts.Type = p_type;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetActivatedTestAnswer` (IN `p_question_id` INT, IN `p_link_account_activated_test_id` INT)   BEGIN
    SELECT 
        answers.text AS answer_text, 
        answers.correct
    FROM 
        link_account_activated_tests_answer 
        LEFT JOIN answers ON link_account_activated_tests_answer.answer_id = answers.id 
    WHERE 
        link_account_activated_tests_answer.link_account_activated_test_id = p_link_account_activated_test_id
        AND link_account_activated_tests_answer.question_id = p_question_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetActivatedTestInfo` (IN `activated_id` INT)   SELECT tests.name, tests.time, activated_tests.activation_time FROM activated_tests JOIN tests ON activated_tests.test_id = tests.id WHERE activated_tests.id = activated_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetActivatedTestQuestions` (IN `p_link_account_activated_test_id` INT)   BEGIN
    SELECT 
        questions.text AS question_text, 
        link_account_activated_tests_answer.question_id,
        questions.opened,
        questions.points

    FROM 
        link_account_activated_tests_answer 
        LEFT JOIN questions ON link_account_activated_tests_answer.question_id = questions.id 
    WHERE 
        link_account_activated_tests_answer.link_account_activated_test_id = p_link_account_activated_test_id
    GROUP BY link_account_activated_tests_answer.question_id
    ORDER BY link_account_activated_tests_answer.id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAnswerDetails` (IN `p_question_id` INT)   BEGIN
    SELECT 
        answers.text AS answer_text 
    FROM 
        answers 
    WHERE 
        answers.question_id = p_question_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetRecentlyActivatedTests` (IN `user_id` INT)   BEGIN
    SELECT activated_tests.id AS ID, tests.name AS Name, activated_tests.activation_time AS ActivationTime, COUNT(link_account_activated_tests.id) AS Participants
    FROM activated_tests
    JOIN tests ON activated_tests.test_id = tests.id
    LEFT JOIN link_account_activated_tests ON activated_tests.id = link_account_activated_tests.activated_test_id
   WHERE tests.id IN (
        SELECT tests.id
        FROM tests
        WHERE tests.account_id = user_id
    )
    GROUP BY activated_tests.id, tests.name, activated_tests.activation_time
    ORDER BY activated_tests.activation_time DESC
    LIMIT 10;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTestAccountData` (IN `p_activated_test_id` INT)   BEGIN
    SELECT 
        ad.name, 
        ad.surname, 
        lat.points, 
        lat.checked,
        lat.id
    FROM 
        link_account_activated_tests lat
        JOIN account_data ad ON lat.account_id = ad.account_id
        LEFT JOIN activated_tests at ON lat.activated_test_id = at.id
    WHERE 
        at.id = p_activated_test_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTestDetails` (IN `user_id` INT)   BEGIN
    SELECT 
        tests.id, 
        tests.name, 
        tests.time, 
        COUNT(questions.id) AS question_count, 
        SUM(questions.points) AS total_points
    FROM 
        tests 
        LEFT JOIN link_tests_groups ON tests.id = link_tests_groups.test_id
        LEFT JOIN groups ON link_tests_groups.group_id = groups.id
        LEFT JOIN link_groups_questions ON groups.id = link_groups_questions.group_id
        LEFT JOIN questions ON link_groups_questions.question_id = questions.id
    WHERE 
        tests.account_id = user_id
    GROUP BY 
        tests.id, 
        tests.name, 
        tests.time;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `process_answer` (IN `p_link_account_activated_test_id` INT, IN `p_question_id` INT, IN `p_answer_id` INT)   BEGIN
    DECLARE total_answers INT;
    DECLARE correct_answers INT;
    DECLARE question_points INT;

    -- Sprawdzenie, czy pytanie jest zamknięte
    IF (SELECT opened FROM questions WHERE id = p_question_id) = 0 THEN
        -- Liczba odpowiedzi dla danego question_id i link_account_activated_test_id
        SELECT COUNT(*) INTO total_answers
        FROM link_account_activated_tests_answer
        WHERE question_id = p_question_id
        AND link_account_activated_test_id = p_link_account_activated_test_id;

        -- Liczba poprawnych odpowiedzi dla danego question_id i link_account_activated_test_id
        SELECT COUNT(*) INTO correct_answers
        FROM link_account_activated_tests_answer laata
        JOIN answers a ON laata.answer_id = a.id
        WHERE laata.question_id = p_question_id
        AND laata.link_account_activated_test_id = p_link_account_activated_test_id
        AND a.correct = 1;

        -- Jeśli wszystkie odpowiedzi są poprawne, dodaj punkty
        IF total_answers = correct_answers THEN
            UPDATE link_account_activated_tests
            SET points = points + (SELECT points FROM questions WHERE id = p_question_id)
            WHERE id = p_link_account_activated_test_id;
        END IF;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SelectActivetedTests` (IN `account_id` INT)   BEGIN
    SELECT activated_tests.id, tests.name, activated_tests.activation_time, tests.time, activated_tests.test_code
    FROM activated_tests 
    LEFT JOIN tests ON activated_tests.test_id = tests.id
    WHERE activated_tests.account_id = account_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SelectQuestionGroups` (IN `p_user_id` INT)   BEGIN
    SELECT groups.id, groups.name, COUNT(questions.id) AS question_count
    FROM groups
    LEFT JOIN link_groups_questions ON groups.id = link_groups_questions.group_id
    LEFT JOIN questions ON link_groups_questions.question_id = questions.id
    WHERE groups.account_id = p_user_id
    GROUP BY groups.id, groups.name
    ORDER BY groups.id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SelectQuestionGroupsForTest` (IN `p_user_id` INT, IN `p_test_id` INT)   BEGIN
    SELECT groups.id, groups.name, COUNT(questions.id) AS question_count
    FROM groups
    LEFT JOIN link_groups_questions ON groups.id = link_groups_questions.group_id
    LEFT JOIN questions ON link_groups_questions.question_id = questions.id
    LEFT JOIN link_tests_groups ON groups.id = link_tests_groups.group_id
    WHERE groups.account_id = p_user_id AND link_tests_groups.test_id = p_test_id
    GROUP BY groups.id, groups.name
    ORDER BY groups.id;
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
(2, 'Patryk.Orzechowski', '5c027691a1c8af0f69d769df91ccd9a11b4f0a6eb129d80b861e2232d2b2d198', 'b24db6b35f2d5ed7f6abb31871bd9efd', 2),
(8, 'aloizy.nowak', '6e716e830dbff9a1c5aa716c9b480cf6d30109977b63154e0ed94ad2d1397564', '0', 1),
(11, 'Alicja.Kozlowska', 'e12f46d2ebd9ed53b881f050dade5c8b967decd4992cecfde19b7d1d604e085b', '7acd5a5e702500c2a300fc65d8acb175', 3);

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
(2, 'Patryk', 'Orzechowski', NULL, 'patryk@cos.com'),
(11, 'Alicja', 'Kozlowska', 0, 'alicja@cos.com');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `activated_tests`
--

CREATE TABLE `activated_tests` (
  `id` int(11) NOT NULL,
  `activation_time` datetime NOT NULL,
  `test_code` varchar(6) DEFAULT NULL,
  `test_id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `code_generated` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activated_tests`
--

INSERT INTO `activated_tests` (`id`, `activation_time`, `test_code`, `test_id`, `account_id`, `code_generated`) VALUES
(5, '2024-05-25 02:41:00', NULL, 4, 2, 1),
(6, '2024-05-25 02:56:00', NULL, 4, 2, 1),
(7, '2024-05-29 00:40:00', NULL, 8, 2, 1),
(8, '2024-06-01 10:00:00', NULL, 4, 2, 1),
(9, '2024-06-01 11:00:00', NULL, 5, 8, 1),
(10, '2024-06-01 12:00:00', NULL, 6, 11, 1),
(11, '2024-06-09 18:08:00', 'M3AXHE', 7, 2, 1);

--
-- Wyzwalacze `activated_tests`
--
DELIMITER $$
CREATE TRIGGER `generate_test_code` BEFORE INSERT ON `activated_tests` FOR EACH ROW BEGIN
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
  `id` int(11) NOT NULL,
  `text` text DEFAULT NULL,
  `correct` tinyint(1) DEFAULT NULL,
  `question_id` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`id`, `text`, `correct`, `question_id`, `deleted`) VALUES
(17, 'Tak', 0, 2, 0),
(18, 'Nie', 0, 2, 0),
(80, 'Nie', 0, 1, 0),
(81, 'Super', 1, 1, 0),
(82, 'odpowiedź otwarta', NULL, 13, 0);

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
(16, 'Grupa 1', 2),
(20, 'Grupa 3', 1),
(21, 'Grupa 4', 2),
(22, 'Grupa 5', 8);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `link_account_activated_tests`
--

CREATE TABLE `link_account_activated_tests` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `activated_test_id` int(11) NOT NULL,
  `points` int(11) NOT NULL DEFAULT 0,
  `checked` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `link_account_activated_tests`
--

INSERT INTO `link_account_activated_tests` (`id`, `account_id`, `activated_test_id`, `points`, `checked`) VALUES
(1, 11, 5, 0, 0),
(2, 2, 7, 1000, 0),
(3, 8, 5, 20, 1),
(13, 11, 11, 1000, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `link_account_activated_tests_answer`
--

CREATE TABLE `link_account_activated_tests_answer` (
  `id` int(11) NOT NULL,
  `link_account_activated_test_id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  `checked` tinyint(1) NOT NULL DEFAULT 0,
  `question_id` int(11) NOT NULL,
  `points` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `link_account_activated_tests_answer`
--

INSERT INTO `link_account_activated_tests_answer` (`id`, `link_account_activated_test_id`, `answer_id`, `checked`, `question_id`, `points`) VALUES
(1, 2, 17, 0, 2, 0),
(3, 2, 18, 0, 2, 0),
(4, 2, 81, 0, 1, 0),
(13, 2, 81, 0, 1, 0),
(19, 13, 81, 0, 1, 0);

--
-- Wyzwalacze `link_account_activated_tests_answer`
--
DELIMITER $$
CREATE TRIGGER `after_insert_link_account_activated_tests_answer` AFTER INSERT ON `link_account_activated_tests_answer` FOR EACH ROW BEGIN
    DECLARE total_answers INT;
    DECLARE correct_answers INT;
    DECLARE question_points INT;

    -- Sprawdzenie, czy pytanie jest zamknięte
    IF (SELECT opened FROM questions WHERE id = NEW.question_id) = 0 THEN
        -- Liczba odpowiedzi dla danego question_id i link_account_activated_test_id
        SELECT COUNT(*) INTO total_answers
        FROM link_account_activated_tests_answer
        WHERE question_id = NEW.question_id
        AND link_account_activated_test_id = NEW.link_account_activated_test_id;

        -- Liczba poprawnych odpowiedzi dla danego question_id i link_account_activated_test_id
        SELECT COUNT(*) INTO correct_answers
        FROM link_account_activated_tests_answer laata
        JOIN answers a ON laata.answer_id = a.id
        WHERE laata.question_id = NEW.question_id
        AND laata.link_account_activated_test_id = NEW.link_account_activated_test_id
        AND a.correct = 1;

        -- Jeśli wszystkie odpowiedzi są poprawne, dodaj punkty
        IF total_answers = correct_answers THEN
            UPDATE link_account_activated_tests
            SET points = points + (SELECT points FROM questions WHERE id = NEW.question_id)
            WHERE id = NEW.link_account_activated_test_id;
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_update_link_account_activated_tests_answer_points` AFTER UPDATE ON `link_account_activated_tests_answer` FOR EACH ROW BEGIN
    DECLARE total_points INT;

    -- Obliczenie sumy punktów dla danego link_account_activated_test_id
    SELECT SUM(points) INTO total_points
    FROM link_account_activated_tests_answer
    WHERE link_account_activated_test_id = NEW.link_account_activated_test_id;

    -- Aktualizacja punktów w link_account_activated_tests
    UPDATE link_account_activated_tests
    SET points = total_points
    WHERE id = NEW.link_account_activated_test_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `link_groups_questions`
--

CREATE TABLE `link_groups_questions` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `link_groups_questions`
--

INSERT INTO `link_groups_questions` (`id`, `question_id`, `group_id`) VALUES
(38, 1, 15),
(39, 1, 16),
(40, 1, 20),
(41, 2, 21),
(42, 1, 22);

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
(1, 2, 1),
(2, 1, 1),
(5, 8, 2),
(6, 11, 1),
(7, 2, 1),
(8, 8, 2),
(9, 11, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `link_tests_groups`
--

CREATE TABLE `link_tests_groups` (
  `id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `question_count` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `link_tests_groups`
--

INSERT INTO `link_tests_groups` (`id`, `test_id`, `group_id`, `question_count`) VALUES
(5, 7, 15, 1),
(6, 7, 16, 1),
(7, 8, 15, 0),
(8, 8, 16, 0),
(9, 4, 20, 5),
(10, 5, 21, 10),
(11, 6, 22, 15);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `organisations`
--

CREATE TABLE `organisations` (
  `id` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `initials` varchar(4) NOT NULL,
  `Address` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organisations`
--

INSERT INTO `organisations` (`id`, `Name`, `initials`, `Address`) VALUES
(1, 'Uniwersytet Łódzki', 'UL', 'Narutowicz 68'),
(2, 'Uniwersytet Warszawski', 'UW', 'aa');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `opened` tinyint(1) NOT NULL,
  `text` text NOT NULL,
  `points` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `opened`, `text`, `points`, `account_id`) VALUES
(1, 0, 'Czy się udało?', 1000, 2),
(2, 0, 'Czy się udało?', 1000, NULL),
(6, 0, 'Czy lubisz programowanie?', 5, 2),
(7, 1, 'Opisz swoje doświadczenie w programowaniu.', 10, 2),
(8, 0, 'Czy jesteś zadowolony z nauki programowania?', 7, 2),
(13, 1, 'Otwarte?', 0, 2);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `question_image`
--

CREATE TABLE `question_image` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question_image`
--

INSERT INTO `question_image` (`id`, `question_id`, `image`) VALUES
(4, 1, 'image1.jpg'),
(5, 2, 'image2.jpg'),
(6, 6, 'image3.jpg');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `tests`
--

CREATE TABLE `tests` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `time` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`id`, `name`, `time`, `account_id`) VALUES
(1, 'Test', 10, NULL),
(3, 'Test', 10, NULL),
(4, 'Uniwersytet Łódzki', 1, 2),
(5, 'Test', 10, NULL),
(6, 'Test3', 10, NULL),
(7, 'Test', 10, 2),
(8, 'Test4', 1, 2);

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
-- Indeksy dla tabeli `activated_tests`
--
ALTER TABLE `activated_tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_id` (`test_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indeksy dla tabeli `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indeksy dla tabeli `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indeksy dla tabeli `link_account_activated_tests`
--
ALTER TABLE `link_account_activated_tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `activated_test_id` (`activated_test_id`);

--
-- Indeksy dla tabeli `link_account_activated_tests_answer`
--
ALTER TABLE `link_account_activated_tests_answer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `answer_id` (`answer_id`),
  ADD KEY `link_account_activated_test_id` (`link_account_activated_test_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indeksy dla tabeli `link_groups_questions`
--
ALTER TABLE `link_groups_questions`
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
-- Indeksy dla tabeli `link_tests_groups`
--
ALTER TABLE `link_tests_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_id` (`test_id`),
  ADD KEY `link_test_question_ibfk_2` (`group_id`);

--
-- Indeksy dla tabeli `organisations`
--
ALTER TABLE `organisations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `initials` (`initials`);

--
-- Indeksy dla tabeli `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indeksy dla tabeli `question_image`
--
ALTER TABLE `question_image`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `image` (`image`),
  ADD KEY `question_id` (`question_id`);

--
-- Indeksy dla tabeli `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `name` (`name`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `activated_tests`
--
ALTER TABLE `activated_tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `link_account_activated_tests`
--
ALTER TABLE `link_account_activated_tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `link_account_activated_tests_answer`
--
ALTER TABLE `link_account_activated_tests_answer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `link_groups_questions`
--
ALTER TABLE `link_groups_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `link_organisations_accounts`
--
ALTER TABLE `link_organisations_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `link_tests_groups`
--
ALTER TABLE `link_tests_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `organisations`
--
ALTER TABLE `organisations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `question_image`
--
ALTER TABLE `question_image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account_data`
--
ALTER TABLE `account_data`
  ADD CONSTRAINT `account_data_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`);

--
-- Constraints for table `activated_tests`
--
ALTER TABLE `activated_tests`
  ADD CONSTRAINT `activated_tests_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`),
  ADD CONSTRAINT `activated_tests_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`);

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`);

--
-- Constraints for table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`);

--
-- Constraints for table `link_account_activated_tests`
--
ALTER TABLE `link_account_activated_tests`
  ADD CONSTRAINT `link_account_activated_tests_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`),
  ADD CONSTRAINT `link_account_activated_tests_ibfk_2` FOREIGN KEY (`activated_test_id`) REFERENCES `activated_tests` (`id`);

--
-- Constraints for table `link_account_activated_tests_answer`
--
ALTER TABLE `link_account_activated_tests_answer`
  ADD CONSTRAINT `link_account_activated_tests_answer_ibfk_1` FOREIGN KEY (`answer_id`) REFERENCES `answers` (`id`),
  ADD CONSTRAINT `link_account_activated_tests_answer_ibfk_2` FOREIGN KEY (`link_account_activated_test_id`) REFERENCES `link_account_activated_tests` (`id`),
  ADD CONSTRAINT `link_account_activated_tests_answer_ibfk_3` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`);

--
-- Constraints for table `link_groups_questions`
--
ALTER TABLE `link_groups_questions`
  ADD CONSTRAINT `link_groups_questions_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `link_groups_questions_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`);

--
-- Constraints for table `link_organisations_accounts`
--
ALTER TABLE `link_organisations_accounts`
  ADD CONSTRAINT `link_organisations_accounts_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`),
  ADD CONSTRAINT `link_organisations_accounts_ibfk_2` FOREIGN KEY (`organisation_id`) REFERENCES `organisations` (`id`);

--
-- Constraints for table `link_tests_groups`
--
ALTER TABLE `link_tests_groups`
  ADD CONSTRAINT `link_tests_groups_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`),
  ADD CONSTRAINT `link_tests_groups_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`);

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`);

--
-- Constraints for table `question_image`
--
ALTER TABLE `question_image`
  ADD CONSTRAINT `question_image_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`);

--
-- Constraints for table `tests`
--
ALTER TABLE `tests`
  ADD CONSTRAINT `tests_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`);

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `update_test_code_to_null` ON SCHEDULE EVERY 1 MINUTE STARTS '2024-05-25 02:54:55' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE activated_tests at
  JOIN tests t ON at.test_id = t.id
  SET at.test_code = NULL
  WHERE at.test_code IS NOT NULL
  AND at.activation_time <= DATE_SUB(NOW(), INTERVAL t.time MINUTE)$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
