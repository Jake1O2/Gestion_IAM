<?php
/**
 * auth_guard.php
 * Inclure en haut de chaque page protégée.
 * Usage : require_once __DIR__ . '/../CONTROLLER/auth_guard.php';
 * Définir $allowedRoles avant l'inclusion. Ex: $allowedRoles = ['admin','administrateur'];
 */

if (session_status() === PHP_SESSION_NONE) session_start();

// Non connecté → login
if (empty($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$currentUser = $_SESSION['user'];
$userRole    = strtolower($currentUser['role'] ?? '');

// Super admins ont accès à tout
$superAdmins = ['admin', 'administrateur'];
if (in_array($userRole, $superAdmins)) {
    return; // accès autorisé
}

// Vérification du rôle si $allowedRoles est défini
if (!empty($allowedRoles)) {
    $allowed = array_map('strtolower', $allowedRoles);
    if (!in_array($userRole, $allowed)) {
        // Rediriger vers l'espace correspondant au rôle
        $redirectMap = [
            'student'   => 'espace_etudiant.php',
            'teacher'   => 'espace_enseignant.php',
            'parent'    => 'espace_parent.php',
            'comptable' => 'espace_administration.php',
        ];
        $redirect = $redirectMap[$userRole] ?? 'index.php';
        header("Location: $redirect");
        exit;
    }
}
