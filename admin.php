<?php
// Initialisation de données factices pour simuler l'IAM si aucune session n'existe
session_start();

if (!isset($_SESSION['iam_users']) || empty($_SESSION['iam_users'])) {
    $_SESSION['iam_users'] = [
        [
            'id' => 1,
            'nom' => 'Gueye',
            'prenom' => 'Ibrahima Khalil',
            'email' => 'ibrahimakhalil2k4@gmail.com',
            'telephone' => '0601020304',
            'role' => 'Administrateur',
            'photo' => 'user-13.jpg',
            'created_at' => '2026-01-15',
            'status' => 'Actif',
            'extra' => []
        ],
        [
            'id' => 2,
            'nom' => 'Sonko',
            'prenom' => 'Ibrahima Harris',
            'email' => 'richcvnv5@gmail.com',
            'telephone' => '762825938',
            'role' => 'Administrateur',
            'photo' => 'user-13.jpg',
            'created_at' => '2026-06-20',
            'status' => 'Actif',
            'extra' => []
        ]
    ];
}

if (!isset($_SESSION['iam_permissions'])) {
    $_SESSION['iam_permissions'] = [
        'Administrateur' => ['gererUtilisateurs', 'configurerPermissions', 'consulterLogs'],
        'Etudiant' => ['consulterNotes', 'voirEmploiDuTemps', 'accederVote'],
        'Parent' => ['suivreNotes', 'suivreAbsences'],
        'Professeur' => ['saisirNotes', 'gererPresences', 'partagerDocuments'],
        'Comptable' => ['validerPaiement', 'genererRecu']
    ];
}

if (!isset($_SESSION['iam_logs'])) {
    $_SESSION['iam_logs'] = [
        ['timestamp' => '2026-06-20 14:10:05', 'user' => 'Ibrahima Khalil Gueye (Admin)', 'action' => 'Modification de l\'utilisateur ID : 1', 'ip' => '::1', 'status' => 'Success'],
        ['timestamp' => '2026-06-20 12:35:22', 'user' => 'Ibrahima Khalil Gueye (Admin)', 'action' => 'Mise à jour des permissions du rôle Administrateur', 'ip' => '::1', 'status' => 'Success'],
        ['timestamp' => '2026-06-20 09:27:24', 'user' => 'Ibrahima Khalil Gueye (Admin)', 'action' => 'Création de l\'utilisateur Ibrahima Harris Sonko (Rôle: Administrateur)', 'ip' => '::1', 'status' => 'Success']
    ];
}

// Logique pour gérer les requêtes POST (Ajout, Modification, Suppression, Permissions)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'reset_session') {
            $_SESSION['iam_users'] = [];
            $_SESSION['iam_logs'] = [];
            header('Location: admin.php');
            exit;
        }
        
        if ($_POST['action'] === 'add_user') {
            $newId = count($_SESSION['iam_users']) > 0 ? max(array_column($_SESSION['iam_users'], 'id')) + 1 : 1;
            $role = $_POST['role'];
            $extra = [];
            if ($role === 'Etudiant') {
                $extra = [
                    'code' => $_POST['etudiant_code'] ?? '',
                    'niveau' => $_POST['etudiant_niveau'] ?? '',
                    'parent_id' => $_POST['etudiant_parent'] ?? ''
                ];
            } elseif ($role === 'Professeur') {
                $extra = [
                    'specialite' => $_POST['professeur_specialite'] ?? ''
                ];
            }
            
            $newUser = [
                'id' => $newId,
                'nom' => $_POST['nom'],
                'prenom' => $_POST['prenom'],
                'email' => $_POST['email'],
                'telephone' => $_POST['telephone'],
                'role' => $role,
                'photo' => 'user-13.jpg',
                'created_at' => date('Y-m-d'),
                'status' => 'Actif',
                'extra' => $extra
            ];
            $_SESSION['iam_users'][] = $newUser;
            
            $_SESSION['iam_logs'][] = [
                'timestamp' => date('Y-m-d H:i:s'),
                'user' => 'Ibrahima Khalil Gueye (Admin)',
                'action' => "Création de l'utilisateur {$newUser['prenom']} {$newUser['nom']} (Rôle: {$role})",
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'status' => 'Success'
            ];
        } elseif ($_POST['action'] === 'edit_user') {
            $userId = (int)$_POST['user_id'];
            foreach ($_SESSION['iam_users'] as &$user) {
                if ($user['id'] === $userId) {
                    $user['nom'] = $_POST['nom'];
                    $user['prenom'] = $_POST['prenom'];
                    $user['email'] = $_POST['email'];
                    $user['telephone'] = $_POST['telephone'];
                    $user['status'] = $_POST['status'];
                    break;
                }
            }
            $_SESSION['iam_logs'][] = [
                'timestamp' => date('Y-m-d H:i:s'),
                'user' => 'Ibrahima Khalil Gueye (Admin)',
                'action' => "Modification de l'utilisateur ID: {$userId}",
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'status' => 'Success'
            ];
        } elseif ($_POST['action'] === 'delete_user') {
            $userId = (int)$_POST['user_id'];
            $_SESSION['iam_users'] = array_filter($_SESSION['iam_users'], function($u) use ($userId) {
                return $u['id'] !== $userId;
            });
            $_SESSION['iam_logs'][] = [
                'timestamp' => date('Y-m-d H:i:s'),
                'user' => 'Ibrahima Khalil Gueye (Admin)',
                'action' => "Suppression de l'utilisateur ID: {$userId}",
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'status' => 'Success'
            ];
        } elseif ($_POST['action'] === 'update_permissions') {
            $role = $_POST['role_permission'];
            $perms = isset($_POST['perms']) ? $_POST['perms'] : [];
            $_SESSION['iam_permissions'][$role] = $perms;
        }
    }
    header('Location: admin.php');
    exit;
}

