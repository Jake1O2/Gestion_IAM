<?php
require_once __DIR__ . '/userDB.php';

class ClasseModel {
    protected $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query('SELECT * FROM classe ORDER BY libelle');
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM classe WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($id, $libelle, $section) {
        $stmt = $this->pdo->prepare('INSERT INTO classe (id, libelle, section) VALUES (:id, :libelle, :section)');
        return $stmt->execute(['id' => $id, 'libelle' => $libelle, 'section' => $section]);
    }

    public function update($id, $libelle, $section) {
        $stmt = $this->pdo->prepare('UPDATE classe SET libelle = :libelle, section = :section WHERE id = :id');
        return $stmt->execute(['id' => $id, 'libelle' => $libelle, 'section' => $section]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM classe WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function exists($id) {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM classe WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetchColumn() > 0;
    }
}
