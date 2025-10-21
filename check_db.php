<?php
try {
    $db = new PDO('sqlite:mqs_quiz.db');
    $stmt = $db->query('SELECT name FROM sqlite_master WHERE type="table"');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo 'Tables: ' . implode(', ', $tables) . PHP_EOL;
    $stmt = $db->query('SELECT COUNT(*) FROM users');
    $count = $stmt->fetchColumn();
    echo 'Users count: ' . $count . PHP_EOL;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>
