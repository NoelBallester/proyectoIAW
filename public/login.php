<?php
// Rutas corregidas hacia la carpeta app
session_start();
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/auth.php';

$error = null;

// Si ya está logueado, redirigir al panel
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Asegurar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$username = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Comprobación básica CSRF
    $postedToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $postedToken)) {
        http_response_code(400);
        $error = 'Solicitud inválida.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = 'Usuario y contraseña son requeridos.';
        } else {
            $pdo = getPDO();
            $stmt = $pdo->prepare('SELECT id, password FROM usuarios WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Evitar fijación de sesión
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];

                // Rehashear si el algoritmo cambió
                if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $upd = $pdo->prepare('UPDATE usuarios SET password = ? WHERE id = ?');
                    $upd->execute([$newHash, $user['id']]);
                }

                header('Location: index.php');
                exit;
            } else {
                $error = 'Usuario o contraseña incorrectos.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Gestor de Incidencias</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 450px;
            width: 100%;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .login-header h1 {
            font-size: 2em;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .login-header p {
            opacity: 0.9;
            font-size: 1em;
        }

        .login-icon {
            font-size: 3em;
            margin-bottom: 15px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .login-body {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 0.95em;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group input::placeholder {
            color: #aaa;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.5);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .error-message {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .input-icon {
            position: relative;
        }

        .input-icon::before {
            content: attr(data-icon);
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2em;
            color: #667eea;
        }

        .input-icon input {
            padding-left: 45px;
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 10px;
            }
            
            .login-header {
                padding: 30px 20px;
            }
            
            .login-body {
                padding: 30px 20px;
            }
            
            .login-header h1 {
                font-size: 1.8em;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="login-icon">🔐</div>
            <h1>Gestor de Incidencias</h1>
            <p>Inicia sesión para continuar</p>
        </div>

        <div class="login-body">
            <?php if ($error): ?>
                <div class="error-message">
                    ⚠️ <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                
                <div class="form-group">
                    <label for="username">👤 Usuario</label>
                    <div class="input-icon" data-icon="👤">
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            placeholder="Ingresa tu usuario" 
                            required 
                            autofocus
                            value="<?= htmlspecialchars($username) ?>"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">🔒 Contraseña</label>
                    <div class="input-icon" data-icon="🔒">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Ingresa tu contraseña" 
                            required
                        >
                    </div>
                </div>

                <button type="submit" class="login-btn">
                    🚀 Iniciar Sesión
                </button>
            </form>
        </div>
    </div>
</body>
</html>