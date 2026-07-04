<?php
// Vérification session et rôle admin uniquement
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user'])) { header('Location: login.php'); exit; }
$currentUser = $_SESSION['user'];
$role = strtolower($currentUser['role'] ?? '');
if ($role !== 'admin' && $role !== 'administrateur') {
    header('Location: index.php');
    exit;
}

// Reset de session si demandé
if (isset($_GET['reset'])) {
    unset($_SESSION['iam_users'], $_SESSION['iam_logs'], $_SESSION['iam_permissions']);
    header('Location: gestion_iam.php');
    exit;
}

if (!isset($_SESSION['iam_users'])) {
    $_SESSION['iam_users'] = [
        [
            'id' => 1,
            'nom' => $currentUser['name'] ?? 'Admin',
            'prenom' => '',
            'email' => $currentUser['email'] ?? '',
            'telephone' => '',
            'role' => 'Administrateur',
            'photo' => 'user-13.jpg',
            'created_at' => date('Y-m-d'),
            'status' => 'Actif',
            'extra' => []
        ]
    ];
}

if (!isset($_SESSION['iam_permissions'])) {
    $_SESSION['iam_permissions'] = [
        'Administrateur' => ['gererUtilisateurs', 'configurerPermissions', 'consulterLogs'],
        'Etudiant'       => ['consulterNotes', 'voirEmploiDuTemps', 'accederVote'],
        'Parent'         => ['suivreNotes', 'suivreAbsences'],
        'Professeur'     => ['saisirNotes', 'gererPresences', 'partagerDocuments'],
        'Comptable'      => ['validerPaiement', 'genererRecu']
    ];
}

if (!isset($_SESSION['iam_logs'])) {
    $_SESSION['iam_logs'] = [];
}

// Gestion POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $adminName = htmlspecialchars($currentUser['name'] ?? 'Admin') . ' (Admin)';

    if ($_POST['action'] === 'add_user') {
        $newId = count($_SESSION['iam_users']) > 0 ? max(array_column($_SESSION['iam_users'], 'id')) + 1 : 1;
        $rolePost = $_POST['role'];
        $extra = [];
        if ($rolePost === 'Etudiant') {
            $extra = ['code' => $_POST['etudiant_code'] ?? '', 'niveau' => $_POST['etudiant_niveau'] ?? '', 'parent_id' => $_POST['etudiant_parent'] ?? ''];
        } elseif ($rolePost === 'Professeur') {
            $extra = ['specialite' => $_POST['professeur_specialite'] ?? ''];
        }
        $newUser = [
            'id' => $newId, 'nom' => $_POST['nom'], 'prenom' => $_POST['prenom'],
            'email' => $_POST['email'], 'telephone' => $_POST['telephone'],
            'role' => $rolePost, 'photo' => 'user-13.jpg',
            'created_at' => date('Y-m-d'), 'status' => 'Actif', 'extra' => $extra
        ];
        $_SESSION['iam_users'][] = $newUser;
        $_SESSION['iam_logs'][] = ['timestamp' => date('Y-m-d H:i:s'), 'user' => $adminName, 'action' => "Création de {$newUser['prenom']} {$newUser['nom']} (Rôle: {$rolePost})", 'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', 'status' => 'Success'];

    } elseif ($_POST['action'] === 'edit_user') {
        $userId = (int)$_POST['user_id'];
        foreach ($_SESSION['iam_users'] as &$u) {
            if ($u['id'] === $userId) {
                $u['nom'] = $_POST['nom']; $u['prenom'] = $_POST['prenom'];
                $u['email'] = $_POST['email']; $u['telephone'] = $_POST['telephone'];
                $u['status'] = $_POST['status'];
                if ($u['role'] === 'Etudiant') { $u['extra']['code'] = $_POST['etudiant_code'] ?? ''; $u['extra']['niveau'] = $_POST['etudiant_niveau'] ?? ''; $u['extra']['parent_id'] = $_POST['etudiant_parent'] ?? ''; }
                elseif ($u['role'] === 'Professeur') { $u['extra']['specialite'] = $_POST['professeur_specialite'] ?? ''; }
                break;
            }
        }
        $_SESSION['iam_logs'][] = ['timestamp' => date('Y-m-d H:i:s'), 'user' => $adminName, 'action' => "Modification utilisateur ID: {$userId}", 'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', 'status' => 'Success'];

    } elseif ($_POST['action'] === 'delete_user') {
        $userId = (int)$_POST['user_id'];
        $_SESSION['iam_users'] = array_values(array_filter($_SESSION['iam_users'], fn($u) => $u['id'] !== $userId));
        $_SESSION['iam_logs'][] = ['timestamp' => date('Y-m-d H:i:s'), 'user' => $adminName, 'action' => "Suppression utilisateur ID: {$userId}", 'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', 'status' => 'Success'];

    } elseif ($_POST['action'] === 'update_permissions') {
        $roleP = $_POST['role_permission'];
        $_SESSION['iam_permissions'][$roleP] = $_POST['perms'] ?? [];
        $_SESSION['iam_logs'][] = ['timestamp' => date('Y-m-d H:i:s'), 'user' => $adminName, 'action' => "Mise à jour permissions rôle {$roleP}", 'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', 'status' => 'Success'];
    }

    header('Location: gestion_iam.php');
    exit;
}

