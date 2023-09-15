<?php
session_start(); // On démarre la session AVANT toute chose
require_once("functions.php");
check_cookies();
?>

<?php 
	checkRank($rankNeeded_viewBoxList);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	<head>
		<title><?php echo($html_title); ?></title>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link rel="stylesheet" media="screen" type="text/css" title="Design" href="./main.css" />

	</head>
	<body>
		<?php
		drawNameRank();
		?>

		<?php 
			if(isset($_SESSION['rank']) && ($_SESSION['rank']>=$rankNeeded_delBox) )
			{
				$newName = generateBoxName();
				if($newName != "")
				{
					$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
					mysqli_select_db($conMySql,$DB_structure);
					mysqli_query($conMySql,"INSERT INTO ".$DB_table_box." VALUES(NULL, '$newName','','','');");
					mysqli_close($conMySql);
					boxModified($newName);
				}
				
			}
			// Return to index.php
			?>
			<SCRIPT>
				window.location.href = '<?php echo($baseUrl); ?>index.php';
			</SCRIPT>
			
		
		
	</body>
</html>

























