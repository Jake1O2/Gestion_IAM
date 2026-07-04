<?php
$allowedRoles = ['parent'];
require_once __DIR__ . '/CONTROLLER/auth_guard.php';
$pageTitle    = 'Mes Enfants';
$pageSubtitle = 'Suivi scolaire';
$activePage   = 'enfants';
require_once 'VIEW/layouts/admin_header.php';
?>
<div class="panel panel-inverse">
    <div class="panel-heading"><h4 class="panel-title">Suivi de mes enfants</h4></div>
    <div class="panel-body">
        <p class="text-muted">Les informations de vos enfants seront affichées ici une fois la base de données connectée.</p>
        <a href="espace_parent.php" class="btn btn-default">← Retour</a>
    </div>
</div>
<?php require_once 'VIEW/layouts/admin_footer.php'; ?>
