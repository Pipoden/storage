<?php
session_start(); // On démarre la session AVANT toute chose
require_once("functions.php");
check_cookies();
?>

<html>
	<head>
		<link rel="stylesheet" media="screen" type="text/css" title="Design" href="main.css" />
		<meta http-equiv="refresh" content="10; URL=index.php">
	</head>
	<body>
		<SPAN style="position: absolute; top: 200px; left: 50px;">
			<?php
			// *******************************
			// on note la date et l'adresse IP
			// *******************************
			function get_ip()
			{ 
				if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
				{ 
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				} 
				elseif(isset($_SERVER['HTTP_CLIENT_IP']))
				{ 
					$ip = $_SERVER['HTTP_CLIENT_IP'];
				} 
				else
				{ 
					$ip = $_SERVER['REMOTE_ADDR'];
				} 
				return $ip;
			}
			$ip = get_ip();
			$date = date("d-m-Y  H:i");
			// **************************************
			// recuperation des données du formulaire
			// **************************************
			if (isset($_POST['name']) AND isset($_POST['pw']) AND isset($_POST['pwc'])) // Si les variables existent
			{
				if ($_POST['name'] != NULL AND ($_POST['pw'] == $_POST['pwc'])) // Si on a quelque chose à enregistrer
				{
					// D'abord, on se connecte à MySQL
					$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
					mysqli_select_db($conMySql,$DB_structure);
					
					// anti injection SQL
					$name = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['name']));
					$password = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['pw']));
					$passcrypt = MD5($password);
					$email = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['em']));
					$rank = 0;
					
					// on cherche si le nom est deja pris
					echo "Vérification de la disponibilitée du nom <b>";
					echo $name;
					echo "</b>...<br>";
					$namecheck = mysqli_query($conMySql,"SELECT * FROM ".$DB_table_user." where login='$name'");
					mysqli_close($conMySql);
					$wrongname = 0;
					while ($donnees = mysqli_fetch_array($namecheck) )
					{
						$wrongname = 1;
						echo "Le nom <b>";
						echo $donnees['1'];
						echo "</b> est deja utilisé, merci de choisir un autre nom...<br>";
					}
					if ($wrongname == 0 )
					{
						echo "Le nom <b>";
						echo $name;
						echo "</b> est disponible, création du compte...<br>";
						// on entre les données ds la BDD 'user'
						$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
						mysqli_select_db($conMySql,$DB_structure);
						mysqli_query($conMySql,"INSERT INTO ".$DB_table_user." VALUES(NULL, '$name', '$passcrypt', '1', '$email', '$ip')");
						mysqli_close($conMySql);		
						echo "Votre compte a bien été créé.<br>Bienvenue sur notre serveur !";
						
					}
					//echo $namecheck;
				}
				else
				{
					echo "Erreur dans le remplissage de votre formulaire...";
				}
			}	
			?>
		</SPAN>
	</body>
</html>
