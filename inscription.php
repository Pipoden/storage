<?php
session_start(); // On démarre la session AVANT toute chose
require_once("functions.php");
check_cookies();
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	<head>
		<title><?php echo($html_title); ?></title>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link rel="stylesheet" media="screen" type="text/css" title="Design" href="main.css" />
	</head>
	<body>		
		<SPAN style="position: absolute; top: 200px; left: 50px;">
			<form method="post" action="inscriptionSubmit.php" name="inscription">
				<label for="name">Nom de compte:</label><br>
				<input type="text" name="name"><br>
				<label for="pw">Password:</label><br>
				<input type="password" name="pw"><label for="pwc"><br>Password (verfication):<br></label><input type="password" name="pwc"><br>
				<label for="em">E-mail:</label><br>
				<input type="text" name="em"><br>
				<!--label for="emc">E-mail (verification):</label><input type="text" name="emc"><br-->
				<!--img src="nospam.php?name=livreor&strlen=4" alt="anti-flood" /-->
				<input type="submit" />
			</form>
		</SPAN>
		
	</body>
</html>