<?php
$allowedRoles = ['admin', 'administrateur', 'comptable'];
require_once __DIR__ . '/CONTROLLER/auth_guard.php';
$pageTitle    = 'Administration';
$pageSubtitle = 'Gestion de l\'établissement';
$activePage   = 'espace';
require_once 'VIEW/layouts/admin_header.php';
?>
<div class="row">
    <?php if (in_array(strtolower($user['role'] ?? ''), ['admin','administrateur'])): ?>
    <div class="col-xl-4 col-md-6">
        <div class="widget widget-stats bg-green">
            <div class="stats-icon"><i class="fa fa-user-shield"></i></div>
            <div class="stats-info"><h4>GESTION IAM</h4><p>Utilisateurs & Accès</p></div>
            <div class="stats-link"><a href="gestion_iam.php">Gérer les accès <i class="fa fa-arrow-alt-circle-right"></i></a></div>
        </div>
    </div>
    <?php endif; ?>
    <div class="col-xl-4 col-md-6">
        <div class="widget widget-stats bg-blue">
            <div class="stats-icon"><i class="fa fa-file-invoice-dollar"></i></div>
            <div class="stats-info"><h4>PAIEMENTS</h4><p>Suivi financier</p></div>
            <div class="stats-link"><a href="javascript:;">Gérer les paiements <i class="fa fa-arrow-alt-circle-right"></i></a></div>
        </div>
    </div>
</div>
<div class="panel panel-inverse">
    <div class="panel-heading"><h4 class="panel-title">Bienvenue, <?= htmlspecialchars($user['name'] ?? 'Administrateur') ?></h4></div>
    <div class="panel-body"><p>Vous êtes connecté en tant que <strong><?= htmlspecialchars(ucfirst($user['role'] ?? '')) ?></strong>. Utilisez le menu de gauche pour accéder à vos fonctionnalités.</p></div>
</div>
<?php require_once 'VIEW/layouts/admin_footer.php'; ?>
