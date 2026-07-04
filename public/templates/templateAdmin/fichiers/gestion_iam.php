<?php
// Initialisation de données factices pour simuler l'IAM si aucune session n'existe
session_start();

// Reset de session si demandé
if (isset($_GET['reset'])) {
    session_destroy();
    header('Location: gestion_iam.php');
    exit;
}

if (!isset($_SESSION['iam_users'])) {
    $_SESSION['iam_users'] = [
        [
            'id' => 1,
            'nom' => 'Gueye',
            'prenom' => 'Ibrahima Khalil',
            'email' => 'ibrahima.gueye@ecole.com',
            'telephone' => '0601020304',
            'role' => 'Administrateur',
            'photo' => 'user-13.jpg',
            'created_at' => '2026-01-15',
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
    $_SESSION['iam_logs'] = [];
}

// Logique pour gérer les requêtes POST (Ajout, Modification, Suppression, Permissions)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
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
                'photo' => 'user-13.jpg', // valeur par défaut
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
                    
                    if ($user['role'] === 'Etudiant') {
                        $user['extra']['code'] = $_POST['etudiant_code'] ?? '';
                        $user['extra']['niveau'] = $_POST['etudiant_niveau'] ?? '';
                        $user['extra']['parent_id'] = $_POST['etudiant_parent'] ?? '';
                    } elseif ($user['role'] === 'Professeur') {
                        $user['extra']['specialite'] = $_POST['professeur_specialite'] ?? '';
                    }
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
            
            $_SESSION['iam_logs'][] = [
                'timestamp' => date('Y-m-d H:i:s'),
                'user' => 'Ibrahima Khalil Gueye (Admin)',
                'action' => "Mise à jour des permissions du rôle {$role}",
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'status' => 'Success'
            ];
        }
    }
    header('Location: gestion_iam.php');
    exit;
}

