<?php
require_once __DIR__ . '/../app/auth.php';
require_login();

// Si quieres mostrar el nombre del usuario:
require_once __DIR__ . '/../app/pdo.php';
$pdo = getPDO();
$stmt = $pdo->prepare("SELECT username FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
$username = $user['username'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal - Gestor de Incidencias</title>
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

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 900px;
            width: 100%;
            overflow: hidden;
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .user-info {
            background: rgba(255, 255, 255, 0.2);
            display: inline-block;
            padding: 10px 20px;
            border-radius: 50px;
            margin-top: 15px;
            backdrop-filter: blur(10px);
        }

        .user-info strong {
            font-weight: 600;
        }

        .content {
            padding: 40px;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
            border-color: #667eea;
        }

        .card-icon {
            font-size: 3em;
            margin-bottom: 15px;
            display: block;
        }

        .card-title {
            font-size: 1.3em;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .card-description {
            color: #666;
            font-size: 0.95em;
        }

        .card.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .card.primary .card-title,
        .card.primary .card-description {
            color: white;
        }

        .logout-section {
            text-align: center;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }

        .logout-btn {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(245, 87, 108, 0.3);
        }

        .logout-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(245, 87, 108, 0.4);
        }

        .logout-btn:active {
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2em;
            }
            
            .cards-grid {
                grid-template-columns: 1fr;
            }
            
            .content {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎯 Gestor de Incidencias</h1>
            <div class="user-info">
                👤 Bienvenido, <strong><?= htmlspecialchars($username) ?></strong>
            </div>
        </div>

        <div class="content">
            <div class="cards-grid">
                <a href="lista_tickets.php" class="card primary">
                    <span class="card-icon">📋</span>
                    <div class="card-title">Listado de Incidencias</div>
                    <div class="card-description">Ver y gestionar todas las incidencias</div>
                </a>

                <a href="editar_ticket.php" class="card">
                    <span class="card-icon">➕</span>
                    <div class="card-title">Crear Incidencia</div>
                    <div class="card-description">Registrar una nueva incidencia</div>
                </a>

                <a href="preferencias.php" class="card">
                    <span class="card-icon">⚙️</span>
                    <div class="card-title">Preferencias</div>
                    <div class="card-description">Configurar tema y opciones</div>
                </a>
            </div>

            <div class="logout-section">
                <form action="logout.php" method="post">
                    <button type="submit" class="logout-btn">🚪 Cerrar Sesión</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>