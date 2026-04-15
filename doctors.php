<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Наши врачи — МЦ «Здоровье+»</title>
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
                <li><a href="doctors.php" class="active">Врачи</a></li>
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
        <h1>Наши врачи</h1>
        <div class="breadcrumbs">
            <a href="index.php">Главная</a> / <span>Врачи</span>
        </div>
    </div>
</section>

<!-- ===== ФИЛЬТР ПО СПЕЦИАЛИЗАЦИЯМ ===== -->
<section class="doctors-filter">
    <div class="container">
        <div class="filter-tabs">
            <span class="filter-btn active">Все специалисты</span>
            <span class="filter-btn">Терапевты</span>
            <span class="filter-btn">Кардиологи</span>
            <span class="filter-btn">Неврологи</span>
            <span class="filter-btn">Педиатры</span>
            <span class="filter-btn">Стоматологи</span>
        </div>
    </div>
</section>

<!-- ===== СПИСОК ВРАЧЕЙ ИЗ БД ===== -->
<section class="doctors-list">
    <div class="container">
        <div class="doctors-grid-full">
            
            <?php
            $stmt = $pdo->query("SELECT * FROM doctors ORDER BY id");
            $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($doctors as $doctor):
                $photo = !empty($doctor['photo']) ? $doctor['photo'] : '';
            ?>
            
            <div class="doctor-card-full">
                <div class="doctor-photo">
                    <?php if ($photo && file_exists($photo)): ?>
                        <img src="<?php echo $photo; ?>" alt="<?php echo htmlspecialchars($doctor['name']); ?>">
                    <?php else: ?>
                        <div class="doctor-photo-fallback"><i class="fas fa-user-md"></i></div>
                    <?php endif; ?>
                </div>
                <div class="doctor-info">
                    <h3><?php echo htmlspecialchars($doctor['name']); ?></h3>
                    <p class="doctor-spec"><?php echo htmlspecialchars($doctor['specialization']); ?></p>
                    <div class="doctor-details">
                        <span><i class="fas fa-calendar-alt"></i> Стаж <?php echo $doctor['experience']; ?> лет</span>
                        <span><i class="fas fa-star"></i> <?php echo $doctor['rating']; ?> (отзывы)</span>
                    </div>
                    <p class="doctor-about"><?php echo htmlspecialchars($doctor['description']); ?></p>
                    <div class="doctor-schedule">
                        <i class="fas fa-clock"></i> Ближайшая запись: уточняйте по телефону
                    </div>
                </div>
                <div class="doctor-actions">
                    <div class="doctor-price">от <?php echo number_format($doctor['price'], 0, '', ' '); ?> ₽</div>
                    <a href="appointment.php" class="btn-doctor-full">Записаться →</a>
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