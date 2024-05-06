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

		<SCRIPT>
		var items = [];
		<?php
		$box_name = "";
		if(isset($_GET['idx'])) // Conversion Box_id -> box_name
		{
			// Connection to the database
			$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
			mysqli_select_db($conMySql,$DB_structure);
			$id = mysqli_real_escape_string($conMySql,htmlspecialchars($_GET['idx']));
			// Conversion Box_id -> box_name
			$query = mysqli_query($conMySql,"SELECT * FROM ".$DB_table_box." WHERE id='$id'");
			while ($data = mysqli_fetch_array($query))
			{
				$box_name = $data['name'];
			}
			mysqli_close($conMySql);
		}else
		{
			if(isset($_GET['id']))
			{
				
				// Connection to the database
				$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
				mysqli_select_db($conMySql,$DB_structure);
				$box_name = mysqli_real_escape_string($conMySql,htmlspecialchars($_GET['id']));
				mysqli_close($conMySql);
			}
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
			$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
			mysqli_select_db($conMySql,$DB_structure);
			echo("var boxId='".$box_name."';\n");
			// Todo : Ajout d'un formulaire "oneclick" pour update une photo
			
			$namecheck = mysqli_query($conMySql,"SELECT * FROM ".$DB_table_item." WHERE packageId='$box_name'");
			while ($donnees = mysqli_fetch_array($namecheck))
			{
				// ajout des items a un tableau JS
				$data_name = str_replace(array("\r\n","\r","\"n"),array("\\r\\n","\\r","\\n"),$donnees[1]);
				$data_desc =  str_replace(array("\r\n","\r","\"n"),array("\\r\\n","\\r","\\n"),$donnees[2]);
				echo("items.push(['".$donnees[0]."','".$data_name."','".$data_desc."','".$donnees[3]."','".$donnees[5]."']);\n");
			}
		}
			
		 
		?>
		</SCRIPT>
		
		<?php
		
			echo('<SPAN style="font-size:100px;display: block;text-align:center;top=50px;width=100%;">');
			echo '<form name="myForm" id="myForm" action="addReceive.php" method="post" enctype="multipart/form-data">'."\r\n";
			echo("<a href='boxView.php?name=".$box_name."'>");
			echo("Boite #".$box_name);
			echo("</a>");
			
			echo '<input name="name" type="hidden" value="Objet Inconnu">';
			echo '<input name="desc" type="hidden" value="">';
			echo '<input name="box" type="hidden" value="'.$box_name.'">';
				
				
			echo '<SPAN class="icons2" style="font-size: 100px ;height:150px; width:150px;">';
			echo '<label for="oneClic">';
			echo 'B';
			echo '</label>';
			echo '<input type="file" id="oneClic" name="oneClic" style="display: none;position:absolute;height:150px; width:150px;" onchange="oneClic()"/>';

			echo '</SPAN>';
			echo '</form>'."\r\n";
			echo('</SPAN>');
		?>
		<DIV id="list" style="position: absolute; top: 150px; left: 0%; width: 100%;">
		</DIV>
		
		<SPAN class="icons2" id="listDisplay" style="position: absolute; top: 10px; left: 90%; width: 10%;height:100px;" onclick="toggleDislay()">=</SPAN>
		<SCRIPT>
			document.getElementById("oneClic").onchange = function()
			{
				document.getElementById("myForm").submit();
			}
		</SCRIPT>
		<SCRIPT>
		//const heightOutput = document.querySelector("#height");
		//const widthOutput = document.querySelector("#width");
		
		var displayAsList = false;
		
		function toggleDislay()
		{
			displayAsList = !displayAsList;
			reportWindowSize();
		}

		function reportWindowSize() 
		{
			//heightOutput.textContent = window.innerHeight;
			//widthOutput.textContent = window.innerWidth;
			
			let list = document.getElementById('list');
			
			list.innerHTML = "";
			
			if(displayAsList)
			{
				let addHtml = "";
				for(let i=0;i<items.length;i++)
				{
					addHtml += "- " + items[i][1] + "<br>";
				}
				addHtml += "<br>";
				addHtml += "<br>";
				addHtml += "<a href='search.php'>";
				addHtml += "<SPAN class='icons' style='width: "+256+"px; height: "+256+"px;'>";
				addHtml += "4";
				addHtml += "</SPAN>";
				addHtml += "</a>";
				addHtml += "<a href='add.php?box="+boxId+"'>";
				addHtml += "<SPAN class='icons' style='width: "+256+"px; height: "+256+"px;'>";
				addHtml += "{";
				addHtml += "</SPAN>";
				list.innerHTML += addHtml;
				
			}else
			{
				//list.innerHTML += window.innerWidth + "x"+window.innerHeight + "<br>";
				// on determine l'agencement des cases du tableaux d'items
				// Case par ligne = 4
				let aimedBoxWidth = 256;
				let itemPerLine = Math.round(window.innerWidth/aimedBoxWidth);
				let margin = 15;	// in pixel
				let boxWidth = (window.innerWidth - (itemPerLine+1)*margin) / itemPerLine;
				for(let i=0;i<items.length;i++)
				{
					let addHtml = "";
					// largeur de la case :
					
					let boxPositionX = Math.round(margin + (i%itemPerLine)*(margin+boxWidth));
					let boxPositionY = Math.round(margin + Math.floor(i/itemPerLine)*(margin+boxWidth));
					addHtml += "<a href='edit.php?id="+items[i][0]+"'>";
					if(items[i][3] == '')
					{
						addHtml += "<SPAN style='position: absolute; top: "+boxPositionY+"px; left: "+boxPositionX+"px; width: "+boxWidth+"px; height: "+boxWidth+"px; border: 2px solid black;'>";
					}else
					{
						addHtml += "<SPAN class='spanObject' style='position: absolute; top: "+boxPositionY+"px; left: "+boxPositionX+"px; width: "+boxWidth+"px; height: "+boxWidth+"px; background-image: url(pictures/"+items[i][3]+");display:block;border: 2px solid black;'>";
					}
					// *** Affichage de l'objet ***
					
					addHtml += items[i][1];
					addHtml += "</SPAN>";
					addHtml += "</a>";
					list.innerHTML += addHtml;
				}		
				// Ajoute une nouvelle ligne, avec les boutons " Rechercher" et "Ajouter"
				let newLinePositionY = (Math.floor(items.length/itemPerLine)+1) * (boxWidth + margin) + margin;
				addHtml = "";
				addHtml += "<a href='search.php'>";
				addHtml += "<SPAN class='icons' style='position: Absolute; top: "+newLinePositionY+"px; left: "+margin+"px; width: "+boxWidth+"px; height: "+boxWidth+"px; border: 2px solid black;'>";
				addHtml += "4";
				addHtml += "</SPAN>";
				addHtml += "</a>";
				addHtml += "<a href='add.php?box="+boxId+"'>";
				addHtml += "<SPAN class='icons' style='position: Absolute; top: "+newLinePositionY+"px; left: "+(margin+boxWidth + margin)+"px; width: "+boxWidth+"px; height: "+boxWidth+"px; border: 2px solid black;'>";
				addHtml += "{";
				addHtml += "</SPAN>";
				list.innerHTML += addHtml;
			}
		}
		reportWindowSize();
		window.onresize = reportWindowSize;

		</SCRIPT>
		
		
		
	</body>
</html>

























