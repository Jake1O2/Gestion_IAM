<?php
require_once __DIR__ . '/../MODEL/coursModel.php';
require_once __DIR__ . '/../MODEL/classeModel.php';

class CoursController {
    protected $model;
    protected $classeModel;

    public function __construct() {
        $this->model       = new CoursModel();
        $this->classeModel = new ClasseModel();
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    protected function validate($id, $matiere, $horaire, $salle) {
        $errors = [];
        if (empty(trim($id)))       $errors[] = "L'identifiant du cours est requis.";
        elseif (strlen($id) > 50)   $errors[] = "L'identifiant est trop long (50 max).";
        if (empty(trim($matiere)))  $errors[] = "La matière est requise.";
        elseif (strlen($matiere) > 255) $errors[] = "La matière est trop longue.";
        if (empty(trim($horaire)))  $errors[] = "L'horaire est requis.";
        else {
            $d = DateTime::createFromFormat('Y-m-d\TH:i', $horaire);
            if (!$d) $errors[] = "Le format de l'horaire est invalide.";
        }
        if (strlen($salle) > 255)   $errors[] = "La salle est trop longue.";
        return $errors;
    }

    public function getAll()      { return $this->model->getAll(); }
    public function getById($id)  { return $this->model->getById($id); }
    public function getClasses()  { return $this->classeModel->getAll(); }

    public function create($id, $matiere, $horaire, $salle, $classe_id) {
        $errors = $this->validate($id, $matiere, $horaire, $salle);
        if (!empty($errors)) return ['success' => false, 'msg' => implode(' ', $errors)];
        if ($this->model->exists($id)) return ['success' => false, 'msg' => "Cet identifiant de cours existe déjà."];
        // Convertir datetime-local en format MySQL
        $horaire = str_replace('T', ' ', $horaire) . ':00';
        $ok = $this->model->create(trim($id), trim($matiere), $horaire, trim($salle), $classe_id ?: null);
        return $ok ? ['success' => true] : ['success' => false, 'msg' => "Erreur lors de la création."];
    }

    public function update($id, $matiere, $horaire, $salle, $classe_id) {
        $errors = $this->validate($id, $matiere, $horaire, $salle);
        if (!empty($errors)) return ['success' => false, 'msg' => implode(' ', $errors)];
        $horaire = str_replace('T', ' ', $horaire) . ':00';
        $ok = $this->model->update(trim($id), trim($matiere), $horaire, trim($salle), $classe_id ?: null);
        return $ok ? ['success' => true] : ['success' => false, 'msg' => "Erreur lors de la modification."];
    }

    public function delete($id) {
        if (empty($id)) return ['success' => false, 'msg' => "Identifiant manquant."];
        $ok = $this->model->delete($id);
        return $ok ? ['success' => true] : ['success' => false, 'msg' => "Erreur lors de la suppression."];
    }
}
