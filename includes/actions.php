<?php
require_once 'db.php';
session_start();

// REGISTRATION
if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $company = trim($_POST['company']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, company_name, email, password, status, is_admin) VALUES (?, ?, ?, ?, 'pending', 0)");
    
    try {
        $stmt->execute([$name, $company, $email, $password]);
        header("Location: ../index.php?msg=awaiting_approval");
    } catch (Exception $e) {
        header("Location: ../index.php?msg=error");
    }
    exit;
}

// LOGIN
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['status'] == 'approved') {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['is_admin'] = $user['is_admin'];
            header("Location: ../index.php");
        } else {
            header("Location: ../index.php?msg=pending");
        }
    } else {
        header("Location: ../index.php?msg=invalid");
    }
    exit;
}

// ADMIN APPROVAL
if (isset($_POST['action']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
    $userId = $_POST['user_id'];
    if ($_POST['action'] == 'approve') {
        $stmt = $pdo->prepare("UPDATE users SET status = 'approved' WHERE id = ?");
        $stmt->execute([$userId]);
    }
    header("Location: ../index.php");
    exit;
}