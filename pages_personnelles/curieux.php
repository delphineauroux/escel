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
		if ($_POST["curiosite_identifiant"] == "")
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
			$id = $_POST["curiosite_identifiant"];
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
				    		<h3 class=\"ares-heading heading\">Identifiant</h3>
				    	</div>
				    	<div class=\"body\">
				    		<p>L&rsquo;identifiant saisi ne correspond &agrave; aucune entr&eacute;e dans la base de donn&eacute;es. Seuls les identifiants de curieux(ses)" .
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
			<a class="brand">Accueil</a>
			<div class="nav">
				<a href="#selection_experimentation">S&eacute;lection de l&rsquo;exp&eacute;rimentation</a>
			</div>
		</div>
	</header>
	
	<article style="margin-top: 3em;">
		<section>
			<style type="text/css">
				input[type="radio"] + label::before
				{
					position: relative;
					top: -0.25em;
				}
			</style>
			<div id="selection_experimentation" class="ares-navbar">
				<span class="brand">S&eacute;lection de l&rsquo;exp&eacute;rimentation</span>
			</div>
			<form method="post" action="../experimentations/experimentation.php">
				<?php
					$bdd = new Base_de_donnees($bdd_hote, $bdd_identifiant, $bdd_motdepasse, $bdd_basededonnees);
					$list_exp = $bdd->sql_request("SELECT fichier_source FROM experimentations WHERE ouverte=TRUE;");
					echo("<input type=\"hidden\" name=\"type_participation\" value=\"curiosite\" />\n");
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
				?>
			</form>
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