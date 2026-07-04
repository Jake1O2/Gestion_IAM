<?php
require_once __DIR__ . '/userDB.php';

class EtudiantModel {
    protected $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT u.*, c.libelle AS classe_libelle
            FROM utilisateur u
            LEFT JOIN classe c ON u.role_id = c.id
            WHERE u.role = 'Etudiant'
            ORDER BY u.nom, u.prenom
        ");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE id = :id AND role = 'Etudiant'");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($nom, $prenom, $email, $telephone, $classe_id, $niveau) {
        $stmt = $this->pdo->prepare("
            INSERT INTO utilisateur (nom, prenom, email, telephone, role, etat, role_id, created_at)
            VALUES (:nom, :prenom, :email, :telephone, 'Etudiant', 'Actif', :classe_id, :created_at)
        ");
        return $stmt->execute([
            'nom'       => $nom,
            'prenom'    => $prenom,
            'email'     => $email,
            'telephone' => $telephone,
            'classe_id' => $classe_id ?: null,
            'created_at'=> date('Y-m-d')
        ]);
    }

    public function update($id, $nom, $prenom, $email, $telephone, $classe_id, $etat) {
        $stmt = $this->pdo->prepare("
            UPDATE utilisateur
            SET nom = :nom, prenom = :prenom, email = :email,
                telephone = :telephone, role_id = :classe_id, etat = :etat
            WHERE id = :id AND role = 'Etudiant'
        ");
        return $stmt->execute([
            'id'        => $id,
            'nom'       => $nom,
            'prenom'    => $prenom,
            'email'     => $email,
            'telephone' => $telephone,
            'classe_id' => $classe_id ?: null,
            'etat'      => $etat
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM utilisateur WHERE id = :id AND role = 'Etudiant'");
        return $stmt->execute(['id' => $id]);
    }

    public function emailExiste($email, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM utilisateur WHERE email = :email AND id != :id');
            $stmt->execute(['email' => $email, 'id' => $excludeId]);
        } else {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM utilisateur WHERE email = :email');
            $stmt->execute(['email' => $email]);
        }
        return $stmt->fetchColumn() > 0;
    }
}
