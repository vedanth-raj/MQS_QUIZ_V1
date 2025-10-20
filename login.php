<?php
session_start();

$isRender = getenv('RENDER') !== false;
$dbPath = $isRender ? __DIR__ . '/mqs_quiz.db' : 'sqlite:mqs_quiz.db';

try {
    $db = new PDO($dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if (!empty($name) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Check if user exists
        $stmt = $db->prepare("SELECT taken FROM users WHERE email = :email");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($user['taken'] == 1) {
                // User already took the quiz
                header('Location: already_taken.html');
                exit;
            } else {
                // User exists but not taken quiz
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                $_SESSION['taken'] = 0;
                header('Location: quiz.html');
                exit;
            }
        } else {
            // New user, insert into database
            $insert = $db->prepare("INSERT INTO users (name, email, taken) VALUES (:name, :email, 0)");
            $insert->bindValue(':name', $name, PDO::PARAM_STR);
            $insert->bindValue(':email', $email, PDO::PARAM_STR);
            $insert->execute();

            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['taken'] = 0;
            header('Location: quiz.html');
            exit;
        }
    } else {
        echo 'Invalid input. Please provide a valid name and email.';
    }
} else {
    header('Location: login.html');
    exit;
}
?>
