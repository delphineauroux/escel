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
<body class="ares-body">
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
	?>
	<script type="text/javascript">
		affichermasquer_explications = function()
		{
			if (document.getElementById("explications__content").style.height == "0")
			{document.getElementById("explications__content").style.height = "auto";}
			else
			{document.getElementById("explications__content").style.height = "0";}
		}
	</script>
	<header>
		<h1 class="ares-heading">Exp&eacute;rimentation de sciences cognitives en ligne</h1>
		<div class="ares-navbar">
			<a class="brand">Accueil</a>
			<div class="nav">
				<a href="#explications" onclick="javascript:affichermasquer_explications();">Explications</a>
				<a href="#editeur">&Eacute;diteur d&rsquo;exp&eacute;rimentation</a>
			</div>
		</div>
	</header>

	<article>
		<section>
		<div id="explications" class="ares-navbar">
			<a class="brand" onclick="javascript:affichermasquer_explications();">Explications</a>
			<a href="#" class="ares-button arrow-up" style="float:right"></a>
		</div>
			<div id="explications__content" style="height: 0; overflow: hidden;">
			<p>...</p>
			</div>
		</section>
		
		<section>
		<div id="editeur" class="ares-navbar">
			<a class="brand">&Eacute;diteur d&rsquo;exp&eacute;rimentation</a>
			<a href="#" class="ares-button arrow-up" style="float: right;"></a>
		</div>
			<fieldset class="ares-fieldset">
			<legend>&Eacute;diter une exp&eacute;rimentation</legend>
				<form method="post" action="<?php $PHPSELF ?>" enctype="multipart/form-data">
					<input type="hidden" name="editeur_type" value="editer" />
					<select name="editeur_fichier">
						<option selected value="none">Choisir le fichier</option>
						<?php
							$bdd = new Base_de_donnees($bdd_hote, $bdd_identifiant, $bdd_motdepasse, $bdd_basededonnees);
							$liste_experimentations = $bdd->sql_request("SELECT fichier_source FROM experimentations WHERE id_scientifique = '" . $_SESSION["connecte"] . "';");
							for ($i = 0 ; $i < count($liste_experimentations) ; ++$i)
							{
								$nom_fichier = pathinfo($liste_experimentations[$i]["fichier_source"]);
								$nom_fichier = $nom_fichier["filename"];
								echo("\t<option value=\"$nom_fichier\">$nom_fichier</option>\n");
							}
							# DEBUG -> BEGIN
							if ($_SESSION["debug"])
							{
								Debug::print_variable_data("identifiant", $_SESSION["connecte"]);
								Debug::print_variable_data("liste_experimentations", $liste_experimentations);
							}
							# DEBUG -> END
						?>
					</select>
					<input type="submit" class="ares-button primary small" value="&Eacute;diter" />
					<?php
						if (isset($_POST["editeur_fichier"]))
						{
							if ($_POST["editeur_fichier"] != "none")
							{$fichier_editeur = "../experimentations/" . $_POST["editeur_fichier"] . ".xml";}
							else
							{echo("<span class=\"ares-text failure\">Vous devez renseigner une exp&eacute;riementation.</span>");}
						}
					?>
				</form>
			</fieldset>
			<br />
			<fieldset class="ares-fieldset">
			<legend>
				<?php
					if (($_POST["editeur_type"] == "editer") && (isset($fichier_editeur)))
					{echo("&Eacute;diter l&rsquo;exp&eacute;rimentation");}
					else
					{echo("Cr&eacute;er une exp&eacute;rimentation");}
				?>
			</legend>
			<form method="post" action="../experimentations/creer_modifier.php" enctype="multipart/form-data">
			<?php
				if ($_POST["editeur_type"] == "editer")
				{
					if ($fichier_editeur)
					{$fichier_xml = simplexml_load_file($fichier_editeur);}
					unset($_POST["editeur_type"]);
					echo("<input type=\"hidden\" name=\"fichier_xml\" value=\"$fichier_editeur\" />");
				}
				# DEBUG -> BEGIN
				if (isset($_SESSION["debug"]))
				{
					if (isset($fichier_editeur))
					{Debug::print_variable_data("fichier_xml", $fichier_xml);} // PROBLÈME, CONTENU SUR PLUSIEURS LIGNES (ne plais pas à javascript).
				}
				# DEBUG -> END
			?>
				<div class="ares-group horizontal" style="width: 100%;">
					<table style="width: 100%;">
						<tr>
							<td>
								<label for="nom" class="add-on">Nom de l&rsquo;exp&eacute;rimentation</label>
								<input id="nom" type="text" class="ares-textinput" name="nom" style="width: 40%;" <?php
										if ($fichier_xml)
										{
											echo("value=\"" . htmlentities($fichier_xml->nom) . "\" disabled />\n" .
												 "<input type=\"hidden\" name=\"nom\" value=\"" . htmlentities($fichier_xml->nom) . "\" />");
										}
										else
										{echo(" />");}
									?>
							</td>
						</tr>
						<tr>
							<td>
								<input id="experimentation_ouverte" type="checkbox" class="ares-checkbox" name="experimentation_ouverte" value="true" <?php
									if ($fichier_xml)
									{
										if ($fichier_xml->ouverte == "oui")
										{echo("checked");}
									}
								?> />
								<label for="experimentation_ouverte">Ouvrir l&rsquo;exp&eacute;rimentation</label>
							</td>
						</tr>
					</table>
				</div>
				<div class="ares-blockcontainer" style="margin-bottom: 0.5em; margin-top: 0.5em;">
				<span class="caption">Consignes</span>
					<table class="ares-table no-header no-outer-border horizontal-inner-border form-like">
						<tr>
							<td>
								Nombre de tests
							</td>
							<td>
								<input id="nombre_consignes" type="number" min="1" max="100" step="1" name="nombre_tests" <?php
									if ($fichier_xml)
									{echo("value=\"" . $fichier_xml->consignes->nombre_tests . "\"");}
									else
									{echo("value=\"1\"");}
								?> onchange="javascript:change_nombre_consignes(this);" />
							</td>
						</tr>
						<tr>
							<td>
								&Eacute;nonc&eacute;s des consignes<br />
								<small>(une consigne par test)</small>
							</td>
							<td style="width: 85%;">
								<script type="text/javascript">
									change_nombre_consignes = function(input_element)
									{
										var div_consignes = document.getElementById("div_consignes");
										var nombre_consignes_input = input_element.value;
										var nct = parseInt(div_consignes.childNodes.length);
										var nombre_consignes = 0;
										var toRemove = new Array();
										for (var i = 0 ; i < nct ; ++i)
										{
											if (div_consignes.childNodes.item(i).nodeName == 'SPAN')
												nombre_consignes++;
											else
											{
												toRemove.push(i);
											}
										}
										for (var i = 0 ; i < toRemove.length ; ++i)
										{										
											div_consignes.removeChild(div_consignes.childNodes[toRemove.pop()]);
										}
										if (nombre_consignes < nombre_consignes_input)
										{
											for (var i = nombre_consignes ; i < nombre_consignes_input ; ++i)
											{
												var nouvelle_consigne = document.createElement("span");
													var enonce = document.createElement("textarea");
														enonce.setAttribute("class", "ares-textinput");
														enonce.setAttribute("name", "consigne_enonce" + (i + 1));
														enonce.setAttribute("style", "width: 100%; font-size: 70%; resize: vertical;");
														enonce.setAttribute("placeholder", "Laisser vide pour que les tests s’enchaînent.")
													nouvelle_consigne.appendChild(document.createElement("br"));
													nouvelle_consigne.appendChild(enonce);
												div_consignes.appendChild(nouvelle_consigne);
											}
										}
										else
										{
											for (var i = (nombre_consignes - 1) ; i >= nombre_consignes_input ; --i)
											{
												div_consignes.removeChild(div_consignes.childNodes[i]);
											}
										}
									}
								</script>
								<div id="div_consignes">
									<span>
									<?php
										if ($fichier_xml)
										{
											for ($i = 0 ; $i < intval($fichier_xml->consignes->nombre_tests) ; ++$i)
											{
												echo("<textarea class=\"ares-textarea\" name=\"consigne_enonce" . ($i + 1) .
														"\" style=\"width: 100%; font-size: 70%; resize: vertical;\"" .
														" placeholder=\"Laisser vide pour que les tests s&rsquo;encha&icirc;nent.\">" .
														$fichier_xml->consignes->enonce[$i] .
														"</textarea>");
											}
										}
										else
										{
											echo("<textarea class=\"ares-textarea\" name=\"consigne_enonce1\"" . 
												 " placeholder=\"Laisser vide pour que les tests s&rsquo;encha&icirc;nent.\"" .
												 " style=\"width: 100%; font-size: 70%; resize: vertical;\"></textarea>");
										}
									?>
										<!-- Léger bug : taille du premier 'textarea' -->
										<!-- Les 'textarea' créés ne sont jamais supprimés -->
									</span>
								</div>
							</td>
						</tr>
					</table>
				</div>
				<div class="ares-blockcontainer" style="margin-bottom: 0.5em;">
				<span class="caption">Param&egrave;tres de l&rsquo;exp&eacute;rimentation</span>
					<table class="ares-table no-header no-outer-border horizontal-inner-border form-like">
						<tr>
							<script type="text/javascript">
								ajouter_parametre = function()
								{
									var div_parametres = document.getElementById("div_parametres");
									var npt = parseInt(div_parametres.childNodes.length);
									var nombre_parametres = 0;
									var toRemove = new Array();
									for (var i = 0 ; i < npt ; ++i)
									{
										if (div_parametres.childNodes.item(i).nodeName == 'SPAN')
											nombre_parametres++;
										else
										{
											toRemove.push(i);
										}
									}
									var removeTemp = toRemove.pop();
									while (removeTemp != undefined)
									{
										div_parametres.removeChild(div_parametres.childNodes[removeTemp]);
										removeTemp = toRemove.pop();
									}									
									var nouveau_parametre = document.createElement("span");
										var nom = document.createElement("input");
											nom.setAttribute("type", "text");
											nom.setAttribute("class", "ares-textinput");
											nom.setAttribute("name", "parametre_nom" + (nombre_parametres + 1));
											nom.setAttribute("placeholder", "nom");
										var valeur = document.createElement("input");
											valeur.setAttribute("type", "text");
											valeur.setAttribute("class", "ares-textinput");
											valeur.setAttribute("name", "parametre_valeur" + (nombre_parametres + 1));
											valeur.setAttribute("placeholder", "valeur par défaut");
										var type = document.createElement("select");
											type.setAttribute("name", "parametre_type" + (nombre_parametres + 1));
											var option_entier = document.createElement("option");
												option_entier.setAttribute("value", "entier");
												option_entier.innerHTML = "Entier";
											var option_decimal = document.createElement("option");
												option_decimal.setAttribute("value", "decimal");
												option_decimal.innerHTML = "D&eacute;cimal";
											var option_texte = document.createElement("option");
												option_texte.setAttribute("value", "texte");
												option_texte.innerHTML = "Texte";
											type.appendChild(option_entier);
											type.appendChild(option_decimal);
											type.appendChild(option_texte);
										var bouton_supprimer = document.createElement("span");
											bouton_supprimer.setAttribute("class", "ares-button failure xsmall");
											bouton_supprimer.setAttribute("onclick", "javascript:supprimer_parametre(" + (nombre_parametres + 1) + ");");
											bouton_supprimer.innerHTML = "Supprimer";
										nouveau_parametre.appendChild(document.createElement("br"));
										nouveau_parametre.appendChild(nom);
										nouveau_parametre.appendChild(valeur);
										nouveau_parametre.appendChild(type);
										nouveau_parametre.appendChild(bouton_supprimer);
									div_parametres.appendChild(nouveau_parametre);
									document.getElementById("nombre_parametres").value++;
								}
								supprimer_parametre = function(numero)
								{
									var div_parametres = document.getElementById("div_parametres");
									var npt = parseInt(div_parametres.childNodes.length);
									var nombre_parametres = 0;
									var toRemove = new Array();
									for (var i = 0 ; i < npt ; ++i)
									{
										if (div_parametres.childNodes.item(i).nodeName == 'SPAN')
											nombre_parametres++;
										else
										{
											toRemove.push(i);
										}
									}
									var removeTemp = toRemove.pop();
									while (removeTemp != undefined)
									{
										div_parametres.removeChild(div_parametres.childNodes[removeTemp]);
										removeTemp = toRemove.pop();
									}
									div_parametres.removeChild(div_parametres.childNodes[numero - 1]);
									--nombre_parametres;
									for (var i = (numero - 1) ; i < nombre_parametres ; ++i) 
									{
										div_parametres.childNodes[i].childNodes[1].setAttribute("name", "parametre_nom" + (i + 1));
										div_parametres.childNodes[i].childNodes[2].setAttribute("name", "parametre_valeur" + (i + 1));
										div_parametres.childNodes[i].childNodes[4].setAttribute("onclick", "javascript:supprimer_parametre(" + (i + 1) + ");");
									}
									document.getElementById("nombre_parametres").value = nombre_parametres;
								}
							</script>
							<input type="hidden" id="nombre_parametres" name="nombre_parametres" <?php
								if ($fichier_xml)
								{echo("value=\"" . $fichier_xml->parametres->nombre . "\"");}
								else
								{echo("value=\"1\"");}
							?> />
							<td style="border-bottom: none;">
								<span class="ares-button" onclick="javascript:ajouter_parametre();" style="text-align: left;">Ajouter un param&egrave;tre</span>
							</td>
						</tr>
						<tr>
							<td id="div_parametres">
							<?php
								if ($fichier_xml)
								{
									echo(
										"<span>" .
											"<input type=\"text\" class=\"ares-textinput\" name=\"parametre_nom1\" placeholder=\"nom\"" .
											"		value=\"" . $fichier_xml->parametres->parametre[0]->nom . "\" />" .
											"<input type=\"text\" class=\"ares-textinput\" name=\"parametre_valeur1\" placeholder=\"valeur par défaut\"" .
											"		value=\"" . $fichier_xml->parametres->parametre[0]->valeur . "\" />" .
											"<select name=\"parametre_type$i\">" .
												"<option value=\"entier\"" . ($fichier_xml->parametres->parametre[0]->type == "entier" ? "selected" : "") . ">Entier</option>" .
												"<option value=\"decimal\"" . ($fichier_xml->parametres->parametre[0]->type == "decimal" ? "selected" : "") . ">D&eacute;cimal</option>" .
												"<option value=\"texte\"" . ($fichier_xml->parametres->parametre[0]->type == "texte" ? "selected" : "") . ">Texte</option>" .
											"</select>" .
										"</span>"
									);
									for ($i = 1 ; $i < count($fichier_xml->parametres->parametre) ; ++$i)
									{
										echo(
											"<span>" .
												"<input type=\"text\" class=\"ares-textinput\" name=\"parametre_nom" . ($i + 1) . "\" placeholder=\"nom\"" .
												"		value=\"" . $fichier_xml->parametres->parametre[$i]->nom . "\" />" .
												"<input type=\"text\" class=\"ares-textinput\" name=\"parametre_valeur" . ($i + 1) . "\" placeholder=\"valeur par défaut\"" .
												"		value=\"" . $fichier_xml->parametres->parametre[$i]->valeur . "\" />" .
												"<select name=\"parametre_type$i\">" .
													"<option value=\"entier\"" . ($fichier_xml->parametres->parametre[$i]->type == "entier" ? "selected" : "") . ">Entier</option>" .
													"<option value=\"decimal\"" . ($fichier_xml->parametres->parametre[$i]->type == "decimal" ? "selected" : "") . ">D&eacute;cimal</option>" .
													"<option value=\"texte\"" . ($fichier_xml->parametres->parametre[$i]->type == "texte" ? "selected" : "") . ">Texte</option>" .
												"</select>" .
												"<span class=\"ares-button failure xsmall\" onclick=\"javascript:supprimer_parametre(" . ($i + 1) . ");\">Supprimer</span>" .
											"</span>"
										);
									}
								}
								else
								{
									echo(
										"<span>" .
											"<input type=\"text\" class=\"ares-textinput\" name=\"parametre_nom1\" placeholder=\"nom\" />" .
											"<input type=\"text\" class=\"ares-textinput\" name=\"parametre_valeur1\" placeholder=\"valeur par défaut\" />" .
											"<select name=\"parametre_type1\">" .
												"<option value=\"entier\">Entier</option>" .
												"<option value=\"decimal\">D&eacute;cimal</option>" .
												"<option value=\"texte\">Texte</option>" .
											"</select>" .
										"</span>"
									);
								}
							?>
								
							</td>
						</tr>
					</table>
				</div>
				<div class="ares-blockcontainer" style="margin-bottom: 0.5em">
				<span class="caption">D&eacute;roulement de l&rsquo;exp&eacute;rimentation</span>
					<table class="ares-table no-header no-outer-border horizontal-inner-border form-like">
						<tr>
							<td>
								Type de stimulus
							</td>
							<td>
								<script type="text/javascript">
									cocher_autre = function()
									{
										document.getElementById("type_stimulus1").checked = false;
										document.getElementById("type_stimulus2").checked = false;
										document.getElementById("type_stimulus3").checked = false;
										document.getElementById("type_stimulus4").checked = false;
										autre_coche();
									}
									decocher_autre = function()
									{
										document.getElementById("type_stimulus5").checked = false;
										autre_decoche();
									}
								</script>
								<input id="type_stimulus1" type="checkbox" class="ares-checkbox" name="typestimulus_lettres" value="true" onclick="javascript:decocher_autre();" <?php
										if ($fichier_xml)
										{
											for ($i = 0 ; $i < count($fichier_xml->deroulement->type_stimulus->type) ; ++$i)
											{
												if ($fichier_xml->deroulement->type_stimulus->type[$i] == "lettres")
												{echo("checked");}
											}
										}
										else
										{echo("checked");}
								?> >
								<label for="type_stimulus1">Grille de lettres al&eacute;atoires</label>
								<br />
								<input id="type_stimulus2" type="checkbox" class="ares-checkbox" name="typestimulus_mots" value="true" onclick="javascript:decocher_autre();" <?php
										if ($fichier_xml)
										{
											for ($i = 0 ; $i < count($fichier_xml->deroulement->type_stimulus->type) ; ++$i)
											{
												if ($fichier_xml->deroulement->type_stimulus->type[$i] == "mots")
												{echo("checked");}
											}
										}
								?>>
								<label for="type_stimulus2">Grille de mots al&eacute;atoires :</label>
								<br />
								<textarea 	id="liste_mots" type="text" class="ares-textinput" name="typestimulus_listemots"
											placeholder="les mots doivent &ecirc;tre s&eacute;par&eacute;s par des virgules"
											style="margin-left: 2em; width: 100%; height: 5em;"><?php
												if ($fichier_xml)
												{
													for ($i = 0 ; $i < count($fichier_xml->deroulement->type_stimulus->type) ; ++$i)
													{
														if ($fichier_xml->deroulement->type_stimulus->type[$i] == "mots")
														{
															echo($fichier_xml->deroulement->type_stimulus->liste_mots->mot[0]);
															for ($i = 1 ; $i < count($fichier_xml->deroulement->type_stimulus->liste_mots->mot) ; ++$i)
															{echo("," . $fichier_xml->deroulement->type_stimulus->liste_mots->mot[$i]);}
														}
													}
												}
											?></textarea>
								<br />
								<input id="type_stimulus3" type="checkbox" class="ares-checkbox" name="typestimulus_chiffres" value="true" onclick="javascript:decocher_autre();" <?php
										if ($fichier_xml)
										{
											for ($i = 0 ; $i < count($fichier_xml->deroulement->type_stimulus->type) ; ++$i)
											{
												if ($fichier_xml->deroulement->type_stimulus->type[$i] == "chiffres")
												{echo("checked");}
											}
										}
								?>>
								<label for="type_stimulus3">Grille de chiffres al&eacute;atoires</label>
								<br />
								<input id="type_stimulus4" type="checkbox" class="ares-checkbox" name="typestimulus_couleurs" value="true" onclick="javascript:decocher_autre();" <?php
										if ($fichier_xml)
										{
											for ($i = 0 ; $i < count($fichier_xml->deroulement->type_stimulus->type) ; ++$i)
											{
												if ($fichier_xml->deroulement->type_stimulus->type[$i] == "couleurs")
												{echo("checked");}
											}
										}
								?>>
								<label for="type_stimulus4">Grille de couleurs al&eacute;atoires</label>
								<br />
								<input id="type_stimulus5" type="checkbox" class="ares-checkbox" name="typestimulus_autre" value="true" onclick="javascript:cocher_autre();" <?php
										if ($fichier_xml && ($fichier_xml->deroulement->type_stimulus->type == "autre"))
										{echo("checked");}
								?>>
								<label for="type_stimulus5">Autre :</label>
								<p style="line-height: 1.2em; font-size: 0.75em;">
									Afin d&rsquo;utiliser les images dans le code du stimulus, il est important de pr&eacute;fixer
									les noms de ces derni&egrave;res par &quot;images/&quot;.
								</p>
								<script type="text/javascript">
									ajouter_image = function()
									{
										var div_images = document.getElementById("div_images");
										var nit = parseInt(div_images.childNodes.length);
										var nombre_images = 0;
										var toRemove = new Array();
										for (var i = 0 ; i < nit ; ++i)
										{
											if (div_images.childNodes.item(i).nodeName == 'SPAN')
												nombre_images++;
											else
											{
												toRemove.push(i);
											}
										}
										var removeTemp = toRemove.pop();
										while (removeTemp != undefined)
										{
											div_images.removeChild(div_images.childNodes[removeTemp]);
											removeTemp = toRemove.pop();
										}
										var span = document.createElement("span");
										var nouvelle_image = document.createElement("input");
											nouvelle_image.setAttribute("type", "file");
											nouvelle_image.setAttribute("name", "fichier_son" + (nombre_images + 1));
										var bouton_supprimer = document.createElement("span");
											bouton_supprimer.setAttribute("class", "ares-button failure xsmall");
											bouton_supprimer.setAttribute("onclick", "javascript:supprimer_image(" + (nombre_images + 1) + ");");
											bouton_supprimer.innerHTML = "Supprimer";
										span.appendChild(document.createElement("br"));
										span.appendChild(nouvelle_image);
										span.appendChild(bouton_supprimer);
										div_images.appendChild(span);
									}
									supprimer_image = function(numero)
									{
										var div_images = document.getElementById("div_images");
										var nit = parseInt(div_images.childNodes.length);
										var nombre_images = 0;
										var toRemove = new Array();
										for (var i = 0 ; i < nit ; ++i)
										{
											if (div_images.childNodes.item(i).nodeName == 'SPAN')
												nombre_images++;
											else
											{
												toRemove.push(i);
											}
										}
										var removeTemp = toRemove.pop();
										while (removeTemp != undefined)
										{
											div_images.removeChild(div_images.childNodes[removeTemp]);
											removeTemp = toRemove.pop();
										}
										div_images.removeChild(div_images.childNodes[numero - 1]);
										--nombre_images;
										for (var i = (numero - 1) ; i < nombre_images ; ++i) 
										{											
											div_images.childNodes[i].childNodes[1].setAttribute("name", "fichier_image" + (i + 1));
											div_images.childNodes[i].childNodes[2].setAttribute("onclick", "javascript:supprimer_image(" + (i + 1) + ");");
										}
									}
								</script>
								<textarea class="ares-textarea" name="typestimulus_autrecode" style="text-indent: left; min-width: 50em; min-height: 7em;"><?php
										if ($fichier_xml)
										{
											if ($fichier_xml->deroulement->type_stimulus->type == "autre")
											{
												echo(preg_replace(	array("/%lt%/",	"/%gt%/",	"/%quote%/",	"/%quotemark%/",	"/%end_line%/",	"/%tab%/"),
																	array("<",		">",		"'",			"\"",				"\n",			"\t"),
																	$fichier_xml->deroulement->type_stimulus->code[0]["value"]
																	)
												);
											}
											else
											{
												echo(
													/*affichage_stimulus = function()\n
													{\n*/
													"// code javascript de l&rsquo;affichage du stimulus ici...\n" .
													"// return (\n" .
													"           Array(/* objet HTML contenant le stimulus */,\n" .
													"                 /* facultatif : objet HTML contenant le fond fixe du stimulus */)\n" .
													"          );\n"
													/*}*/
												);
											}
										}
										else
										{
											echo(
												/*affichage_stimulus = function()\n
												{\n*/
												"// code javascript de l&rsquo;affichage du stimulus ici...\n" .
												"// return (\n" .
												"           Array(/* objet HTML contenant le stimulus */,\n" .
												"                 /* facultatif : objet HTML contenant le fond fixe du stimulus */)\n" .
												"          );\n"
												/*}*/
											);
										}
									?></textarea>
								<br />
								<span class="ares-button" onclick="ajouter_image();">Ajouter une image</span>
									<div id="div_images">
										<?php
											if ($fichier_xml)
											{
												if (count($fichier_xml->deroulement->type_stimulus->image) > 0)
												{
													echo(
														"<span>" .
														"	<input type=\"file\" name=\"fichier_image1\" value=\"" . $fichier_xml->deroulement->type_stimulus->image[0] . "\" />" .
														"</span>"
													);
													for ($i = 1 ; $i < count($fichier_xml->deroulement->type_stimulus->image) ; ++$i)
													{
														echo(
															"<span>" .
															"	<input type=\"file\" name=\"fichier_image" . ($i + 1) . "\" value=\"" .
																$fichier_xml->deroulement->type_stimulus->image[$i] . "\" />" .
															"</span>"
														);
													}
												}
												else
												{echo("<span><input type=\"file\" name=\"fichier_image1\" /></span>");}
											}
											else
											{echo("<span><input type=\"file\" name=\"fichier_image1\" /></span>");}
										?>
									</div>
							</td>
						</tr>
						<tr>
							<td>
								Dur&eacute;e du stimulus
							</td>
							<td>
							<style type="text/css">
								input[type=number][name^="stimulus_duree"]
								{
									width: 5.5em;
									vertical-align: text-bottom;
								}
								input[type=number][name^="stimulus_duree"]::after
								{
									content: "ms";
									position: relative;
									top: -1.26em;
									right: -3em;
								}
							</style>
								<input id="stimulus_dureefixe" type="radio" class="ares-radio" name="stimulus_typeduree" value="fixe" checked />
								<label for="stimulus_dureefixe">Dur&eacute;e fixe</label>
								<input type="number" name="stimulus_dureefixe" min="15" max="2000" step="5" <?php
									if ($fichier_xml)
									{
										if ($fichier_xml->deroulement->duree_stimulus == "fixe")
										{echo("value=\"" . $fichier_xml->deroulement->duree_stimulus->duree . "\"");}
										else
										{echo("value=\"50\"");}
									}
									else
									{echo("value=\"50\"");}
								?> style="vertical-align: text-bottom; width: 5.5em;" />
								<br />
								<input id="stimulus_dureeinfinie" type="radio" class="ares-radio" name="stimulus_typeduree" value="infinie" />
								<label for="stimulus_dureeinfinie">Dur&eacute;e infinie</label>
								<br />
								<input id="stimulus_dureealeatoire" type="radio" class="ares-radio" name="stimulus_typedyree" value="aleatoire" />
								<label for="stimulus_dureealeatoire">Dur&eacute;e al&eacute;atoire :</label>
								<textarea class="ares-textarea" name="code_dureestimulus" style="min-width: 30em; min-height: 3em;"><?php
									if ($fichier_xml)
									{
										if ($fichier_xml->deroulement->duree_stimulus == "aleatoire")
										{echo($fichier_xml->deroulement->duree_stimulus->code[0]["value"]);}
										else
										{
											echo(
												/*duree_stimulus = function()
												}*/
												"// code s&eacute;lectionnant la dur&eacute;e al&eacute;atoirement.\n" .
												"return (/* dur&eacute;e */);"
												/*}*/
											);
										}
									}
									else
									{
										echo(
											/*duree_stimulus = function()
											}*/
											"// code s&eacute;lectionnant la dur&eacute;e al&eacute;atoirement.\n" .
											"return (/* dur&eacute;e */);"
											/*}*/
										);
									}
								?></textarea>
								<br />
								<input id="stimulus_dureeauchoix" type="radio" class="ares-radio" name="stimulus_typeduree" value="auchoix" />
								<label for="stimulus_dureeauchoix">Dur&eacute;es au choix du sujet :</label>
								<br />
								<script type="text/javascript">
									ajouter_duree = function()
									{
										if (!document.getElementById("stimulus_dureeauchoix").checked)
										{return;}
										var div_durees = document.getElementById("div_durees");
										var ndt = parseInt(div_durees.childNodes.length);
										var nombre_durees = 0;
										var toRemove = new Array();
										for (var i = 0 ; i < ndt ; ++i)
										{
											if (div_durees.childNodes.item(i).nodeName == 'SPAN')
												nombre_durees++;
											else
											{
												toRemove.push(i);
											}
										}
										var removeTemp = toRemove.pop();
										while (removeTemp != undefined)
										{
											div_durees.removeChild(div_durees.childNodes[removeTemp]);
											removeTemp = toRemove.pop();
										}
										var span = document.createElement("span");
											var nouvelle_duree = document.createElement("input");
												nouvelle_duree.setAttribute("type", "number");
												nouvelle_duree.setAttribute("name", "stimulus_dureeauchoix" + (nombre_durees + 1));
												nouvelle_duree.setAttribute("min", "15");
												nouvelle_duree.setAttribute("max", "2000");
												nouvelle_duree.setAttribute("step", "5");
												nouvelle_duree.setAttribute("value", "15");
											var bouton_supprimer = document.createElement("span");
												bouton_supprimer.setAttribute("class", "ares-button xxsmall failure");
												bouton_supprimer.setAttribute("onclick", "javascript:supprimer_duree(" + (nombre_durees + 1) + ");");
												bouton_supprimer.innerHTML = "Supprimer";
											span.appendChild(document.createElement("br"));
											span.appendChild(nouvelle_duree);
											span.appendChild(bouton_supprimer);
										div_durees.appendChild(span);
									}
									supprimer_duree = function(numero)
									{
										var div_durees = document.getElementById("div_durees");
										var ndt = parseInt(div_durees.childNodes.length);
										var nombre_durees = 0;
										var toRemove = new Array();
										for (var i = 0 ; i < ndt ; ++i)
										{
											if (div_durees.childNodes.item(i).nodeName == 'SPAN')
												nombre_durees++;
											else
											{
												toRemove.push(i);
											}
										}
										var removeTemp = toRemove.pop();
										while (removeTemp != undefined)
										{
											div_durees.removeChild(div_durees.childNodes[removeTemp]);
											removeTemp = toRemove.pop();
										}
										div_durees.removeChild(div_durees.childNodes[numero - 1]);
										--nombre_durees;
										for (var i = (numero - 1) ; i < nombre_durees ; ++i) 
										{	
											div_durees.childNodes[i].childNodes[1].setAttribute("name", "stimulus_dureeauchoix" + (i + 1));
											div_durees.childNodes[i].childNodes[2].setAttribute("onclick", "javascript:supprimer_duree(" + (i + 1) + ");");
										}
									}
								</script>
								<input type="button" class="ares-button" value="Ajouter une dur&eacute;e" onclick="javascript:ajouter_duree();" />
								<div id="div_durees" style="position: relative; left: 2em;">
									<span>
										<input type="number" name="stimulus_dureeauchoix1" min="15" max="2000" step="5" value="15" style="vertical-align: text-bottom; width: 5.5em;" />
									</span>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								Taille grille stimulus<br />
								(uniquement si le choix du stimulus<br />
								n&rsquo;est pas &quot;autre&quot;)
							</td>
							<td>
								<input id="stimulus_taillefixe" type="radio" class="ares-radio" name="stimulus_taille" value="fixe" checked />
								<label for="stimulus_taillefixe">Taille fixe :</label>
								<br />
								<table style="position: relative; left: 2em;">
									<tr>
										<td>
											<input type="number" name="stimulus_nbrlignes" min="1" max="10" <?php
												if ($fichier_xml)
												{
													if ($fichier_xml->deroulement->taille_stimulus->type == "fixe")
													{echo("value=\"" . $fichier_xml->deroulement->taille_stimulus->taille->nbrlignes . "\"");}
													else
													{echo("value=\"1\"");}
												}
												else
												{echo("value=\"1\"");}
											?> />
											Nombre de lignes
										</td>
									</tr>
									<tr>
										<td>
											<input type="number" name="stimulus_nbrcolonnes" min="1" max="10" <?php
												if ($fichier_xml)
												{
													if ($fichier_xml->deroulement->taille_stimulus->type == "fixe")
													{echo("value=\"" . $fichier_xml->deroulement->taille_stimulus->taille->nbrcolonnes . "\"");}
													else
													{echo("value=\"3\"");}
												}
												else
												{echo("value=\"3\"");}
											?> />
											Nombre de colonnes
										</td>
									</tr>
								</table>
								<input id="stimulus_tailleauchoix" type="radio" class="ares-radio" name="stimulus_taille" value="auchoix" />
								<label for="stimulus_tailleauchoix">Taille au choix du sujet :</label>
								<br />
								<script type="text/javascript">
									ajouter_taille = function()
									{
										if (!document.getElementById("stimulus_tailleauchoix").checked)
										{return;}
										var div_tailles = document.getElementById("div_tailles");
										var ntt = parseInt(div_tailles.childNodes.length);
										var nombre_tailles = 0;
										var toRemove = new Array();
										for (var i = 0 ; i < ntt ; ++i)
										{
											if (div_tailles.childNodes.item(i).nodeName == 'SPAN')
												nombre_tailles++;
											else
											{
												toRemove.push(i);
											}
										}
										var removeTemp = toRemove.pop();
										while (removeTemp != undefined)
										{
											div_tailles.removeChild(div_tailles.childNodes[removeTemp]);
											removeTemp = toRemove.pop();
										}
										var nouvelle_taille = document.createElement("span");
											var nbrlignes = document.createElement("input");
												nbrlignes.setAttribute("type", "number");
												nbrlignes.setAttribute("name", "choixtaille_nbrlignes" + (nombre_tailles + 1));
												nbrlignes.setAttribute("min", "1");
												nbrlignes.setAttribute("max", "10");
												nbrlignes.setAttribute("value", "1");
											var nbrcolonnes = document.createElement("input");
												nbrcolonnes.setAttribute("type", "number");
												nbrcolonnes.setAttribute("name", "choixtaille_nbrcolonnes" + (nombre_tailles + 1));
												nbrcolonnes.setAttribute("min", "1");
												nbrcolonnes.setAttribute("max", "10");
												nbrcolonnes.setAttribute("value", "1");
											var bouton_supprimer = document.createElement("span");
												bouton_supprimer.setAttribute("class", "ares-button xxsmall failure");
												bouton_supprimer.setAttribute("onclick", "javascript:supprimer_taille(" + (nombre_tailles + 1) + ");");
												bouton_supprimer.innerHTML = "Supprimer";
											nouvelle_taille.appendChild(document.createElement("br"));
											nouvelle_taille.appendChild(nbrlignes);
											nouvelle_taille.appendChild(nbrcolonnes);
											nouvelle_taille.appendChild(bouton_supprimer);
										div_tailles.appendChild(nouvelle_taille);
									}
									supprimer_taille = function(numero)
									{
										var div_tailles = document.getElementById("div_tailles");
										var ntt = parseInt(div_tailles.childNodes.length);
										var nombre_tailles = 0;
										var toRemove = new Array();
										for (var i = 0 ; i < ntt ; ++i)
										{
											if (div_tailles.childNodes.item(i).nodeName == 'SPAN')
												nombre_tailles++;
											else
											{
												toRemove.push(i);
											}
										}
										var removeTemp = toRemove.pop();
										while (removeTemp != undefined)
										{
											div_tailles.removeChild(div_tailles.childNodes[removeTemp]);
											removeTemp = toRemove.pop();
										}
										div_tailles.removeChild(div_tailles.childNodes[numero - 1]);
										--nombre_tailles;
										for (var i = (numero - 1) ; i < nombre_tailles ; ++i) 
										{
											div_tailles.childNodes[i].childNodes[1].setAttribute("name", "choixtaille_nbrlignes" + (i + 1));
											div_tailles.childNodes[i].childNodes[2].setAttribute("name", "choixtaille_nbrcolonnes" + (i + 1));
											div_tailles.childNodes[i].childNodes[3].setAttribute("onclick", "javascript:supprimer_taille(" + (i + 1) + ");");
										}
									}
								</script>
								<input type="button" class="ares-button" value="Ajouter une taille" onclick="javascript:ajouter_taille();" />
								<div id="div_tailles" style="position: relative; left: 2em;">
									<span>
										<input 	type="number"
												name="choixtaille_nbrlignes1"
												min="1" max="10" value="1" /><input type="number" name="choixtaille_nbrcolonnes1" min="1" max="10" value="1" />
									</span>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								Ajout de sons
							</td>
							<td>
								<input id="son1" type="radio" class="ares-radio" name="son_stimulus" value="non" <?php
										if ($fichier_xml)
										{
											if ($fichier_xml->deroulement->son->booleen == "non")
											{echo("checked");}
										}
										else
										{echo("checked");}
									?> />
								<label for="son1">Non</label>
								<br />
								<input id="son2" type="radio" class="ares-radio" name="son_stimulus" value="oui" <?php
										if ($fichier_xml)
										{
											if ($fichier_xml->deroulement->son->booleen == "oui")
											{echo("checked");}
										}
									?> />
								<label for="son2">Oui :</label>
								<br />
								<span style="position: relative; left: 2em;">
									<table>
										<tr>
											<td>
												<script type="text/javascript">
													ajouter_son = function()
													{
														var div_sons = document.getElementById("div_sons");
														var nst = parseInt(div_sons.childNodes.length);
														var nombre_sons = 0;
														var toRemove = new Array();
														for (var i = 0 ; i < nst ; ++i)
														{
															if (div_sons.childNodes.item(i).nodeName == 'SPAN')
																nombre_sons++;
															else
															{
																toRemove.push(i);
															}
														}
														var removeTemp = toRemove.pop();
														while (removeTemp != undefined)
														{
															div_sons.removeChild(div_sons.childNodes[removeTemp]);
															removeTemp = toRemove.pop();
														}
														var span = document.createElement("span");
														var nouveau_son = document.createElement("input");
															nouveau_son.setAttribute("type", "file");
															nouveau_son.setAttribute("name", "fichier_son" + (nombre_sons + 1));
														var bouton_supprimer = document.createElement("span");
															bouton_supprimer.setAttribute("class", "ares-button failure xsmall");
															bouton_supprimer.setAttribute("onclick", "javascript:supprimer_son(" + (nombre_sons + 1) + ");");
															bouton_supprimer.innerHTML = "Supprimer";
														span.appendChild(document.createElement("br"));
														span.appendChild(nouveau_son);
														span.appendChild(bouton_supprimer);
														div_sons.appendChild(span);
													}
													supprimer_son = function(numero)
													{
														var div_sons = document.getElementById("div_sons");
														var nst = parseInt(div_sons.childNodes.length);
														var nombre_sons = 0;
														var toRemove = new Array();
														for (var i = 0 ; i < nst ; ++i)
														{
															if (div_sons.childNodes.item(i).nodeName == 'SPAN')
																nombre_sons++;
															else
															{
																toRemove.push(i);
															}
														}
														var removeTemp = toRemove.pop();
														while (removeTemp != undefined)
														{
															div_sons.removeChild(div_sons.childNodes[removeTemp]);
															removeTemp = toRemove.pop();
														}
														div_sons.removeChild(div_sons.childNodes[numero - 1]);
														--nombre_sons;
														for (var i = (numero - 1) ; i < nombre_sons ; ++i) 
														{
															div_sons.childNodes[i].childNodes[1].setAttribute("name", "fichier_son" + (i + 1));
															div_sons.childNodes[i].childNodes[2].setAttribute("onclick", "javascript:supprimer_son(" + (i + 1) + ");");
														}
													}
												</script>
												<textarea id="code_sons" class="ares-textarea" name="son_code" style="text-indent: left; min-width: 40em; min-height: 12em;"><?php
														if ($fichier_xml)
														{
															if ($fichier_xml->deroulement->son->booleen == "oui")
															{echo($fichier_xml->deroulement->son->code[0]["value"]);}
															else
															{
																echo(
																	/*jeu_son = function()
																	{*/
																	"// code s&eacute;lectionnant le son &agrave; jouer si al&eacute;atoire.\n" .
																	"// attention ! : les num&eacute;ros de sons correspondent aux num&eacute;ros\n" .
																	"//               des fichiers de sons et commencent &agrave; l&rsquo;index 0\n" .
																	"//               (exemple : le premier fichier poss&egrave;de l&rsquo;index 0).\n" .
																	"return\n" .
																	"       ({\n" .
																	"           \"index\" : /* numero du son */,\n" .
																	"           \"decalage\" : /* d&eacute;calage du son */\n" .
																	"       });"
																	/*}*/
																);
															}
														}
														else
														{
															echo(
																/*jeu_son = function()
																{*/
																"// code s&eacute;lectionnant le son &agrave; jouer si al&eacute;atoire.\n" .
																"// attention ! : les num&eacute;ros de sons correspondent aux num&eacute;ros\n" .
																"//               des fichiers de sons et commencent &agrave; l&rsquo;index 0\n" .
																"//               (exemple : le premier fichier poss&egrave;de l&rsquo;index 0).\n" .
																"return\n" .
																"       ({\n" .
																"           \"index\" : /* numero du son */,\n" .
																"           \"decalage\" : /* d&eacute;calage du son */\n" .
																"       });"
																/*}*/
															);
														}
														
													?></textarea>
											</td>
											<td>
												<span class="ares-button" onclick="ajouter_son();">Ajouter un son</span>
												<div id="div_sons">
													<?php
														if ($fichier_xml)
														{
															if ($fichier_xml->deroulement->son->booleen == "oui")
															{
																echo(
																	"<span>" .
																	"	<input type=\"file\" name=\"fichier_son1\" value=\"" . $fichier_xml->deroulement->son->fichier[0] . "\" />" .
																	"</span>"
																);
																for ($i = 1 ; $i < count($fichier_xml->deroulement->son->fichier) ; ++$i)
																{
																	echo(
																		"<span>" .
																		"	<input type=\"file\" name=\"fichier_son" . ($i + 1) . "\" value=\"" .
																			$fichier_xml->deroulement->son->fichier[$i] . "\" />" .
																		"</span>"
																	);
																}
															}
															else
															{echo("<span><input type=\"file\" name=\"fichier_son1\" /></span>");}
														}
														else
														{echo("<span><input type=\"file\" name=\"fichier_son1\" /></span>");}
													?>
												</div>
											</td>
									</table>
								</span>
							</td>
						</tr>
					</table>
				</div>
				<div class="ares-blockcontainer" style="margin-bottom: 0.5em">
				<span class="caption">R&eacute;ponse(s) du sujet</span>
					<table class="ares-table no-header no-outer-border horizontal-inner-border form-like">
						<tr>
							<td>
								Zones de texte<br />
								<small><small>(bouton automatiquement ajout&eacute;)</small></small>
							</td>
							<script type="text/javascript"> /* Désactiver "taille_grille" si "autre" est coché */
								autre_coche = function()
								{
									var input = document.getElementById("typeinputs_taillegrille");
									if (input.checked)
									{document.getElementById("typeinputs_aucun").setAttribute("checked", "");}
									input.setAttribute("disabled", "");
								}
								autre_decoche = function()
								{
									var input = document.getElementById("typeinputs_taillegrille")
									input.removeAttribute("disabled");
									input.removeAttribute("title");
								}
							</script>
							<style type="text/css" media="screen">
								input[type="checkbox"][disabled] + label,
								input[type="radio"][disabled] + label,
								input[type="checkbox"][disabled],
								input[type="radio"][disabled]
								{
									cursor: not-allowed;
									color: gray;
								}
							</style>
							<td>
								<input id="typeinputs_aucun" type="radio" class="ares-radio" name="type_input" value="aucun" <?php
									if ($fichier_xml)
									{
										if ($fichier_xml->reponses->zones_texte->type == "aucun")
										{echo("checked");}
									}
									else
									{echo("checked");}
								?> />
								<label for="typeinputs_aucun">Aucune</label>
								<br />
								<input id="typeinputs_fixe" type="radio" class="ares-radio" name="type_input" value="fixe" <?php
									if ($fichier_xml)
									{
										if ($fichier_xml->reponses->zones_texte->type == "fixe")
										{echo("checked");}
									}
								?> />
								<label for="typeinputs_fixe">Fixe : </label>
								<input type="number" name="nombre_inputs" min="1" max="10" step="1" <?php
									if ($fichier_xml)
									{
										if ($fichier_xml->reponses->zones_texte->type == "fixe")
										{echo("value=\"" . $fichier_xml->reponses->zones_texte->nombre . "\"");}
										else
										{echo("value=\"1\"");}
									}
									else
									{echo("value=\"1\"");}
								?> />
									<small>(chaque zone de texte ne comporte qu&rsquo;une seule ligne)</small>
								<br />
								<input id="typeinputs_taillegrille" type="radio" class="ares-radio" name="type_input" value="taille_grille" <?php
									if ($fichier_xml)
									{
										if ($fichier_xml->reponses->zones_texte->type = "taille_grille")
										{echo("checked");}
									}
								?> />
								<label for="typeinputs_taillegrille">
									D&eacute;pendant de la taille de la grille
									<small>(une zone de texte par ligne)</small>
								</label>
							</td>
						</tr>
						<tr>
							<td>
								Touche du clavier
							</td>
							<td>
								<input id="touche" type="checkbox" class="ares-checkbox" name="touche" value="true" <?php
									if ($fichier_xml)
									{
										if ($fichier_xml->reponses->touche != "aucune")
										{echo("checked");}
									}
								?> />
								<label for="touche">Activer</label>
								<select name="touche_choix">
									<option disabled>Touches fl&eacute;ch&eacute;es</option>
										<option value="KEY_DOWN">KEY_DOWN</option>
										<option value="KEY_UP">KEY_UP</option>
										<option value="KEY_LEFT">KEY_LEFT</option>
										<option value="KEY_RIGHT">KEY_RIGHT</option>
									
									<option disabled>Touches sp&eacute;ciales</option>
										<option value="KEY_END">KEY_END</option>
										<option value="KEY_BEGIN">KEY_BEGIN</option>
										<option value="KEY_BACK_TAB">KEY_BACK_TAB</option>
										<option value="KEY_TAB">KEY_TAB</option>
										<option value="KEY_SH_TAB">KEY_SH_TAB</option>
										<option value="KEY_ENTER">KEY_ENTER</option>
										<option value="KEY_ESC">KEY_ESC</option>
										<option value="KEY_SPACE">KEY_SPACE</option>
										<option value="KEY_DEL">KEY_DEL</option>
									
									<option disabled>Touches alphab&eacute;tiques</option>
										<option value="KEY_A">KEY_A</option>
										<option value="KEY_B">KEY_B</option>
										<option value="KEY_C">KEY_C</option>
										<option value="KEY_D">KEY_D</option>
										<option value="KEY_E">KEY_E</option>
										<option value="KEY_F">KEY_F</option>
										<option value="KEY_G">KEY_G</option>
										<option value="KEY_H">KEY_H</option>
										<option value="KEY_I">KEY_I</option>
										<option value="KEY_J">KEY_J</option>
										<option value="KEY_K">KEY_K</option>
										<option value="KEY_L">KEY_L</option>
										<option value="KEY_M">KEY_M</option>
										<option value="KEY_N">KEY_N</option>
										<option value="KEY_O">KEY_O</option>
										<option value="KEY_P">KEY_P</option>
										<option value="KEY_Q">KEY_Q</option>
										<option value="KEY_R">KEY_R</option>
										<option value="KEY_S">KEY_S</option>
										<option value="KEY_T">KEY_T</option>
										<option value="KEY_U">KEY_U</option>
										<option value="KEY_V">KEY_V</option>
										<option value="KEY_W">KEY_W</option>
										<option value="KEY_X">KEY_X</option>
										<option value="KEY_Y">KEY_Y</option>
										<option value="KEY_Z">KEY_Z</option>
									
									<option disabled>Touches fonctionnelles</option>
										<option value="KEY_PF1">KEY_PF1</option>
										<option value="KEY_PF2">KEY_PF2</option>
										<option value="KEY_PF3">KEY_PF3</option>
										<option value="KEY_PF4">KEY_PF4</option>
										<option value="KEY_PF5">KEY_PF5</option>
										<option value="KEY_PF6">KEY_PF6</option>
										<option value="KEY_PF7">KEY_PF7</option>
										<option value="KEY_PF8">KEY_PF8</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								Bouton de fin
							</td>
							<td>
								<input id="boutonfin_desactiver" type="radio" class="ares-radio" name="bouton_fin" value="desactiver" <?php
									if ($fichier_xml)
									{
										if ($fichier_xml->reponses->bouton_fin == "desactiver")
										{echo("checked");}
									}
									else
									{echo("checked");}
								?> />
								<label for="boutonfin_desactiver">D&eacute;sactiver</label>
								<br />
								<input id="boutonfin_activer" type="radio" class="ares-radio" name="bouton_fin" value="activer" <?php
									if ($fichier_xml)
									{
										if ($fichier_xml->reponses->bouton_fin == "activer")
										{echo("checked");}
									}
								?> />
								<label for="boutonfin_activer">Activer</label>
							</td>
						</tr>
					</table>
				</div>
				<div class="ares-blockcontainer" style="margin-bottom: 0.5em">
				<span class="caption">Correction</span>
					<table>
						<tr>
							<td>Fonction de correction (r&eacute;cup&eacute;ration des r&eacute;sultats) :</td>
						</tr>
						<tr>
							<td>
								<textarea class="ares-textarea" style="min-width: 81em; min-height: 13em;" name="correction"><?php
									if ($fichier_xml)
									{
										$correction = $fichier_xml->correction->code[0]["value"];
										$correction = preg_replace(	array("/%lt%/",	"/%gt%/",	"/%quote%/",	"/%quotemark%/",	"/%end_line%/",	"/%tab%/"),
																	array("<",		">",		"'",			"\"",				"\n",			"\t"),
																	$correction
																	);
										echo($correction);
									}
									else
									{
										echo(
											/*correction = function(reponses)
											{*/
											"Code de correction ici." .
											" &rsquo;reponses&rsquo; correspond au contenu des inputs (s&rsquo;il y en a) et se pr&eacute;sente" .
												" sous la forme d&rsquo;un tableau num&eacute;rique. Si le temps de l&rsquo;essai doit &ecirc;tre r&eacute;cup&eacute;r&eacute;," .
												" il suffit d&rsquo;utiliser la variable &quot;temps_reponse&quot;.\n" .
											"&rsquo;inputs&rsquo; correpond au stimulus et correspond &agrave; un tableau bidimensionnel si le stimulus est une grille" .
												" de caract&egrave;res al&eacute;atoires.\n" .
											"Le stimulus est conserv&eacute; dans la variable &rsquo;stimulus&rsquo; sous la forme d&rsquo;un objet HTML.\n" .
											"Le temps de r&eacute;ponse est automatiquement conserv&eacute;.\n" .
											"return ({\n" .
											"           // tableau associatif contenant les resultats &agrave sauvegarder dans la base de donn&eacute;es\n" .
											"       });"
											/*}*/
										);
									}
								?></textarea>
							</td>
						</tr>
					</table>
				</div>
				<!-- SUBMIT -->
				<input type="submit" class="ares-button primary" value="Enregistrer l&rsquo;exp&eacute;rimentation" />
			</form>
			</fieldset>
		</section>
		<div class="ares-group horizontal" style="margin: 5px; margin-right: 0; position: relative; float: right;">
			<button onclick="javascript:document.location.href = '<?php echo($PHPSELF); ?>?deconnexion';" class="ares-button">D&eacute;connexion</a>
		</div>
	</article>
</body>
 </html>