// Compter les statistiques
$stats = [
    'Administrateur' => 0,
    'Etudiant' => 0,
    'Professeur' => 0,
    'Parent' => 0,
    'Comptable' => 0,
    'Total' => count($_SESSION['iam_users'])
];
foreach ($_SESSION['iam_users'] as $user) {
    if (isset($stats[$user['role']])) {
        $stats[$user['role']]++;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8" />
	<title>IAM | Gestion des Identités et Accès</title>
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
	<meta content="Identity and Access Management Panel" name="description" />
	<meta content="Antigravity" name="author" />
	
	<!-- ================== BEGIN BASE CSS STYLE ================== -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
	<link href="../assets/css/default/app.min.css" rel="stylesheet" />
	<link href="../assets/css/default/theme/green.min.css" rel="stylesheet" />
	<!-- ================== END BASE CSS STYLE ================== -->
	<style>
		/* === Couleurs IAM #1a6b3c === */
		.navbar-brand img.iam-logo { height: 36px; width: auto; margin-right: 8px; vertical-align: middle; }

		/* Sidebar */
		.sidebar, .sidebar-bg { background: #1a6b3c !important; }
		.sidebar .nav > li > a:hover,
		.sidebar .nav > li.active > a,
		.sidebar .nav > li.active > a:hover { background: #145530 !important; }
		.sidebar .nav > li > a { color: #d4edda !important; }
		.sidebar .nav > li.active > a { color: #fff !important; }
		.sidebar .nav > li.nav-header { color: #a8d5b5 !important; }

		/* Header */
		.header.navbar-default { background: #fff; border-bottom: 1px solid #ddd; }

		/* Boutons primaires */
		.btn-primary,
		.btn-success { background-color: #1a6b3c !important; border-color: #1a6b3c !important; }
		.btn-primary:hover,
		.btn-success:hover { background-color: #145530 !important; border-color: #145530 !important; }

		/* Labels & badges thème */
		.label-theme,
		.badge-success,
		.label-success { background-color: #1a6b3c !important; }

		/* Liens actifs */
		.text-theme, a.text-theme { color: #1a6b3c !important; }

		/* Scroll to top */
		.btn-scroll-to-top { background-color: #1a6b3c !important; border-color: #1a6b3c !important; }

		/* Widget stats - premier widget en vert IAM */
		.widget.widget-stats.bg-blue { background-color: #1a6b3c !important; }
		/* Désactiver le drag & drop des panels */
		.panel { cursor: default !important; }
		.panel-heading { cursor: default !important; }
	</style>
</head>
<body>
	<!-- begin #page-loader -->
	<div id="page-loader" class="fade show">
		<span class="spinner"></span>
	</div>
	<!-- end #page-loader -->
	
	<!-- begin #page-container -->
	<div id="page-container" class="fade page-sidebar-fixed page-header-fixed">
		<!-- begin #header -->
		<div id="header" class="header navbar-default">
			<div class="navbar-header">
				<a href="index.html" class="navbar-brand">
					<img src="../assets/img/logo/logo-iam.jpg" alt="Logo IAM" class="iam-logo" />
					<b>IAM</b> Admin
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
						<img src="../assets/img/user/user-13.jpg" alt="" /> 
						<span class="d-none d-md-inline">Ibrahima Khalil Gueye (Admin)</span> <b class="caret"></b>
					</a>
					<div class="dropdown-menu dropdown-menu-right">
						<a href="javascript:;" class="dropdown-item">Profil</a>
						<div class="dropdown-divider"></div>
						<a href="javascript:;" class="dropdown-item">Se déconnecter</a>
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
								<img src="../assets/img/user/user-13.jpg" alt="" />
							</div>
							<div class="info">
								<b class="caret pull-right"></b>Ibrahima Khalil Gueye
								<small>Administrateur IAM</small>
							</div>
						</a>
					</li>
				</ul>
				
				<ul class="nav">
					<li class="nav-header">Navigation</li>
					<li>
						<a href="index.html">
							<i class="fa fa-th-large"></i>
							<span>Dashboard</span>
						</a>
					</li>
					<li class="active">
						<a href="gestion_iam.php">
							<i class="fa fa-user-shield"></i>
							<span>Gestion IAM</span>
						</a>
					</li>
					<li>
						<a href="table_basic.html">
							<i class="fa fa-table"></i>
							<span>Tables basiques</span>
						</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="sidebar-bg"></div>
		<!-- end #sidebar -->
		
		<!-- begin #content -->
		<div id="content" class="content">
			<!-- begin breadcrumb -->
			<ol class="breadcrumb pull-right">
				<li class="breadcrumb-item"><a href="javascript:;">Accueil</a></li>
				<li class="breadcrumb-item active">Gestion IAM</li>
			</ol>
			<!-- end breadcrumb -->
			
			<!-- begin page-header -->
			<h1 class="page-header">Gestion des Identités et des Accès (IAM) <small>Portail Administrateur</small></h1>
			<!-- end page-header -->
			
			<!-- begin row (Statistiques) -->
			<div class="row">
				<div class="col-lg-3 col-md-6">
					<div class="widget widget-stats bg-blue">
						<div class="stats-icon"><i class="fa fa-users"></i></div>
						<div class="stats-info">
							<h4>UTILISATEURS TOTAL</h4>
							<p><?php echo $stats['Total']; ?></p>	
						</div>
						<div class="stats-link">
							<a href="javascript:;">Détail des comptes <i class="fa fa-arrow-alt-circle-right"></i></a>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="widget widget-stats bg-info">
						<div class="stats-icon"><i class="fa fa-user-graduate"></i></div>
						<div class="stats-info">
							<h4>ÉTUDIANTS / PARENTS</h4>
							<p><?php echo $stats['Etudiant']; ?> / <?php echo $stats['Parent']; ?></p>	
						</div>
						<div class="stats-link">
							<a href="javascript:;">Voir les fiches <i class="fa fa-arrow-alt-circle-right"></i></a>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="widget widget-stats bg-orange">
						<div class="stats-icon"><i class="fa fa-chalkboard-teacher"></i></div>
						<div class="stats-info">
							<h4>PROFESSEURS</h4>
							<p><?php echo $stats['Professeur']; ?></p>	
						</div>
						<div class="stats-link">
							<a href="javascript:;">Voir les spécialités <i class="fa fa-arrow-alt-circle-right"></i></a>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="widget widget-stats bg-purple">
						<div class="stats-icon"><i class="fa fa-file-invoice-dollar"></i></div>
						<div class="stats-info">
							<h4>COMPTABLES / ADMINS</h4>
							<p><?php echo $stats['Comptable']; ?> / <?php echo $stats['Administrateur']; ?></p>	
						</div>
						<div class="stats-link">
							<a href="javascript:;">Gérer les rôles <i class="fa fa-arrow-alt-circle-right"></i></a>
						</div>
					</div>
				</div>
			</div>
			<!-- end row -->
			
			<!-- begin row (Main Content) -->
			<div class="row">
				<!-- Panel de Gestion des Utilisateurs -->
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
											<th>ID</th>
											<th>Photo</th>
											<th>Nom & Prénom</th>
											<th>Email</th>
											<th>Téléphone</th>
											<th>Rôle</th>
											<th>Attributs Spécifiques</th>
											<th>Créé le</th>
											<th>Statut</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($_SESSION['iam_users'] as $u): ?>
										<tr>
											<td><?php echo $u['id']; ?></td>
											<td>
												<img src="../assets/img/user/<?php echo htmlspecialchars($u['photo']); ?>" alt="" class="img-rounded" height="30" />
											</td>
											<td><strong><?php echo htmlspecialchars($u['nom'] . ' ' . $u['prenom']); ?></strong></td>
											<td><?php echo htmlspecialchars($u['email']); ?></td>
											<td><?php echo htmlspecialchars($u['telephone']); ?></td>
											<td>
												<span class="label <?php 
													echo $u['role'] === 'Administrateur' ? 'label-danger' : 
														($u['role'] === 'Etudiant' ? 'label-success' : 
														($u['role'] === 'Professeur' ? 'label-warning' : 
														($u['role'] === 'Parent' ? 'label-primary' : 'label-inverse'))); 
												?>">
													<?php echo htmlspecialchars($u['role']); ?>
												</span>
											</td>
											<td>
												<small>
													<?php if ($u['role'] === 'Etudiant'): ?>
														<strong>Code:</strong> <?php echo htmlspecialchars($u['extra']['code'] ?? ''); ?><br>
														<strong>Niveau:</strong> <?php echo htmlspecialchars($u['extra']['niveau'] ?? ''); ?><br>
														<strong>Parent ID:</strong> <?php echo htmlspecialchars($u['extra']['parent_id'] ?? 'Aucun'); ?>
													<?php elseif ($u['role'] === 'Professeur'): ?>
														<strong>Spécialité:</strong> <?php echo htmlspecialchars($u['extra']['specialite'] ?? ''); ?>
													<?php else: ?>
														-
													<?php endif; ?>
												</small>
											</td>
											<td><?php echo htmlspecialchars($u['created_at']); ?></td>
											<td>
												<span class="badge badge-<?php echo $u['status'] === 'Actif' ? 'success' : 'warning'; ?>">
													<?php echo htmlspecialchars($u['status']); ?>
												</span>
											</td>
											<td>
												<button onclick='openEditModal(<?php echo json_encode($u); ?>)' class="btn btn-xs btn-info"><i class="fa fa-edit"></i> Modifier</button>
												<button onclick="openDeleteModal(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['prenom'] . ' ' . $u['nom']); ?>')" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Supprimer</button>
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
			<!-- end row -->

			<!-- begin row (Permissions & Logs) -->
			<div class="row">
				<!-- Panel de Configuration des Permissions par Rôle -->
				<div class="col-lg-6">
					<div class="panel panel-inverse">
						<div class="panel-heading">
							<h4 class="panel-title">Permissions & Fonctions par Rôle</h4>
						</div>
						<div class="panel-body">
							<p class="text-muted">Configurez les méthodes et accès autorisés par rôle conformément au diagramme de classes.</p>
							
							<div class="accordion" id="accordionPermissions">
								<?php foreach ($_SESSION['iam_permissions'] as $role => $perms): ?>
								<div class="card bg-light m-b-10">
									<div class="card-header font-weight-bold" style="cursor: pointer;" data-toggle="collapse" data-target="#collapse<?php echo $role; ?>">
										<i class="fa fa-shield-alt text-muted m-r-5"></i> Rôle : <?php echo $role; ?>
										<span class="badge badge-secondary pull-right"><?php echo count($perms); ?> méthode(s)</span>
									</div>
									<div id="collapse<?php echo $role; ?>" class="collapse" data-parent="#accordionPermissions">
										<div class="card-body">
											<form action="gestion_iam.php" method="POST">
												<input type="hidden" name="action" value="update_permissions" />
												<input type="hidden" name="role_permission" value="<?php echo htmlspecialchars($role); ?>" />
												
												<div class="form-group">
													<label>Méthodes autorisées :</label>
													
													<?php if ($role === 'Administrateur'): ?>
														<div class="checkbox checkbox-css">
															<input type="checkbox" id="p_admin_1" name="perms[]" value="gererUtilisateurs" <?php echo in_array('gererUtilisateurs', $perms) ? 'checked' : ''; ?> />
															<label for="p_admin_1"><code>gererUtilisateurs()</code> - Gérer les comptes utilisateurs</label>
														</div>
														<div class="checkbox checkbox-css">
															<input type="checkbox" id="p_admin_2" name="perms[]" value="configurerPermissions" <?php echo in_array('configurerPermissions', $perms) ? 'checked' : ''; ?> />
															<label for="p_admin_2"><code>configurerPermissions()</code> - Assigner les droits</label>
														</div>
														<div class="checkbox checkbox-css">
															<input type="checkbox" id="p_admin_3" name="perms[]" value="consulterLogs" <?php echo in_array('consulterLogs', $perms) ? 'checked' : ''; ?> />
															<label for="p_admin_3"><code>consulterLogs()</code> - Lire les journaux de sécurité</label>
														</div>
													<?php elseif ($role === 'Etudiant'): ?>
														<div class="checkbox checkbox-css">
															<input type="checkbox" id="p_etud_1" name="perms[]" value="consulterNotes" <?php echo in_array('consulterNotes', $perms) ? 'checked' : ''; ?> />
															<label for="p_etud_1"><code>consulterNotes()</code> - Consulter son relevé</label>
														</div>
														<div class="checkbox checkbox-css">
															<input type="checkbox" id="p_etud_2" name="perms[]" value="voirEmploiDuTemps" <?php echo in_array('voirEmploiDuTemps', $perms) ? 'checked' : ''; ?> />
															<label for="p_etud_2"><code>voirEmploiDuTemps()</code> - Voir l'agenda des cours</label>
														</div>
														<div class="checkbox checkbox-css">
															<input type="checkbox" id="p_etud_3" name="perms[]" value="accederVote" <?php echo in_array('accederVote', $perms) ? 'checked' : ''; ?> />
															<label for="p_etud_3"><code>accederVote()</code> - Voter aux élections d'établissement</label>
														</div>
													<?php elseif ($role === 'Parent'): ?>
														<div class="checkbox checkbox-css">
															<input type="checkbox" id="p_parent_1" name="perms[]" value="suivreNotes" <?php echo in_array('suivreNotes', $perms) ? 'checked' : ''; ?> />
															<label for="p_parent_1"><code>suivreNotes()</code> - Suivre le bulletin de l'élève rattaché</label>
														</div>
														<div class="checkbox checkbox-css">
															<input type="checkbox" id="p_parent_2" name="perms[]" value="suivreAbsences" <?php echo in_array('suivreAbsences', $perms) ? 'checked' : ''; ?> />
															<label for="p_parent_2"><code>suivreAbsences()</code> - Suivre les absences de l'élève</label>
														</div>
													<?php elseif ($role === 'Professeur'): ?>
														<div class="checkbox checkbox-css">
															<input type="checkbox" id="p_prof_1" name="perms[]" value="saisirNotes" <?php echo in_array('saisirNotes', $perms) ? 'checked' : ''; ?> />
															<label for="p_prof_1"><code>saisirNotes()</code> - Saisir les notes d'examens</label>
														</div>
														<div class="checkbox checkbox-css">
															<input type="checkbox" id="p_prof_2" name="perms[]" value="gererPresences" <?php echo in_array('gererPresences', $perms) ? 'checked' : ''; ?> />
															<label for="p_prof_2"><code>gererPresences()</code> - Faire l'appel de cours</label>
														</div>
														<div class="checkbox checkbox-css">
															<input type="checkbox" id="p_prof_3" name="perms[]" value="partagerDocuments" <?php echo in_array('partagerDocuments', $perms) ? 'checked' : ''; ?> />
															<label for="p_prof_3"><code>partagerDocuments()</code> - Publier des ressources pédagogiques</label>
														</div>
													<?php elseif ($role === 'Comptable'): ?>
														<div class="checkbox checkbox-css">
															<input type="checkbox" id="p_comp_1" name="perms[]" value="validerPaiement" <?php echo in_array('validerPaiement', $perms) ? 'checked' : ''; ?> />
															<label for="p_comp_1"><code>validerPaiement()</code> - Valider les transactions frais scolaires</label>
														</div>
														<div class="checkbox checkbox-css">
															<input type="checkbox" id="p_comp_2" name="perms[]" value="genererRecu" <?php echo in_array('genererRecu', $perms) ? 'checked' : ''; ?> />
															<label for="p_comp_2"><code>genererRecu()</code> - Émettre des reçus financiers</label>
														</div>
													<?php endif; ?>
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

				<!-- Panel des Logs d'Audit Sécurité -->
				<div class="col-lg-6">
					<div class="panel panel-inverse">
						<div class="panel-heading">
							<h4 class="panel-title">Audit Trail / Journal d'activité</h4>
						</div>
						<div class="panel-body">
							<div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
								<table class="table table-condensed table-striped">
									<thead>
										<tr>
											<th>Date/Heure</th>
											<th>Utilisateur</th>
											<th>Action</th>
											<th>Adresse IP</th>
											<th>Résultat</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach (array_reverse($_SESSION['iam_logs']) as $log): ?>
										<tr>
											<td><small><?php echo htmlspecialchars($log['timestamp']); ?></small></td>
											<td><small><?php echo htmlspecialchars($log['user']); ?></small></td>
											<td><small><?php echo htmlspecialchars($log['action']); ?></small></td>
											<td><small><code><?php echo htmlspecialchars($log['ip']); ?></code></small></td>
											<td><span class="badge badge-success"><?php echo htmlspecialchars($log['status']); ?></span></td>
										</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- end row -->
		</div>
		<!-- end #content -->

		<!-- Modal d'Ajout d'Utilisateur -->
		<div class="modal fade" id="modalAddUser" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Créer un nouvel Utilisateur</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<form action="gestion_iam.php" method="POST">
						<input type="hidden" name="action" value="add_user" />
						<div class="modal-body">
							<div class="row">
								<div class="col-md-6 form-group">
									<label>Prénom <span class="text-danger">*</span></label>
									<input type="text" name="prenom" class="form-control" required />
								</div>
								<div class="col-md-6 form-group">
									<label>Nom <span class="text-danger">*</span></label>
									<input type="text" name="nom" class="form-control" required />
								</div>
							</div>
							<div class="form-group">
								<label>Adresse E-mail <span class="text-danger">*</span></label>
								<input type="email" name="email" class="form-control" required />
							</div>
							<div class="form-group">
								<label>Téléphone <span class="text-danger">*</span></label>
								<input type="text" name="telephone" class="form-control" required />
							</div>
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

							<!-- Champs spécifiques pour Étudiant -->
							<div id="add_fields_etudiant" style="display:none; border-left: 3px solid #00acac; padding-left: 15px; margin-bottom:15px;">
								<div class="form-group">
									<label>Code Étudiant (code) <span class="text-danger">*</span></label>
									<input type="text" name="etudiant_code" class="form-control" placeholder="Ex: 8876" />
								</div>
								<div class="form-group">
									<label>Niveau <span class="text-danger">*</span></label>
									<input type="text" name="etudiant_niveau" class="form-control" placeholder="Ex: Licence 3, Master 2" />
								</div>
								<div class="form-group">
									<label>Parent Rattaché (Optionnel)</label>
									<select name="etudiant_parent" class="form-control">
										<option value="">-- Aucun --</option>
										<?php foreach($_SESSION['iam_users'] as $u): if($u['role'] === 'Parent'): ?>
											<option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['prenom'].' '.$u['nom']); ?></option>
										<?php endif; endforeach; ?>
									</select>
								</div>
							</div>

							<!-- Champs spécifiques pour Professeur -->
							<div id="add_fields_professeur" style="display:none; border-left: 3px solid #f5b041; padding-left: 15px; margin-bottom:15px;">
								<div class="form-group">
									<label>Spécialité <span class="text-danger">*</span></label>
									<input type="text" name="professeur_specialite" class="form-control" placeholder="Ex: Algorithmique, Réseaux" />
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
							<button type="submit" class="btn btn-primary">Créer</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- Modal de Modification d'Utilisateur -->
		<div class="modal fade" id="modalEditUser" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Modifier l'Utilisateur</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<form action="gestion_iam.php" method="POST">
						<input type="hidden" name="action" value="edit_user" />
						<input type="hidden" id="edit_user_id" name="user_id" />
						<div class="modal-body">
							<div class="row">
								<div class="col-md-6 form-group">
									<label>Prénom</label>
									<input type="text" id="edit_prenom" name="prenom" class="form-control" required />
								</div>
								<div class="col-md-6 form-group">
									<label>Nom</label>
									<input type="text" id="edit_nom" name="nom" class="form-control" required />
								</div>
							</div>
							<div class="form-group">
								<label>Adresse E-mail</label>
								<input type="email" id="edit_email" name="email" class="form-control" required />
							</div>
							<div class="form-group">
								<label>Téléphone</label>
								<input type="text" id="edit_telephone" name="telephone" class="form-control" required />
							</div>
							<div class="form-group">
								<label>Statut</label>
								<select id="edit_status" name="status" class="form-control">
									<option value="Actif">Actif</option>
									<option value="Suspendu">Suspendu</option>
								</select>
							</div>

							<!-- Rôle affiché en lecture seule car lié à l'héritage de classe UML -->
							<div class="form-group">
								<label>Rôle UML</label>
								<input type="text" id="edit_role_display" class="form-control" disabled />
							</div>

							<!-- Champs spécifiques pour Étudiant (modifiés si rôle = Etudiant) -->
							<div id="edit_fields_etudiant" style="display:none; border-left: 3px solid #00acac; padding-left: 15px; margin-bottom:15px;">
								<div class="form-group">
									<label>Code Étudiant (code)</label>
									<input type="text" id="edit_etudiant_code" name="etudiant_code" class="form-control" />
								</div>
								<div class="form-group">
									<label>Niveau</label>
									<input type="text" id="edit_etudiant_niveau" name="etudiant_niveau" class="form-control" />
								</div>
								<div class="form-group">
									<label>Parent Rattaché</label>
									<select id="edit_etudiant_parent" name="etudiant_parent" class="form-control">
										<option value="">-- Aucun --</option>
										<?php foreach($_SESSION['iam_users'] as $u): if($u['role'] === 'Parent'): ?>
											<option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['prenom'].' '.$u['nom']); ?></option>
										<?php endif; endforeach; ?>
									</select>
								</div>
							</div>

							<!-- Champs spécifiques pour Professeur -->
							<div id="edit_fields_professeur" style="display:none; border-left: 3px solid #f5b041; padding-left: 15px; margin-bottom:15px;">
								<div class="form-group">
									<label>Spécialité</label>
									<input type="text" id="edit_professeur_specialite" name="professeur_specialite" class="form-control" />
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
							<button type="submit" class="btn btn-info">Sauvegarder les modifications</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- Modal de Confirmation de Suppression -->
		<div class="modal fade" id="modalDeleteUser" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog text-center" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title text-danger">Supprimer l'Utilisateur ?</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<form action="gestion_iam.php" method="POST">
						<input type="hidden" name="action" value="delete_user" />
						<input type="hidden" id="delete_user_id" name="user_id" />
						<div class="modal-body">
							<p>Êtes-vous sûr de vouloir supprimer définitivement l'utilisateur <strong id="delete_user_name"></strong> ? Cette action est irréversible.</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
							<button type="submit" class="btn btn-danger">Supprimer définitivement</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- begin scroll to top btn -->
		<a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
		<!-- end scroll to top btn -->
	</div>
	<!-- end page container -->
	
	<!-- ================== BEGIN BASE JS ================== -->
	<script src="../assets/js/app.min.js"></script>
	<script src="../assets/js/theme/default.min.js"></script>
	<script>
		// Désactiver le drag & drop des panels
		$(document).ready(function() {
			if (typeof $.fn.sortable !== 'undefined') {
				$('.sortable').sortable('destroy');
			}
		});
	</script>
	<!-- ================== END BASE JS ================== -->

	<!-- Script Custom pour gérer l'affichage dynamique et les Modaux -->
	<script>
		// Affiche/Cache les champs spécifiques du formulaire d'ajout
		function toggleAddFields(role) {
			document.getElementById('add_fields_etudiant').style.display = (role === 'Etudiant') ? 'block' : 'none';
			document.getElementById('add_fields_professeur').style.display = (role === 'Professeur') ? 'block' : 'none';
		}

		// Affiche le modal d'ajout
		function openAddModal() {
			$('#modalAddUser').modal('show');
			toggleAddFields(document.getElementById('add_user_role').value);
		}

		// Pré-remplit et affiche le modal d'édition
		function openEditModal(user) {
			document.getElementById('edit_user_id').value = user.id;
			document.getElementById('edit_prenom').value = user.prenom;
			document.getElementById('edit_nom').value = user.nom;
			document.getElementById('edit_email').value = user.email;
			document.getElementById('edit_telephone').value = user.telephone;
			document.getElementById('edit_status').value = user.status;
			document.getElementById('edit_role_display').value = user.role;

			// Gérer les champs spécifiques Étudiant
			if (user.role === 'Etudiant') {
				document.getElementById('edit_fields_etudiant').style.display = 'block';
				document.getElementById('edit_etudiant_code').value = user.extra.code || '';
				document.getElementById('edit_etudiant_niveau').value = user.extra.niveau || '';
				document.getElementById('edit_etudiant_parent').value = user.extra.parent_id || '';
			} else {
				document.getElementById('edit_fields_etudiant').style.display = 'none';
			}

			// Gérer les champs spécifiques Professeur
			if (user.role === 'Professeur') {
				document.getElementById('edit_fields_professeur').style.display = 'block';
				document.getElementById('edit_professeur_specialite').value = user.extra.specialite || '';
			} else {
				document.getElementById('edit_fields_professeur').style.display = 'none';
			}

			$('#modalEditUser').modal('show');
		}

		// Ouvre le modal de suppression
		function openDeleteModal(userId, userName) {
			document.getElementById('delete_user_id').value = userId;
			document.getElementById('delete_user_name').innerText = userName;
			$('#modalDeleteUser').modal('show');
		}
	</script>
</body>
</html>
