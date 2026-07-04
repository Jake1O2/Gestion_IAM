<div id="home" class="content has-bg home">
	<div class="content-bg" style="background-image: url(public/templates/templateVitrine/assets/img/bg/bg-home.jpg);"
		data-paroller="true"
		data-paroller-factor="0.5"
		data-paroller-factor-xs="0.25">
	</div>
	<div class="container home-content">
		<h1>Institut Africain de Management</h1>
		<h3>Plateforme de Gestion Scolaire</h3>
		<p>
			Un espace numérique centralisé pour les étudiants, enseignants, parents et services administratifs<br />
			de l'IAM — pensé pour simplifier le quotidien de toute la communauté scolaire.
		</p>
		<a href="#roles" data-click="scroll-to-target" class="btn btn-theme btn-primary">Découvrir la plateforme</a>
		<?php if (!empty($_SESSION['user'])): ?>
			<a href="logout.php" class="btn btn-theme btn-outline-white">Se déconnecter</a>
		<?php else: ?>
			<a href="login.php" class="btn btn-theme btn-outline-white">Se connecter</a>
		<?php endif; ?>
	</div>
</div>