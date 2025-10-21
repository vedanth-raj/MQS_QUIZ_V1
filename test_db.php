<?php
try {
    $db = new PDO('sqlite:mqs_quiz.db');
    $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo 'Tables: ' . implode(', ', $tables);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
