<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8" />
	<title>IAM | Institut Africain de Management</title>
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
	<meta content="Plateforme numérique de gestion scolaire de l'Institut Africain de Management." name="description" />
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
	<link href="public/templates/templateVitrine/assets/css/one-page-parallax/app.min.css" rel="stylesheet" />
</head>
<body data-spy="scroll" data-target="#header" data-offset="51">
	<div id="page-container" class="fade">

		<?php require_once "VIEW/sections/vitrine/menu.php"; ?>
		<?php require_once "VIEW/sections/vitrine/baniere.php"; ?>
		<?php require_once "VIEW/sections/vitrine/about.php"; ?>
		<?php require_once "VIEW/sections/vitrine/chiffrage.php"; ?>
		<?php require_once "VIEW/sections/vitrine/Team.php"; ?>
		<?php require_once "VIEW/sections/vitrine/info.php"; ?>
		<?php require_once "VIEW/sections/vitrine/service.php"; ?>
		<?php require_once "VIEW/sections/vitrine/publicite.php"; ?>
		<?php require_once "VIEW/sections/vitrine/temoignage.php"; ?>
		<?php require_once "VIEW/sections/vitrine/contact.php"; ?>
		<?php require_once "VIEW/sections/vitrine/footer.php"; ?>
		<?php require_once "VIEW/sections/vitrine/config.php"; ?>

	</div>

	<script src="public/templates/templateVitrine/assets/js/one-page-parallax/app.min.js"></script>
</body>
</html>