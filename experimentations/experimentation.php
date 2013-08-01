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
	
	<?php $_SESSION["experimentation_en_cours"] = $_POST["choix_experimentation"] . ".xml"; ?>
	<script type="text/javascript" src="../base_de_donnees/acces-js.php"></script>

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
		else if (!isset($_POST["choix_experimentation"]))
		{
			echo(
				"
				<div class=\"ares-modal failure attention\">
					<div class=\"header\">
			    		<span class=\"ares-button close\"
			    			  onclick=\"javascript:document.location.href = '../page_accueil__fr.php';\"></span>
			    		<h3 class=\"ares-heading heading\">Exp&eacute;rimentation</h3>
			    	</div>
			    	<div class=\"body\">
			    		<p>L&rsquo;acc&egrave;s &agrave; cette page ne peut se faire qu&rsquo;apr&egrave;s connexion et choix de l&rsquo;exp&eacute;rimentation." .
			    		" Veuillez vous connecter avant de poursuivre.</p>
			    	</div>
				</div>
				"
			);
		}
		else
		{
			if (!in_array($_POST["type_participation"], array("recherche", "etudes", "curiosite")))
			{
				echo(
					"
					<div class=\"ares-modal failure attention\">
						<div class=\"header\">
							<span class=\"ares-button close\"
								  onclick=\"javascript:document.location.href = '../page_accueil__fr.php';\"></span>
							<h3 class=\"ares-heading heading\">Exp&eacute;riementation</h3>
						</div>
						<div class=\"body\">
							<p>L&rsquo;acc&egrave;s &agrave; cette page ne peut se faire qu&rsquo;apr&egrave;s connexion par l&rsquo;une des trois voix disponibles : " .
							"recherche, &eacute;tudes ou curiosit&eacute;." .
							" Veuillez vous connecter avant de poursuivre.</p>
						</div>
					</div>
					"
				);
			}
		}
		$fichier_xml = simplexml_load_file($_POST["choix_experimentation"] . ".xml");
		
		# Suppression de l'identifiant sujet de la table 'connectes'
		$bdd = new Base_de_donnees($bdd_hote, $bdd_identifiant, $bdd_motdepasse, $bdd_basededonnees);
	?>
	<article>
		<!-- Code javascript de l'expérimentation -> DEBUT -->
		<script type="text/javascript" src="external_SoundManager/script/soundmanager2-jsmin.js"></script>
		<script type="text/javascript" src="sons.js"></script>
		<script type="text/javascript">
			var Experimentation = function()
			{
				/* Variables de l'expérimentation */
				this.chronometres = undefined;
				this.compteur_tests = undefined;
				this.compteur_essais = undefined;
				this.nombre_tests = undefined;
				this.nombre_essai = undefined;
				this.consignes = undefined;
				
				// parametres d'expérimentation au choix du sujet
				this.parametres = undefined; /* new Array() */
				
				// variables de test
				this.stimulus = undefined;
				this.touches = undefined;
				this.inputs = undefined;
				this.son = undefined;
				
				/* Fonctions utiles pour le javascript entré par les chercheurs */
				this.demarrer_chronometre = function(clef)
				{
					if ((clef == undefined) || (clef == null))
					{clef = "__base_chronometre__";}
					if (this.chronometre != undefined)
					{
						for (var i = 0 ; i < this.chronometres.length ; ++i)
						{delete this.chronometres[i];}
					}
					this.chronometres[clef] = new Array();
					var date = new Date();
					this.chronometres[clef][0] = date.getTime();
				};
				this.ajoutertemps_chronometre = function(clef)
				{
					if ((clef == undefined) || (clef == null))
					{clef = "__base_chronometre__";}
					var date = new Date();
					var new_time = date.getTime();
					this.chronometres[clef].push(new_time);
					return (new_time - this.chronometres[clef][0]);
				};

				/* Fonctions de l'expérimentation */			
				this.demarrer = function()
				{
					// initialisation de la variable 'chronometres'
					this.chronometres = new Array();
					// récupération des paramètres choisi par le sujet.
					this.parametres = new Array();
						// concernant le stimulus
						this.stimulus = new Array();
					<?php # Durée du stimulus
						if ($fichier_xml->deroulement->duree_stimulus->type == "fixe")
						{
							echo(
								"this.stimulus[\"duree\"] = " . $fichier_xml->deroulement->duree_stimulus->duree . ";\n"
							);
						}
						else if ($fichier_xml->deroulement->duree_stimulus->type == "auchoix")
						{
							echo(
								"inputs = document.getElementsByName(\"choixduree\");\n" .
								"for (var i = 0 ; i < inputs.length ; ++i)\n" .
								"{\n" .
								"	if (inputs[i].checked)\n" .
								"	{\n" .
								"		this.stimulus[\"duree\"] = inputs[i].value;\n" .
								"		break;\n" .
								"	}\n" .
								"}\n"
							);
						}
						else if ($fichier_xml->deroulement->duree_stimulus->type == "aleatoire")
						{
							echo(
								"this.stimulus[\"duree\"] = (function()\n" .
								"{\n" .
								"	" . $fichier_xml->deroulement->duree_stimulus->code . "\n" .
								"})();\n"
							);
						}
					?>
					<?php # Taille du stimulus
						if ($fichier_xml->deroulement->taille_stimulus->type == "fixe")
						{
							echo(
								"this.stimulus[\"taille\"] = new Array();\n" .
								"this.stimulus[\"taille\"][0] = " . $fichier_xml->deroulement->taille_stimulus->taille->nbrlignes . ";\n" .
								"this.stimulus[\"taille\"][1] = " . $fichier_xml->deroulement->taille_stimulus->taille->nbrcolonnes . ";\n"
 							);
						}
						else if ($fichier_xml->deroulement->taille_stimulus->type == "auchoix")
						{
							echo(
								"inputs = document.getElementsByName(\"taillestimulus\");\n" .
								"for (var i = 0 ; i < inputs.length ; ++i)\n" .
								"{\n" .
								"	if (inputs[i].checked)\n" .
								"	{\n" .
								"		this.stimulus[\"taille\"] = inputs[i].value.split(\";\");\n" .
								"		break;\n" .
								"	}\n" .
								"}\n"
							);
						}
					?>
						// concernant les autres paramètres
					inputs = new Array();
					for (var i = 0 ; ; ++i)
					{
						var input = document.getElementById("parametre" + i);
						if ((input == undefined) || (input == null))
						{break;}
						inputs.push(input);
					}
					for (var i = 0 ; i < inputs.length ; ++i)
					{this.parametres[inputs[i].name] = inputs[i].value;}
					
					// initialisation des attributs
					/* this.chronometre = undefined; */
					this.compteur_tests = 0;
					this.compteur_essais = 0;
					this.nombre_tests = <?php echo($fichier_xml->consignes->nombre_tests); ?>;
					this.nombre_essais = document.getElementById("nombre_essais").value;
					this.consignes = new Array(<?php
						echo("\"" . $fichier_xml->consignes->enonce[0] . "\"");
						for ($i = 1 ; $i < $fichier_xml->consignes->nombre_tests ; ++$i)
						{echo(", \"" . $fichier_xml->consignes->enonce[$i] . "\"");}
					?>);
					
					// initialisation des variables de test
					this.touches = <?php
						if ($fichier_xml->reponses->touche != "aucune")
						{
							echo("new Array(");
							echo($fichier_xml->reponses->touche[0]);
							for ($i = 1 ; $i < count($fichier_xml->reponses->touche) ; ++$i)
							{echo(", " . $fichier_xml->reponses->touche[$i]);}
							echo(")");
						}
						else
						{echo("undefined");}
					?>;
					this.son = <?php
						if ($fichier_xml->deroulement->son->booleen == "oui")
						{
							echo("new Array(");
							echo("new Son(0, " . $fichier_xml->deroulement->son->fichier[0] . ")");
							for ($i = 1 ; $i < count($fichier_xml->deroulement->son->fichier) ; ++$i)
							{echo(", new Son(" . $i . ", " . $fichier_xml->deroulement->son->fichier[$i] . ")");}
							echo(")");
						}
						else
						{echo("undefined");}
					?>;
					
					this.test_suivant();
				};
				this.test_suivant = function()
				{
					var div_experimentation = document.getElementById("experimentation");
					// si aucune consigne n'est spécifiée, le démarrage est direct (avec les 3s de délai)
					if (this.consignes[0] == "")
					{
						// suppression de l'ancien affichage
						while (div_experimentation.firstChild)
						{div_experimentation.removeChild(div_experimentation.firstChild);}
						return;
					}
					// modification de l'affichage
						// modification du style de la 'div' de présentation
					div_experimentation.style.cssText = "position: absolute; top: 10%; bottom: 10%; left: 10%; right: 10%;";
						// suppression de l'ancien affichage
					while (div_experimentation.firstChild)
					{div_experimentation.removeChild(div_experimentation.firstChild);}
						// ajout de la première consigne.
					var consigne = document.createElement("p");
						consigne.setAttribute("class", "ares-text lead");
						consigne.innerHTML = "Consigne : " + ((this.consignes[this.compteur_tests] != "") ? this.consignes[this.compteur_tests] : "aucune.")  + "<br />";
					var parametres = document.createElement("div");
						parametres.innerHTML = "<p>Nombre d&rsquo;essais restant : " + (this.nombre_essais - this.compteur_essais) + "</p>";
					var bouton = document.createElement("button");
						bouton.setAttribute("class", "ares-button primary");
						bouton.setAttribute("onclick", "javascript:experimentation.decompte_avant_essai();");
						bouton.innerHTML = "D&eacute;marrer les tests";
					div_experimentation.appendChild(consigne);
					div_experimentation.appendChild(parametres);
					div_experimentation.appendChild(bouton);
				};
				this.fin_test = function()
				{
					// incrémentation du compteur
					++this.compteur_tests;
					if (this.compteur_tests >= this.nombre_tests)
					{this.arreter();}
					else
					{this.test_suivant();}
				};
				this.decompte_avant_essai = function()
				{
					// suppression de l'ancien affichage
					var div_experimentation = document.getElementById("experimentation");
					while (div_experimentation.firstChild)
					{div_experimentation.removeChild(div_experimentation.firstChild);}
				
					// jeu du son si son il y a
					<?php
						if ($fichier_xml->deroulement->son->booleen == "oui")
						{
							echo(
								"var informations = (function()\n" .
								"{\n" .
								"	" . $fichier_xml->deroulement->son->code . "\n" .
								"})();\n" .
								"setTimeout(\"this.son[informations[\\\"index\\\"]]\", informations[\"decalage\"] + 3000);"
							);
						}
					?>
						// '-> le son doit être prévu ici avec un 'setTimeout', car il se peut que le délai soit inférieur à 3000 ms.
					// Voir ensuite pour barre de progression.
					setTimeout("experimentation.essai_suivant();", 3000);
				};
				this.essai_suivant = function()
				{
					var div_experimentation = document.getElementById("experimentation");
					// récupération de la durée si 'aléatoire'
					<?php
						if ($fichier_xml->deroulement->duree_stimulus->type == "aleatoire")
						{
							echo(
								"this.stimulus[\"duree\"] = (function()\n" .
								"{\n" .
								"	" . $fichier_xml->deroulement->duree_stimulus->code . "\n" .
								"})();\n"
							);
						}
					?>
					// affichage du stimulus
					var stimulus;
					<?php
						if ($fichier_xml->deroulement->type_stimulus->type != "autre")
						{
							echo("var possibilites = new Array();\n");
							echo("var couleurs = new Array();\n");
							for ($i = 0 ; $i < count($fichier_xml->deroulement->type_stimulus->type) ; ++$i)
							{
								if ($fichier_xml->deroulement->type_stimulus->type[$i] == "lettres")
								{
									echo("possibilites.push( \"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\", \"J\", \"K\"," .
															"\"L\", \"M\", \"N\", \"O\", \"P\", \"Q\", \"R\", \"S\", \"T\", \"U\", \"V\"," .
															"\"W\", \"X\", \"Y\", \"Z\");\n");
									break;
								}
								if ($fichier_xml->deroulement->type_stimulus->type[$i] == "chiffres")
								{
									echo("possibilites.push(\"0\", \"1\", \"2\", \"3\", \"4\", \"5\", \"6\", \"7\", \"8\", \"9\");\n");
									break;
								}
								if ($fichier_xml->deroulement->type_stimulus->type[$i] == "mots")
								{
									for ($i = 0 ; $i < count($fichier_xml->deroulement->type_stimulus->liste_mots->mot) ; ++$i)
									{echo("possibilites.push(\"" . $fichier_xml->deroulement->type_stimulus->liste_mots->mot[$i] . "\");\n");}
								}
							}
							for ($i = 0 ; $i < count($fichier_xml->deroulement->type_stimulus->type) ; ++$i)
							{
								if ($fichier_xml->deroulement->type_stimulus->type[$i] == "couleurs")
								{
									echo(
										"couleurs = [" .
											// trouver des couleurs plus agréables 'rgba()'.
											"\"aqua\", " .
											"\"black\", " .
											"\"blue\", " .
											"\"fuchsia\", " .
											"\"gray\", " .
											"\"green\", " .
											"\"grey\", " .
											"\"lime\", " .
											"\"maroon\", " .
											"\"navy\", " .
											"\"olive\", " .
											"\"purple\", " .
											"\"red\", " .
											"\"silver\", " .
											"\"teal\", " .
											"\"white\", " .
											"\"yellow\"]" .
											";\n"
									);
									break;
								}
							}
							echo("this.stimulus[\"affichage\"] = new Array();\n");
							echo(
								"stimulus = document.createElement(\"table\");\n" .
							    "stimulus.setAttribute(\"id\", \"stimulus\");\n" .
								"stimulus.setAttribute(\"class\", \"ares-table no-header no-outer-border no-inner-border\");\n" .
								"for (var i = 0 ; i < this.stimulus[\"taille\"][0] ; ++i)\n" .
								"{\n" .
								"	var tr = document.createElement(\"tr\");\n" .
								"	this.stimulus[\"affichage\"][i] = new Array();\n" .
								"	for (var j = 0 ; j < this.stimulus[\"taille\"][1] ; ++j)\n" .
								"	{\n" .
								"		var td = document.createElement(\"td\");\n" .
								"			td.style.fontSize = \"0.45in\";\n" .
								"			td.innerHTML = possibilites[Math.floor(Math.random() * possibilites.length)];\n" .
								"		/* insert in 'this.stimulus' */\n" .
								"		this.stimulus[\"affichage\"][i][j] = td.innerHTML;\n" .
								"		if (!couleurs.empty)\n" .
								"		{td.style.color = couleurs[Math.floor(Math.random() * couleurs.length)];}\n" .
								"		tr.appendChild(td);\n" .
								"	}\n" .
								"	stimulus.appendChild(tr);\n" .
								"}\n"
							);
						}
						else
						{
							echo(
								"stimulus = (function(test_actuel)\n" .
								"{\n" .
								"	" . $fichier_xml->deroulement->type_stimulus->code . "\n" .
								"})(this.nombre_tests);\n" .
								"this.stimulus[\"affichage\"] = stimulus;\n"
							);
						}
					?>
					
					// Démarrage du chronomètre
					this.demarrer_chronometre(undefined);
					
					// images déjà gérées dans le code du stimulus
					// (vide)
					
					// gestion des touches
					<?php if ($fichier_xml->reponses->touche == "aucune") {echo("/*\n");} ?>
					document.onkeydown = function (__event)
					{
						var objet_event = (function(__event)
						{
							// Internet Explorer
							if (window.event)
							{return (window.event);}
							// Autres navigateurs
							return (__event);
						})();
						var code_touche = objet_event.keyCode;
						var alt_pressee = objet_event.intAltKey;
						var ctrl_pressee = objet_event.intCtrlKey;
						var touche_attendue = <?php echo($fichier_xml->reponses->touche); ?>;
						
						// Si la touche est pressée
						if (code_touche == touche_attendue)
						{this.fin_essai();}
					};
					<?php if ($fichier_xml->reponses->touche == "aucune") {echo("*/\n");} ?>
					
					// ajout du stimulus à la 'div' de l'expérimentation
					document.getElementById("experimentation").appendChild(stimulus);
					// préparation de l'affichage des réponses
					affichage_reponses = function (__nombre_lignes)
					{
						var div_experimentation = document.getElementById("experimentation");
						// affichage des champs de réponse
						<?php
							echo("var table = document.createElement(\"table\");\n");
							echo("	table.setAttribute(\"class\", \"ares-table no-header no-outer-border\");\n");
							if ($fichier_xml->deroulement->type_stimulus->type != "autre")
							{
								// Zones de texte
								if ($fichier_xml->reponses->zones_texte->type == "taille_grille")
								{
									echo(
										"for (var i = 0 ; i < __nombre_lignes ; ++i)\n" .
										"{\n" .
										"	var tr = document.createElement(\"tr\");\n" .
										"	var td = document.createElement(\"td\");\n" .
										"	var input = document.createElement(\"input\");\n" .
										"		input.setAttribute(\"type\", \"text\");\n" .
										"		input.setAttribute(\"id\", \"input_reponse\" + i);\n" .
										"		input.setAttribute(\"class\", \"ares-textinput\");\n" .
										"	td.appendChild(input);\n" .
										"	tr.appendChild(td);\n" .
										"	table.appendChild(tr);\n" .
										"}\n"
									);
								}
								else if ($fichier_xml->reponses->zones_texte->type == "fixe")
								{
									echo(
										"for (var i = 0 ; i < " . $fichier_xml->reponses->zones_texte->nombre . " ; ++i)\n" .
										"{\n" .
										"	var tr = document.createElement(\"tr\");\n" .
										"	var td = document.createElement(\"td\");\n" .
										"	var input = document.createElement(\"input\");\n" .
										"		input.setAttribute(\"type\", \"text\");\n" .
										"		input.setAttribute(\"id\", \"input_reponse\" + i);\n" .
										"	td.appendChild(input);\n" .
										"	tr.appendChild(td);\n" .
										"	table.appendChild(tr);\n" .
										"}\n"
									);
								}
								echo(
									"var tr = document.createElement(\"tr\");\n" .
									"var td = document.createElement(\"td\");\n" .
									"var bouton = document.createElement(\"button\");\n" .
									"	bouton.setAttribute(\"class\", \"ares-button success\");\n" .
									"	bouton.setAttribute(\"onclick\", \"javascript:experimentation.fin_essai();\");\n" .
									"	bouton.innerHTML = \"Valider\";\n" .
									"	document.onkeydown = function (__event)\n" .
									"	{\n" .
									"		var objet_event = (function(__event)\n" .
									"		{\n" .
									"			if (window.event)\n" .
									"			{return (window.event);}\n" .
									"			return (__event);\n" .
									"		})();\n" .
									"		var code_touche = objet_event.keyCode;\n" .
									"		var alt_pressee = objet_event.intAltKey;\n" .
									"		var ctrl_pressee = objet_event.intCtrlKey;\n" .
									"		if ((code_touche == 13) && !alt_pressee && !ctrl_pressee)\n" .
									"		{experimentation.fin_essai();}\n" .
									"	};\n" .
									"td.appendChild(bouton);\n" .
									"tr.appendChild(td);\n" .
									"table.appendChild(tr);\n"
								);
								echo("document.getElementById(\"experimentation\").appendChild(table);\n");
								echo("document.getElementById(\"input_reponse0\").setAttribute(\"autofocus\", \"\");\n");
							}
							if ($fichier_xml->reponses->bouton_fin == "activer")
							{
								echo(
									"var tr = document.createElement(\"tr\");\n" .
									"var td = document.createElement(\"td\");\n" .
									"var bouton = document.createElement(\"button\");\n" .
									"	bouton.setAttribute(\"class\", \"ares-button success\");\n" .
									"	bouton.setAttribute(\"onclick\", \"javascript:experimentation.fin_essai();\");\n" .
									"	bouton.innerHTML = \"Finir l&rsquo;essai\";\n" .
									"td.appendChild(bouton);\n" .
									"tr.appendChild(td);\n" .
									"table.appendChild(tr);\n"
								);
							}
							echo("document.getElementById(\"experimentation\").appendChild(table);\n");
						?>
					}
					// masquage du stimulus si non 'infini'
					<?php if ($fichier_xml->deroulement->duree_stimulus->type == "infini") {echo("/*\n");} ?>
					setTimeout(
					"while (document.getElementById(\"experimentation\").firstChild)" +
					"{document.getElementById(\"experimentation\").removeChild(document.getElementById(\"experimentation\").firstChild);}"
					, this.stimulus["duree"]);
						<?php if ($fichier_xml->deroulement->type_stimulus->type == "autre") {echo("/*\n");} ?>
						setTimeout("affichage_reponses(experimentation.stimulus[\"taille\"][0]);", this.stimulus["duree"]);
						<?php if ($fichier_xml->deroulement->type_stimulus->type == "autre") {echo("*/\n");} ?>
					<?php if ($fichier_xml->deroulement->duree_stimulus->type == "infini") {echo("*/\n");} ?>
				};
				this.fin_essai = function()
				{
					var temps_reponses = this.ajoutertemps_chronometre(undefined);
					
					this.inputs = new Array();
					for (var i = 0 ; input_temp = document.getElementById("input_reponse" + i) ; ++i)
					{
						this.inputs[i] = new Array();
						this.inputs[i] = input_temp.value.toUpperCase().split("");
					}
					this.correction(temps_reponses);
					
					// incrémentation du compteur
					++this.compteur_essais;
					if (this.compteur_essais >= this.nombre_essais)
					{this.fin_test();}
					else
					{this.decompte_avant_essai();}
				};
				this.arreter = function()
				{
					var modal = document.createElement("div");
						modal.setAttribute("class", "ares-modal success attention");
						modal.innerHTML = 	"<div class=\"header\">" +
											"	<h3 class=\"ares-heading heading\">Fin de l&rsquo;exp&eacute;rimentation</h3>" +
											"</div>" +
											"<div class=\"body\">" +
											"	<p>L&rsquo;exp&eacute;rimentation est termin&eacute;e. Fermez cette fen&ecirc;tre afin d&rsquo;&ecirc;tre" +
												" redirig&eacute;(e) vers votre page personnelle.</p>" +
											"	<br />" +
											"	<a class=\"ares-button success\" href=\"<?php
													switch ($_POST["type_participation"])
													{
														case "recherche":
															echo("../pages_personnelles/sujet.php");
															break;
														
														case "etudes":
															echo("../pages_personnelles/etudiants.php");
															break;
														
														case "curiosite":
															echo("../pages_personnelles/curieux.php");
															break;
													}
												?>\">Fermer la fen&ecirc;tre</a>" +
											"</div>"
											;
					document.getElementById("experimentation").appendChild(modal);
				};
				
				/* Fonctions internes de l'expérimentation */
				this.correction = function(temps_reponses)
				{
					var resultats = (function(stimulus, reponses)
					{
						<?php
							echo(preg_replace(array("/%lt%/",	"/%gt%/", 	"/%quote%/",	"/%quotemark%/",	"/%end_line%/",	"/%tab%/"),
											  array("<", 		">", 		"'",			"\"", 				"\n",			"\t"),
											  $fichier_xml->correction["code"]
							));
						?>
					})(this.stimulus["affichage"], this.inputs);
					resultats["temps_reponses"] = temps_reponses;
					this.enregistrer_essai(resultats);
				};
				this.enregistrer_essai = function(resultats)
				{
					var parametres = this.parametres;
					<?php if ($fichier_xml->deroulement->type_stimulus == "autre") {echo("// ");}
					?>parametres["taille_stimulus"] = this.stimulus["taille"].join("x");
					<?php if ($fichier_xml->deroulement->duree_stimulus == "infini") {echo("// ");}
					?>parametres["duree_stimulus"] = this.stimulus["duree"];
					var bdd = new Base_de_donnees();
					var requete = bdd.create_sql_query(resultats, parametres);
					bdd.sql_request(requete);
				};
			};
			experimentation = new Experimentation();
		</script>
		<!-- Code javascript de l'expérimentation -> FIN -->
		<section>
			<div id="experimentation">
				<h1>
					Vous &ecirc;tes sur le point de participer &agrave; une exp&eacute;rimentation. Lorsque vous aurez appuy&eacute; sur le bouton ci-dessous,
					l&rsquo;exp&eacute;rimentation commencera.<br />
					Avant chaque test, la consigne de celui-ci sera affich&eacute;e. Une fois la consigne lue, un bouton permettra de proc&eacute;der au test,
					qui d&eacute;butera trois secondes apr&egrave;s clic sur le-dit bouton.<br />
					<span style="color: rgba(143,50,50,1) !important;">Attention !</span> Si un test ne comporte aucune consigne, celui-ci d&eacute;butera
					imm&eacute;diatement apr&egrave;s appui sur le premier bouton (trois secondes de d&eacute;lai).<br />
					Avant de commencer l&rsquo;exp&eacute;rimentation, veuillez renseigner les diff&eacute;rents param&egrave;tres que vous voulez modifier.<br />
					Il n'y aura aucun d&eacute;lai entre les diff&eacute;rents essais (hormis les 3 secondes avant le stimulus).
				</h1>
				<div id="parametres">
					<style>
						input[type="radio"] + label::before
						{
							position: relative;
							top: -0.25em;
						}
					</style>
					<table class="ares-table no-header no-outer-border form-like">
						<tr>
							<td><label for="nombre_essais">Nombre d&rsquo;essais<br />(nombre de fois que chaque test sera r&eacute;p&eacute;t&eacute;) :</label></td>
							<td><input type="number" id="nombre_essais" min="1" max="100" step="1" value="1" /></td>
						</tr>
						<?php
							if ($fichier_xml->deroulement->duree_stimulus->type == "auchoix")
							{
								echo("<tr>\n");
								echo("<td>Dur&eacute;e du stimulus :</td>\n");
								echo("<td>\n");
								echo(
									"<span id=\"choixduree0\">\n" .
									"	<input id=\"choixduree0_input\" name=\"choixduree\" type=\"radio\" class=\"ares-radio\"" .
										" value=\"" . $fichier_xml->deroulement->duree_stimulus->possibilite[0] . "\" checked />" .
									"	<label for=\"choixduree0_input\">" . $fichier_xml->deroulement->duree_stimulus->possibilite[0] . " ms</label>\n" .
									"</span>"
								);
								for ($i = 1 ; $i < count($fichier_xml->deroulement->duree_stimulus->possibilite) ; ++$i)
								{
									echo(
										"<span id=\"choixduree$i\">\n" .
										"	<br />\n" .
										"	<input id=\"choixduree" . $i . "_input\" name=\"choixduree\" type=\"radio\" class=\"ares-radio\"" .
											" value=\"" . $fichier_xml->deroulement->duree_stimulus->possibilite[$i] . "\" />" .
										"	<label for=\"choixduree" . $i . "_input\">" . $fichier_xml->deroulement->duree_stimulus->possibilite[$i] . " ms</label>\n" .
										"</span>"
									);
								}
								echo("</td>\n");
								echo("</tr>\n");
							}
							if ($fichier_xml->deroulement->taille_stimulus->type == "auchoix")
							{
								echo("<tr>\n");
								echo("<td>Taille de la grille du stimulus : </td>\n");
								echo("<td>\n");
								echo(
									"<span id=\"taillestimulus0\">\n" .
									"	<input id=\"taillestimulus0_input\" type=\"radio\" name=\"taillestimulus\" class=\"ares-radio\"" .
										" value=\"" . $fichier_xml->deroulement->taille_stimulus->possibilite[0]->nbrlignes . ";" .
													  $fichier_xml->deroulement->taille_stimulus->possibilite[0]->nbrcolonnes . "\" checked />\n" .
									"	<label for=\"taillestimulus0_input\">" . $fichier_xml->deroulement->taille_stimulus->possibilite[0]->nbrlignes . " lignes, " .
																				 $fichier_xml->deroulement->taille_stimulus->possibilite[0]->nbrcolonnes . " colonnes</label>\n" .
									"</span>\n"
								);
								for ($i = 1 ; $i < count($fichier_xml->deroulement->taille_stimulus->possibilite) ; ++$i)
								{
									echo(
										"<br />\n" .
										"<span id=\"taillestimulus$i\">\n" .
										"	<input id=\"taillestimulus" . $i . "_input\" type=\"radio\" name=\"taillestimulus\" class=\"ares-radio\"" .
											" value=\"" . $fichier_xml->deroulement->taille_stimulus->possibilite[$i]->nbrlignes . ";" .
														  $fichier_xml->deroulement->taille_stimulus->possibilite[$i]->nbrcolonnes . "\" />\n" .
										"	<label for=\"taillestimulus" . $i . "_input\">" . $fichier_xml->deroulement->taille_stimulus->possibilite[$i]->nbrlignes . " lignes, " .
																					 		  $fichier_xml->deroulement->taille_stimulus->possibilite[$i]->nbrcolonnes . " colonnes</label>\n" .
										"</span>\n"
									);
								}
								echo("</td>\n");
								echo("</tr>\n");
							}
						?>
					<?php
						for ($i = 0 ; $i < count($fichier_xml->parametres->parametre) ; ++$i)
						{
							echo(
								"<tr>" .
								"<td>" . preg_replace("/%blank%/", " ", $fichier_xml->parametres->parametre[$i]->nom) . " : </td>"
							);
							if ($fichier_xml->parametres->parametre[$i]->type == "entier")
							{
								echo("<td>" . "<input id=\"parametre$i\" type=\"number\" step=\"1\"" .
									 " name=\"" . preg_replace("/%blank%/", " ", $fichier_xml->parametres->parametre[$i]->nom) . "\"" .
									 " value=\"" . $fichier_xml->parametres->parametre[$i]->valeur . "\" />" . "</td>");
							}
							else if ($fichier_xml->parametres->parametre[$i]->type == "decimal")
							{
								echo("<td>" . "<input id=\"parametre$i\" type=\"number\" step=\"0.01\"" .
									 " name=\"" . preg_replace("/%blank%/", " ", $fichier_xml->parametres->parametre[$i]->nom) . "\"" .
									 " value=\"" . $fichier_xml->parametres->parametre[$i]->valeur . "\" />" . "</td>");
							}
							else if ($fichier_xml->parametres->parametre[$i]->type == "texte")
							{
								echo("<td>" . "<input id=\"parametre$i\" type=\"text\"" .
									 " name=\"" . preg_replace("/%blank%/", " ", $fichier_xml->parametres->parametre[$i]->nom) . "\"" .
									 " value=\"" . $fichier_xml->parametres->parametre[$i]->valeur . "\" />" . "</td>");
							}
							echo("</tr>");
						}
					?>
					</table>
				</div>
				<button class="ares-button primary" onclick="javascript:experimentation.demarrer();">D&eacute;marrer l&rsquo;exp&eacute;rimentation</button>
				<?php
					if ($fichier_xml->consignes->enonce[0] == "")
					{echo("&nbsp;&nbsp;<small>Aucune consigne pour le premier test, appuyer sur ce bouton fait d&eacute;buter ce test.</small>");}
				?>
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