<?php
require_once 'config.php';

// Переменные для сообщений
$success_message = '';
$error_message = '';

// Обработка формы при отправке
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $birthdate = trim($_POST['birthdate'] ?? '');
    $service_id = intval($_POST['service'] ?? 0);
    $doctor_id = intval($_POST['doctor'] ?? 0);
    $date = trim($_POST['date'] ?? '');
    $time = trim($_POST['time'] ?? '');
    $comment = trim($_POST['comment'] ?? '');
    
    // Простая валидация
    if (empty($name) || empty($phone) || empty($date) || $service_id == 0) {
        $error_message = 'Пожалуйста, заполните все обязательные поля.';
    } else {
        try {
            // Сохраняем запись в базу данных
            $stmt = $pdo->prepare("
                INSERT INTO appointments (patient_name, patient_phone, patient_email, appointment_date, appointment_time, service_id, doctor_id, comment, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'new', NOW())
            ");
            $stmt->execute([$name, $phone, $email, $date, $time, $service_id, $doctor_id, $comment]);
            
            $success_message = '✅ Заявка успешно отправлена! Мы свяжемся с вами для подтверждения.';
            
            // Очищаем форму (опционально)
            $_POST = [];
        } catch (PDOException $e) {
            $error_message = 'Ошибка при сохранении: ' . $e->getMessage();
        }
    }
}

