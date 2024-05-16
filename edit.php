<?php
session_start(); // On démarre la session AVANT toute chose
include("functions.php");
check_cookies();
?>

<?php 
	checkRank($rankNeeded_add);
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
		<h1>Modification d'un objet</h1>
		<?php 
		// On affiche le contenu des boites même sans compte
		// par contre la modification des boites demande un rank
		if (true)
		{
			if(isset($_GET['id']))
			{
				// Connection to the database
				$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
				mysqli_select_db($conMySql,$DB_structure);
				
				$id = mysqli_real_escape_string($conMySql,htmlspecialchars($_GET['id']));
				$namecheck = mysqli_query($conMySql,"SELECT * FROM ".$DB_table_item." WHERE id='$id'");
				$name = "";
				$desc = "";
				$picture = "";
				$box = "";
				while ($donnees = mysqli_fetch_array($namecheck))
				{
					$name = $donnees[1];
					$desc = $donnees[2];
					$picture = $donnees[3];
					$box = $donnees[4];
				}
				mysqli_close($conMySql);
			}
			
			if($id!="")
			{
				echo '<SPAN id="comment" style="position: absolute; top: 150px; left: 5%; width: 95%; height: 25%;">';
				echo '<form action="EditReceive.php" method="post" enctype="multipart/form-data">'."\r\n";
				echo '<input name="id" type="hidden" value="'.$id.'"><br>';
				echo 'Nom de l\'objet:<br>';
				echo '<input name="name" type="text" value="'.$name.'"><br>';
				
				echo 'Description de l\'objet:<br>';
				echo '<textarea name="desc" rows="4" cols="50">'.$desc.'</textarea><br>';
				
				echo 'Photo de l\'objet:<br>';
				echo '<input type="file" name="file" style="height:50px; width:500px"  />'."\r\n";
				echo '<br />'."\r\n";
				
				echo 'Numero de la boite:<br>';
				echo '<input name="box" type="text" value="'.$box.'"><br><br>';
				
				echo '<input type="submit" value="Modifier" style="height:100px; width:500px" />'."\r\n";
				echo '<input id="delButton" type="button" value="Supprimer" style="height:100px; width:250px" /><br>'."\r\n";
				echo '<input id="turn" type="button" value="Tourner l\'image" style="height:100px; width:500px" />'."\r\n";
				// Link to QRCode
				echo '<a href="'.$baseUrl.'qrcodeItem.php?name='.$id.'">QRCode</a>';
				echo '</form>'."\r\n";
				echo '</SPAN>';
				
				?>
				<Script>
					document.getElementById("delButton").addEventListener("click", del);
					function del()
					{
						let delId = <?php echo($id); ?>;
						window.location.href = 'delete.php?id='+delId;
					}
					document.getElementById("turn").addEventListener("click", turn);
					function turn()
					{
						let delId = <?php echo($id); ?>;
						window.location.href = 'EditReceive.php?id='+delId+'&turn=true';
					}
				</SCRIPT>
				<?php
				if($picture != "")
				{
					?>
						<SPAN class="spanObject" style="position: absolute; top: 100px; left: 70%; width: 250px; height: 250px; background-image: url(pictures/<?php echo($picture); ?>)">
						</SPAN>
					<?php
				}
			}
		}
		 
		?>
	
	</body>
</html>

























