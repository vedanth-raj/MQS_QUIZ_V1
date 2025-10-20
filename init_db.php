<?php
// Initialize SQLite database and create users table

// Check if running on Render (use SQLite) or local (use SQLite)
$isRender = getenv('RENDER') !== false;

if ($isRender) {
    // For Render, use a writable directory for SQLite
    $dbPath = __DIR__ . '/mqs_quiz.db';
} else {
    $dbPath = 'sqlite:mqs_quiz.db';
}

try {
    $db = new PDO($dbPath);
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
