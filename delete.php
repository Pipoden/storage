<?php
session_start(); // On démarre la session AVANT toute chose
include("functions.php");
check_cookies();
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	<head>
		<title>Storage</title>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link rel="stylesheet" media="screen" type="text/css" title="Design" href="./main.css" />

	</head>
	<body>
		<?php 
		// On affiche le contenu des boites même sans compte
		// par contre la modification des boites demande un rank
		if ($_SESSION['rank'] > 1)
		{
			if(isset($_GET['id']))
			{
				// Connection to the database
				$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
				mysqli_select_db($conMySql,$DB_structure);
				
				$id = mysqli_real_escape_string($conMySql,htmlspecialchars($_GET['id']));
				$namecheck = mysqli_query($conMySql,"SELECT * FROM ".$DB_table_item." WHERE id='$id'");
				$name = "";
				$box = "";
				while ($donnees = mysqli_fetch_array($namecheck))
				{
					$name = $donnees[1];
					$box = $donnees[4];
				}
				mysqli_close($conMySql);
				if(isset($_GET['valid']))
				{
					// Connection to the database
					$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
					mysqli_select_db($conMySql,$DB_structure);
					$id = mysqli_real_escape_string($conMySql,htmlspecialchars($_GET['id']));
					mysqli_query($conMySql,"DELETE FROM ".$DB_table_item." WHERE id='$id'");
					mysqli_close($conMySql);
					?>
						<SCRIPT>
							let boxId = <?php echo('"'.$box.'"'); ?>;
							window.location.href = '<?php echo($baseUrl); ?>view.php?id='+boxId;
						</SCRIPT>
					<?php
					
				}else
				{
					echo '<SPAN style="text-align:center;position: absolute; top: 150px; left: 25%; width: 50%; height: 25%;">';
					echo '<h1>Confirmer la suppression de '.$name.'?</h1>';
					echo '<form action="EditReceive.php" method="post" enctype="multipart/form-data">'."\r\n";
					echo '<input id="oui" type="button" value="OUI" style="height:100px; width:200px" />';
					echo '<input id="non" type="button" value="NON" style="height:100px; width:200px" />';
					echo '</form>'."\r\n";
					echo '</SPAN>';
					?>
					<SCRIPT>
						document.getElementById("oui").addEventListener("click", del);
						function del()
						{
							let delId = <?php echo($id); ?>;
							window.location.href = '<?php echo($baseUrl); ?>delete.php?id='+delId+"&valid=true";
						}
						document.getElementById("non").addEventListener("click", quit);
						function quit()
						{
							let delId = <?php echo($id); ?>;
							let boxId = '<?php echo($box); ?>';
							window.location.href = '<?php echo($baseUrl); ?>view.php?id='+boxId;
						}
					</SCRIPT>
					
					<?php
				}

			}
		}
		 
		?>
	
	</body>
</html>

























