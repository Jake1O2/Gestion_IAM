<?php
require_once __DIR__ . '/../MODEL/classeModel.php';

class ClasseController {
    protected $model;

    public function __construct() {
        $this->model = new ClasseModel();
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    protected function validate($id, $libelle, $section) {
        $errors = [];
        if (empty(trim($id)))      $errors[] = "L'identifiant est requis.";
        elseif (strlen($id) > 50)  $errors[] = "L'identifiant est trop long (50 max).";
        if (empty(trim($libelle))) $errors[] = "Le libellé est requis.";
        elseif (strlen($libelle) > 255) $errors[] = "Le libellé est trop long (255 max).";
        if (strlen($section) > 255) $errors[] = "La section est trop longue (255 max).";
        return $errors;
    }

    public function getAll()      { return $this->model->getAll(); }
    public function getById($id)  { return $this->model->getById($id); }

    public function create($id, $libelle, $section) {
        $errors = $this->validate($id, $libelle, $section);
        if (!empty($errors)) return ['success' => false, 'msg' => implode(' ', $errors)];
        if ($this->model->exists($id)) return ['success' => false, 'msg' => "Cet identifiant de classe existe déjà."];
        $ok = $this->model->create(trim($id), trim($libelle), trim($section));
        return $ok ? ['success' => true] : ['success' => false, 'msg' => "Erreur lors de la création."];
    }

    public function update($id, $libelle, $section) {
        $errors = $this->validate($id, $libelle, $section);
        if (!empty($errors)) return ['success' => false, 'msg' => implode(' ', $errors)];
        $ok = $this->model->update(trim($id), trim($libelle), trim($section));
        return $ok ? ['success' => true] : ['success' => false, 'msg' => "Erreur lors de la modification."];
    }

    public function delete($id) {
        if (empty($id)) return ['success' => false, 'msg' => "Identifiant manquant."];
        $ok = $this->model->delete($id);
        return $ok ? ['success' => true] : ['success' => false, 'msg' => "Erreur lors de la suppression."];
    }
}
