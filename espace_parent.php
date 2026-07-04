<?php
$allowedRoles = ['parent'];
require_once __DIR__ . '/CONTROLLER/auth_guard.php';
$pageTitle    = 'Espace Parent';
$pageSubtitle = 'Suivi de vos enfants';
$activePage   = 'espace';
require_once 'VIEW/layouts/admin_header.php';
?>
<div class="row">
    <div class="col-xl-4 col-md-6">
        <div class="widget widget-stats bg-orange">
            <div class="stats-icon"><i class="fa fa-child"></i></div>
            <div class="stats-info"><h4>MES ENFANTS</h4><p>Suivi scolaire</p></div>
            <div class="stats-link"><a href="consulter_enfants.php">Voir mes enfants <i class="fa fa-arrow-alt-circle-right"></i></a></div>
        </div>
    </div>
</div>
<div class="panel panel-inverse">
    <div class="panel-heading"><h4 class="panel-title">Bienvenue, <?= htmlspecialchars($user['name'] ?? 'Parent') ?></h4></div>
    <div class="panel-body"><p>Vous êtes connecté en tant que <strong>Parent</strong>. Suivez les notes et les absences de vos enfants depuis le menu de gauche.</p></div>
</div>
<?php require_once 'VIEW/layouts/admin_footer.php'; ?>
