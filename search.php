<?php
session_start(); // On démarre la session AVANT toute chose
include("functions.php");
check_cookies();
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	<head>
		<title>popup test</title>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link rel="stylesheet" media="screen" type="text/css" title="Design" href="main.css" />
	</head>
	<body>
		<?php
			if(isset($_POST['search']))
			{
				// **********************************************
				// *** On affiche le resultat de la recherche ***
				// **********************************************
				// Connection to the database
				$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
				mysqli_select_db($conMySql,$DB_structure);
				$search = "nope";
				$search = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['search']));
				$search = formatName($search);
				echo("Search= ".$search."<br>");
				// Search form items
				$query = mysqli_query($conMySql,"SELECT * FROM ".$DB_table_item);
				$score = array();
				while($data = mysqli_fetch_array($query))
				{
					// *** Scoring System for result classification ***
					$tempScore = 0;
					// Bonus = premier mot du nom
					if(strpos(formatName($data['name']), $search." ") === 0)
					{
						$tempScore += 6;
					}
					if(strpos(formatName($data['name']), " ".$search." ") !== false)
					{
						$tempScore += 10;
					}else
					{
						if(strpos(formatName($data['name']), " ".$search) !== false)
						{
							$tempScore += 4;
						}else
						{
							if(strpos(formatName($data['name']), $search." ") !== false)
							{
								$tempScore += 4;
							}
						}
					}
					if(strpos(formatName($data['name']), $search) !== false)
					{
						$tempScore += 2;
					}
					
					if(strpos(formatName($data['description']), $search." ") === 0)
					{
						$tempScore += 3;
					}
					if(strpos(formatName($data['description']), " ".$search." ") !== false)
					{
						$tempScore += 5;
					}else
					{
						if(strpos(formatName($data['description']), " ".$search) !== false)
						{
							$tempScore += 2;
						}else
						{
							if(strpos(formatName($data['description']), $search." ") !== false)
							{
								$tempScore += 2;
							}
						}
					}
					if(strpos(formatName($data['description']), $search) !== false)
					{
						$tempScore += 1;
					}
					
					
					if($tempScore > 0)
					{
						array_push($score, array('id'=>$data['id'], 'score'=>$tempScore, 'title'=>$data['name'], 'content'=>$data['description'], 'box'=>$data['packageId']));
					}
				}
				mysqli_close($conMySql);
				
				// classement du tableau de resultats
				$key_values = array_column($score, 'score'); 
				array_multisort($key_values, SORT_DESC, $score);
				
				$html = "";
				foreach ($score as $key => $val)
				{
					$html .= "<a href='view.php?name=".$val['box']."'>";
					$html .= $val['title']." (".substr($val['content'],0,30)."...)";
					$html .= "</a><br>";
					//$html .= "<button onclick='send(".$val['id'].")'>#</button>";
					//$html .= $val['title']." (". substr($val['score'],0,30)."...)<br>";
					//echo $val['id']."-->".$val['score']."<br>";
				}
				echo '<span class="searchResult" style="position:absolute;height:95%;">';
				echo $html;
				echo "</span>";
				
			}else
			{
				echo '<SPAN id="comment" style="position: absolute; top: 150px; left: 5%; width: 90%; height: 25%;">';
				echo '<form name="myForm" id="myForm" action="search.php" method="post" enctype="multipart/form-data">'."\r\n";
				echo 'Recherche:<br>';
				echo '<input name="search" type="text" value="Objet"><br>';

				echo '<input type="submit" value="Rechercher" style="height:150px; width:500px" />'."<br>\r\n";
			}
		?>
		
		
	</body>
</html>
