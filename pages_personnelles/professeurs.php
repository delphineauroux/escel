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

	<link rel="stylesheet" type="text/css" href="../style.css" />

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
		if (!isset($_POST["enseignement_motdepasse"]))
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
		$resultat = $bdd->sql_request("SELECT motdepasse FROM scientifiques WHERE id_scientifique='__etudes__';");
		if ($_POST["enseignement_motdepasse"] != $resultat[0]["motdepasse"])
		{
			echo(
			"
			<div class=\"ares-modal failure attention\">
				<div class=\"header\">
		    		<span class=\"ares-button close\"
		    			  onclick=\"javascript:document.location.href = '../page_accueil__fr.php'\"></span>
		    		<h3 class=\"ares-heading heading\">Mot de passe</h3>
		    	</div>
		    	<div class=\"body\">
		    		<p>Le mot de passe utilis&eacute; est incorrect.</p>
		    	</div>
			</div>
			"
			);
		}
	?>
	<header>
		<h3 class="ares-heading">Exp&eacute;rimentation de sciences cognitives en ligne</h3>
		<div class="ares-navbar">
			<span class="brand">Accueil</span>
			<div class="nav">
				<a href="#identifiant_tp">Identifiant de TP</a>
				<a href="#experimentation">Exp&eacute;riementation</a>
			</div>
		</div>
	</header>
	
	<article>
		<section>
			<div class="ares-navbar">
			<span id="identifiant_tp" class="brand">Identifiant de TP</span>
		</div>
		<div id="identifiant_tp__content">
			<div class="ares-alert success" style="text-align: center; margin-top: -1em;">
				<h2><?php
					$counter = 0;
					$nouvel_id = "";
					while (true)
					{
						$date = new DateTime("now");
						$nouvel_id = $date->format("Ymd_His_") . $counter;
						if ((count($bdd->sql_request("SELECT id_sujet FROM essais WHERE id_sujet='$nouvel_id';")) == 0) &&
							(count($bdd->sql_request("SELECT id FROM connectes WHERE id='$nouvel_id'")) == 0))
						{
							$_SESSION["connecte"] = $nouvel_id;
							$bdd->sql_request("INSERT INTO connectes (id) VALUES ('$nouvel_id');");
							break;
						}
					}
					echo($nouvel_id);
				?></h2>
			</div>
		</div>
		</section>
		<section>
			<div class="ares-navbar">
				<span id="experimentation" class="brand">Exp&eacute;rimentation</span>
			</div>
			<div id="experimentation__content">
			</div>
		</section>
		<span class="ares-group horizontal" style="margin: 5px; margin-right: 0; position: relative; float: right;">
			<button class="ares-button" onclick="javascript:document.getElementById('modal_idsujet').style.display = 'block';">Identifiant sujet</button>
			<button onclick="javascript:document.location.href = '<?php echo($PHPSELF); ?>?deconnexion';" class="ares-button">D&eacute;connexion</button>
		</span>
		<div id="modal_idsujet" class="ares-modal information attention" style="display: none;">
			<div class="header">
				<span class="ares-button close" onclick="javascript:this.parentNode.parentNode.style.display = 'none';"></span>
				<h3 class="ares-heading heading">Identifiant sujet</h3>
			</div>
			<div class="body">
				<p>Voici votre identifiant sujet :</p>
				<p class="ares-text lead" style="text-indent: 1em;"><?php echo($_SESSION["connecte"]); ?></p>
				<p>
					Il vous est conseill&eacute; de le conserver. Vous pourrez l&rsquo;utiliser de nouveau lors de votre prochaine
					connexion, vous permettant ainsi de se faire suivre vos r&eacute;sultats.
				</p>
			</div>
		</div>
	</article>
</body>
 </html>