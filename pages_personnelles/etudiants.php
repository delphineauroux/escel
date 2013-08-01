<?php
	session_start();
	date_default_timezone_set("Europe/Paris");
	
	include_once("../inc/debug.inc.php");
	include_once("../base_de_donnees/infos_bdd.inc.php");
	include_once("../base_de_donnees/acces.php");
?>
 <!DOCTYPE html>
 <html>
<head>
	<meta charset="utf-8" />
	<meta name="description" content="Ce site propose plusieurs exp&eacute;rimentations r&eacute;alisables
									  soi-m&ecirc;me en ligne, en se basant sur les recherches
									  effectu&eacute;es en sciences cognitives." />
	<meta name="keywords" content="sciences cognitives, expériences, expérimentations, en ligne" />
	<meta name="author" content="Nicolaï LABORDE--SCHUMACHER" />

	<link rel="stylesheet" type="text/css" href="style.css" />

	<title>Exp&eacute;rimentations de sciences cognitives en ligne</title>
</head>
<body>
	<?php
		if (isset($_GET["deconnexion"]))
		{
			unset($_SESSION["connecte"]);
			echo(
				"
				<script type=\"text/javascript\">
					document.location.href = \"../page_accueil__fr.php\";
				</script>
				");
		}
		if (!isset($_SESSION["connecte"]) || !$_SESSION["connecte"])
		{
			echo(
			"
			<div class=\"ares-modal failure attention\">
				<div class=\"header\">
		    		<span class=\"ares-button close\"
		    			  onclick=\"javascript:document.location.href = '../page_accueil__fr.php'\"></span>
		    		<h3 class=\"ares-heading heading\">Hors-connexion</h3>
		    	</div>
		    	<div class=\"body\">
		    		<p>Vous essayez d&rsquo;acc&eacute;der &agrave; une page n&eacute;cessitant une connexion. Veuillez vous connecter avant de tenter d&rsquo;y acc&eacute;der de nouveau.</p>
		    	</div>
			</div>
			"
			);
		}
		
		$bdd = new Base_de_donnees($bdd_hote, $bdd_identifiant, $bdd_motdepasse, $bdd_basededonnees);
		$resultat = $bdd->sql_request("SELECT identifiant_tp FROM essais WHERE identifiant_tp IS NOT NULL;");
	?>
	<header>
		<h3 class="ares-heading">Exp&eacute;rimentation de sciences cognitives en ligne</h3>
		<div class="ares-navbar">
			<span class="brand">Accueil</span>
			<div class="nav">
				<a href="#sciences_cognitives"></a>
			</div>
		</div>
	</header>
</body>
 </html>