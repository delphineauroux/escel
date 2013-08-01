<?php
	final class Base_de_donnees extends mysqli
	{
		public function __construct($hote /* string */, $identifiant /* string */, $motdepasse /* string */, $basededonnees /* string */)
		{
			parent::__construct($hote, $identifiant, $motdepasse, $basededonnees);
			
			if (phpversion() >= "5.2.9")
			{
				if ($mysqli->connect_error)
				{die("[MySql] Erreur de connexion (" . $mysqli->connect_errno . ") : " . $mysqli->connect_error);}
			}
			else
			{
				if (mysqli_connect_error())
				{die("[MySql] Erreur de connexion (" . mysqli_connect_errno() . ") : " . mysqli_connect_error());}
			}
		}
		
		public function __destruct()
		{parent::close();}
		
		public function error()
		{return ($mysqli->error);}
		
		public function sql_request($requete /* string */)
		{
			$requetes = explode(";", $requete);
			$requetes_length = count($requetes);
			if (preg_match("#^[ \n\t\r]*$#", $requetes[$requetes_length - 1]))
			{--$requetes_length;}
			$resultats = true;
			for ($i = 0 ; $i < $requetes_length ; ++$i)
			{
				$res = parent::query($requetes[$i] . ";") or die("[MySql] Error : " . $this->error . ".<br /><br />");
				if (gettype($res) != "boolean")
				{
					$resultats = array();
					while ($row = $res->fetch_array(MYSQLI_ASSOC))
					{array_push($resultats, $row);}
				}
			}
			return ($resultats);
		}
	}
	
?>