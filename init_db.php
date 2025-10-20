<?php
// Initialize SQLite database and create users table

try {
    $db = new PDO('sqlite:mqs_quiz.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        taken INTEGER NOT NULL DEFAULT 0
    )";

    $db->exec($query);

    echo "Database and users table initialized successfully.";
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
