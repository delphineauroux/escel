<?php
	session_start();
	
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
	$identifiant = $_POST["recherche_identifiant"];
	$motdepasse = crypt($_POST["recherche_motdepasse"], $identifiant);
	
	# DEBUG -> BEGIN
	if (isset($_SESSION["debug"]))
	{
		Debug::print_message("Tentative d'enregistrement d'un(e) scientifique");
		Debug::print_variable_data("id_scientifique", $_POST["recherche_identifiant"]);
		Debug::print_variable_data("motdepasse", $_POST["recherche_motdepasse"]);
		Debug::print_variable_data("motdepasse (crypt)", $motdepasse);
	}
	# DEBUG -> END
	
	$bdd = new Base_de_donnees($bdd_hote, $bdd_identifiant, $bdd_motdepasse, $bdd_basededonnees);
	$resultat = $bdd->sql_request("SELECT id_scientifique, motdepasse FROM scientifiques WHERE id_scientifique='$identifiant';");
	if (!$resultat) # Pas de scientifique existant
	{
		$bdd->sql_request("INSERT INTO scientifiques (id_scientifique, motdepasse) VALUES ('$identifiant', '$motdepasse');");
		unset($_SESSION["modal_enregistrement"]);
		
		# Connexion
		$_SESSION["connecte"] = $identifiant;
		
		if (!isset($_SESSION["debug"]))
		{
			echo(
				"
				<div class=\"ares-modal success attention\">
					<div class=\"header\">
			    		<span class=\"ares-button close\"
			    			  onclick=\"javascript:document.location.href = '../page_accueil__fr.php'\"></span>
			    		<h3 class=\"ares-heading heading\">Enregistrement</h3>
			    	</div>
			    	<div class=\"body\">
			    		<p>Votre demande d&rsquo;inscription a bien &eacute;t&eacute; prise en compte. Seule sa validation vous permettra de vous connecter.</p>
			    		<p>Pour plus d&rsquo;informations, envoyez un courriel au <a href=\"mailto:risc@risc.cnrs.fr\">RISC</a>.</p>
			    	</div>
				</div>
				"
			);
		}
	}
	else # id_scientifique déjà pris
	{
		echo(
			"
			<div class=\"ares-modal failure attention\">
				<div class=\"header\">
		    		<span class=\"ares-button close\"
		    			  onclick=\"javascript:document.location.href = '../page_accueil__fr.php'\"></span>
		    		<h3 class=\"ares-heading heading\">Identifiant d&eacute;j&agrave; utilis&eacute;</h3>
		    	</div>
		    	<div class=\"body\">
		    		<p>L&rsquo;identifiant que vous venez d&rsquo;entrer est indisponible, veuillez en choisir un autre !</p>
		    	</div>
			</div>
			"
			);
	}
?>
</body>
 </html>