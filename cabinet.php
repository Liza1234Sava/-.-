<?php
session_start();
require_once 'config.php';

// Функция для очистки телефона
function cleanPhone($phone) {
    return preg_replace('/[^0-9]/', '', $phone);
}

$error = '';
$success = '';

// Загрузка аватарки
if (isset($_POST['upload_avatar']) && isset($_FILES['avatar'])) {
    $user_id = $_SESSION['user_id'] ?? 0;
    if ($user_id) {
        $upload_dir = 'uploads/avatars/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file = $_FILES['avatar'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($ext, $allowed) && $file['size'] < 2 * 1024 * 1024) {
            $new_name = 'user_' . $user_id . '_' . time() . '.' . $ext;
            $path = $upload_dir . $new_name;
            
            if (move_uploaded_file($file['tmp_name'], $path)) {
                $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                $stmt->execute([$path, $user_id]);
                $_SESSION['user_avatar'] = $path;
                $success = 'Аватарка успешно обновлена!';
            } else {
                $error = 'Ошибка при загрузке файла';
            }
        } else {
            $error = 'Разрешены только JPG, PNG, GIF, WEBP до 2МБ';
        }
    }
}

// Редактирование профиля
if (isset($_POST['update_profile'])) {
    $user_id = $_SESSION['user_id'] ?? 0;
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    if ($user_id && !empty($name)) {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $email, $user_id]);
        $_SESSION['user_name'] = $name;
        $success = 'Профиль успешно обновлён!';
    } else {
        $error = 'Имя не может быть пустым';
    }
}

// Смена пароля
if (isset($_POST['change_password'])) {
    $user_id = $_SESSION['user_id'] ?? 0;
    $old_pass = $_POST['old_password'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';
    
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($old_pass, $user['password'])) {
        if (strlen($new_pass) < 4) {
            $error = 'Пароль должен содержать минимум 4 символа';
        } elseif ($new_pass !== $confirm_pass) {
            $error = 'Новый пароль и подтверждение не совпадают';
        } else {
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $user_id]);
            $success = 'Пароль успешно изменён!';
        }
    } else {
        $error = 'Неверный текущий пароль';
    }
}

// Отмена записи
if (isset($_GET['cancel_appointment']) && isset($_GET['id'])) {
    $app_id = intval($_GET['id']);
    $user_phone = $_SESSION['user_phone'] ?? '';
    
    $stmt = $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ? AND patient_phone = ?");
    $stmt->execute([$app_id, $user_phone]);
    $success = 'Запись отменена';
    header('Location: cabinet.php');
    exit;
}

// Регистрация
if (isset($_POST['register'])) {
    $name = trim($_POST['reg_name'] ?? '');
    $phone_raw = trim($_POST['reg_phone'] ?? '');
    $phone = cleanPhone($phone_raw);
    $email = trim($_POST['reg_email'] ?? '');
    $password = $_POST['reg_password'] ?? '';
    $confirm = $_POST['reg_confirm'] ?? '';
    
    if (empty($name) || empty($phone) || empty($password)) {
        $error = 'Заполните все обязательные поля';
    } elseif (strlen($phone) < 10) {
        $error = 'Введите корректный номер телефона';
    } elseif ($password !== $confirm) {
        $error = 'Пароли не совпадают';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        if ($stmt->fetch()) {
            $error = 'Пользователь с таким телефоном уже зарегистрирован';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, phone, email, password) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $phone, $email, $hashed])) {
                $success = 'Регистрация успешна! Теперь вы можете войти.';
            } else {
                $error = 'Ошибка при регистрации';
            }
        }
    }
}

// Вход
if (isset($_POST['login'])) {
    $phone_raw = trim($_POST['login_phone'] ?? '');
    $phone = cleanPhone($phone_raw);
    $password = $_POST['login_password'] ?? '';
    
    if (empty($phone) || empty($password)) {
        $error = 'Заполните все поля';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_phone'] = $user['phone'];
            $_SESSION['user_avatar'] = $user['avatar'];
            $success = 'Добро пожаловать, ' . htmlspecialchars($user['name']) . '!';
        } else {
            $error = 'Неверный телефон или пароль';
        }
    }
}

// Выход
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: cabinet.php');
    exit;
}

