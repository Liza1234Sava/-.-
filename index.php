<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Медицинский центр «Здоровье+» — современная клиника в Москве</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="promo-fix.css">
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
                <li><a href="index.php" class="active">Главная</a></li>
                <li><a href="about.html">О клинике</a></li>
                <li><a href="services.php">Услуги и цены</a></li>
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

<!-- ===== HERO СЕКЦИЯ ===== -->
<section class="hero">
    <div class="hero-pattern"></div>
    <div class="container">
        <div class="hero-content">
            <span class="hero-badge">Лицензия № ЛО-77-01-012345</span>
            <h1>
                Забота о вашем здоровье<br>
                <span class="hero-subtitle">с <span class="highlight-soft">современным</span> подходом</span>
            </h1>
            <p class="hero-text">Собственная лаборатория, экспертный класс УЗИ, МРТ 3 Тесла и опытные врачи. <br>Всё для точной диагностики и эффективного лечения.</p>
            <div class="hero-buttons">
                <a href="appointment.php" class="btn btn-primary btn-large">Записаться онлайн</a>
                <a href="services.php" class="btn btn-outline btn-large">Узнать цены</a>
            </div>
            <div class="hero-features">
                <div class="feature-item">
                    <i class="fas fa-calendar-check"></i>
                    <span>Запись за 1 минуту</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-file-invoice"></i>
                    <span>Прозрачные цены</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Стерильно и безопасно</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== СТАТИСТИКА ===== -->
<section class="stats">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-number">20+</span>
                <span class="stat-label">лет работы</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">65</span>
                <span class="stat-label">врачей</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">150K+</span>
                <span class="stat-label">пациентов</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">12</span>
                <span class="stat-label">отделений</span>
            </div>
        </div>
    </div>
</section>

<!-- ===== УСЛУГИ С ЦЕНАМИ (из БД) ===== -->
<section class="services">
    <div class="container">
        <div class="section-header">
            <h2>Наши услуги <span class="highlight">и цены</span></h2>
            <p class="section-desc">Фиксированная стоимость без скрытых платежей</p>
        </div>
        <div class="services-grid">
            
            <?php
            $stmt = $pdo->query("SELECT * FROM services ORDER BY id LIMIT 6");
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($services as $service):
                $icon = $service['icon'] ?? 'stethoscope';
            ?>
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-<?php echo $icon; ?>"></i>
                </div>
                <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                <div class="service-price"><?php echo number_format($service['price'], 0, '', ' '); ?> ₽</div>
                <p class="service-desc"><?php echo htmlspecialchars($service['description']); ?></p>
                <a href="appointment.php" class="service-link">Записаться →</a>
            </div>
            <?php endforeach; ?>
            
        </div>
        <div class="services-all">
            <a href="services.php" class="btn btn-secondary">Все услуги и цены</a>
        </div>
    </div>
</section>

<!-- ===== АКЦИИ ===== -->
<section class="promo">
    <div class="container">
        <div class="section-header">
            <h2>Акции <span class="highlight">и предложения</span></h2>
        </div>
        <div class="promo-grid">
            <div class="promo-card promo-card-orange">
                <span class="promo-tag">🔥 -20%</span>
                <h3>Комплексное обследование</h3>
                <p>Check-up за 2 часа: терапевт + УЗИ + анализы</p>
                <div class="promo-price-block">
                    <span class="old-price">7 400 ₽</span>
                    <span class="new-price">5 900 ₽</span>
                </div>
                <a href="promo.html" class="btn btn-light">Подробнее</a>
            </div>
            <div class="promo-card promo-card-green">
                <span class="promo-tag">🎁 Бесплатно</span>
                <h3>Консультация при записи на МРТ</h3>
                <p>Осмотр невролога в подарок</p>
                <div class="promo-date">до 31 марта 2025</div>
                <a href="promo.html" class="btn btn-light">Подробнее</a>
            </div>
        </div>
    </div>
</section>

