<?php
require_once __DIR__ . '/../MODEL/userModel.php';

class UserController {
    protected $model;

    protected $rolesAutorises = ['student', 'teacher', 'parent', 'comptable', 'admin', 'administrateur'];

    public function __construct() {
        $this->model = new UserModel();
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    /* =========================================================
     *  VALIDATION
     * ========================================================= */

    /**
     * Valide les champs de connexion.
     * Retourne un tableau d'erreurs (vide si tout est valide).
     */
    protected function validateLogin($email, $password) {
        $errors = [];

        if (empty($email)) {
            $errors[] = "L'adresse email est requise.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse email n'est pas valide.";
        } elseif (strlen($email) > 200) {
            $errors[] = "L'adresse email est trop longue.";
        }

        if (empty($password)) {
            $errors[] = "Le mot de passe est requis.";
        }

        return $errors;
    }

    /**
     * Valide les champs d'inscription.
     */
    protected function validateRegister($name, $email, $password, $role) {
        $errors = [];

        // --- Nom ---
        $name = trim($name);
        if (empty($name)) {
            $errors[] = "Le nom complet est requis.";
        } elseif (mb_strlen($name) < 2) {
            $errors[] = "Le nom doit contenir au moins 2 caractères.";
        } elseif (mb_strlen($name) > 150) {
            $errors[] = "Le nom est trop long (150 caractères maximum).";
        } elseif (!preg_match("/^[\p{L}\s'\-]+$/u", $name)) {
            $errors[] = "Le nom ne doit contenir que des lettres, espaces, tirets et apostrophes.";
        }

        // --- Email ---
        if (empty($email)) {
            $errors[] = "L'adresse email est requise.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse email n'est pas valide.";
        } elseif (strlen($email) > 200) {
            $errors[] = "L'adresse email est trop longue.";
        }

        // --- Mot de passe ---
        if (empty($password)) {
            $errors[] = "Le mot de passe est requis.";
        } elseif (strlen($password) < 6) {
            $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
        } elseif (strlen($password) > 72) {
            // limite technique de bcrypt
            $errors[] = "Le mot de passe est trop long (72 caractères maximum).";
        } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une lettre et un chiffre.";
        }

        // --- Rôle ---
        if (empty($role)) {
            $errors[] = "Le rôle est requis.";
        } elseif (!in_array($role, $this->rolesAutorises, true)) {
            $errors[] = "Le rôle sélectionné n'est pas valide.";
        }

        return $errors;
    }

    /* =========================================================
     *  ACTIONS
     * ========================================================= */

    public function login($email, $password) {
        $email = trim($email);

        $errors = $this->validateLogin($email, $password);
        if (!empty($errors)) {
            return ['success' => false, 'msg' => implode(' ', $errors)];
        }

        $user = $this->model->findByEmail($email);
        if (!$user) {
            // Message volontairement générique pour ne pas révéler si l'email existe
            return ['success' => false, 'msg' => 'Email ou mot de passe incorrect.'];
        }
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'msg' => 'Email ou mot de passe incorrect.'];
        }

        unset($user['password']);
        $_SESSION['user'] = $user;

        return ['success' => true, 'user' => $user, 'redirect' => $this->getRedirectByRole($user['role'])];
    }

    public function register($name, $email, $password, $role = 'student') {
        $name  = trim($name);
        $email = trim($email);
        $role  = trim($role);

        // 1. Validation de format des champs
        $errors = $this->validateRegister($name, $email, $password, $role);
        if (!empty($errors)) {
            return ['success' => false, 'msg' => implode(' ', $errors)];
        }

        // 2. Validation métier : unicité de l'email (vérifiée avant insertion)
        if ($this->model->findByEmail($email)) {
            return ['success' => false, 'msg' => 'Cet email est déjà utilisé par un autre compte.'];
        }

        // 3. Insertion en base (le Model se charge du hash + requête préparée)
        $ok = $this->model->create($name, $email, $password, $role);
        if (!$ok) {
            return ['success' => false, 'msg' => 'Erreur lors de la création du compte. Veuillez réessayer.'];
        }

        // 4. Connexion automatique après inscription
        $user = $this->model->findByEmail($email);
        if ($user) {
            unset($user['password']);
            $_SESSION['user'] = $user;
            return ['success' => true, 'redirect' => $this->getRedirectByRole($role)];
        }

        return ['success' => true, 'redirect' => 'login.php'];
    }

    public function logout() {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }

    public function getRedirectByRole($role) {
        $map = [
            'admin'          => 'espace_administration.php',
            'administrateur' => 'espace_administration.php',
            'student'        => 'espace_etudiant.php',
            'etudiant'       => 'espace_etudiant.php',
            'teacher'        => 'espace_enseignant.php',
            'enseignant'     => 'espace_enseignant.php',
            'professeur'     => 'espace_enseignant.php',
            'parent'         => 'espace_parent.php',
            'comptable'      => 'espace_administration.php',
        ];
        return $map[strtolower($role)] ?? 'index.php';
    }
}
