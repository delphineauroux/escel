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
		$erreur = "";
		# Récupération des variables du formulaire
			# ==
			## tri des fichiers
			$files = array();
			$files["sons"] = array();
			$files["images"] = array();
			for ($i = 1 ; $i <= count($_FILES) ; ++$i)
			{
				if (isset($_FILES["fichier_son$i"]))
				{array_push($files["sons"], $_FILES["fichier_son$i"]);}
				else if (isset($_FILES["fichier_image$i"]))
				{array_push($files["images"], $_FILES["fichier_image$i"]);}
			}
			# ==
			# --
		$nom = $_POST["nom"];
		$experimentation_ouverte = ($_POST["experimentation_ouverte"] == "true");
			# --
		if (isset($_POST["nombre_tests"]))
		{$nombre_tests = $_POST["nombre_tests"];}
		else
		{$erreur .= "<p>Nombre de tests : le nombre de tests entr&eacute; n&rsquo;est pas un nombre ou est n&eacute;gative</p>";}
		$consignes_enonces = array();
		for ($i = 1 ; $i <= $nombre_tests ; ++$i)
		{array_push($consignes_enonces, $_POST["consigne_enonce$i"]);}
			# --
		$nombre_parametres = $_POST["nombre_parametres"];
		$parametres = array();
		for ($i = 0 ; $i < $nombre_parametres ; ++$i)
		{$parametres[$_POST["parametre_nom$i"]] = array("type" => $_POST["parametre_type$i"], "valeur" => $_POST["parametre_valeur$i"]);}
			# --
		$type_stimulus = array();
		if ($_POST["typestimulus_autre"] == "true")
		{
			array_push($type_stimulus, "autre");
			$code_stimulus = $_POST["typestimulus_autrecode"];
		}
		else
		{
			if ($_POST["typestimulus_chiffres"] == "true")
			{array_push($type_stimulus, "chiffres");}
			if ($_POST["typestimulus_mots"] == "true")
			{
				array_push($type_stimulus, "mots");
				$liste_mots = $_POST["typestimulus_listemots"];
				$liste_mots = strtr($liste_mots, "ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ", "AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy");
				$liste_mots = preg_replace("/[ \t\n]/", "", $liste_mots);
				$liste_mots = explode(",", $liste_mots);
			}
			if ($_POST["typestimulus_lettres"] == "true")
			{array_push($type_stimulus, "lettres");}
			if ($_POST["typestimulus_couleurs"] == "true")
			{array_push($type_stimulus, "couleurs");}
		}
		if (!isset($type_stimulus) || (empty($type_stimulus)))
		{$erreur .= "<p>Type stimulus : type du stimulus non-sp&eacute;cifi&eacute;.</p>";}
		if (!isset($_POST["stimulus_typeduree"]))
		{$erreur .= "<p>Dur&eacute;e stimulus : dur&eacute;e du stimulus non-pr&eacute;cis&eacute;e.</p>";}
		else
		{
			$stimulus_typeduree = $_POST["stimulus_typeduree"];
			if ($stimulus_typeduree == "fixe")
			{$duree_stimulus = $_POST["stimulus_dureefixe"];}
			else if ($stimulus_typeduree == "aleatoire")
			{$duree_stimulus = $_POST["code_dureestimulus"];}
			else if ($stimulus_typeduree == "auchoix")
			{
				$duree_stimulus = array();
				for ($i = 0 ; isset($_POST["stimulus_dureeauchoix" . $i]) ; ++$i)
				{array_push($duree_stimulus, $_POST["stimulus_dureeauchoix" . $i]);}
			}
			else
			{$erreur .= "<p>Dur&eacute;e stimulus : type de dur&eacute;e non-pr&eacute;vue.</p>";}
		}
		if (in_array("autre", $type_stimulus)) # récupération des fichiers image
		{
			$fichiers_image = array();
			$extensions = array("jpg", "png", "gif");
			$taille_maximum = 102400;
			foreach ($files["images"] as $file)
			{
				$erreur_locale = "";
				# Récupération du fichier et copie dans le dossier 'images' de 'expérimentations'
					# extension du fichier
				$extension = strrchr($file["name"], ".");
				if (!in_array($extension, $extensions))
				{$erreur_locale .= "<p>Upload de l'image : l&rsquo;extension du fichier est incorrecte (correctes : &quot;jpg&quot; &quot;png&quot; &quot;gif&quot;).</p>";}
					# taille du fichier
				if (filesize($file["tmp_name"]) > $taille_maximum)
				{$erreur_locale .= "<p>Upload de l'image : le fichier est trop volumineux (taille maximum : 100 Ko).</p>";}
				
				if ($erreur_locale != "")
				{
						# nom du fichier
					$nom_fichier = $_SESSION["connecte"] . "__" . $nom;
					$nom_fichier = strtr($nom_fichier, "ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ", "AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy");
					$nom_fichier = preg_replace("/[^.a-zA-Z0-9]+/i", "_", $nom_fichier);
					
					
					if (!move_uploaded_file($file["tmp_name"], "sons/" . $nom_fichier))
					{$erreur_locale . "<p>Upload de l'image : &eacute;chec de l&rsquo;upload. Raison(s) inconnue(s).</p>";}
					array_push($fichiers_image, "sons/" . $nom_fichier);
				}
				
				$erreur .= $erreur_locale;
			}
		}
		if (!isset($_POST["stimulus_taille"]))
		{
			$erreur .= "<p>Taille stimulus : taille du stimulus non-sp&eacute;cifi&eacute;e.</p>";
		}
		else
		{
			$stimulus_typetaille = $_POST["stimulus_taille"];
			if ($stimulus_typetaille == "fixe")
			{
				$stimulus_taillelignes = $_POST["stimulus_nbrlignes"];
				$stimulus_taillecolonnes = $_POST["stimulus_nbrcolonnes"];
			}
			else if ($stimulus_typetaille == "auchoix")
			{
				$stimulus_choixtaille = array();
				for ($i = 0 ; isset($_POST["choixtaille_nbrlignes" + $i]) ; ++$i)
				{array_push($stimulus_choixtaille, array("nbrlignes" => $_POST["choixtaille_nbrlignes" + $i], "nbrcolonnes" => $_POST["choixtaille_nbrcolonnes" + $i]));}
			}
		}
		$presence_son = $_POST["son_stimulus"];
		if ($presence_son == "oui")
		{$code_son = $_POST["son_code"];}
			# Modification du code son (si existant), afin de remplacer les termes adéquats par les chemins des son, après récupération du fichier
			if (isset($code_son))
			{
				$fichiers_son = array();
				$extensions = array("m4a", "ogg", "mp3", "wav");
				$taille_maximum = 102400;
				foreach ($files["sons"] as $file)
				{
					$erreur_locale = "";
					# Récupération du fichier et copie dans le dossiers 'sons' de 'expérimentations'
						# extension du fichier
					$extension = strrchr($file["name"], ".");
					if (!in_array($extension, $extensions))
					{$erreur_locale .= "<p>Upload du son : l&rsquo;extension du fichier est incorrecte (correctes : &quot;m4a&quot; &quot;ogg&quot; &quot;mp3&quot; &quot;wav&quot;).</p>";}
						# taille du fichier
					if (filesize($file["tmp_name"]) > $taille_maximum)
					{$erreur_locale .= "<p>Upload du son : le fichier est trop volumineux (taille maximum : 100 Ko).</p>";}
					
					if ($erreur_locale != "")
					{
							# nom du fichier
						$nom_fichier = $_SESSION["connecte"] . "__" . $nom;
						$nom_fichier = strtr($nom_fichier, "ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ", "AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy");
						$nom_fichier = preg_replace("/[^.a-zA-Z0-9]+/i", "_", $nom_fichier);
						
						if (!move_uploaded_file($file["tmp_name"], "sons/" . $nom_fichier))
						{$erreur_locale .= "<p>Upload du son : &eacute;chec de l&rsquo;upload. Raison(s) inconnue(s).</p>";}
						array_push($fichiers_son, "sons/" . $nom_fichier);
					}
					
					$erreur .= $erreur_locale;
				}
			}
			# --
		$type_zonestexte = $_POST["type_input"];
		if ($type_zonestexte == "fixe")
		{$nombre_zonetexte = $_POST["nombre_inputs"];}
		$touche_clavier = ($_POST["touche"] == "true") ? $_POST["touche_choix"] : "aucune";
		$bouton_fin = $_POST["bouton_fin"];
			# --
		$correction = $_POST["correction"];
		if (!preg_match("#return#", $correction))
		{$erreur .= "<p>Le code de correction doit contenir le &rsquo;return&rsquo; ad&eacute;quat.</p>";}
		
		if ($erreur == "")
		{
			$code_stimulus = preg_replace(	array("/return[ \t\r\n(\r\n)]+/",	"/</", 	"/>/", 	"/'/",		"/\\\\\"/", 		"/\r\n/",		"/\n/",			"/\r/",			"/\t/"),
											array("return ",					"%lt%",	"%gt%", "%quote%",	"%quotemark%",	"%end_line%",	"%end_line%",	"%end_line%",	"%tab%"),
											$code_stimulus
										);
			$code_son = 	preg_replace(	array("/return[ \t\r\n(\r\n)]+/",	"/</", 	"/>/", 	"/'/",		"/\\\\\"/", 		"/\r\n/",		"/\n/",			"/\r/",			"/\t/"),
											array("return ",					"%lt%",	"%gt%", "%quote%",	"%quotemark%",	"%end_line%",	"%end_line%",	"%end_line%",	"%tab%"),
											$code_son
										);
			$correction = 	preg_replace(	array("/return[ \t\r\n(\r\n)]+/",	"/</", 	"/>/", 	"/'/",		"/\\\\\"/", 		"/\r\n/",		"/\n/",			"/\r/",			"/\t/"),
											array("return ",					"%lt%",	"%gt%", "%quote%",	"%quotemark%",	"%end_line%",	"%end_line%",	"%end_line%",	"%tab%"),
											$correction
										);
		
			$xml_content = 	"<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n" .
							"<experimentation>\n" .
							"	<nom>$nom</nom>\n" .
							"	<consignes>\n" .
							"		<nombre_tests>$nombre_tests</nombre_tests>\n";
							for ($i = 0 ; $i < $nombre_tests ; ++$i)
							{
			$xml_content .= "		<enonce>" . htmlspecialchars($consignes_enonces[$i]) . "</enonce>\n";
							}
			$xml_content .=	"	</consignes>\n" .
							"	<parametres>\n" .
							"		<nombre>$nombre_parametres</nombre>\n";
							for ($i = 0 ; $i < $nombre_parametres ; ++$i)
							{
								if($parametres[$i]["nom"] != "")
								{
			$xml_content .=	"		<parametre>\n" .
							"			<nom>" . preg_replace("/[ \t]/", "%blank%", $parametres[$i]["nom"]) . "</nom>\n" .
							"			<valeur>" . $parametres[$i]["valeur"] . "</valeur>\n" .
							"			<type>" . $parametres[$i]["type"] . "</type>\n" .
							"		</parametre>\n";
								}
							}
			$xml_content .=	"	</parametres>\n" .
							"	<deroulement>\n" .
							"		<type_stimulus>\n";
							for ($i = 0 ; $i < count($type_stimulus) ; ++$i)
							{
			$xml_content .=	"			<type>" . $type_stimulus[$i] . "</type>\n";
							}
							if ((gettype($type_stimulus) == "array") && in_array("mots", $type_stimulus))
							{
			$xml_content .=	"			<liste_mots>\n";
								for ($i = 0 ; $i < count($liste_mots) ; ++$i)
								{
			$xml_content .=	"				<mot>" . $liste_mots[$i] . "</mot>\n";
								}
			$xml_content .=	"			</liste_mots>\n";
							}
							if (in_array("autre", $type_stimulus))
							{
			$xml_content .=	"			<code value='$code_stimulus' />\n";
							}
							for ($i = 0 ; $i < count($fichiers_image) ; ++$i)
							{
			$xml_content .=	"			<fichier>" . $fichiers_image[$i] . "</fichier>\n";
							}
			$xml_content .=	"		</type_stimulus>\n" .
							"		<duree_stimulus>\n" .
							"			<type>$stimulus_typeduree</type>\n";
							if ($stimulus_typeduree == "fixe")
							{
			$xml_content .=	"			<duree>$duree_stimulus</duree>\n";
							}
							else if ($stimulus_typeduree == "aleatoire")
							{
			$xml_content .=	"			<code value='$duree_stimulus' />\n";
							}
							else if ($stimulus_typeduree == "auchoix")
							{
								for ($i = 0 ; $i < count($duree_stimulus) ; ++$i)
								{
			$xml_content .=	"			<possibilite>" . $duree_stimulus[$i] . "</possibilite>\n";
								}
							}
			$xml_content .=	"		</duree_stimulus>\n" .
							"		<taille_stimulus>\n" .
							"			<type>$stimulus_typetaille</type>\n";
							if ($stimulus_typetaille == "fixe")
							{
			$xml_content .=	"			<taille>\n" .
							"				<nbrlignes>$stimulus_taillelignes</nbrlignes>\n" .
							"				<nbrcolonnes>$stimulus_taillecolonnes</nbrcolonnes>\n" .
							"			</taille>\n";
							}
							else if ($stimulus_typetaille == "auchoix")
							{
								for ($i = 0 ; $i < count($stimulus_choixtaille) ; ++$i)
								{
			$xml_content .=	"			<possibilite>\n" .
							"				<nbrlignes>" . $stimulus_choixtaille[$i]["nbrlignes"] . "</nbrlignes>\n" .
							"				<nbrcolonnes>" . $stimulus_choixtaille[$i]["nbrcolonnes"] . "</nbrcolonnes>\n" .
							"			</possibilite>\n";
								}
							}
			$xml_content .=	"		</taille_stimulus>\n" .
							"		<son>\n" .
							"			<booleen>$presence_son</booleen>\n";
							if ($presence_son == "oui")
							{
			$xml_content .=	"			<code value='$code_son' />\n";
								for ($i = 0 ; $i < count($fichiers_son) ; ++$i)
								{
			$xml_content .=	"			<fichier>" . $fichiers_son[$i] . "</fichier>\n";
								}
							}
			$xml_content .=	"		</son>\n" .
							"	</deroulement>\n" .
							"	<reponses>\n" .
							"		<zones_texte>\n" .
							"			<type>$type_zonestexte</type>\n";
							if ($type_zonestexte == "fixe")
							{
			$xml_content .=	"			<nombre>$nombre_zonestexte</nombre>\n";
							}
			$xml_content .=	"		</zones_texte>\n" .
							"		<touche>$touche_clavier</touche>\n" .
							"		<bouton_fin>$bouton_fin</bouton_fin>\n" .
							"	</reponses>\n";
			$xml_content .=	"	<correction>\n" .
							"		<code value='$correction' />\n" .
							"	</correction>\n" .
							"</experimentation>";
			# Insertion dans le fichier
			$chemin_fichier = $_POST["fichier_xml"];
			if (!isset($chemin_fichier) || ($chemin_fichier == "")) # le fichier doit être créé
			{
				$nom = preg_replace("[ \t]", "%blank%", "$nom");
				$chemin_fichier = "$nom.xml";
				if ($fichier_xml = fopen($chemin_fichier, "x")) # le fichier n'existe pas
				{
					fwrite($fichier_xml, $xml_content);
					fclose($fichier_xml);
					chmod($chemin_fichier, 0776);
				}
				else # le fichier existe déjà
				{
					$i = 0;
					do
					{
						++$i;
						$chemin_fichier = "$nom" . "__" . "$i.xml";
					} while (!($fichier_xml = fopen($chemin_fichier, "x")));
					fwrite($fichier_xml, $xml_content);
					fclose($fichier_xml);
				}
				# Enregistrement dans la base de données
				$bdd = new Base_de_donnees($bdd_hote, $bdd_identifiant, $bdd_motdepasse, $bdd_basededonnees);
				$bdd->sql_request("INSERT INTO experimentations (id_scientifique, fichier_source, ouverte) VALUES ('" . $_SESSION["connecte"] . "', '$chemin_fichier', $experimentation_ouverte);");
			}
			else # le fichier doit être édité
			{
				unlink($chemin_fichier);
				$fichier_xml = fopen($chemin_fichier, "x");
				fwrite($fichier_xml, $xml_content);
				fclose($fichier_xml);
			}
		}
		if ($erreur != "")
		{
			echo(
				"<div class=\"ares-modal information attention\">\n" .
				"	<div class=\"header\">\n" .
				"		<span class=\"ares-button close\" onclick=\"javascript:document.location.href = '../pages_personnelles/recherche.php'\"></span>\n" .
				"		<h3 class=\"ares-heading heading\">Identifiant sujet</h3>\n" .
				"	</div>\n" .
				"	<div class=\"body\">\n" .
				"		<p>Voici votre identifiant sujet :</p>\n" .
				"		<p class=\"ares-text lead\" style=\"text-indent: 1em;\">Erreur</p>\n" .
				"		<div>\n" .
				"			$erreur" .
				"		</div>\n" .
				"	</div>\n" .
				"</div>\n"
			);
		}
		else
		{
			echo(
				"<div class=\"ares-modal information attention\">\n" .
				"	<div class=\"header\">\n" .
				"		<span class=\"ares-button close\" onclick=\"javascript:document.location.href = '../pages_personnelles/recherche.php'\"></span>\n" .
				"		<h3 class=\"ares-heading heading\">Identifiant sujet</h3>\n" .
				"	</div>\n" .
				"	<div class=\"body\">\n" .
				"		<p>Voici votre identifiant sujet :</p>\n" .
				"		<p class=\"ares-text lead\">Enregistrement de l&rsquo;exp&eacute;rimentation</p>\n" .
				"		<p>\n" .
				"			L&rsquo;exp&eacute;rimentation a &eacute;t&eacute; correctement enregistr&eacute;e." .
				"		</p>\n" .
				"	</div>\n" .
				"</div>\n"
			);
		}
	?>
</body>
 </html>