<!-- ===== ВРАЧИ НА ГЛАВНОЙ (с фото из БД) ===== -->
<section class="doctors">
    <div class="container">
        <div class="section-header">
            <h2>Наши <span class="highlight">специалисты</span></h2>
            <p class="section-desc">Врачи высшей категории, кандидаты наук, с многолетним опытом</p>
        </div>
        <div class="doctors-grid">
            
            <?php
            $stmt = $pdo->query("SELECT * FROM doctors ORDER BY id LIMIT 3");
            $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($doctors as $doctor):
                // Путь к фото: если в БД есть photo, используем его, иначе иконка
                $photo = $doctor['photo'] ?? '';
            ?>
            <div class="doctor-card">
                <div class="doctor-photo">
                    <?php if (!empty($photo) && file_exists($photo)): ?>
                        <img src="<?php echo htmlspecialchars($photo); ?>" alt="<?php echo htmlspecialchars($doctor['name']); ?>">
                    <?php else: ?>
                        <div class="doctor-photo-fallback"><i class="fas fa-user-md"></i></div>
                    <?php endif; ?>
                </div>
                <h3><?php echo htmlspecialchars($doctor['name']); ?></h3>
                <p class="doctor-spec"><?php echo htmlspecialchars($doctor['specialization']); ?></p>
                <p class="doctor-exp">Стаж <?php echo $doctor['experience']; ?> лет</p>
                <div class="doctor-rating">
                    <?php
                    $fullStars = floor($doctor['rating']);
                    $halfStar = ($doctor['rating'] - $fullStars) >= 0.5;
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $fullStars) {
                            echo '<i class="fas fa-star"></i>';
                        } elseif ($halfStar && $i == $fullStars + 1) {
                            echo '<i class="fas fa-star-half-alt"></i>';
                            $halfStar = false;
                        } else {
                            echo '<i class="far fa-star"></i>';
                        }
                    }
                    ?>
                    <span><?php echo $doctor['rating']; ?></span>
                </div>
                <a href="doctors.php" class="btn-doctor">Записаться</a>
            </div>
            <?php endforeach; ?>
            
        </div>
    </div>
</section>

<!-- ===== ОТЗЫВЫ ===== -->
<section class="reviews">
    <div class="container">
        <div class="section-header">
            <h2>Отзывы <span class="highlight">пациентов</span></h2>
            <p class="section-desc">Более 5000 положительных отзывов</p>
        </div>
        <div class="reviews-grid">
            <div class="review-card">
                <div class="reviewer">
                    <div class="reviewer-avatar"><i class="fas fa-user-circle"></i></div>
                    <div class="reviewer-info"><h4>Анна Смирнова</h4><div class="review-rating">★★★★★</div></div>
                </div>
                <p class="review-text">Очень довольна приёмом у кардиолога. Врач внимательный, всё подробно объяснил. Спасибо!</p>
                <span class="review-date">12 февраля 2025</span>
            </div>
            <div class="review-card">
                <div class="reviewer">
                    <div class="reviewer-avatar"><i class="fas fa-user-circle"></i></div>
                    <div class="reviewer-info"><h4>Михаил Волков</h4><div class="review-rating">★★★★★</div></div>
                </div>
                <p class="review-text">Делал УЗИ брюшной полости. Быстро, качественно, современное оборудование. Рекомендую.</p>
                <span class="review-date">5 февраля 2025</span>
            </div>
            <div class="review-card">
                <div class="reviewer">
                    <div class="reviewer-avatar"><i class="fas fa-user-circle"></i></div>
                    <div class="reviewer-info"><h4>Елена Новикова</h4><div class="review-rating">★★★★★</div></div>
                </div>
                <p class="review-text">Водила ребёнка к педиатру. Очень чуткий врач, нашла подход к дочке. Теперь только сюда!</p>
                <span class="review-date">28 января 2025</span>
            </div>
        </div>
    </div>
</section>

<!-- ===== КОНТАКТЫ ===== -->
<section class="contacts">
    <div class="container">
        <div class="contacts-grid">
            <div class="contacts-info">
                <h2>Контакты</h2>
                <div class="contacts-list">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div><strong>Адрес:</strong><p>Москва, ул. Кожевническая, д. 10, стр. 1 (м. Павелецкая)</p></div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <div><strong>Режим работы:</strong><p>Пн-Пт: 9:00 – 20:00<br>Сб: 10:00 – 16:00<br>Вс: выходной</p></div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone-alt"></i>
                        <div><strong>Телефон:</strong><p><a href="tel:+78001234567">8 (800) 123-45-67</a></p></div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <div><strong>Email:</strong><p><a href="mailto:info@mc-zdorovie.ru">info@mc-zdorovie.ru</a></p></div>
                    </div>
                </div>
                <div class="contacts-social">
                    <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-telegram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-vk"></i></a>
                </div>
            </div>
            <div class="contacts-map">
                <div class="map-container">
                    <img src="karta.png" alt="Карта проезда">
                </div>
            </div>
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
                <ul><li><a href="about.html">О нас</a></li><li><a href="#">Лицензии</a></li><li><a href="#">Фотогалерея</a></li><li><a href="#">Вакансии</a></li></ul>
            </div>
            <div class="footer-col">
                <h4>Пациентам</h4>
                <ul><li><a href="services.php">Услуги и цены</a></li><li><a href="doctors.php">Врачи</a></li><li><a href="promo.html">Акции</a></li><li><a href="#">Отзывы</a></li></ul>
            </div>
            <div class="footer-col">
                <h4>Контакты</h4>
                <ul><li><a href="contacts.html">Адрес</a></li><li><a href="contacts.html">Обратная связь</a></li><li><a href="appointment.php">Запись онлайн</a></li><li><a href="#">Вопрос-ответ</a></li></ul>
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