<?php
$allowedRoles = ['admin', 'administrateur'];
require_once __DIR__ . '/CONTROLLER/auth_guard.php';
require_once __DIR__ . '/CONTROLLER/coursController.php';

$ctrl    = new CoursController();
$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $res = $ctrl->create(
            $_POST['id'] ?? '', $_POST['matiere'] ?? '',
            $_POST['horaire'] ?? '', $_POST['salle'] ?? '',
            $_POST['classe_id'] ?? ''
        );
        if ($res['success']) $success = "Cours ajouté avec succès.";
        else $error = $res['msg'];

    } elseif ($action === 'update') {
        $res = $ctrl->update(
            $_POST['id'] ?? '', $_POST['matiere'] ?? '',
            $_POST['horaire'] ?? '', $_POST['salle'] ?? '',
            $_POST['classe_id'] ?? ''
        );
        if ($res['success']) $success = "Cours modifié avec succès.";
        else $error = $res['msg'];

    } elseif ($action === 'delete') {
        $res = $ctrl->delete($_POST['id'] ?? '');
        if ($res['success']) $success = "Cours supprimé avec succès.";
        else $error = $res['msg'];
    }
}

$coursList  = $ctrl->getAll();
$classes    = $ctrl->getClasses();
$editCours  = null;
if (!empty($_GET['edit'])) {
    $editCours = $ctrl->getById($_GET['edit']);
}

// Formater l'horaire pour l'input datetime-local
$horaireEdit = '';
if ($editCours && !empty($editCours['horaire'])) {
    $horaireEdit = date('Y-m-d\TH:i', strtotime($editCours['horaire']));
}

$pageTitle   = 'Gestion des Cours';
$pageSubtitle = 'Ajouter, modifier et supprimer des cours';
$activePage  = 'cours';
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
                <h4 class="panel-title"><?= $editCours ? 'Modifier le Cours' : 'Ajouter un Cours' ?></h4>
            </div>
            <div class="panel-body">
                <form method="post" action="cours.php">
                    <input type="hidden" name="action" value="<?= $editCours ? 'update' : 'create' ?>" />

                    <div class="form-group">
                        <label>Identifiant <span class="text-danger">*</span></label>
                        <input type="text" name="id" class="form-control"
                            value="<?= htmlspecialchars($editCours['id'] ?? '') ?>"
                            <?= $editCours ? 'readonly' : '' ?> required />
                        <small class="text-muted">Ex: COURS-001</small>
                    </div>
                    <div class="form-group">
                        <label>Matière <span class="text-danger">*</span></label>
                        <input type="text" name="matiere" class="form-control"
                            value="<?= htmlspecialchars($editCours['matiere'] ?? '') ?>" required />
                    </div>
                    <div class="form-group">
                        <label>Horaire <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="horaire" class="form-control"
                            value="<?= htmlspecialchars($horaireEdit) ?>" required />
                    </div>
                    <div class="form-group">
                        <label>Salle</label>
                        <input type="text" name="salle" class="form-control"
                            placeholder="Ex: A-101"
                            value="<?= htmlspecialchars($editCours['salle'] ?? '') ?>" />
                    </div>
                    <div class="form-group">
                        <label>Classe</label>
                        <select name="classe_id" class="form-control">
                            <option value="">-- Aucune --</option>
                            <?php foreach ($classes as $cl): ?>
                            <option value="<?= htmlspecialchars($cl['id']) ?>"
                                <?= ($editCours['classe_id'] ?? '') == $cl['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cl['libelle']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary btn-block">
                                <?= $editCours ? 'Modifier' : 'Ajouter' ?>
                            </button>
                        </div>
                        <?php if ($editCours): ?>
                        <div class="col-6">
                            <a href="cours.php" class="btn btn-default btn-block">Annuler</a>
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
                <h4 class="panel-title">Liste des Cours (<?= count($coursList) ?>)</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Matière</th>
                                <th>Horaire</th>
                                <th>Salle</th>
                                <th>Classe</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($coursList)): ?>
                            <tr><td colspan="6" class="text-center text-muted">Aucun cours enregistré.</td></tr>
                            <?php else: ?>
                            <?php foreach ($coursList as $c): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($c['id']) ?></strong></td>
                                <td><?= htmlspecialchars($c['matiere']) ?></td>
                                <td><?= htmlspecialchars($c['horaire'] ? date('d/m/Y H:i', strtotime($c['horaire'])) : '-') ?></td>
                                <td><?= htmlspecialchars($c['salle'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($c['classe_libelle'] ?? '-') ?></td>
                                <td>
                                    <a href="cours.php?edit=<?= urlencode($c['id']) ?>" class="btn btn-xs btn-info">
                                        <i class="fa fa-edit"></i> Modifier
                                    </a>
                                    <form method="post" action="cours.php" style="display:inline;"
                                        onsubmit="return confirm('Supprimer ce cours ?')">
                                        <input type="hidden" name="action" value="delete" />
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($c['id']) ?>" />
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
