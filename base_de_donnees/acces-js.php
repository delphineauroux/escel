<?php
	session_start();
	date_default_timezone_set("Europe/Copenhagen");
	include_once("../inc/debug.inc.php");
	include_once("infos_bdd.inc.php");
	include_once("acces.php");


	if (isset($_POST["xhr"]) && ($_POST["xhr"] == "true"))
	{
		unset($_POST["xhr"]);
		$bdd = new Base_de_donnees($bdd_hote, $bdd_identifiant, $bdd_motdepasse, $bdd_basededonnees);
		$id_essai = $bdd->sql_request("SELECT MAX(id_essai) AS num_essai FROM essais;");
		$id_essai = $id_essai[0]["num_essai"];
		$requete = preg_replace("/%id_essai%/", $id_essai, $_POST["requete"]);
		$requete = stripslashes($requete);
		
		$_SESSION["debug"] = "console";
		if (isset($_SESSION["debug"]))
		{
			echo("window.alert(\"requete = " . $requete . "\");");
		}
		
		$bdd->sql_request($requete);
	}
?>
//<script>
function Base_de_donnees()
{
	this.xhr = (function ()
	{
		var xhr;
		if (window.ActiveXObject)
		{xhr = new ActiveXObject("Microsoft.XMLHTTP");}
		else if (window.XMLHttpRequest)
		{xhr = new XMLHttpRequest();}
		else
		{
			window.alert("Le navigateur est trop ancien. La fonctionnalité de sauvegarde des données n'est pas disponible.\n" +
						 "Merci de bien vouloir utiliser un autre navigateur ou une version plus récente " +
						 ((navigator.userAgent.indexOf("Safari")			!= -1)	? "de Safari"				:
						  (navigator.userAgent.indexOf("Firefox")			!= -1)	? "de Firefox"				:
						  (navigator.userAgent.indexOf("Internet Explorer")	!= -1)	? "de Internet Explorer"	:
						  (navigator.userAgent.indexOf("Opera")				!= -1)	? "de Opera"				:
						  "du vôtre") +
						 ". Ou bien abandonnez tout espoir de participer aux exp&eacute;rimentations en ligne."); 
	    	return; 
		}
		xhr.open("POST", "../base_de_donnees/acces-js.php", false);
		xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhr.onreadystatechange = function()
		{ 
			if(xhr.readyState == 4)
			{eval(xhr.responseText);}
			return xhr.readyState;
		}
		return (xhr);
	})();
	
	this.create_sql_query = function(resultats, parametres)
	{
		var id_experimentation = <?php
			$bdd = new base_de_donnees($bdd_hote, $bdd_identifiant, $bdd_motdepasse, $bdd_basededonnees);
			$result_query = $bdd->sql_request("SELECT id_experimentation FROM experimentations WHERE fichier_source='" . $_SESSION["experimentation_en_cours"] . "';");
			echo($result_query[0]["id_experimentation"]);
		?>;
		var query = new String();
		query += "INSERT INTO essais (id_sujet, <?php echo(isset($_SESSION["identifiant_tp"]) ? "identifiant_tp, " : ""); ?> id_experimentation) VALUES (" +
				 "'<?php echo($_SESSION["connecte"]); ?>', " +
				 "<?php echo(isset($_SESSION["identifiant_tp"]) ? "'" . $_SESSION["identifiant_tp"] . "', " : ""); ?>" +
				 id_experimentation +
				 ");";
		for (var key in resultats)
		{
			query += "INSERT INTO resultats (id_essai, nom, valeur) VALUES (%id_essai%, '" + key + "', ";
			if (typeof resultats[key] != "number")
			{query += "'";}
			query += resultats[key];
			if (typeof resultats[key] != "number")
			{query += "'";}
			query += ");";
		}
		for (var key in parametres)
		{
			query += "INSERT INTO parametres (id_essai, nom, valeur) VALUES (%id_essai%, '" + key + "', ";
			if (typeof parametres[key] != "number")
			{query += "'";}
			query += parametres[key];
			if (typeof parametres[key] != "number")
			{query += "'";}
			query += ");";
		}
		return (query);
	};
	
	this.sql_request = function(requete)
	{this.xhr.send("xhr=true&requete=" + requete);};
}
