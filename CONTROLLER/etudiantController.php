<?php
require_once __DIR__ . '/../MODEL/etudiantModel.php';
require_once __DIR__ . '/../MODEL/classeModel.php';

class EtudiantController {
    protected $model;
    protected $classeModel;

    public function __construct() {
        $this->model       = new EtudiantModel();
        $this->classeModel = new ClasseModel();
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    protected function validate($nom, $prenom, $email, $telephone = '') {
        $errors = [];
        if (empty(trim($nom)))    $errors[] = "Le nom est requis.";
        elseif (mb_strlen($nom) < 2) $errors[] = "Le nom doit contenir au moins 2 caractères.";
        elseif (mb_strlen($nom) > 255) $errors[] = "Le nom est trop long.";
        elseif (!preg_match("/^[\p{L}\s'\-]+$/u", $nom)) $errors[] = "Le nom contient des caractères invalides.";

        if (empty(trim($prenom))) $errors[] = "Le prénom est requis.";
        elseif (mb_strlen($prenom) < 2) $errors[] = "Le prénom doit contenir au moins 2 caractères.";
        elseif (!preg_match("/^[\p{L}\s'\-]+$/u", $prenom)) $errors[] = "Le prénom contient des caractères invalides.";

        if (empty(trim($email)))  $errors[] = "L'email est requis.";
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "L'email n'est pas valide.";
        elseif (strlen($email) > 255) $errors[] = "L'email est trop long.";

        if (!empty($telephone) && !preg_match('/^[0-9\s\+\-\(\)]{6,20}$/', $telephone)) {
            $errors[] = "Le numéro de téléphone n'est pas valide.";
        }
        return $errors;
    }

    public function getAll()      { return $this->model->getAll(); }
    public function getById($id)  { return $this->model->getById($id); }
    public function getClasses()  { return $this->classeModel->getAll(); }

    public function create($nom, $prenom, $email, $telephone, $classe_id, $niveau) {
        $errors = $this->validate($nom, $prenom, $email, $telephone);
        if (!empty($errors)) return ['success' => false, 'msg' => implode(' ', $errors)];
        if ($this->model->emailExiste($email)) return ['success' => false, 'msg' => "Cet email est déjà utilisé."];
        $ok = $this->model->create(trim($nom), trim($prenom), trim($email), trim($telephone), $classe_id, trim($niveau));
        return $ok ? ['success' => true] : ['success' => false, 'msg' => "Erreur lors de la création."];
    }

    public function update($id, $nom, $prenom, $email, $telephone, $classe_id, $etat) {
        $errors = $this->validate($nom, $prenom, $email, $telephone);
        if (!empty($errors)) return ['success' => false, 'msg' => implode(' ', $errors)];
        if ($this->model->emailExiste($email, $id)) return ['success' => false, 'msg' => "Cet email est déjà utilisé."];
        $etats = ['Actif', 'Inactif', 'Suspendu'];
        if (!in_array($etat, $etats)) return ['success' => false, 'msg' => "État invalide."];
        $ok = $this->model->update($id, trim($nom), trim($prenom), trim($email), trim($telephone), $classe_id, $etat);
        return $ok ? ['success' => true] : ['success' => false, 'msg' => "Erreur lors de la modification."];
    }

    public function delete($id) {
        if (empty($id) || !is_numeric($id)) return ['success' => false, 'msg' => "Identifiant invalide."];
        $ok = $this->model->delete($id);
        return $ok ? ['success' => true] : ['success' => false, 'msg' => "Erreur lors de la suppression."];
    }
}
