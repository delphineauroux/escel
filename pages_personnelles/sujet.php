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
		
		if ($_POST["recherche_idsujet"] == "")
		{
			$bdd = new Base_de_donnees($bdd_hote, $bdd_identifiant, $bdd_motdepasse, $bdd_basededonnees);
			$counter = 0;
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
		}
		else
		{
			$id = $_POST["recherche_idsujet"];
			$bdd = new Base_de_donnees($bdd_hote, $bdd_identifiant, $bdd_motdepasse, $bdd_basededonnees);
			if (count($bdd->sql_request("SELECT id_sujet FROM essais WHERE id_sujet='$id';")) > 0)
			{$_SESSION["connecte"] = $id;}
			else
			{
				echo(
					"
					<div class=\"ares-modal failure attention\">
						<div class=\"header\">
				    		<span class=\"ares-button close\"
				    			  onclick=\"javascript:document.location.href = '../page_accueil__fr.php'\"></span>
				    		<h3 class=\"ares-heading heading\">Identifiant sujet</h3>
				    	</div>
				    	<div class=\"body\">
				    		<p>L&rsquo;identifiant saisi ne correspond &agrave; aucune entr&eacute;e dans la base de donn&eacute;es. Seuls les identifiants de sujets" .
				    			" ayant particip&eacute;(e)s &agrave; au moins une exp&eacute;rimentation sont autoris&eacute;s.</p>
				    	</div>
					</div>
					"
				);
			}
		}
		
	?>
	<header>
		<h1 class="ares-heading">Exp&eacute;rimentation de sciences cognitives en ligne</h1>
		<div class="ares-navbar">
			<a class="brand" href="../page_accueil__fr.php">Accueil</a>
			<div class="nav">
				<a href="#experimentations">Effectuer une exp&eacute;rimentation</a>
			</div>
		</div>
	</header>
	
	<article style="margin-top: 3em;">
		<section>
		<div id="experimentations" class="ares-navbar">
			<a class="brand">Effectuer une exp&eacute;rimentation</a>
			<a href="#" class="ares-button arrow-up" style="float: right;"></a>
		</div>
			<p>
				Pour qu&rsquo;un sujet participe &agrave; une exp&eacute;rimentation, il est n&eacute;cessaire qu&rsquo;il(elle) renseigne deux choses.<br />
				La premi&egrave;re est l&rsquo;identifiant du(de la) scientifique pour lequel(laquelle) le sujet effectue l&rsquo;exp&eacute;rimentation.<br />
				Une fois ce champ renseign&eacute;, la liste des exp&eacute;rimentations disponbiles apparaît.
			</p>
			<form method="post" action="<?php echo($PHPSELF); ?>">
				<input type="hidden" name="liste_experimentations" value="true" />
				<div class="ares-group horizontal">
					<label for="search_idscientifique" class="add-on">Identifiant du(de la) scientifique</label>
					<input id="search_idscientifique" type="text" class="ares-textinput" name="id_scientifique" style="height: 1.6em;" />
					<button class="ares-button small" style="height: 2em;">Afficher la liste</button>
				</div>
			</form>
			<fieldset class="ares-fieldset">
			<legend>Liste des exp&eacute;rimentations</legend>
				<style type="text/css">
					input[type="radio"] + label::before
					{
						position: relative;
						top: -0.25em;
					}
				</style>
				<form method="post" action="../experimentations/experimentation.php">
					<?php
						if ($_POST["liste_experimentations"] == "true")
						{
							$bdd = new Base_de_donnees($bdd_hote, $bdd_identifiant, $bdd_motdepasse, $bdd_basededonnees);
							$list_exp = $bdd->sql_request("	SELECT fichier_source FROM experimentations, scientifiques WHERE experimentations.id_scientifique='" . $_POST["id_scientifique"] . "'" .
																"AND experimentations.ouverte=TRUE" .
																"AND experimentations.id_scientifique = scientifiques.id_scientifique" .
																"AND scientifiques.valide=TRUE;");
							if (count($list_exp) > 0)
							{
								echo("<input type=\"hidden\" name=\"type_participation\" value=\"recherche\" />\n");
								echo("<table style=\"width: 100%; text-align: center;\">\n");
								echo("\t<tr>\n");
								for ($i = 0 ; $i < count($list_exp) ; ++$i)
								{
									echo("\t\t<td>");
									$fichier_exp = pathinfo($list_exp[$i]["fichier_source"]);
									echo("<input id=\"choix_experimentation$i\" type=\"radio\" name=\"choix_experimentation\" value=\"" . $fichier_exp["filename"] . "\" class=\"ares-radio\" />" .
										 "<label for=\"choix_experimentation$i\">" . $fichier_exp["filename"] . "</label>");
									echo("</td>\n");
									if (($i % 3) == 0)
									{echo("\t</tr>\n\t<tr>");}
								}
								echo("\t</tr>\n");
								echo("\t<tr>\n");
								echo("<td colspan=\"" . ((count($list_exp) < 3) ? count($list_exp) : "3") . "\">" .
									 "<input type=\"hidden\" name=\"type_visite\" value=\"sujet\" />" .
									 "<button class=\"ares-button primary\" style=\"width: 100%;\">Effectuer l&rsquo;exp&eacute;rimentation</button></td>");
								echo("\t</tr>\n");
								echo("</table>\n");
							}
							else
							{echo("<p class=\"ares-text warning\">Aucun identifiant de scientifique renseign&eacute;.</p>");}
						}
						else
						{echo("<p class=\"ares-text warning\">Aucun identifiant de scientifique renseign&eacute;.</p>");}
					?>
				</form>
			</fieldset>
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