<?php
session_start();

header('Content-Type: application/json');

try {
    $db = new PDO('sqlite:mqs_quiz.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['logged_in' => false, 'taken' => 0, 'name' => '', 'email' => '']);
    exit;
}

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $stmt = $db->prepare("SELECT name, taken FROM users WHERE email = :email");
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $logged_in = true;
        $taken = $user['taken'];
        $name = $user['name'];
    } else {
        $logged_in = false;
        $taken = 0;
        $name = '';
        $email = '';
    }
} else {
    $logged_in = false;
    $taken = 0;
    $name = '';
    $email = '';
}

echo json_encode(['logged_in' => $logged_in, 'taken' => $taken, 'name' => $name, 'email' => $email]);
?>
