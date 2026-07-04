<?php
$allowedRoles = ['admin', 'administrateur'];
require_once __DIR__ . '/CONTROLLER/auth_guard.php';
require_once __DIR__ . '/CONTROLLER/etudiantController.php';

$ctrl    = new EtudiantController();
$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $res = $ctrl->create(
            $_POST['nom'] ?? '', $_POST['prenom'] ?? '',
            $_POST['email'] ?? '', $_POST['telephone'] ?? '',
            $_POST['classe_id'] ?? '', $_POST['niveau'] ?? ''
        );
        if ($res['success']) $success = "Étudiant ajouté avec succès.";
        else $error = $res['msg'];

    } elseif ($action === 'update') {
        $res = $ctrl->update(
            $_POST['id'] ?? '',
            $_POST['nom'] ?? '', $_POST['prenom'] ?? '',
            $_POST['email'] ?? '', $_POST['telephone'] ?? '',
            $_POST['classe_id'] ?? '', $_POST['etat'] ?? 'Actif'
        );
        if ($res['success']) $success = "Étudiant modifié avec succès.";
        else $error = $res['msg'];

    } elseif ($action === 'delete') {
        $res = $ctrl->delete($_POST['id'] ?? '');
        if ($res['success']) $success = "Étudiant supprimé avec succès.";
        else $error = $res['msg'];
    }
}

$etudiants  = $ctrl->getAll();
$classes    = $ctrl->getClasses();
$editEtud   = null;
if (!empty($_GET['edit'])) {
    $editEtud = $ctrl->getById((int)$_GET['edit']);
}

$pageTitle   = 'Gestion des Étudiants';
$pageSubtitle = 'Ajouter, modifier et supprimer des étudiants';
$activePage  = 'etudiants';
require_once 'VIEW/layouts/admin_header.php';
?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible"><button class="close" data-dismiss="alert">&times;</button><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible"><button class="close" data-dismiss="alert">&times;</button><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="row">
    <!-- Formulaire -->
    <div class="col-lg-4">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title"><?= $editEtud ? 'Modifier l\'Étudiant' : 'Ajouter un Étudiant' ?></h4>
            </div>
            <div class="panel-body">
                <form method="post" action="etudiants.php">
                    <input type="hidden" name="action" value="<?= $editEtud ? 'update' : 'create' ?>" />
                    <?php if ($editEtud): ?>
                        <input type="hidden" name="id" value="<?= $editEtud['id'] ?>" />
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control"
                            value="<?= htmlspecialchars($editEtud['nom'] ?? '') ?>" required />
                    </div>
                    <div class="form-group">
                        <label>Prénom <span class="text-danger">*</span></label>
                        <input type="text" name="prenom" class="form-control"
                            value="<?= htmlspecialchars($editEtud['prenom'] ?? '') ?>" required />
                    </div>
                    <div class="form-group">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control"
                            value="<?= htmlspecialchars($editEtud['email'] ?? '') ?>" required />
                    </div>
                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="text" name="telephone" class="form-control"
                            value="<?= htmlspecialchars($editEtud['telephone'] ?? '') ?>" />
                    </div>
                    <div class="form-group">
                        <label>Classe</label>
                        <select name="classe_id" class="form-control">
                            <option value="">-- Aucune --</option>
                            <?php foreach ($classes as $cl): ?>
                            <option value="<?= htmlspecialchars($cl['id']) ?>"
                                <?= ($editEtud['role_id'] ?? '') == $cl['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cl['libelle']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Niveau</label>
                        <input type="text" name="niveau" class="form-control"
                            placeholder="Ex: Licence 2"
                            value="<?= htmlspecialchars($editEtud['niveau'] ?? '') ?>" />
                    </div>
                    <?php if ($editEtud): ?>
                    <div class="form-group">
                        <label>État</label>
                        <select name="etat" class="form-control">
                            <option value="Actif"    <?= ($editEtud['etat'] ?? '') === 'Actif'    ? 'selected' : '' ?>>Actif</option>
                            <option value="Inactif"  <?= ($editEtud['etat'] ?? '') === 'Inactif'  ? 'selected' : '' ?>>Inactif</option>
                            <option value="Suspendu" <?= ($editEtud['etat'] ?? '') === 'Suspendu' ? 'selected' : '' ?>>Suspendu</option>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary btn-block">
                                <?= $editEtud ? 'Modifier' : 'Ajouter' ?>
                            </button>
                        </div>
                        <?php if ($editEtud): ?>
                        <div class="col-6">
                            <a href="etudiants.php" class="btn btn-default btn-block">Annuler</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Liste -->
    <div class="col-lg-8">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title">Liste des Étudiants (<?= count($etudiants) ?>)</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nom & Prénom</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Classe</th>
                                <th>État</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($etudiants)): ?>
                            <tr><td colspan="6" class="text-center text-muted">Aucun étudiant enregistré.</td></tr>
                            <?php else: ?>
                            <?php foreach ($etudiants as $e): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($e['nom'].' '.$e['prenom']) ?></strong></td>
                                <td><?= htmlspecialchars($e['email'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($e['telephone'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($e['classe_libelle'] ?? '-') ?></td>
                                <td>
                                    <span class="label label-<?= $e['etat'] === 'Actif' ? 'success' : ($e['etat'] === 'Suspendu' ? 'danger' : 'warning') ?>">
                                        <?= htmlspecialchars($e['etat'] ?? 'Actif') ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="etudiants.php?edit=<?= $e['id'] ?>" class="btn btn-xs btn-info">
                                        <i class="fa fa-edit"></i> Modifier
                                    </a>
                                    <form method="post" action="etudiants.php" style="display:inline;"
                                        onsubmit="return confirm('Supprimer cet étudiant ?')">
                                        <input type="hidden" name="action" value="delete" />
                                        <input type="hidden" name="id" value="<?= $e['id'] ?>" />
                                        <button type="submit" class="btn btn-xs btn-danger">
                                            <i class="fa fa-trash"></i> Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'VIEW/layouts/admin_footer.php'; ?>
