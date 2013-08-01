<?php

	include_once("infos_bdd.inc.php");
	include_once("acces.php");
	//echo("<pre>");
	//echo("'$bdd_hote'\n'$bdd_identifiant'\n'$bdd_motdepasse'\n'$bdd_basededonnees'");
	//echo("</pre>");
	function install_bdd($bdd_hote, $bdd_identifiant, $bdd_motdepasse, $bdd_basededonnees)
	{
		$bdd = new Base_de_donnees($bdd_hote, $bdd_identifiant, $bdd_motdepasse, $bdd_basededonnees);
		//echo "bdd = " . $bdd;
		$bdd->sql_request(
			"
			CREATE TABLE IF NOT EXISTS scientifiques
			(
				id_scientifique		VARCHAR (40) NOT NULL,
				motdepasse			VARCHAR (40),
				valide				BOOLEAN NOT NULL DEFAULT FALSE,
				
				PRIMARY KEY (id_scientifique)
			);
			INSERT INTO scientifiques (id_scientifique, motdepasse) VALUES ('__etudes__', 'lb%78#-eb@');
			
			CREATE TABLE IF NOT EXISTS experimentations
			(
				id_experimentation	SERIAL,
				id_scientifique		VARCHAR (40) NOT NULL,
				fichier_source		VARCHAR (100) NOT NULL,
				ouverte				BOOLEAN NOT NULL DEFAULT FALSE,
				
				PRIMARY KEY (id_experimentation),
				FOREIGN KEY (id_scientifique) REFERENCES scientifiques(id_scientifique)
			);
			
			CREATE TABLE IF NOT EXISTS essais
			(
				id_essai			SERIAL,
				id_sujet			VARCHAR (40) NOT NULL,
				identifiant_tp		VARCHAR (40),
				id_experimentation	BIGINT UNSIGNED NOT NULL UNIQUE,
				date				TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				
				PRIMARY KEY (id_essai),
				FOREIGN KEY (id_experimentation) REFERENCES experimentations(id_experimentation)
			);
			
			CREATE TABLE IF NOT EXISTS parametres
			(
				id_parametre		SERIAL,
				id_essai			BIGINT UNSIGNED NOT NULL UNIQUE,
				nom					VARCHAR(40),
				valeur				VARCHAR(40),
				
				PRIMARY KEY (id_parametre),
				FOREIGN KEY (id_essai) REFERENCES essais(id_essai)
			);
			
			CREATE TABLE IF NOT EXISTS resultats
			(
			    id_resultats		SERIAL,
			    id_essai			BIGINT UNSIGNED NOT NULL UNIQUE,
			    nom					VARCHAR (40),
			    valeur				VARCHAR (40),
			    
			    PRIMARY KEY (id_resultats),
			    FOREIGN KEY (id_essai) REFERENCES essais(id_essai)
			);
			
			CREATE TABLE IF NOT EXISTS connectes
			(
				id					VARCHAR ( 40 ),
				
				PRIMARY KEY (id)
			);
			
			SET GLOBAL event_scheduler = 'ON';
			
			CREATE EVENT IF NOT EXISTS vider_connectes ON SCHEDULE EVERY 10 MINUTE
			COMMENT 'Suppression des connectes non-supprimes.'
			DO
				DELETE FROM basededonnees.connectes;
			"
		);
	}
	
	function uninstall_bdd($bdd_hote, $bdd_identifiant, $bdd_motdepasse, $bdd_basededonnees)
	{
		$bdd = new Base_de_donnees($bdd_hote, $bdd_identifiant, $bdd_motdepasse, $bdd_basededonnees);
		
		$bdd->sql_request(
			"
			DROP TABLE IF EXISTS resultats, parametres, essais, experimentations, scientifiques, connectes;
			DROP EVENT IF EXISTS vider_connectes;
			"
		);
	}

	//*
		uninstall_bdd($bdd_hote, $bdd_identifiant, $bdd_motdepasse, $bdd_basededonnees);
		install_bdd($bdd_hote, $bdd_identifiant, $bdd_motdepasse, $bdd_basededonnees);
	//*/


?>
