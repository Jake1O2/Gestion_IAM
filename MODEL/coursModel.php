<?php
require_once __DIR__ . '/userDB.php';

class CoursModel {
    protected $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT c.*, cl.libelle AS classe_libelle
            FROM com c
            LEFT JOIN classe cl ON c.classe_id = cl.id
            ORDER BY c.horaire DESC
        ");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM com WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($id, $matiere, $horaire, $salle, $classe_id) {
        $stmt = $this->pdo->prepare("
            INSERT INTO com (id, matiere, horaire, salle, classe_id)
            VALUES (:id, :matiere, :horaire, :salle, :classe_id)
        ");
        return $stmt->execute([
            'id'        => $id,
            'matiere'   => $matiere,
            'horaire'   => $horaire,
            'salle'     => $salle,
            'classe_id' => $classe_id ?: null
        ]);
    }

    public function update($id, $matiere, $horaire, $salle, $classe_id) {
        $stmt = $this->pdo->prepare("
            UPDATE com SET matiere = :matiere, horaire = :horaire,
                salle = :salle, classe_id = :classe_id
            WHERE id = :id
        ");
        return $stmt->execute([
            'id'        => $id,
            'matiere'   => $matiere,
            'horaire'   => $horaire,
            'salle'     => $salle,
            'classe_id' => $classe_id ?: null
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM com WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function exists($id) {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM com WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetchColumn() > 0;
    }
}
