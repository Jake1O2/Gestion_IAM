<?php
$allowedRoles = ['teacher', 'enseignant', 'professeur'];
require_once __DIR__ . '/CONTROLLER/auth_guard.php';
$pageTitle    = 'Gestion des Notes';
$pageSubtitle = 'Saisir et modifier les notes';
$activePage   = 'notes';
require_once 'VIEW/layouts/admin_header.php';
?>
<div class="panel panel-inverse">
    <div class="panel-heading"><h4 class="panel-title">Saisie des notes</h4></div>
    <div class="panel-body">
        <p class="text-muted">L'interface de saisie des notes sera disponible une fois la base de données connectée.</p>
        <a href="espace_enseignant.php" class="btn btn-default">← Retour</a>
    </div>
</div>
<?php require_once 'VIEW/layouts/admin_footer.php'; ?>
