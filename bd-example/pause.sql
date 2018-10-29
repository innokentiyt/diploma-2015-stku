-- phpMyAdmin SQL Dump
-- version 4.3.11.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Июн 26 2015 г., 10:24
-- Версия сервера: 5.5.43-0ubuntu0.12.04.1-log
-- Версия PHP: 5.5.26-1+deb.sury.org~precise+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `pause`
--

-- --------------------------------------------------------

--
-- Структура таблицы `chairs`
--

CREATE TABLE IF NOT EXISTS `chairs` (
  `id` int(10) NOT NULL,
  `name` varchar(20) DEFAULT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `faculty` int(10) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `chairs`
--

INSERT INTO `chairs` (`id`, `name`, `fullname`, `faculty`) VALUES
(1, 'ЗСС', 'Защищенных систем связи', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `disciplines`
--

CREATE TABLE IF NOT EXISTS `disciplines` (
  `id` int(10) NOT NULL,
  `name` varchar(20) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `chair` int(10) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `disciplines`
--

INSERT INTO `disciplines` (`id`, `name`, `fullname`, `chair`) VALUES
(1, 'Java', 'Защищенные приложения на Java', 1),
(2, 'ЗИвОСWS', 'Защита информации в ОС Windows Server', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `faculties`
--

CREATE TABLE IF NOT EXISTS `faculties` (
  `id` int(10) NOT NULL,
  `name` varchar(20) DEFAULT NULL,
  `fullname` varchar(100) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Факультеты';

--
-- Дамп данных таблицы `faculties`
--

INSERT INTO `faculties` (`id`, `name`, `fullname`) VALUES
(1, 'ИКСС', 'Инфокоммуникационных сетей и систем');

-- --------------------------------------------------------

--
-- Структура таблицы `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(10) NOT NULL,
  `name` varchar(20) NOT NULL,
  `chair` int(10) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `groups`
--

INSERT INTO `groups` (`id`, `name`, `chair`) VALUES
(1, 'ИКТЗ-15', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `lessons`
--

CREATE TABLE IF NOT EXISTS `lessons` (
  `id` int(10) NOT NULL,
  `discipline` int(10) NOT NULL,
  `group` int(10) NOT NULL,
  `teacher` int(10) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `lessons`
--

INSERT INTO `lessons` (`id`, `discipline`, `group`, `teacher`) VALUES
(1, 1, 1, 1),
(2, 2, 1, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `registry`
--

CREATE TABLE IF NOT EXISTS `registry` (
  `id` int(10) NOT NULL,
  `student_id` int(10) NOT NULL,
  `date` date NOT NULL,
  `lesson` int(10) NOT NULL,
  `evaluation` int(10) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `registry`
--

INSERT INTO `registry` (`id`, `student_id`, `date`, `lesson`, `evaluation`) VALUES
(1, 2, '2015-05-30', 1, 1),
(2, 4, '2015-05-30', 1, 4),
(3, 1, '2015-05-30', 1, 5),
(11, 2, '2015-06-01', 1, 4),
(12, 4, '2015-06-01', 1, 4),
(13, 1, '2015-06-01', 1, 5),
(14, 2, '2015-06-03', 1, 1),
(15, 4, '2015-06-03', 1, 3),
(16, 1, '2015-06-03', 1, 4),
(17, 2, '2015-06-20', 1, 1),
(18, 4, '2015-06-20', 1, 5),
(19, 1, '2015-06-20', 1, 5),
(20, 2, '2015-06-21', 1, 5),
(21, 4, '2015-06-21', 1, 4),
(22, 1, '2015-06-21', 1, 5);

-- --------------------------------------------------------

--
-- Структура таблицы `students`
--

CREATE TABLE IF NOT EXISTS `students` (
  `id` int(10) NOT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `group` int(10) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `students`
--

INSERT INTO `students` (`id`, `last_name`, `first_name`, `middle_name`, `birth_date`, `group`) VALUES
(0, 'stub', 'stub', 'stub', '2015-05-01', NULL),
(1, 'Стручков', 'Иннокентий', 'Тимофеевич', '1993-02-18', 1),
(2, 'Исаченков', 'Павел', 'Андреевич', '1994-06-27', 1),
(4, 'Михайлова', 'Юлия', 'Олеговна', NULL, 1),
(5, 'Аршинов', 'Александр', 'Сергеевич', NULL, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `teachers`
--

CREATE TABLE IF NOT EXISTS `teachers` (
  `id` int(10) NOT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `chair` int(10) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `teachers`
--

INSERT INTO `teachers` (`id`, `last_name`, `first_name`, `middle_name`, `chair`) VALUES
(0, 'stub', 'stub', 'stub', NULL),
(1, 'Цветков', 'Александр', 'Юрьевич', 1),
(2, 'Штеренберг', 'Станислав', 'Игоревич', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `types`
--

CREATE TABLE IF NOT EXISTS `types` (
  `id` int(10) NOT NULL,
  `name` varchar(20) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `types`
--

INSERT INTO `types` (`id`, `name`) VALUES
(1, 'teacher'),
(2, 'student');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(40) NOT NULL,
  `type` int(10) NOT NULL,
  `student_id` int(10) NOT NULL DEFAULT '0',
  `teacher_id` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `type`, `student_id`, `teacher_id`) VALUES
(11, 'keshan', '14be292abdd651b7f0cc13bea865d5a591b25a8b', 2, 1, 0),
(13, 'kofshikz', '554dbf0b41b3cd068ee1fcfd6235466a263647b4', 2, 2, 0),
(23, 'user2', '554dbf0b41b3cd068ee1fcfd6235466a263647b4', 1, 0, 2),
(24, 'user1', '554dbf0b41b3cd068ee1fcfd6235466a263647b4', 1, 0, 1),
(26, 'pupkin', '63b53e7cb7501422f366b81500c25680524611c6', 2, 4, 0);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `chairs`
--
ALTER TABLE `chairs`
  ADD PRIMARY KEY (`id`), ADD KEY `faculty` (`faculty`);

--
-- Индексы таблицы `disciplines`
--
ALTER TABLE `disciplines`
  ADD PRIMARY KEY (`id`), ADD KEY `chair` (`chair`);

--
-- Индексы таблицы `faculties`
--
ALTER TABLE `faculties`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`), ADD KEY `chair` (`chair`);

--
-- Индексы таблицы `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`), ADD KEY `discipline` (`discipline`), ADD KEY `group` (`group`), ADD KEY `teacher` (`teacher`);

--
-- Индексы таблицы `registry`
--
ALTER TABLE `registry`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `student_id_2` (`student_id`,`date`,`lesson`) COMMENT 'Одна оценка на один урок в один день', ADD KEY `student_id` (`student_id`), ADD KEY `lesson` (`lesson`);

--
-- Индексы таблицы `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`), ADD KEY `group` (`group`);

--
-- Индексы таблицы `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`), ADD KEY `chair` (`chair`);

--
-- Индексы таблицы `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`id`), ADD KEY `id` (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `username` (`username`), ADD UNIQUE KEY `unique_id` (`student_id`,`teacher_id`), ADD KEY `student_id` (`student_id`), ADD KEY `teacher_id` (`teacher_id`), ADD KEY `type` (`type`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `chairs`
--
ALTER TABLE `chairs`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `disciplines`
--
ALTER TABLE `disciplines`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `faculties`
--
ALTER TABLE `faculties`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `registry`
--
ALTER TABLE `registry`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT для таблицы `students`
--
ALTER TABLE `students`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT для таблицы `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `types`
--
ALTER TABLE `types`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=28;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `chairs`
--
ALTER TABLE `chairs`
ADD CONSTRAINT `chairs_ibfk_1` FOREIGN KEY (`faculty`) REFERENCES `faculties` (`id`);

--
-- Ограничения внешнего ключа таблицы `disciplines`
--
ALTER TABLE `disciplines`
ADD CONSTRAINT `disciplines_ibfk_1` FOREIGN KEY (`chair`) REFERENCES `chairs` (`id`);

--
-- Ограничения внешнего ключа таблицы `groups`
--
ALTER TABLE `groups`
ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`chair`) REFERENCES `chairs` (`id`);

--
-- Ограничения внешнего ключа таблицы `lessons`
--
ALTER TABLE `lessons`
ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`discipline`) REFERENCES `disciplines` (`id`),
ADD CONSTRAINT `lessons_ibfk_2` FOREIGN KEY (`group`) REFERENCES `groups` (`id`),
ADD CONSTRAINT `lessons_ibfk_3` FOREIGN KEY (`teacher`) REFERENCES `teachers` (`id`);

--
-- Ограничения внешнего ключа таблицы `registry`
--
ALTER TABLE `registry`
ADD CONSTRAINT `registry_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
ADD CONSTRAINT `registry_ibfk_2` FOREIGN KEY (`lesson`) REFERENCES `lessons` (`id`);

--
-- Ограничения внешнего ключа таблицы `students`
--
ALTER TABLE `students`
ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`group`) REFERENCES `groups` (`id`);

--
-- Ограничения внешнего ключа таблицы `teachers`
--
ALTER TABLE `teachers`
ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`chair`) REFERENCES `chairs` (`id`);

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`type`) REFERENCES `types` (`id`),
ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
ADD CONSTRAINT `users_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
