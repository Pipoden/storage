<?php
session_start(); // On démarre la session AVANT toute chose
require_once("functions.php");

setcookie('pipo_name', NULL, time() + 365*24*3600, null, null, false, true);
setcookie('pipo_md5', NULL, time() + 365*24*3600, null, null, false, true);
?>

<html>
	<head>
		<link rel="stylesheet" media="screen" type="text/css" title="Design" href="main.css" />

		<?php
		if ( $_SESSION['name'] != NULL )	// non identifié
		{
			$_SESSION['name'] = NULL;
			$_SESSION['rank'] = 0;
		}
		?>
		<meta http-equiv="refresh" content="00; URL=index.php">
	</head>
	<body>
	</body>
</html>