// Stats
$stats = ['Administrateur' => 0, 'Etudiant' => 0, 'Professeur' => 0, 'Parent' => 0, 'Comptable' => 0, 'Total' => count($_SESSION['iam_users'])];
foreach ($_SESSION['iam_users'] as $u) { if (isset($stats[$u['role']])) $stats[$u['role']]++; }

$ASSETS = 'public/templates/templateAdmin/assets';
$LOGO   = 'public/templates/templateVitrine/assets/img/logo/logo-iam.jpg';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>IAM | Gestion des Identités et Accès</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="<?= $ASSETS ?>/css/default/app.min.css" rel="stylesheet" />
    <link href="<?= $ASSETS ?>/css/default/theme/green.min.css" rel="stylesheet" />
    <style>
        .navbar-brand img.iam-logo { height: 36px; width: auto; margin-right: 8px; vertical-align: middle; }
        .sidebar, .sidebar-bg { background: #1a6b3c !important; }
        .sidebar .nav > li > a:hover, .sidebar .nav > li.active > a { background: #145530 !important; }
        .sidebar .nav > li > a { color: #d4edda !important; }
        .sidebar .nav > li.active > a { color: #fff !important; }
        .sidebar .nav > li.nav-header { color: #a8d5b5 !important; }
        .btn-primary, .btn-success { background-color: #1a6b3c !important; border-color: #1a6b3c !important; }
        .btn-primary:hover, .btn-success:hover { background-color: #145530 !important; }
        .label-theme, .badge-success, .label-success { background-color: #1a6b3c !important; }
        .panel { cursor: default !important; }
        .panel-heading { cursor: default !important; }
    </style>
</head>
<body>
<div id="page-loader" class="fade show"><span class="spinner"></span></div>
<div id="page-container" class="fade page-sidebar-fixed page-header-fixed">

    <!-- HEADER -->
    <div id="header" class="header navbar-default">
        <div class="navbar-header">
            <a href="index.php" class="navbar-brand">
                <img src="<?= $LOGO ?>" alt="Logo IAM" class="iam-logo" />
                <b>IAM</b> Admin
            </a>
            <button type="button" class="navbar-toggle" data-click="sidebar-toggled">
                <span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
            </button>
        </div>
        <ul class="navbar-nav navbar-right">
            <li class="dropdown navbar-user">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:50%;background:#1a6b3c;color:#fff;font-weight:700;font-size:0.8rem;"><?= strtoupper(substr($currentUser["name"] ?? "A", 0, 1)) ?></span>
                    <span class="d-none d-md-inline"><?= htmlspecialchars($currentUser['name'] ?? 'Admin') ?> (Admin)</span>
                    <b class="caret"></b>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="logout.php" class="dropdown-item">Se déconnecter</a>
                </div>
            </li>
        </ul>
    </div>

    <!-- SIDEBAR -->
    <div id="sidebar" class="sidebar">
        <div data-scrollbar="true" data-height="100%">
            <ul class="nav">
                <li class="nav-profile">
                    <a href="javascript:;" data-toggle="nav-profile">
                        <div class="cover with-shadow"></div>
                        <div class="image"><span style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:50%;background:#1a6b3c;color:#fff;font-weight:700;font-size:0.8rem;"><?= strtoupper(substr($currentUser["name"] ?? "A", 0, 1)) ?></span></div>
                        <div class="info">
                            <b class="caret pull-right"></b><?= htmlspecialchars($currentUser['name'] ?? 'Admin') ?>
                            <small>Administrateur IAM</small>
                        </div>
                    </a>
                </li>
            </ul>
            <ul class="nav">
                <li class="nav-header">Navigation</li>
                <li><a href="index.php"><i class="fa fa-globe"></i> <span>Retour au site</span></a></li>
                <li><a href="espace_administration.php"><i class="fa fa-th-large"></i> <span>Dashboard</span></a></li>
                <li class="active"><a href="gestion_iam.php"><i class="fa fa-user-shield"></i> <span>Gestion IAM</span></a></li>
                <li><a href="logout.php"><i class="fa fa-sign-out-alt"></i> <span>Déconnexion</span></a></li>
                <li><a href="javascript:;" class="sidebar-minify-btn" data-click="sidebar-minify"><i class="fa fa-angle-double-left"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="sidebar-bg"></div>

    <!-- CONTENT -->
    <div id="content" class="content">
        <ol class="breadcrumb float-xl-right">
            <li class="breadcrumb-item"><a href="espace_administration.php">Accueil</a></li>
            <li class="breadcrumb-item active">Gestion IAM</li>
        </ol>
        <h1 class="page-header">Gestion des Identités et des Accès (IAM) <small>Portail Administrateur</small></h1>

        <!-- Stats -->
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="widget widget-stats bg-blue">
                    <div class="stats-icon"><i class="fa fa-users"></i></div>
                    <div class="stats-info"><h4>UTILISATEURS TOTAL</h4><p><?= $stats['Total'] ?></p></div>
                    <div class="stats-link"><a href="javascript:;">Détail des comptes <i class="fa fa-arrow-alt-circle-right"></i></a></div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="widget widget-stats bg-info">
                    <div class="stats-icon"><i class="fa fa-user-graduate"></i></div>
                    <div class="stats-info"><h4>ÉTUDIANTS / PARENTS</h4><p><?= $stats['Etudiant'] ?> / <?= $stats['Parent'] ?></p></div>
                    <div class="stats-link"><a href="javascript:;">Voir les fiches <i class="fa fa-arrow-alt-circle-right"></i></a></div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="widget widget-stats bg-orange">
                    <div class="stats-icon"><i class="fa fa-chalkboard-teacher"></i></div>
                    <div class="stats-info"><h4>PROFESSEURS</h4><p><?= $stats['Professeur'] ?></p></div>
                    <div class="stats-link"><a href="javascript:;">Voir les spécialités <i class="fa fa-arrow-alt-circle-right"></i></a></div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="widget widget-stats bg-purple">
                    <div class="stats-icon"><i class="fa fa-file-invoice-dollar"></i></div>
                    <div class="stats-info"><h4>COMPTABLES / ADMINS</h4><p><?= $stats['Comptable'] ?> / <?= $stats['Administrateur'] ?></p></div>
                    <div class="stats-link"><a href="javascript:;">Gérer les rôles <i class="fa fa-arrow-alt-circle-right"></i></a></div>
                </div>
            </div>
        </div>

        <!-- Liste utilisateurs -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-inverse">
                    <div class="panel-heading">
                        <h4 class="panel-title">Liste des Utilisateurs</h4>
                        <div class="panel-heading-btn">
                            <button onclick="openAddModal()" class="btn btn-xs btn-primary"><i class="fa fa-plus"></i> Ajouter un Utilisateur</button>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped m-b-0">
                                <thead>
                                    <tr>
                                        <th>ID</th><th>Photo</th><th>Nom & Prénom</th><th>Email</th>
                                        <th>Téléphone</th><th>Rôle</th><th>Attributs Spécifiques</th>
                                        <th>Créé le</th><th>Statut</th><th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($_SESSION['iam_users'] as $u): ?>
                                    <tr>
                                        <td><?= $u['id'] ?></td>
                                        <td>
                                            <span style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:50%;background:#1a6b3c;color:#fff;font-weight:700;font-size:0.8rem;">
                                                <?= strtoupper(substr($u['prenom'] ?? $u['nom'] ?? 'U', 0, 1)) ?>
                                            </span>
                                        </td>
                                        <td><strong><?= htmlspecialchars($u['nom'].' '.$u['prenom']) ?></strong></td>
                                        <td><?= htmlspecialchars($u['email']) ?></td>
                                        <td><?= htmlspecialchars($u['telephone']) ?></td>
                                        <td>
                                            <span class="label <?= $u['role']==='Administrateur'?'label-danger':($u['role']==='Etudiant'?'label-success':($u['role']==='Professeur'?'label-warning':($u['role']==='Parent'?'label-primary':'label-inverse'))) ?>">
                                                <?= htmlspecialchars($u['role']) ?>
                                            </span>
                                        </td>
                                        <td><small>
                                            <?php if ($u['role']==='Etudiant'): ?>
                                                <strong>Code:</strong> <?= htmlspecialchars($u['extra']['code']??'') ?><br>
                                                <strong>Niveau:</strong> <?= htmlspecialchars($u['extra']['niveau']??'') ?>
                                            <?php elseif ($u['role']==='Professeur'): ?>
                                                <strong>Spécialité:</strong> <?= htmlspecialchars($u['extra']['specialite']??'') ?>
                                            <?php else: ?>-<?php endif; ?>
                                        </small></td>
                                        <td><?= htmlspecialchars($u['created_at']) ?></td>
                                        <td><span class="badge badge-<?= $u['status']==='Actif'?'success':'warning' ?>"><?= htmlspecialchars($u['status']) ?></span></td>
                                        <td>
                                            <button onclick='openEditModal(<?= json_encode($u) ?>)' class="btn btn-xs btn-info"><i class="fa fa-edit"></i> Modifier</button>
                                            <button onclick="openDeleteModal(<?= $u['id'] ?>, '<?= htmlspecialchars($u['prenom'].' '.$u['nom']) ?>')" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Supprimer</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions & Logs -->
        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-inverse">
                    <div class="panel-heading"><h4 class="panel-title">Permissions & Fonctions par Rôle</h4></div>
                    <div class="panel-body">
                        <p class="text-muted">Configurez les méthodes et accès autorisés par rôle conformément au diagramme de classes.</p>
                        <div class="accordion" id="accordionPermissions">
                            <?php foreach ($_SESSION['iam_permissions'] as $roleP => $perms): ?>
                            <div class="card bg-light m-b-10">
                                <div class="card-header font-weight-bold" style="cursor:pointer;" data-toggle="collapse" data-target="#collapse<?= $roleP ?>">
                                    <i class="fa fa-shield-alt text-muted m-r-5"></i> Rôle : <?= $roleP ?>
                                    <span class="badge badge-secondary pull-right"><?= count($perms) ?> méthode(s)</span>
                                </div>
                                <div id="collapse<?= $roleP ?>" class="collapse" data-parent="#accordionPermissions">
                                    <div class="card-body">
                                        <form action="gestion_iam.php" method="POST">
                                            <input type="hidden" name="action" value="update_permissions" />
                                            <input type="hidden" name="role_permission" value="<?= htmlspecialchars($roleP) ?>" />
                                            <div class="form-group">
                                                <label>Méthodes autorisées :</label>
                                                <?php
                                                $allPerms = [
                                                    'Administrateur' => [['gererUtilisateurs','gererUtilisateurs()','Gérer les comptes utilisateurs'],['configurerPermissions','configurerPermissions()','Assigner les droits'],['consulterLogs','consulterLogs()','Lire les journaux']],
                                                    'Etudiant'       => [['consulterNotes','consulterNotes()','Consulter son relevé'],['voirEmploiDuTemps','voirEmploiDuTemps()','Voir l\'agenda'],['accederVote','accederVote()','Voter aux élections']],
                                                    'Parent'         => [['suivreNotes','suivreNotes()','Suivre le bulletin'],['suivreAbsences','suivreAbsences()','Suivre les absences']],
                                                    'Professeur'     => [['saisirNotes','saisirNotes()','Saisir les notes'],['gererPresences','gererPresences()','Faire l\'appel'],['partagerDocuments','partagerDocuments()','Publier ressources']],
                                                    'Comptable'      => [['validerPaiement','validerPaiement()','Valider paiements'],['genererRecu','genererRecu()','Émettre reçus']],
                                                ];
                                                $i = 0;
                                                foreach ($allPerms[$roleP] ?? [] as [$val, $code, $desc]):
                                                    $i++;
                                                ?>
                                                <div class="checkbox checkbox-css">
                                                    <input type="checkbox" id="p_<?= $roleP ?>_<?= $i ?>" name="perms[]" value="<?= $val ?>" <?= in_array($val, $perms)?'checked':'' ?> />
                                                    <label for="p_<?= $roleP ?>_<?= $i ?>"><code><?= $code ?></code> - <?= $desc ?></label>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-primary m-t-5">Appliquer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="panel panel-inverse">
                    <div class="panel-heading"><h4 class="panel-title">Audit Trail / Journal d'activité</h4></div>
                    <div class="panel-body">
                        <div class="table-responsive" style="max-height:380px; overflow-y:auto;">
                            <table class="table table-condensed table-striped">
                                <thead><tr><th>Date/Heure</th><th>Utilisateur</th><th>Action</th><th>IP</th><th>Résultat</th></tr></thead>
                                <tbody>
                                    <?php foreach (array_reverse($_SESSION['iam_logs']) as $log): ?>
                                    <tr>
                                        <td><small><?= htmlspecialchars($log['timestamp']) ?></small></td>
                                        <td><small><?= htmlspecialchars($log['user']) ?></small></td>
                                        <td><small><?= htmlspecialchars($log['action']) ?></small></td>
                                        <td><small><code><?= htmlspecialchars($log['ip']) ?></code></small></td>
                                        <td><span class="badge badge-success"><?= htmlspecialchars($log['status']) ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL AJOUT -->
    <div class="modal fade" id="modalAddUser" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document"><div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Créer un nouvel Utilisateur</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="gestion_iam.php" method="POST">
                <input type="hidden" name="action" value="add_user" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group"><label>Prénom <span class="text-danger">*</span></label><input type="text" name="prenom" class="form-control" required /></div>
                        <div class="col-md-6 form-group"><label>Nom <span class="text-danger">*</span></label><input type="text" name="nom" class="form-control" required /></div>
                    </div>
                    <div class="form-group"><label>Email <span class="text-danger">*</span></label><input type="email" name="email" class="form-control" required /></div>
                    <div class="form-group"><label>Téléphone</label><input type="text" name="telephone" class="form-control" /></div>
                    <div class="form-group">
                        <label>Rôle <span class="text-danger">*</span></label>
                        <select id="add_user_role" name="role" class="form-control" onchange="toggleAddFields(this.value)" required>
                            <option value="Administrateur">Administrateur</option>
                            <option value="Etudiant">Étudiant</option>
                            <option value="Parent">Parent</option>
                            <option value="Professeur">Professeur</option>
                            <option value="Comptable">Comptable</option>
                        </select>
                    </div>
                    <div id="add_fields_etudiant" style="display:none; border-left:3px solid #00acac; padding-left:15px; margin-bottom:15px;">
                        <div class="form-group"><label>Code Étudiant</label><input type="text" name="etudiant_code" class="form-control" placeholder="Ex: 8876" /></div>
                        <div class="form-group"><label>Niveau</label><input type="text" name="etudiant_niveau" class="form-control" placeholder="Ex: Licence 3" /></div>
                        <div class="form-group">
                            <label>Parent Rattaché</label>
                            <select name="etudiant_parent" class="form-control">
                                <option value="">-- Aucun --</option>
                                <?php foreach($_SESSION['iam_users'] as $u): if($u['role']==='Parent'): ?>
                                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['prenom'].' '.$u['nom']) ?></option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div id="add_fields_professeur" style="display:none; border-left:3px solid #f5b041; padding-left:15px; margin-bottom:15px;">
                        <div class="form-group"><label>Spécialité</label><input type="text" name="professeur_specialite" class="form-control" /></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div></div>
    </div>

    <!-- MODAL EDIT -->
    <div class="modal fade" id="modalEditUser" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document"><div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier l'Utilisateur</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="gestion_iam.php" method="POST">
                <input type="hidden" name="action" value="edit_user" />
                <input type="hidden" id="edit_user_id" name="user_id" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group"><label>Prénom</label><input type="text" id="edit_prenom" name="prenom" class="form-control" required /></div>
                        <div class="col-md-6 form-group"><label>Nom</label><input type="text" id="edit_nom" name="nom" class="form-control" required /></div>
                    </div>
                    <div class="form-group"><label>Email</label><input type="email" id="edit_email" name="email" class="form-control" required /></div>
                    <div class="form-group"><label>Téléphone</label><input type="text" id="edit_telephone" name="telephone" class="form-control" /></div>
                    <div class="form-group"><label>Statut</label><select id="edit_status" name="status" class="form-control"><option value="Actif">Actif</option><option value="Suspendu">Suspendu</option></select></div>
                    <div class="form-group"><label>Rôle</label><input type="text" id="edit_role_display" class="form-control" disabled /></div>
                    <div id="edit_fields_etudiant" style="display:none; border-left:3px solid #00acac; padding-left:15px;">
                        <div class="form-group"><label>Code</label><input type="text" id="edit_etudiant_code" name="etudiant_code" class="form-control" /></div>
                        <div class="form-group"><label>Niveau</label><input type="text" id="edit_etudiant_niveau" name="etudiant_niveau" class="form-control" /></div>
                    </div>
                    <div id="edit_fields_professeur" style="display:none; border-left:3px solid #f5b041; padding-left:15px;">
                        <div class="form-group"><label>Spécialité</label><input type="text" id="edit_professeur_specialite" name="professeur_specialite" class="form-control" /></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-info">Sauvegarder</button>
                </div>
            </form>
        </div></div>
    </div>

    <!-- MODAL DELETE -->
    <div class="modal fade" id="modalDeleteUser" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document"><div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Supprimer l'Utilisateur ?</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="gestion_iam.php" method="POST">
                <input type="hidden" name="action" value="delete_user" />
                <input type="hidden" id="delete_user_id" name="user_id" />
                <div class="modal-body"><p>Êtes-vous sûr de vouloir supprimer <strong id="delete_user_name"></strong> ? Cette action est irréversible.</p></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                </div>
            </form>
        </div></div>
    </div>

    <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
</div>

<script src="<?= $ASSETS ?>/js/app.min.js"></script>
<script src="<?= $ASSETS ?>/js/theme/default.min.js"></script>
<script>
$(document).ready(function() {
    if (typeof $.fn.sortable !== 'undefined') $('.sortable').sortable('destroy');
});
function toggleAddFields(role) {
    document.getElementById('add_fields_etudiant').style.display = (role==='Etudiant') ? 'block' : 'none';
    document.getElementById('add_fields_professeur').style.display = (role==='Professeur') ? 'block' : 'none';
}
function openAddModal() {
    $('#modalAddUser').modal('show');
    toggleAddFields(document.getElementById('add_user_role').value);
}
function openEditModal(user) {
    document.getElementById('edit_user_id').value = user.id;
    document.getElementById('edit_prenom').value = user.prenom;
    document.getElementById('edit_nom').value = user.nom;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_telephone').value = user.telephone;
    document.getElementById('edit_status').value = user.status;
    document.getElementById('edit_role_display').value = user.role;
    document.getElementById('edit_fields_etudiant').style.display = (user.role==='Etudiant') ? 'block' : 'none';
    if (user.role==='Etudiant') {
        document.getElementById('edit_etudiant_code').value = user.extra.code || '';
        document.getElementById('edit_etudiant_niveau').value = user.extra.niveau || '';
    }
    document.getElementById('edit_fields_professeur').style.display = (user.role==='Professeur') ? 'block' : 'none';
    if (user.role==='Professeur') document.getElementById('edit_professeur_specialite').value = user.extra.specialite || '';
    $('#modalEditUser').modal('show');
}
function openDeleteModal(id, name) {
    document.getElementById('delete_user_id').value = id;
    document.getElementById('delete_user_name').innerText = name;
    $('#modalDeleteUser').modal('show');
}
</script>
</body>
</html>
