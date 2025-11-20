<?php
// Es necesario para que el enlace "Ver" funcione
$stmt = $pdo->prepare('SELECT * FROM tickets WHERE id = ?');