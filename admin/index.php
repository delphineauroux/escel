<?
	include_once("../base_de_donnees/infos_bdd.inc.php");
	include_once("../base_de_donnees/acces.php");
?>
<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <meta name="description" content="Ce site propose plusieurs exp&eacute;rimentations r&eacute;alisables
                                      soi-m&ecirc;me en ligne, en se basant sur les recherches
                                      effectu&eacute;es en sciences cognitives.">
    <meta name="keywords" content="sciences cognitives, expériences, expérimentations, en ligne">
    <meta name="author" content="Delphine AUROUX">
    <link rel="stylesheet" type="text/css" href="../style.css">

    <title>Exp&eacute;rimentations de sciences cognitives en ligne</title>
</head>
<body class="ares-body">
    <header>
        <h3 class="ares-heading">Validation des chercheurs</h3>
    </header>
    <article>
		<form method="post" action="index.php"> 
	    <?
	    	$bdd = new Base_de_donnees($bdd_hote, $bdd_identifiant, $bdd_motdepasse, $bdd_basededonnees);
			if (!empty($_POST)) {
				foreach ($_POST as $key => $value)
				{
					$requete = "UPDATE scientifiques SET valide=".$value." WHERE id_scientifique='".$key."';";
					$resultat = $bdd->sql_request($requete);
				}
			}
			$resultat = $bdd->sql_request("SELECT id_scientifique, valide FROM scientifiques;");
			if ($resultat)
			{
				echo "	    	<table>\n";
				echo "		    	<tr>\n";
				echo "			    	<th>\n";
				echo "				    	chercheur\n";
				echo "			    	</th>\n";
				echo "			    	<th>\n";
				echo "				    	valide\n";
				echo "			    	</th>\n";
				echo "		    	</tr>\n";
				for ($i = 0 ; $i < count($resultat) ; ++$i)
				{
					$chercheur = $resultat[$i]["id_scientifique"];
					$valide = $resultat[$i]["valide"];
					echo "		    	<tr>\n";
					echo "			    	<td>\n";
					echo "			    		".$chercheur."\n";
					echo "			    	</td>\n";
					echo "			    	<td>\n";
					echo "			    		<input type='radio' class='ares-radio' id='".$chercheur."' name='".$chercheur."' value=1";
					if ($valide) echo " checked";
					echo ">\n";
					echo "			    		<label for='".$chercheur."'>\n";
					echo "			    			oui\n";
					echo "			    		</label>\n";
					echo "			    		<input type='radio' class='ares-radio' name='".$chercheur."' value=0";
					if (!$valide) echo " checked";
					echo ">\n";
					echo "			    		<label for='".$chercheur."'>\n";
					echo "			    			non\n";
					echo "			    		</label>\n";
					echo "			    	</td>\n";
					echo "		    	</tr>\n";
				}
				echo "	    	</table>\n";
			}
		?>
			<input type="submit" class="ares-button primary xsmall" value="Enregistrer">
		</form>
    </article>
</body>
</html>