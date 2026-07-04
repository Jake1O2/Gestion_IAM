<?php if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user'])) { header('Location: login.php'); exit; }
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title><?php echo $pageTitle ?? 'IAM Gestion'; ?></title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="public/templates/templateAdmin/assets/css/default/app.min.css" rel="stylesheet" />
</head>
<body class="pace-top">
<div id="page-container" class="fade page-sidebar-fixed page-header-fixed show">

    <!-- begin #header -->
    <div id="header" class="header navbar-default">
        <div class="navbar-header">
            <a href="index.php" class="navbar-brand">
                <img src="public/templates/templateVitrine/assets/img/logo/logo-iam.jpg" alt="Logo IAM" style="height:34px; width:auto; margin-right:8px; vertical-align:middle;" />
                <b>IAM</b> Gestion
            </a>
            <button type="button" class="navbar-toggle" data-click="sidebar-toggled">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <ul class="navbar-nav navbar-right">
            <li class="dropdown navbar-user">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:50%;background:#1a6b3c;color:#fff;font-weight:700;font-size:0.8rem;">
                        <?php echo strtoupper(substr($user['name'] ?? 'U', 0, 1)); ?>
                    </span>
                    <span class="d-none d-md-inline"><?php echo htmlspecialchars($user['name'] ?? 'Utilisateur'); ?></span>
                    <b class="caret"></b>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="logout.php" class="dropdown-item">Déconnexion</a>
                </div>
            </li>
        </ul>
    </div>
    <!-- end #header -->

    <!-- begin #sidebar -->
    <div id="sidebar" class="sidebar">
        <div data-scrollbar="true" data-height="100%">
            <ul class="nav">
                <li class="nav-profile">
                    <a href="javascript:;" data-toggle="nav-profile">
                        <div class="cover with-shadow"></div>
                        <div class="image">
                            <span style="display:inline-flex;align-items:center;justify-content:center;width:60px;height:60px;border-radius:50%;background:#fff;color:#1a6b3c;font-weight:700;font-size:1.5rem;">
                                <?php echo strtoupper(substr($user['name'] ?? 'U', 0, 1)); ?>
                            </span>
                        </div>
                        <div class="info">
                            <b class="caret pull-right"></b>
                            <?php echo htmlspecialchars($user['name'] ?? 'Utilisateur'); ?>
                            <small><?php echo htmlspecialchars($user['role'] ?? ''); ?></small>
                        </div>
                    </a>
                </li>
            </ul>
            <ul class="nav">
                <li class="nav-header">Navigation</li>
                <li><a href="index.php"><i class="fa fa-globe"></i> <span>Retour au site</span></a></li>
                <li <?php echo ($activePage ?? '') === 'dashboard' ? 'class="active"' : ''; ?>>
                    <a href="espace_administration.php"><i class="fa fa-th-large"></i> <span>Dashboard</span></a>
                </li>
                <?php $role = strtolower($user['role'] ?? ''); ?>
                <?php if ($role === 'student' || $role === 'etudiant'): ?>
                <li <?php echo ($activePage ?? '') === 'espace' ? 'class="active"' : ''; ?>>
                    <a href="espace_etudiant.php"><i class="fa fa-graduation-cap"></i> <span>Mon Espace</span></a>
                </li>
                <li <?php echo ($activePage ?? '') === 'notes' ? 'class="active"' : ''; ?>>
                    <a href="consulter_notes.php"><i class="fa fa-star-half-alt"></i> <span>Mes Notes</span></a>
                </li>
                <li <?php echo ($activePage ?? '') === 'emploi' ? 'class="active"' : ''; ?>>
                    <a href="gerer_emploi_du_temps.php"><i class="fa fa-calendar-alt"></i> <span>Emploi du Temps</span></a>
                </li>
                <?php elseif ($role === 'teacher' || $role === 'enseignant' || $role === 'professeur'): ?>
                <li <?php echo ($activePage ?? '') === 'espace' ? 'class="active"' : ''; ?>>
                    <a href="espace_enseignant.php"><i class="fa fa-chalkboard-teacher"></i> <span>Mon Espace</span></a>
                </li>
                <li <?php echo ($activePage ?? '') === 'notes' ? 'class="active"' : ''; ?>>
                    <a href="gerer_notes.php"><i class="fa fa-star-half-alt"></i> <span>Gérer les Notes</span></a>
                </li>
                <li <?php echo ($activePage ?? '') === 'emploi' ? 'class="active"' : ''; ?>>
                    <a href="gerer_emploi_du_temps.php"><i class="fa fa-calendar-alt"></i> <span>Emploi du Temps</span></a>
                </li>
                <?php elseif ($role === 'parent'): ?>
                <li <?php echo ($activePage ?? '') === 'espace' ? 'class="active"' : ''; ?>>
                    <a href="espace_parent.php"><i class="fa fa-users"></i> <span>Mon Espace</span></a>
                </li>
                <li <?php echo ($activePage ?? '') === 'enfants' ? 'class="active"' : ''; ?>>
                    <a href="consulter_enfants.php"><i class="fa fa-child"></i> <span>Mes Enfants</span></a>
                </li>
                <?php elseif ($role === 'admin' || $role === 'administrateur'): ?>
                <li <?php echo ($activePage ?? '') === 'espace' ? 'class="active"' : ''; ?>>
                    <a href="espace_administration.php"><i class="fa fa-cogs"></i> <span>Administration</span></a>
                </li>
                <li><a href="gestion_iam.php"><i class="fa fa-user-shield"></i> <span>Gestion IAM</span></a></li>
                <li class="nav-header">Gestion Scolaire</li>
                <li <?php echo ($activePage ?? '') === 'classes' ? 'class="active"' : ''; ?>>
                    <a href="classes.php"><i class="fa fa-school"></i> <span>Classes</span></a>
                </li>
                <li <?php echo ($activePage ?? '') === 'etudiants' ? 'class="active"' : ''; ?>>
                    <a href="etudiants.php"><i class="fa fa-user-graduate"></i> <span>Étudiants</span></a>
                </li>
                <li <?php echo ($activePage ?? '') === 'cours' ? 'class="active"' : ''; ?>>
                    <a href="cours.php"><i class="fa fa-book"></i> <span>Cours</span></a>
                </li>
                <?php elseif ($role === 'comptable'): ?>
                <li <?php echo ($activePage ?? '') === 'espace' ? 'class="active"' : ''; ?>>
                    <a href="espace_administration.php"><i class="fa fa-file-invoice-dollar"></i> <span>Mon Espace</span></a>
                </li>
                <?php endif; ?>
                <li><a href="logout.php"><i class="fa fa-sign-out-alt"></i> <span>Déconnexion</span></a></li>
                <li><a href="javascript:;" class="sidebar-minify-btn" data-click="sidebar-minify"><i class="fa fa-angle-double-left"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="sidebar-bg"></div>
    <!-- end #sidebar -->

    <!-- begin #content -->
    <div id="content" class="content">
        <ol class="breadcrumb float-xl-right">
            <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
            <li class="breadcrumb-item active"><?php echo $pageTitle ?? ''; ?></li>
        </ol>
        <h1 class="page-header"><?php echo $pageTitle ?? ''; ?> <small><?php echo $pageSubtitle ?? 'Institut Africain de Management'; ?></small></h1>