// Получаем данные пользователя
$user_data = [];
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Получаем записи
$appointments = [];
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("
        SELECT a.*, s.name as service_name, d.name as doctor_name 
        FROM appointments a
        LEFT JOIN services s ON a.service_id = s.id
        LEFT JOIN doctors d ON a.doctor_id = d.id
        WHERE a.patient_phone = ?
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ");
    $stmt->execute([$_SESSION['user_phone']]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет — МЦ «Здоровье+»</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .cabinet-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .profile-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 40px;
        }
        .profile-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 40px;
            text-align: center;
            color: white;
        }
        .avatar-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
        }
        .avatar-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .avatar-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: white;
            border: 4px solid white;
        }
        .avatar-upload-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: white;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--primary);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .profile-name {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }
        .profile-phone {
            opacity: 0.9;
            font-size: 1rem;
        }
        .profile-body {
            padding: 30px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        .info-card {
            background: var(--bg-light);
            border-radius: 16px;
            padding: 20px;
        }
        .info-card h3 {
            margin-bottom: 20px;
            color: var(--dark);
            border-left: 4px solid var(--primary);
            padding-left: 15px;
        }
        .info-row {
            display: flex;
            margin-bottom: 12px;
            padding: 8px 0;
            border-bottom: 1px solid var(--light-gray);
        }
        .info-label {
            width: 120px;
            font-weight: 600;
            color: var(--dark);
        }
        .info-value {
            flex: 1;
            color: var(--gray);
        }
        
        /* ===== КРАСИВЫЕ КАРТОЧКИ ДЕЙСТВИЙ ===== */
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 10px;
        }
        .action-card {
            background: white;
            border-radius: 16px;
            padding: 25px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid var(--light-gray);
            text-decoration: none;
            display: block;
        }
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(15, 110, 110, 0.15);
            border-color: var(--primary);
        }
        .action-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }
        .action-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--dark);
            margin-bottom: 8px;
        }
        .action-desc {
            font-size: 0.8rem;
            color: var(--gray);
        }
        .logout-card {
            background: #fff5f5;
            border-color: #ffe0e0;
        }
        .logout-card .action-icon {
            color: #e74c3c;
        }
        .logout-card:hover {
            border-color: #e74c3c;
            background: #fff0f0;
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .btn-cabinet {
            padding: 10px 25px;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            background: white;
            border: 2px solid var(--primary);
            color: var(--primary);
            text-decoration: none;
            display: inline-block;
        }
        .btn-cabinet:hover {
            background: var(--primary);
            color: white;
        }
        .btn-primary-cabinet {
            background: var(--primary);
            color: white;
            border: none;
        }
        .btn-primary-cabinet:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        .appointments-table {
            overflow-x: auto;
            margin-top: 20px;
        }
        .appointments-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .appointments-table th {
            background: var(--primary-light);
            padding: 12px;
            text-align: left;
        }
        .appointments-table td {
            padding: 12px;
            border-bottom: 1px solid var(--light-gray);
        }
        .status-new { color: #e67e22; font-weight: 600; }
        .status-confirmed { color: #27ae60; font-weight: 600; }
        .status-completed { color: #3498db; font-weight: 600; }
        .status-cancelled { color: #e74c3c; font-weight: 600; }
        .cancel-link {
            color: #e74c3c;
            text-decoration: none;
            font-size: 0.85rem;
        }
        .cancel-link:hover {
            text-decoration: underline;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--light-gray);
            border-radius: 8px;
        }
        .alert {
            max-width: 500px;
            margin: 0 auto 20px;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        .cabinet-tabs {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 40px;
        }
        .tab-btn {
            padding: 12px 30px;
            background: white;
            border: 2px solid var(--primary);
            border-radius: 40px;
            font-size: 1rem;
            font-weight: 600;
            color: var(--primary);
            cursor: pointer;
        }
        .tab-btn.active {
            background: var(--primary);
            color: white;
        }
        .login-form {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: var(--shadow);
        }
        .form-submit {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

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
                <li><a href="cabinet.php" class="active">Личный кабинет</a></li>
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

<section class="page-header">
    <div class="container">
        <h1>Личный кабинет</h1>
        <div class="breadcrumbs">
            <a href="index.php">Главная</a> / <span>Личный кабинет</span>
        </div>
    </div>
</section>

<div class="cabinet-container">
    
    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- АВТОРИЗОВАН -->
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="profile-card">
            <div class="profile-header">
                <div class="avatar-wrapper">
                    <?php if (!empty($user_data['avatar']) && file_exists($user_data['avatar'])): ?>
                        <img src="<?php echo $user_data['avatar']; ?>" class="avatar-img" id="avatar-img">
                    <?php else: ?>
                        <div class="avatar-placeholder" id="avatar-placeholder">
                            <i class="fas fa-user-circle"></i>
                        </div>
                    <?php endif; ?>
                    <form method="POST" enctype="multipart/form-data" id="avatar-form" style="display: none;">
                        <input type="file" name="avatar" id="avatar-file" accept="image/*">
                        <button type="submit" name="upload_avatar" id="avatar-submit"></button>
                    </form>
                    <div class="avatar-upload-btn" onclick="document.getElementById('avatar-file').click();">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
                <div class="profile-name"><?php echo htmlspecialchars($user_data['name']); ?></div>
                <div class="profile-phone"><?php echo htmlspecialchars($user_data['phone']); ?></div>
            </div>
            
            <div class="profile-body">
                <div class="info-grid">
                    <div class="info-card">
                        <h3><i class="fas fa-user"></i> Личная информация</h3>
                        <div class="info-row">
                            <span class="info-label">Имя:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user_data['name']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Телефон:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user_data['phone']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user_data['email'] ?: '—'); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">На сайте с:</span>
                            <span class="info-value"><?php echo date('d.m.Y', strtotime($user_data['created_at'])); ?></span>
                        </div>
                    </div>
                    
                    <!-- БЛОК ДЕЙСТВИЙ С КРАСИВЫМИ КАРТОЧКАМИ -->
                    <div class="info-card">
                        <h3><i class="fas fa-cog"></i> Действия</h3>
                        <div class="actions-grid">
                            <button class="action-card" onclick="openModal('edit-modal')">
                                <div class="action-icon"><i class="fas fa-user-edit"></i></div>
                                <div class="action-title">Редактировать</div>
                                <div class="action-desc">Изменить имя и email</div>
                            </button>
                            <button class="action-card" onclick="openModal('password-modal')">
                                <div class="action-icon"><i class="fas fa-key"></i></div>
                                <div class="action-title">Сменить пароль</div>
                                <div class="action-desc">Обновить пароль доступа</div>
                            </button>
                            <a href="?logout=1" class="action-card logout-card">
                                <div class="action-icon"><i class="fas fa-sign-out-alt"></i></div>
                                <div class="action-title">Выйти</div>
                                <div class="action-desc">Завершить сеанс</div>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3><i class="fas fa-calendar-alt"></i> Мои записи</h3>
                    <?php if (empty($appointments)): ?>
                        <p style="text-align: center;">У вас пока нет записей. <a href="appointment.php">Записаться на приём</a></p>
                    <?php else: ?>
                        <div class="appointments-table">
                            <table>
                                <thead>
                                    <tr><th>Дата</th><th>Время</th><th>Услуга</th><th>Врач</th><th>Статус</th><th></th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($appointments as $app): ?>
                                        <tr>
                                            <td><?php echo date('d.m.Y', strtotime($app['appointment_date'])); ?></td>
                                            <td><?php echo $app['appointment_time'] ?: '—'; ?></td>
                                            <td><?php echo htmlspecialchars($app['service_name'] ?? '—'); ?></td>
                                            <td><?php echo htmlspecialchars($app['doctor_name'] ?? 'Любой врач'); ?></td>
                                            <td class="status-<?php echo $app['status']; ?>">
                                                <?php
                                                $statuses = [
                                                    'new' => '🟡 Новая',
                                                    'confirmed' => '🟢 Подтверждена',
                                                    'completed' => '✅ Завершена',
                                                    'cancelled' => '🔴 Отменена'
                                                ];
                                                echo $statuses[$app['status']] ?? '🟡 Новая';
                                                ?>
                                            </td>
                                            <td>
                                                <?php if ($app['status'] == 'new' || $app['status'] == 'confirmed'): ?>
                                                    <a href="?cancel_appointment=1&id=<?php echo $app['id']; ?>" class="cancel-link" onclick="return confirm('Отменить запись?')">Отменить</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Модальное окно редактирования -->
        <div id="edit-modal" class="modal">
            <div class="modal-content">
                <h3>Редактирование профиля</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Имя</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>">
                    </div>
                    <div class="btn-group">
                        <button type="submit" name="update_profile" class="btn-cabinet btn-primary-cabinet">Сохранить</button>
                        <button type="button" class="btn-cabinet" onclick="closeModal('edit-modal')">Отмена</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Модальное окно смены пароля -->
        <div id="password-modal" class="modal">
            <div class="modal-content">
                <h3>Смена пароля</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Текущий пароль</label>
                        <input type="password" name="old_password" required>
                    </div>
                    <div class="form-group">
                        <label>Новый пароль</label>
                        <input type="password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label>Подтвердите пароль</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <div class="btn-group">
                        <button type="submit" name="change_password" class="btn-cabinet btn-primary-cabinet">Сменить</button>
                        <button type="button" class="btn-cabinet" onclick="closeModal('password-modal')">Отмена</button>
                    </div>
                </form>
            </div>
        </div>
        
    <?php else: ?>
        <!-- НЕ АВТОРИЗОВАН -->
        <div class="cabinet-tabs">
            <button class="tab-btn active" data-tab="login">Вход</button>
            <button class="tab-btn" data-tab="register">Регистрация</button>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <!-- Форма входа -->
        <div class="login-form" id="login-form">
            <h2 style="text-align: center;">Вход в личный кабинет</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Телефон</label>
                    <input type="tel" name="login_phone" placeholder="+7 (999) 123-45-67" required>
                </div>
                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" name="login_password" placeholder="••••••••" required>
                </div>
                <div class="form-submit">
                    <button type="submit" name="login" class="btn btn-primary">Войти</button>
                </div>
            </form>
        </div>
        
        <!-- Форма регистрации -->
        <div class="login-form" id="register-form" style="display: none;">
            <h2 style="text-align: center;">Регистрация</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Имя</label>
                    <input type="text" name="reg_name" placeholder="Иванов Иван" required>
                </div>
                <div class="form-group">
                    <label>Телефон</label>
                    <input type="tel" name="reg_phone" placeholder="+7 (999) 123-45-67" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="reg_email" placeholder="ivanov@mail.ru">
                </div>
                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" name="reg_password" placeholder="••••••••" required>
                </div>
                <div class="form-group">
                    <label>Подтвердите пароль</label>
                    <input type="password" name="reg_confirm" placeholder="••••••••" required>
                </div>
                <div class="form-submit">
                    <button type="submit" name="register" class="btn btn-primary">Зарегистрироваться</button>
                </div>
            </form>
        </div>
    <?php endif; ?>
    
</div>

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
            <div class="footer-col"><h4>О клинике</h4><ul><li><a href="about.html">О нас</a></li></ul></div>
            <div class="footer-col"><h4>Пациентам</h4><ul><li><a href="services.php">Услуги</a></li><li><a href="doctors.php">Врачи</a></li></ul></div>
            <div class="footer-col"><h4>Контакты</h4><ul><li><a href="contacts.html">Адрес</a></li><li><a href="appointment.php">Запись</a></li></ul></div>
        </div>
        <div class="footer-bottom"><p>© 2025 Медицинский центр «Здоровье+».</p></div>
    </div>
</footer>

<script>
    function openModal(id) {
        document.getElementById(id).style.display = 'flex';
    }
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
    
    // Загрузка аватарки
    var avatarFile = document.getElementById('avatar-file');
    if (avatarFile) {
        avatarFile.addEventListener('change', function() {
            document.getElementById('avatar-submit').click();
        });
    }
    
    // Переключение вкладок
    const tabBtns = document.querySelectorAll('.tab-btn');
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    
    if (tabBtns.length) {
        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const tabId = btn.dataset.tab;
                tabBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                if (tabId === 'login') {
                    loginForm.style.display = 'block';
                    registerForm.style.display = 'none';
                } else {
                    loginForm.style.display = 'none';
                    registerForm.style.display = 'block';
                }
            });
        });
    }
    
    // Закрытие модалки по клику вне
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
</script>
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