$stats = ['Administrateur' => 0, 'Etudiant' => 0, 'Professeur' => 0, 'Parent' => 0, 'Comptable' => 0, 'Total' => count($_SESSION['iam_users'])];
foreach ($_SESSION['iam_users'] as $user) { if (isset($stats[$user['role']])) { $stats[$user['role']]++; } }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>IAM | Gestion des Identités et Accès</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="assets/css/default/app.min.css" rel="stylesheet" />
    <link href="assets/css/default/theme/green.min.css" rel="stylesheet" />
    <style>
        .navbar-brand img.iam-logo { height: 36px; width: auto; margin-right: 8px; vertical-align: middle; }
        .sidebar, .sidebar-bg { background: #2d353c !important; }
        .sidebar .nav > li > a:hover, .sidebar .nav > li.active > a { background: #1a6b3c !important; color: #fff !important; }
        .sidebar .nav > li > a { color: #a8b2b9 !important; }
        .header.navbar-default { background: #fff; border-bottom: 1px solid #ddd; }
        .label-theme, .badge-success { background-color: #00acac !important; color: #fff; }
    </style>
</head>
<body>
    <div id="page-container" class="fade show page-sidebar-fixed page-header-fixed">
        
        <div id="header" class="header navbar-default">
            <div class="navbar-header">
                <a href="index.html" class="navbar-brand">
                    <img src="assets/img/logo/logo-iam.jpg" alt="Logo IAM" class="iam-logo" />
                    <b>IAM</b> Admin
                </a>
            </div>
            <ul class="navbar-nav navbar-right">
                <li class="dropdown navbar-user">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="assets/img/user/user-13.jpg" alt="" /> 
                        <span class="d-none d-md-inline">Ibrahima Khalil Gueye (Admin)</span> <b class="caret"></b>
                    </a>
                </li>
            </ul>
        </div>
        
        <div id="sidebar" class="sidebar">
            <div data-scrollbar="true" data-height="100%">
                <ul class="nav">
                    <li class="nav-profile">
                        <div class="info">Ibrahima Khalil Gueye<small>Administrateur IAM</small></div>
                    </li>
                    <li class="nav-header">Navigation</li>
                    <li><a href="index.html"><i class="fa fa-th-large"></i> <span>Dashboard</span></a></li>
                    <li class="active"><a href="admin.php"><i class="fa fa-user-shield"></i> <span>Gestion IAM</span></a></li>
                    <li><a href="table_basic.html"><i class="fa fa-table"></i> <span>Tables basiques</span></a></li>
                </ul>
            </div>
        </div>
        <div class="sidebar-bg"></div>
        
        <div id="content" class="content">
            <ol class="breadcrumb pull-right">
                <li class="breadcrumb-item"><a href="index.html">Accueil</a></li>
                <li class="breadcrumb-item active">Gestion IAM</li>
            </ol>
            <h1 class="page-header">Gestion des Identités et des Accès (IAM)</h1>
            
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="widget widget-stats" style="background: #00acac; color: #fff; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                        <div class="stats-info"><h4>UTILISATEURS TOTAL</h4><p><?php echo $stats['Total']; ?></p></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="widget widget-stats" style="background: #348fe2; color: #fff; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                        <div class="stats-info"><h4>ÉTUDIANTS / PARENTS</h4><p><?php echo $stats['Etudiant']; ?> / <?php echo $stats['Parent']; ?></p></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="widget widget-stats" style="background: #f59c1a; color: #fff; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                        <div class="stats-info"><h4>PROFESSEURS</h4><p><?php echo $stats['Professeur']; ?></p></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="widget widget-stats" style="background: #727cb6; color: #fff; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                        <div class="stats-info"><h4>COMPTABLES / ADMINS</h4><p><?php echo $stats['Comptable']; ?> / <?php echo $stats['Administrateur']; ?></p></div>
                    </div>
                </div>
            </div>
            
            <div class="panel panel-inverse" style="border: 1px solid #ddd; background: #fff;">
                <div class="panel-heading" style="background: #2d353c; color: #fff; padding: 10px 15px; display: flex; justify-content: space-between;">
                    <h4 class="panel-title" style="margin:0;">Liste des Utilisateurs</h4>
                    <div>
                        <button onclick="openAddModal()" class="btn btn-xs btn-success" style="background:#00acac; border:none;">+ Ajouter</button>
                    </div>
                </div>
                <div class="panel-body" style="padding: 15px;">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom & Prénom</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Rôle</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['iam_users'] as $u): ?>
                            <tr>
                                <td><?php echo $u['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($u['nom'] . ' ' . $u['prenom']); ?></strong></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td><?php echo htmlspecialchars($u['telephone']); ?></td>
                                <td><span class="label label-theme"><?php echo htmlspecialchars($u['role']); ?></span></td>
                                <td><span class="badge badge-success"><?php echo htmlspecialchars($u['status']); ?></span></td>
                                <td>
                                    <button onclick='openEditModal(<?php echo json_encode($u); ?>)' class="btn btn-xs btn-info">Modifier</button>
                                    <button onclick="openDeleteModal(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['prenom'] . ' ' . $u['nom']); ?>')" class="btn btn-xs btn-danger">Supprimer</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="admin.php" method="POST" id="userForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Ajouter un Utilisateur</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" id="formAction" value="add_user" />
                        <input type="hidden" name="user_id" id="formUserId" value="" />
                        <div class="row">
                            <div class="col-md-6"><label>Prénom</label><input type="text" name="prenom" id="formPrenom" class="form-control" required /></div>
                            <div class="col-md-6"><label>Nom</label><input type="text" name="nom" id="formNom" class="form-control" required /></div>
                        </div>
                        <div class="row m-t-10">
                            <div class="col-md-6"><label>Email</label><input type="email" name="email" id="formEmail" class="form-control" required /></div>
                            <div class="col-md-6"><label>Téléphone</label><input type="text" name="telephone" id="formTelephone" class="form-control" /></div>
                        </div>
                        <div class="row m-t-10">
                            <div class="col-md-6">
                                <label>Rôle</label>
                                <select name="role" id="formRole" class="form-control" required>
                                    <option value="Administrateur">Administrateur</option>
                                    <option value="Etudiant">Étudiant</option>
                                    <option value="Professeur">Professeur</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="statusGroup" style="display:none;">
                                <label>Statut</label>
                                <select name="status" id="formStatus" class="form-control"><option value="Actif">Actif</option><option value="Inactif">Inactif</option></select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="admin.php" method="POST">
                    <div class="modal-header"><h5 class="modal-title">Suppression</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete_user" />
                        <input type="hidden" name="user_id" id="deleteUserId" value="" />
                        <p>Supprimer <strong id="deleteUserName"></strong> ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/js/app.min.js"></script>
    <script src="assets/js/theme/green.min.js"></script>
    <script>
    function openAddModal() {
        document.getElementById('userForm').reset();
        document.getElementById('modalTitle').innerText = "Ajouter un Utilisateur";
        document.getElementById('formAction').value = "add_user";
        document.getElementById('statusGroup').style.display = "none";
        document.getElementById('formRole').disabled = false;
        $('#userModal').modal('show');
    }
    function openEditModal(user) {
        document.getElementById('modalTitle').innerText = "Modifier l'Utilisateur ID: " + user.id;
        document.getElementById('formAction').value = "edit_user";
        document.getElementById('formUserId').value = user.id;
        document.getElementById('formPrenom').value = user.prenom;
        document.getElementById('formNom').value = user.nom;
        document.getElementById('formEmail').value = user.email;
        document.getElementById('formTelephone').value = user.telephone;
        document.getElementById('formRole').value = user.role;
        document.getElementById('formStatus').value = user.status;
        document.getElementById('statusGroup').style.display = "block";
        document.getElementById('formRole').disabled = true;
        $('#userModal').modal('show');
    }
    function openDeleteModal(id, name) {
        document.getElementById('deleteUserId').value = id;
        document.getElementById('deleteUserName').innerText = name;
        $('#deleteModal').modal('show');
    }
    </script>
</body>
</html>