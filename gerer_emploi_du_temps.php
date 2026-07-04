<?php
$allowedRoles = ['student', 'etudiant', 'teacher', 'enseignant', 'professeur'];
require_once __DIR__ . '/CONTROLLER/auth_guard.php';
$pageTitle    = 'Emploi du Temps';
$pageSubtitle = 'Consultation et gestion des plannings';
$activePage   = 'emploi';
require_once 'VIEW/layouts/admin_header.php';
?>
<div class="panel panel-inverse">
    <div class="panel-heading"><h4 class="panel-title">Planning des cours</h4></div>
    <div class="panel-body">
        <p class="text-muted">L'emploi du temps sera affiché ici une fois la base de données connectée.</p>
        <a href="javascript:history.back()" class="btn btn-default">← Retour</a>
    </div>
</div>
<?php require_once 'VIEW/layouts/admin_footer.php'; ?>
