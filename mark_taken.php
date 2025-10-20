<?php
session_start();

try {
    $db = new PDO('sqlite:mqs_quiz.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    try {
        $stmt = $db->prepare("UPDATE users SET taken = 1 WHERE email = :email");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $_SESSION['taken'] = 1;
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Failed to update taken status']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
}
?>