// Получаем список услуг для выпадающего списка
$services = $pdo->query("SELECT id, name, price FROM services ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

// Получаем список врачей для выпадающего списка
$doctors = $pdo->query("SELECT id, name, specialization FROM doctors ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Запись онлайн — МЦ «Здоровье+»</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<!-- ===== ШАПКА ===== -->
<header class="header">
    <div class="container">
        <div class="logo">
            <img src="log.png" alt="МЦ Здоровье+" class="logo-img">
            <span class="logo-name">МЦ <span class="logo-accent">Здоровье+</span></span>
        </div>
        <nav class="nav">
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="about.html">О клинике</a></li>
                <li><a href="services.php">Услуги и цены</a></li>
                <li><a href="doctors.php">Врачи</a></li>
                <li><a href="promo.html">Акции</a></li>
                <li><a href="contacts.html">Контакты</a></li>
            </ul>
        </nav>
        <div class="header-contacts">
            <a href="tel:+78001234567" class="phone">8 (800) 123-45-67</a>
            <a href="appointment.php" class="btn btn-primary btn-small active">Записаться</a>
        </div>
        <div class="burger">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</header>

<!-- ===== ЗАГОЛОВОК СТРАНИЦЫ ===== -->
<section class="page-header">
    <div class="container">
        <h1>Запись на приём</h1>
        <div class="breadcrumbs">
            <a href="index.php">Главная</a> / <span>Запись онлайн</span>
        </div>
    </div>
</section>

<!-- ===== ФОРМА ЗАПИСИ ===== -->
<section class="appointment">
    <div class="container">
        <div class="appointment-form-container">
            <div class="section-header">
                <h2>Заполните <span class="highlight">форму</span></h2>
                <p class="section-desc">Мы свяжемся с вами для подтверждения записи</p>
            </div>
            
            <!-- Сообщения об ошибках/успехе -->
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="appointment-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Ваше имя <span>*</span></label>
                        <input type="text" id="name" name="name" placeholder="Иванов Иван" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Телефон <span>*</span></label>
                        <input type="tel" id="phone" name="phone" placeholder="+7 (999) 123-45-67" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="ivanov@mail.ru" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="birthdate">Дата рождения</label>
                        <input type="date" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($_POST['birthdate'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="service">Выберите услугу <span>*</span></label>
                        <select id="service" name="service" required>
                            <option value="">— Выберите услугу —</option>
                            <?php foreach ($services as $service): ?>
                                <option value="<?php echo $service['id']; ?>" <?php echo (($_POST['service'] ?? '') == $service['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($service['name']); ?> — <?php echo number_format($service['price'], 0, '', ' '); ?> ₽
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="doctor">Выберите врача</label>
                        <select id="doctor" name="doctor">
                            <option value="">— Любой врач —</option>
                            <?php foreach ($doctors as $doctor): ?>
                                <option value="<?php echo $doctor['id']; ?>" <?php echo (($_POST['doctor'] ?? '') == $doctor['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($doctor['name']) . ' (' . htmlspecialchars($doctor['specialization']) . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="date">Желаемая дата <span>*</span></label>
                        <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($_POST['date'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="time">Желаемое время</label>
                        <select id="time" name="time">
                            <option value="">— Любое время —</option>
                            <option value="09:00" <?php echo (($_POST['time'] ?? '') == '09:00') ? 'selected' : ''; ?>>09:00</option>
                            <option value="10:00" <?php echo (($_POST['time'] ?? '') == '10:00') ? 'selected' : ''; ?>>10:00</option>
                            <option value="11:00" <?php echo (($_POST['time'] ?? '') == '11:00') ? 'selected' : ''; ?>>11:00</option>
                            <option value="12:00" <?php echo (($_POST['time'] ?? '') == '12:00') ? 'selected' : ''; ?>>12:00</option>
                            <option value="13:00" <?php echo (($_POST['time'] ?? '') == '13:00') ? 'selected' : ''; ?>>13:00</option>
                            <option value="14:00" <?php echo (($_POST['time'] ?? '') == '14:00') ? 'selected' : ''; ?>>14:00</option>
                            <option value="15:00" <?php echo (($_POST['time'] ?? '') == '15:00') ? 'selected' : ''; ?>>15:00</option>
                            <option value="16:00" <?php echo (($_POST['time'] ?? '') == '16:00') ? 'selected' : ''; ?>>16:00</option>
                            <option value="17:00" <?php echo (($_POST['time'] ?? '') == '17:00') ? 'selected' : ''; ?>>17:00</option>
                            <option value="18:00" <?php echo (($_POST['time'] ?? '') == '18:00') ? 'selected' : ''; ?>>18:00</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group full-width">
                    <label for="comment">Комментарий</label>
                    <textarea id="comment" name="comment" rows="3" placeholder="Дополнительная информация"><?php echo htmlspecialchars($_POST['comment'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group checkbox">
                    <input type="checkbox" id="agree" required>
                    <label for="agree">Я согласен на обработку персональных данных</label>
                </div>
                
                <div class="form-submit">
                    <button type="submit" class="btn btn-primary btn-large">Записаться на приём</button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- ===== ФУТЕР ===== -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <div class="footer-logo">
                    <div class="footer-logo-text">
                        <span class="footer-logo-prefix">МЦ</span>
                        <span class="footer-logo-accent">Здоровье+</span>
                    </div>
                </div>
                <p class="footer-about">Современный медицинский центр с собственным диагностическим оборудованием и опытными врачами.</p>
                <p class="footer-license">Лицензия № ЛО-77-01-012345</p>
            </div>
            <div class="footer-col">
                <h4>О клинике</h4>
                <ul>
                    <li><a href="about.html">О нас</a></li>
                    <li><a href="#">Лицензии</a></li>
                    <li><a href="#">Фотогалерея</a></li>
                    <li><a href="#">Вакансии</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Пациентам</h4>
                <ul>
                    <li><a href="services.php">Услуги и цены</a></li>
                    <li><a href="doctors.php">Врачи</a></li>
                    <li><a href="promo.html">Акции</a></li>
                    <li><a href="#">Отзывы</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Контакты</h4>
                <ul>
                    <li><a href="contacts.html">Адрес</a></li>
                    <li><a href="contacts.html">Обратная связь</a></li>
                    <li><a href="appointment.php">Запись онлайн</a></li>
                    <li><a href="#">Вопрос-ответ</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2025 Медицинский центр «Здоровье+».</p>
            <p>Разработка Web-сайта — курсовой проект</p>
        </div>
    </div>
</footer>
<script>
    // Бургер-меню
    document.addEventListener('DOMContentLoaded', function() {
        var burger = document.querySelector('.burger');
        var nav = document.querySelector('.nav');
        
        if (burger && nav) {
            burger.onclick = function() {
                nav.classList.toggle('active');
                burger.classList.toggle('active');
            };
        }
    });
</script>
</body>
</html>