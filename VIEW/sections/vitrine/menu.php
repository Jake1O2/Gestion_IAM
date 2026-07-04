<div id="header" class="header navbar navbar-transparent navbar-fixed-top navbar-expand-lg">
	<div class="container">
		<a href="index.php" class="navbar-brand">
			<img src="public/templates/templateVitrine/assets/img/logo/logo-iam.jpg" alt="Logo IAM" class="iam-logo" />
			<span class="brand-text">
				<span class="text-primary">IAM</span> Gestion
			</span>
		</a>
		<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#header-navbar">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<div class="collapse navbar-collapse" id="header-navbar">
		
			<ul class="nav navbar-nav navbar-right">
				<li class="nav-item"><a class="nav-link active" href="#home" data-click="scroll-to-target">ACCUEIL</a></li>
				<li class="nav-item"><a class="nav-link" href="#about" data-click="scroll-to-target">À PROPOS</a></li>
				<li class="nav-item"><a class="nav-link" href="#roles" data-click="scroll-to-target">ACTEURS</a></li>
				<li class="nav-item"><a class="nav-link" href="#service" data-click="scroll-to-target">FONCTIONNALITÉS</a></li>
				<li class="nav-item"><a class="nav-link" href="#contact" data-click="scroll-to-target">CONTACT</a></li>
				<li class="nav-item dropdown">
					<a class="nav-link" href="javascript:;">Espace <b class="caret"></b></a>
					<div class="dropdown-menu dropdown-menu-left animated fadeInDown">
						<a class="dropdown-item" href="espace_etudiant.php">Étudiant</a>
						<a class="dropdown-item" href="espace_parent.php">Parent</a>
						<a class="dropdown-item" href="espace_enseignant.php">Enseignant</a>
						<a class="dropdown-item" href="espace_administration.php">Administration</a>
					</div>
				</li>
				<?php if (!empty($_SESSION['user'])): ?>
					<li class="nav-item"><a class="nav-link" href="logout.php">Déconnexion</a></li>
				<?php else: ?>
					<li class="nav-item"><a class="nav-link" href="login.php">Connexion</a></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div>