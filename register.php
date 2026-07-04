<?php
require_once __DIR__ . '/CONTROLLER/usercontroller.php';

if (!empty($_SESSION['user'])) {
    $ctrl = new UserController();
    header('Location: ' . $ctrl->getRedirectByRole($_SESSION['user']['role']));
    exit;
}

$ctrl  = new UserController();
$error = '';

$rolesDisponibles = [
    'student'   => 'Étudiant',
    'parent'    => 'Parent',
    'teacher'   => 'Professeur',
    'comptable' => 'Comptable',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password  = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($password !== $password2) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        $res = $ctrl->register(
            $_POST['name']     ?? '',
            $_POST['email']    ?? '',
            $password,
            $_POST['role']     ?? ''
        );
        if ($res['success']) {
            header('Location: ' . $res['redirect']);
            exit;
        }
        $error = $res['msg'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>Inscription - IAM Gestion</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="public/templates/templateAdmin/assets/css/default/app.min.css" rel="stylesheet" />
</head>
<body class="pace-top">
<div id="page-container" class="fade">
    <div class="login login-v1">
        <div class="login-container">
            <div class="login-header">
                <div class="brand">
                    <img src="public/templates/templateVitrine/assets/img/logo/logo-iam.jpg" alt="Logo IAM"
                        style="height:36px; width:auto; margin-right:8px; vertical-align:middle;" />
                    <b>IAM</b> Gestion
                    <small>Créer un compte</small>
                </div>
                <div class="icon"><i class="fa fa-user-plus"></i></div>
            </div>
            <div class="login-body">
                <div class="login-content">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <form method="post" action="" class="margin-bottom-0">
                        <div class="form-group m-b-15">
                            <input name="name" type="text" class="form-control form-control-lg inverse-mode"
                                placeholder="Nom complet" required
                                value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" />
                        </div>
                        <div class="form-group m-b-15">
                            <input name="email" type="email" class="form-control form-control-lg inverse-mode"
                                placeholder="Adresse email" required
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
                        </div>
                        <div class="form-group m-b-15">
                            <select name="role" class="form-control form-control-lg inverse-mode" required>
                                <option value="">-- Choisir votre rôle --</option>
                                <?php foreach ($rolesDisponibles as $val => $label): ?>
                                    <option value="<?= $val ?>" <?= (($_POST['role'] ?? '') === $val) ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group m-b-15">
                            <input name="password" type="password" class="form-control form-control-lg inverse-mode"
                                placeholder="Mot de passe (lettres + chiffres, 6 min.)" required />
                        </div>
                        <div class="form-group m-b-15">
                            <input name="password2" type="password" class="form-control form-control-lg inverse-mode"
                                placeholder="Confirmer le mot de passe" required />
                        </div>
                        <div class="login-buttons">
                            <button class="btn btn-success btn-block btn-lg" type="submit">S'inscrire</button>
                        </div>
                        <div class="m-t-20 text-center">
                            <a href="login.php">Déjà inscrit ? Se connecter</a>
                        </div>
                        <div class="m-t-10 text-center">
                            <a href="index.php">← Retour au site</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="public/templates/templateAdmin/assets/js/app.min.js"></script>
<script src="public/templates/templateAdmin/assets/js/theme/default.min.js"></script>
</body>
</html>
