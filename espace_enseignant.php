<?php
$allowedRoles = ['teacher', 'enseignant', 'professeur'];
require_once __DIR__ . '/CONTROLLER/auth_guard.php';
$pageTitle    = 'Espace Enseignant';
$pageSubtitle = 'Tableau de bord pédagogique';
$activePage   = 'espace';
require_once 'VIEW/layouts/admin_header.php';
?>
<div class="row">
    <div class="col-xl-4 col-md-6">
        <div class="widget widget-stats bg-purple">
            <div class="stats-icon"><i class="fa fa-star-half-alt"></i></div>
            <div class="stats-info"><h4>NOTES</h4><p>Saisir / modifier</p></div>
            <div class="stats-link"><a href="gerer_notes.php">Gérer les notes <i class="fa fa-arrow-alt-circle-right"></i></a></div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="widget widget-stats bg-blue">
            <div class="stats-icon"><i class="fa fa-calendar-alt"></i></div>
            <div class="stats-info"><h4>EMPLOI DU TEMPS</h4><p>Mes cours</p></div>
            <div class="stats-link"><a href="gerer_emploi_du_temps.php">Voir mon planning <i class="fa fa-arrow-alt-circle-right"></i></a></div>
        </div>
    </div>
</div>
<div class="panel panel-inverse">
    <div class="panel-heading"><h4 class="panel-title">Bienvenue, <?= htmlspecialchars($user['name'] ?? 'Enseignant') ?></h4></div>
    <div class="panel-body"><p>Vous êtes connecté en tant qu'<strong>Enseignant</strong>. Gérez les notes et les emplois du temps depuis le menu de gauche.</p></div>
</div>
<?php require_once 'VIEW/layouts/admin_footer.php'; ?>
