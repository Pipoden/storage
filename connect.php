<?php
session_start(); // On démarre la session AVANT toute chose
require_once("functions.php");
check_cookies();
?>


<html>
<head>
	<link rel="stylesheet" media="screen" type="text/css" title="Design" href="main.css" />

	<?php
	if(!isset($_SESSION['name']))	// non identifié
	{
		if (isset($_POST['name']))	// a posté les ids
		{
			$debugText = "";
			// on check dans la BDD
			$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
			mysqli_select_db($conMySql,$DB_structure);
			
			// securisation des IDs
			$name = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['name']));
			$password = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['pw']));
			$passcrypt = MD5($password);
			$remember = $_POST['remember'];
			
			//echo remember;
			
			$namecheck = mysqli_query($conMySql,"SELECT * FROM ".$DB_table_user." where login='$name'");
			$donnees = mysqli_fetch_array($namecheck);
			if($namecheck != NULL)
			{
				$debugText = "non null";
			}
			mysqli_close($conMySql);
			
			// on check si tout va bien
			if( ($donnees != NULL) and ($passcrypt == $donnees['2']))
			{
				$debugText = "User found";
				// on log l'user vie la session
				$_SESSION['name'] = $name;
				$_SESSION['rank'] = $donnees['3'];
				
				// setup du cookie si la case est cochée
				if (isset($_POST['remember']))
				{
					setcookie('pipo_name', $name, time() + 365*24*3600, null, null, false, true);
					setcookie('pipo_md5', $passcrypt, time() + 365*24*3600, null, null, false, true);
				}
				
				// redirection
				?>
				<meta http-equiv="refresh" content="00; URL=connect.php">
				</head>
				<body>
				<?php
			}
			else
			{
				// redirection vers la page precedente
				?>
				<meta http-equiv="refresh" content="05; URL=index.php">
				</head>
				<body>
				<SPAN style="position: absolute; top: 200px; left: 50px;">
					Erreur d'identification...
					<?php echo $debugText; ?>
				</SPAN>
				<?php
			}
		}
		else	// n'a rien posté
		{
			?>
			</head>
			<body>
			
			<?php
			// formulaire d'identification
			?>
			<SPAN style="position: absolute; top: 200px; left: 50px;">
				<form method="post" action="connect.php" name="inscription">
					<label for="name">Nom de compte:</label><br>
					<input type="text" name="name"><br>
					<label for="pw">Password:</label><br>
					<input type="password" name="pw"><br>
					<input type="checkbox" name="remember">
					<label for="remember">Rester connecté</label><br>
					<input type="submit" />
				</form>
				<a href="inscription.php">S'inscrire</a>
			</SPAN>
			<?php
		}
	}
	else	// l'utilisateur est deja identifié : on affiche la page du compte
	{
		$name2 = $_SESSION['name'];
		?>
		<meta http-equiv="refresh" content="10; URL=index.php">
		</head>
		<body>
		<SPAN style="position: absolute; top: 200px; left: 50px;">
			Vous etes connecté en tant que <b><?php echo $name2; ?></b><br>
			<a href="deconnect.php">Se deconnecter</a>
		</SPAN>
			
		<?php
	}
	?>
</body>
</html>
