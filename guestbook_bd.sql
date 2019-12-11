-- phpMyAdmin SQL Dump
-- version 4.6.2
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Апр 02 2017 г., 19:11
-- Версия сервера: 5.7.13-log
-- Версия PHP: 5.6.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `guestbook_bd`
--

-- --------------------------------------------------------

--
-- Структура таблицы `messages`
--

CREATE TABLE `messages` (
  `id_message` int(11) NOT NULL,
  `filename` mediumblob,
  `message` text NOT NULL,
  `author` varchar(25) NOT NULL,
  `message_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

--
-- Дамп данных таблицы `messages`
--

INSERT INTO `messages` (`id_message`, `filename`, `message`, `author`, `message_time`) VALUES
(1, NULL, 'тестовое сообщение', 'admin', '2017-03-08 05:08:08'),
(2, NULL, 'message1', 'qqq', '2017-03-08 07:22:26'),
(3, NULL, 'message2', 'www', '2017-03-08 11:34:43'),
(4, NULL, 'Сообщение для проверки', 'qwerty', '2017-03-08 13:43:44'),
(5, 0x352e6a7067, 'Финальный тест', 'NIKE', '2017-04-02 16:03:09');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id_message`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `messages`
--
ALTER TABLE `messages`
  MODIFY `id_message` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
