<?php
session_start(); // On démarre la session AVANT toute chose
include("functions.php");
check_cookies();
?>

<?php 
	checkRank($rankNeeded_view);
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
			drawNameRank();
		?>
		<a href="index.php">
		<SPAN class="icons2" style="position: absolute; top: 20px; left: 20px;">&#60;</SPAN>
		</a>

		<?php
			$box_name = "";
			if(isset($_GET['id'])) // Conversion Box_id -> box_name
			{
				// Connection to the database
				$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
				mysqli_select_db($conMySql,$DB_structure);
				$id = mysqli_real_escape_string($conMySql,htmlspecialchars($_GET['id']));
				// Conversion Box_id -> box_name
				$query = mysqli_query($conMySql,"SELECT * FROM ".$DB_table_box." WHERE id='$id'");
				while ($data = mysqli_fetch_array($query))
				{
					$box_name = $data['name'];
				}
				mysqli_close($conMySql);
			}else
			{
				if(isset($_GET['name']))
				{
					// Connection to the database
					$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
					mysqli_select_db($conMySql,$DB_structure);
					$box_name = mysqli_real_escape_string($conMySql,htmlspecialchars($_GET['name']));
					mysqli_close($conMySql);
				}
			}
			
			if($box_name != "")
			{
				// Connection to the database
				$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
				mysqli_select_db($conMySql,$DB_structure);
				$namecheck = mysqli_query($conMySql,"SELECT * FROM ".$DB_table_box." WHERE name='$box_name'");
				$boxId = "";
				$boxName = "";
				$boxDesc = "";
				$boxPhoto = "";
				while ($donnees = mysqli_fetch_array($namecheck))
				{
					$boxId = $donnees[0];
					$boxName = $donnees[1];
					$boxDesc = $donnees[2];
					$boxPhoto = $donnees[3];
				}
				if($boxId != "")
				{
					// Affichage du formulaire 
					echo '<SPAN id="comment" style="position: absolute; top: 150px; left: 5%; width: 95%; height: 25%;">';
					echo '<form action="BoxReceive.php" method="post" enctype="multipart/form-data">'."\r\n";
					echo '<input name="id" type="hidden" value="'.$boxId.'"><br>';
					echo 'Nom de la boite:<br>';
					echo '<input name="name" type="text" value="'.$boxName.'"><br>';
					
					echo 'Description de la boite:<br>';
					echo '<textarea name="desc" rows="4" cols="50">'.$boxDesc.'</textarea><br>';
					
					echo 'Photo de la boite:<br>';
					echo '<input type="file" name="file" style="height:50px; width:500px"  />'."\r\n";
					echo '<br />'."\r\n";
					
					
					echo '<input type="submit" value="Modifier" style="height:100px; width:500px" />'."\r\n";
					//echo '<input id="delButton" type="button" value="Supprimer" style="height:100px; width:250px" /><br>'."\r\n";
					//echo '<input id="turn" type="button" value="Tourner l\'image" style="height:100px; width:500px" />'."\r\n";
					echo '</form>'."\r\n";
					// Link to QRCode
					echo '<a href="'.$baseUrl.'qrcode.php?name='.$boxName.'">QRCode</a>';
					
					
					echo '</SPAN>';
					
					if($boxPhoto != "")
					{
						echo('<SPAN class="spanObject" style="position: absolute; top: 100px; left: 70%; width: 250px; height: 250px; background-image: url(pictures/'.$boxPhoto.')">');
						echo('</SPAN>');
					}
					
					
					
				}
			}
		?>


		
		
		
	</body>
</html>

























