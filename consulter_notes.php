<?php
$allowedRoles = ['student', 'etudiant'];
require_once __DIR__ . '/CONTROLLER/auth_guard.php';
$pageTitle    = 'Mes Notes';
$pageSubtitle = 'Résultats par matière';
$activePage   = 'notes';
require_once 'VIEW/layouts/admin_header.php';
?>
<div class="panel panel-inverse">
    <div class="panel-heading"><h4 class="panel-title">Notes par matière</h4></div>
    <div class="panel-body">
        <p class="text-muted">Les notes seront affichées ici une fois la base de données connectée.</p>
        <a href="espace_etudiant.php" class="btn btn-default">← Retour</a>
    </div>
</div>
<?php require_once 'VIEW/layouts/admin_footer.php'; ?>
