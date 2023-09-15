<?php
session_start(); // On démarre la session AVANT toute chose
include("functions.php");
check_cookies();
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	<head>
		<title><?php echo($html_title) ?></title>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link rel="stylesheet" media="screen" type="text/css" title="Design" href="./main.css" />

	</head>
	<body>
		<?php
			drawNameRank();
		?>

		<SCRIPT>
		<?php 
		if(isset($_GET['box']))
		{
			// Connection to the database
			$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
			mysqli_select_db($conMySql,$DB_structure);
			
			$box_name = mysqli_real_escape_string($conMySql,htmlspecialchars($_GET['box']));
			echo("var boxId=".$box_name.";\n");
			mysqli_close($conMySql);
		}
		 
		?>
		</SCRIPT>
		
		
		<?php 
			echo("<h1>Ajouter un élément à la boite #".$box_name."</h1>\n");
		?>
		
		<?php
			if ($_SESSION['rank'] > 1)
			{
				// Affichage du formulaire
				// edit comment
				echo '<SPAN id="comment" style="position: absolute; top: 150px; left: 5%; width: 90%; height: 25%;">';
				echo '<form name="myForm" id="myForm" action="addReceive.php" method="post" enctype="multipart/form-data">'."\r\n";
				echo 'Nom de l\'objet:<br>';
				echo '<input name="name" type="text" value="Objet Inconnu"><br>';
				
				echo 'Description de l\'objet:<br>';
				echo '<textarea name="desc" rows="4" cols="40">';
				echo '</textarea><br>';
				
				echo 'Photo de l\'objet:<br>';
				echo '<input type="file" name="file" style="height:150px; width:500px"  />'."\r\n";
				echo '<br />'."\r\n";
				
				echo 'Numero de la boite:<br>';
				echo '<input name="box" type="text" value="'.$box_name.'"><br><br>';

				
				echo '<input type="submit" value="Ajouter" style="height:150px; width:500px" />'."<br>\r\n";
				
				
				
				// One click Button, add photo, upload imediatly
				echo '<SPAN class="icons2" style="font-size: 300px ;height:250px; width:450px;">';
				echo '<label for="oneClic">';
				echo 'B';
				echo '</label>';
				echo '<input type="file" id="oneClic" name="oneClic" style="display: none;position:absolute;height:250px; width:450px;align:right;top:100px;right:5%" onchange="oneClic()"/>'."\r\n";
				echo '</SPAN>';
				echo '</form>'."\r\n";
				echo '</SPAN>';
				?>
				<SCRIPT>
					document.getElementById("oneClic").onchange = function()
					{
						document.getElementById("myForm").submit();
					}
				</SCRIPT>
				<?php				
			}else
			{
				// renvoie a la page de login
				echo("<SCRIPT>");
				echo("window.location.href = 'connect.php';");
				echo("</SCRIPT>");
				
			}
			
		?>
		
		
		
		
	</body>
</html>

























