-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Φιλοξενητής: 127.0.0.1
-- Χρόνος δημιουργίας: 21 Δεκ 2020 στις 16:33:18
-- Έκδοση διακομιστή: 10.4.14-MariaDB
-- Έκδοση PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Βάση δεδομένων: `score4`
--

DELIMITER $$
--
-- Διαδικασίες
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `clean_board` ()  BEGIN
UPDATE `players` SET username=NULL, token=NULL ;
UPDATE `game_status` SET `status`='not active', `p_turn`=NULL, `result`=NULL;
	REPLACE INTO board SELECT * FROM board_empty;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `place_piece` (IN `x1` TINYINT, IN `y1` TINYINT)  BEGIN
    DECLARE p_color CHAR;

    SELECT p_turn INTO p_color
    FROM game_status;

    UPDATE board
    SET piece_color=p_color
    WHERE x=x1 AND y=y1;
    
    UPDATE game_status SET p_turn=IF(p_color='R','B','R');
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `board`
--

CREATE TABLE `board` (
  `x` tinyint(1) NOT NULL,
  `y` tinyint(1) NOT NULL,
  `piece_color` enum('R','B') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Άδειασμα δεδομένων του πίνακα `board`
--

INSERT INTO `board` (`x`, `y`, `piece_color`) VALUES
(1, 1, NULL),
(1, 2, NULL),
(1, 3, NULL),
(1, 4, NULL),
(1, 5, NULL),
(1, 6, NULL),
(2, 1, NULL),
(2, 2, NULL),
(2, 3, NULL),
(2, 4, NULL),
(2, 5, NULL),
(2, 6, NULL),
(3, 1, NULL),
(3, 2, NULL),
(3, 3, NULL),
(3, 4, NULL),
(3, 5, NULL),
(3, 6, NULL),
(4, 1, NULL),
(4, 2, NULL),
(4, 3, NULL),
(4, 4, NULL),
(4, 5, NULL),
(4, 6, NULL),
(5, 1, NULL),
(5, 2, NULL),
(5, 3, NULL),
(5, 4, NULL),
(5, 5, NULL),
(5, 6, NULL),
(6, 1, NULL),
(6, 2, NULL),
(6, 3, NULL),
(6, 4, NULL),
(6, 5, NULL),
(6, 6, NULL),
(7, 1, NULL),
(7, 2, NULL),
(7, 3, NULL),
(7, 4, NULL),
(7, 5, NULL),
(7, 6, NULL);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `board_empty`
--

CREATE TABLE `board_empty` (
  `x` tinyint(1) NOT NULL,
  `y` tinyint(1) NOT NULL,
  `piece_color` enum('R','B') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Άδειασμα δεδομένων του πίνακα `board_empty`
--

INSERT INTO `board_empty` (`x`, `y`, `piece_color`) VALUES
(1, 1, NULL),
(1, 2, NULL),
(1, 3, NULL),
(1, 4, NULL),
(1, 5, NULL),
(1, 6, NULL),
(2, 1, NULL),
(2, 2, NULL),
(2, 3, NULL),
(2, 4, NULL),
(2, 5, NULL),
(2, 6, NULL),
(3, 1, NULL),
(3, 2, NULL),
(3, 3, NULL),
(3, 4, NULL),
(3, 5, NULL),
(3, 6, NULL),
(4, 1, NULL),
(4, 2, NULL),
(4, 3, NULL),
(4, 4, NULL),
(4, 5, NULL),
(4, 6, NULL),
(5, 1, NULL),
(5, 2, NULL),
(5, 3, NULL),
(5, 4, NULL),
(5, 5, NULL),
(5, 6, NULL),
(6, 1, NULL),
(6, 2, NULL),
(6, 3, NULL),
(6, 4, NULL),
(6, 5, NULL),
(6, 6, NULL),
(7, 1, NULL),
(7, 2, NULL),
(7, 3, NULL),
(7, 4, NULL),
(7, 5, NULL),
(7, 6, NULL);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `game_status`
--

CREATE TABLE `game_status` (
  `status` enum('not active','initialized','started','ended','aborded') NOT NULL DEFAULT 'not active',
  `p_turn` enum('R','B') DEFAULT NULL,
  `result` enum('R','B','D') DEFAULT NULL,
  `last_change` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Άδειασμα δεδομένων του πίνακα `game_status`
--

INSERT INTO `game_status` (`status`, `p_turn`, `result`, `last_change`) VALUES
('not active', NULL, NULL, '2020-12-20 20:07:02');

--
-- Δείκτες `game_status`
--
DELIMITER $$
CREATE TRIGGER `game_status_update` BEFORE UPDATE ON `game_status` FOR EACH ROW BEGIN
SET NEW.last_change= NOW();
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `players`
--

CREATE TABLE `players` (
  `username` varchar(20) DEFAULT NULL,
  `piece_color` enum('R','B') NOT NULL,
  `token` varchar(100) DEFAULT NULL,
  `last_action` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Άδειασμα δεδομένων του πίνακα `players`
--

INSERT INTO `players` (`username`, `piece_color`, `token`, `last_action`) VALUES
(NULL, 'R', NULL, '2020-12-20 20:06:01'),
(NULL, 'B', NULL, '2020-12-20 20:06:06');

--
-- Ευρετήρια για άχρηστους πίνακες
--

--
-- Ευρετήρια για πίνακα `board`
--
ALTER TABLE `board`
  ADD PRIMARY KEY (`x`,`y`);

--
-- Ευρετήρια για πίνακα `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`piece_color`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
