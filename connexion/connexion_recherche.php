<?php
	session_start(); # Creating or updating session.

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
			Debug::print_message("Connexion scientifique ==> DEBUT");
			Debug::print_variable_data("recherche_identifiant", $identifiant);
			Debug::print_variable_data("recherche_motdepasse", $_POST["recherche_motdepasse"]);
			Debug::print_variable_data("recherche_motdepasse (crypt)", $motdepasse);
			Debug::print_message("Connexion scientifique ==> FIN");
		}
		# DEBUG -> END
		
		$bdd = new Base_de_donnees($bdd_hote, $bdd_identifiant, $bdd_motdepasse, $bdd_basededonnees);
		$resultat = $bdd->sql_request("SELECT motdepasse, valide FROM scientifiques WHERE id_scientifique = '$identifiant';");
		if ($resultat) # Scientifique enregistré
		{
			if ($resultat[0]["valide"])
			{
				if ($motdepasse == $resultat[0]["motdepasse"]) # Mot de passe correct
				{
					unset($_SESSION["modal_connexion"]);
					$_SESSION["connecte"] = $identifiant;
					
					if (!isset($_SESSION["debug"]))
					{echo("<script>document.location.href = \"../pages_personnelles/recherche.php\";</script>");}
				}
				else
				{
					echo( # Close button is temporary connected directly to modal window until found algorithm to close window without 'onclick'.
						"
						<div class=\"ares-modal failure attention\">
							<div class=\"header\">
					    		<span class=\"ares-button close\" onclick=\"javascript:document.location.href = '../page_accueil__fr.php';\"></span>
					    		<h3 class=\"ares-heading heading\">Mot de passe incorrect</h3>
					    	</div>
					    	<div class=\"body\">
					    		<p>Le mot de passe que venez d&rsquo;entrer ne correspond pas &agrave; l&rsquo;identifiant $identifiant.</p>
					    	</div>
						</div>
						"
					);
				}
			}
			else
			{
				echo(
					"
					<div class=\"ares-modal failure attention\">
						<div class=\"header\">
				    		<span class=\"ares-button close\" onclick=\"javascript:document.location.href = '../page_accueil__fr.php';\"></span>
				    		<h3 class=\"ares-heading heading\">Identifiant non-valid&eacute;</h3>
				    	</div>
				    	<div class=\"body\">
				    		<p>L&rsquo;identifiant correspond &agrave; une session non-valid&eacute;e. Veuillez patienter la fin de votre validation.</p>
				    		<p>Pour plus d&rsquo;informations, envoyez un courriel au <a href=\"mailto:risc@risc.cnrs.fr\">RISC</a>.</p>
				    	</div>
					</div>
					"
				);
			}
		}
		else # Scientifique non-enregistré
		{
			echo(
				"
				<div class=\"ares-modal failure attention\">
					<div class=\"header\">
			    		<span class=\"ares-button close\" onclick=\"javascript:document.location.href = '../page_accueil__fr.php';\"></span>
			    		<h3 class=\"ares-heading heading\">Identifiant incorrect</h3>
			    	</div>
			    	<div class=\"body\">
			    		<p>L&rsquo;identifiant que vous venez d&rsquo;entrer ne fait pas partie des inscrits.</p>
			    	</div>
				</div>
				"
			);
		}
	?>
</body>
 </html>