<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Услуги и цены — МЦ «Здоровье+»</title>
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
                <li><a href="services.php" class="active">Услуги и цены</a></li>
                <li><a href="doctors.php">Врачи</a></li>
                <li><a href="promo.html">Акции</a></li>
                <li><a href="contacts.html">Контакты</a></li>
                <li><a href="cabinet.php">Личный кабинет</a></li>
            </ul>
        </nav>
        <div class="header-contacts">
            <a href="tel:+78001234567" class="phone">8 (800) 123-45-67</a>
            <a href="appointment.php" class="btn btn-primary btn-small">Записаться</a>
        </div>
        <div class="burger">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</header>

<!-- ===== ЗАГОЛОВОК СТРАНИЦЫ ===== -->
<section class="page-header">
    <div class="container">
        <h1>Услуги и цены</h1>
        <div class="breadcrumbs">
            <a href="index.php">Главная</a> / <span>Услуги и цены</span>
        </div>
    </div>
</section>

<!-- ===== ФИЛЬТР УСЛУГ ===== -->
<section class="services-filter">
    <div class="container">
        <div class="filter-tabs">
            <span class="filter-btn active">Все услуги</span>
            <span class="filter-btn">Диагностика</span>
            <span class="filter-btn">Лечение</span>
            <span class="filter-btn">Анализы</span>
            <span class="filter-btn">Профилактика</span>
        </div>
    </div>
</section>

<!-- ===== СПИСОК УСЛУГ ИЗ БД ===== -->
<section class="services-list">
    <div class="container">
        <div class="services-grid-full">
            
            <?php
            $stmt = $pdo->query("SELECT * FROM services ORDER BY id");
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($services as $service):
                $icon = $service['icon'] ?? 'stethoscope';
            ?>
            
            <div class="service-item">
                <div class="service-item-icon">
                    <i class="fas fa-<?php echo $icon; ?>"></i>
                </div>
                <div class="service-item-content">
                    <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                    <p><?php echo htmlspecialchars($service['description']); ?></p>
                </div>
                <div class="service-item-price">
                    <div class="price"><?php echo number_format($service['price'], 0, '', ' '); ?> ₽</div>
                    <a href="appointment.php" class="btn-service">Записаться →</a>
                </div>
            </div>
            
            <?php endforeach; ?>
            
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