<?php
$allowedRoles = ['admin', 'administrateur'];
require_once __DIR__ . '/CONTROLLER/auth_guard.php';
require_once __DIR__ . '/CONTROLLER/classeController.php';

$ctrl    = new ClasseController();
$error   = '';
$success = '';

// Traitement POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $res = $ctrl->create($_POST['id'] ?? '', $_POST['libelle'] ?? '', $_POST['section'] ?? '');
        if ($res['success']) { $success = "Classe créée avec succès."; }
        else { $error = $res['msg']; }

    } elseif ($action === 'update') {
        $res = $ctrl->update($_POST['id'] ?? '', $_POST['libelle'] ?? '', $_POST['section'] ?? '');
        if ($res['success']) { $success = "Classe modifiée avec succès."; }
        else { $error = $res['msg']; }

    } elseif ($action === 'delete') {
        $res = $ctrl->delete($_POST['id'] ?? '');
        if ($res['success']) { $success = "Classe supprimée avec succès."; }
        else { $error = $res['msg']; }
    }
}

$classes   = $ctrl->getAll();
$editClasse = null;
if (!empty($_GET['edit'])) {
    $editClasse = $ctrl->getById($_GET['edit']);
}

$pageTitle  = 'Gestion des Classes';
$pageSubtitle = 'Ajouter, modifier et supprimer des classes';
$activePage = 'classes';
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
                <h4 class="panel-title"><?= $editClasse ? 'Modifier la Classe' : 'Ajouter une Classe' ?></h4>
            </div>
            <div class="panel-body">
                <form method="post" action="classes.php">
                    <input type="hidden" name="action" value="<?= $editClasse ? 'update' : 'create' ?>" />
                    <div class="form-group">
                        <label>Identifiant <span class="text-danger">*</span></label>
                        <input type="text" name="id" class="form-control"
                            value="<?= htmlspecialchars($editClasse['id'] ?? '') ?>"
                            <?= $editClasse ? 'readonly' : '' ?> required />
                        <small class="text-muted">Ex: L1-INFO, M2-MKTG</small>
                    </div>
                    <div class="form-group">
                        <label>Libellé <span class="text-danger">*</span></label>
                        <input type="text" name="libelle" class="form-control"
                            value="<?= htmlspecialchars($editClasse['libelle'] ?? '') ?>" required />
                        <small class="text-muted">Ex: Licence 1 Informatique</small>
                    </div>
                    <div class="form-group">
                        <label>Section</label>
                        <input type="text" name="section" class="form-control"
                            value="<?= htmlspecialchars($editClasse['section'] ?? '') ?>" />
                        <small class="text-muted">Ex: A, B, Soir</small>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary btn-block">
                                <?= $editClasse ? 'Modifier' : 'Ajouter' ?>
                            </button>
                        </div>
                        <?php if ($editClasse): ?>
                        <div class="col-6">
                            <a href="classes.php" class="btn btn-default btn-block">Annuler</a>
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
                <h4 class="panel-title">Liste des Classes (<?= count($classes) ?>)</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Identifiant</th>
                                <th>Libellé</th>
                                <th>Section</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($classes)): ?>
                            <tr><td colspan="4" class="text-center text-muted">Aucune classe enregistrée.</td></tr>
                            <?php else: ?>
                            <?php foreach ($classes as $c): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($c['id']) ?></strong></td>
                                <td><?= htmlspecialchars($c['libelle']) ?></td>
                                <td><?= htmlspecialchars($c['section'] ?? '-') ?></td>
                                <td>
                                    <a href="classes.php?edit=<?= urlencode($c['id']) ?>" class="btn btn-xs btn-info">
                                        <i class="fa fa-edit"></i> Modifier
                                    </a>
                                    <form method="post" action="classes.php" style="display:inline;"
                                        onsubmit="return confirm('Supprimer cette classe ?')">
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
