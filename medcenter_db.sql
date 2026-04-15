-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Апр 15 2026 г., 22:02
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `medcenter_db`
--

-- --------------------------------------------------------

--
-- Структура таблицы `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `patient_name` varchar(100) NOT NULL,
  `patient_phone` varchar(20) NOT NULL,
  `patient_email` varchar(100) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `status` enum('new','confirmed','completed','cancelled') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `doctor_id`, `service_id`, `patient_name`, `patient_phone`, `patient_email`, `appointment_date`, `appointment_time`, `comment`, `status`, `created_at`) VALUES
(1, NULL, 3, 3, 'Чучкова Мария', '+79012780859', 'zuu123@gmail.com', '2026-06-13', '14:00:00', '', 'new', '2026-04-02 12:47:07'),
(2, NULL, 2, 6, 'Агапов Алексей', '89036467575', 'alex12@gmail.com', '2026-04-10', '16:00:00', '', 'new', '2026-04-09 12:08:05');

-- --------------------------------------------------------

--
-- Структура таблицы `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `experience` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `price` int(11) NOT NULL,
  `rating` decimal(2,1) DEFAULT 0.0,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `doctors`
--

INSERT INTO `doctors` (`id`, `name`, `specialization`, `experience`, `description`, `price`, `rating`, `photo`) VALUES
(1, 'Иванова Елена Петровна', 'Терапевт', 18, 'Врач высшей категории. Занимается диагностикой и лечением заболеваний внутренних органов.', 1500, 4.8, 'doc5.jpg'),
(2, 'Смирнов Алексей Игоревич', 'Кардиолог', 15, 'Специализируется на лечении гипертонии, ишемической болезни сердца, аритмии.', 1800, 5.0, 'doc7.jpg'),
(3, 'Козлова Мария Андреевна', 'Невролог', 12, 'Лечит головные боли, невралгии, остеохондроз, последствия стрессов.', 1700, 4.9, 'dev3.jpg'),
(4, 'Петрова Наталья Владимировна', 'Педиатр', 20, 'Детский врач высшей категории. Наблюдение детей с рождения до 18 лет.', 1600, 4.9, '25.jpg'),
(5, 'Соколов Дмитрий Сергеевич', 'Стоматолог', 10, 'Лечение кариеса, пульпита, профессиональная гигиена полости рта.', 2200, 4.7, '76.jpg'),
(6, 'Морозова Елена Александровна', 'УЗИ-диагност', 14, 'Проводит УЗИ брюшной полости, щитовидной железы, сосудов.', 2000, 4.8, '99.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` int(11) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `price`, `category`, `icon`) VALUES
(1, 'Приём терапевта', 'Первичный приём, осмотр, консультация, назначение лечения', 1500, 'therapy', 'stethoscope'),
(2, 'УЗИ брюшной полости', 'Комплексное исследование органов брюшной полости', 2500, 'diagnostic', 'heartbeat'),
(3, 'Общий анализ крови', 'Клинический анализ крови с лейкоцитарной формулой', 650, 'analyses', 'flask'),
(4, 'Лечение кариеса', 'Пломбирование зуба светоотверждаемым материалом', 3200, 'treatment', 'tooth'),
(5, 'Консультация кардиолога', 'Приём врача-кардиолога, ЭКГ, расшифровка', 1800, 'therapy', 'heart'),
(6, 'Консультация невролога', 'Приём врача-невролога, диагностика, лечение', 1700, 'therapy', 'brain'),
(7, 'Приём педиатра', 'Консультация детского врача для детей от 0 до 18 лет', 1600, 'therapy', 'baby'),
(8, 'МРТ головного мозга', 'Магнитно-резонансная томография на аппарате 3 Тесла', 5500, 'diagnostic', 'magnet');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `phone`, `email`, `password`, `created_at`, `avatar`) VALUES
(1, 'Иванов Степан', '89058489893', 'Lizok073@yandex.ru', '$2y$10$OhYuKui0HWOqFHY6E5JovOlnsaf1l4qmno5SZ7jvkp32Ip8QTjC.m', '2026-04-02 12:51:22', 'uploads/avatars/user_1_1775210676.jpg'),
(2, 'Алексей', '89036467575', 'alex12@gmail.com', '$2y$10$CHUJqYLeMJLi2ZrTfUwr2OUqKzLgVnICJ.w9GxX2iccb/L9g0lAVa', '2026-04-09 12:04:56', NULL),
(3, 'Савинов Андрей', '89088888888', 'sac@gdjj.hhh', '$2y$10$NFvCGtv3w8Csi2scwrYn4ONlkTTwmzgqdCh39gQfzlnvfx8ZkfqfK', '2026-04-09 13:21:44', NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `fk_appointments_users` (`user_id`),
  ADD KEY `fk_appointments_services` (`service_id`);

--
-- Индексы таблицы `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_appointments_doctors` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_appointments_services` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_appointments_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
