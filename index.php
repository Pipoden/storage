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
			// On affiche la liste des boites
			$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
			mysqli_select_db($conMySql,$DB_structure);
			$query = mysqli_query($conMySql,"SELECT * FROM ".$DB_table_item);
			$boxes = [];
			
			// From items list --> create box if not exists
			while ($data = mysqli_fetch_array($query))
			{
				if(!in_array(strval($data[4]), $boxes))
				{
					array_push($boxes,strval($data[4]));
					$id = strval($data[4]);
					if($id!=null)
					{
						$query2 = mysqli_query($conMySql,"SELECT * FROM ".$DB_table_box." where name='$id';");
						$data2 = mysqli_fetch_array($query2);
						if($data2 == NULL)
						{
							mysqli_query($conMySql,"INSERT INTO ".$DB_table_box." VALUES(NULL, '$id','','','');");
							boxModified($id);
						}
					}
				}
			}
			// checking if boxes have #mod data
			$query4 = mysqli_query($conMySql,"SELECT * FROM ".$DB_table_box.";");
			while ($data4 = mysqli_fetch_array($query4))
			{
				$boxData = $data4['data'];
				$pos = strpos($boxData, "#mod:");
				if($pos === false)
				{
					// listing the content and modify to the last ojbect added's date
					$boxName = $data4['name'];
					$query5 = mysqli_query($conMySql,"SELECT * FROM ".$DB_table_item." WHERE packageId='$boxName';");
					$lastDate = "20230101";
					while($data5 = mysqli_fetch_array($query5))
					{
						if($data5['date'] > $lastDate) $lastDate = $data5['date'];
					}
					boxModified($boxName, $lastDate);
				}
			}
			
			
			
			// From boxes list --> Display boxes
			echo("<h1>Liste des boites</h1>\n");
			$query3 = mysqli_query($conMySql,"SELECT * FROM ".$DB_table_box);
			$myBoxes = array();
			while ($data3 = mysqli_fetch_array($query3))
			{
				// Retrieve last modification date
				$BoxModDate = "20230101";	// default date
				$boxData = $data3['data'];
				$search = strpos($boxData, "#mod:");
				if($search !== false)
				{
					$sub =  intval(substr($boxData, $search+5, 8));
					if(strlen($sub) == 8)
					{
						
						$BoxModDate = strval($sub);
					}
				}
				$desc = explode("\n", $data3[2])[0];
				
				
				array_push($myBoxes,array('id'=>$data3[0], 'name'=>$data3[1],'desc'=>$desc, 'date'=>$BoxModDate));
			}
			mysqli_close($conMySql);
			
			// Array sorting by 'newer first'
			$key_values = array_column($myBoxes, 'date'); 
			array_multisort($key_values, SORT_DESC, $myBoxes);
			$html = "";
			$listCount = 0;
			foreach ($myBoxes as $key => $val)
			{
				$html .= "<a href='view.php?idx=".$val['id']."'>";
				$html .= "- Boite #".$val['name']." (".substr($val['desc'],0,30)."...)";
				$html .= "</a><br>";
				$listCount += 1;
				if($listCount>=$maxBoxInList) break;
			}
			echo $html;
			
			
			
			
			// Add button for search
			?>
				<br>
				<a href='search.php'><SPAN class='icons' style='width:256px; height:256px;'>4</SPAN></a>
			<?php
			// Add button to create new box
			if($_SESSION['rank'] >= $rankNeeded_add)
			{
				?>
					<a href='newBox.php'><SPAN class='icons' style='width:256px; height:256px;'>{</SPAN></a>
				<?php
			}
		?>
	
		
		
		
	</body>
</html>

























