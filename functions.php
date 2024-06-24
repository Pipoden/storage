<?php
	require_once("config.php");
	
	if($debug_mode)
	{
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
	}
	
	function check_cookies()
	{
		global $DB_user;
		global $DB_location;
		global $DB_password;
		global $DB_structure;
		global $DB_table_user;
		global $DB_table_item;
		global $DB_table_box; 
		if(!isset($_SESSION['name']))
		{
			// on check si il y a des cookies valides
			if(isset($_COOKIE['pipo_name']))
			{
				// check ds la BDD
				$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
				mysqli_select_db($conMySql,$DB_structure);
				
				// securisation des IDs
				$name = mysqli_real_escape_string($conMySql,htmlspecialchars($_COOKIE['pipo_name']));
				$passcrypt = mysqli_real_escape_string($conMySql,htmlspecialchars($_COOKIE['pipo_md5']));

				$namecheck = mysqli_query($conMySql,"SELECT * FROM ".$DB_table_user." where login='$name'");
				$donnees = mysqli_fetch_array($namecheck);
				mysqli_close($conMySql);
				
				// on check si tout va bien
				if( ($donnees != NULL) and ($passcrypt == $donnees['2']))
				{
					// on log l'user via la session
					$_SESSION['name'] = $name;
					$_SESSION['rank'] = $donnees['3'];
				}
			}
		}
	}
	
	function drawNameRank()
	{
		global $folder_images;
		if(isset($_SESSION['name']))
		{
			// Affichage du nom / rank
			echo '<a href="connect.php">'; 
			echo '<SPAN style="position: absolute; top: 10px; left: 20px;font-size: 18px;">';
			echo '<b>'.$_SESSION['name'].'</b>';
			for ($i=0;$i<$_SESSION['rank'];$i++)
			{
				echo '<img src="'.$folder_images.'star.png">';
			}
			echo '</SPAN></a>';
		}else
		{
			echo '<a href="connect.php">'; 
			echo '<SPAN style="position: absolute; top: 20px; left: 20px;font-size: 18px;">';
			echo '<b>Not connected</b>';
			echo '</SPAN></a>';
		}
	}
	
	function checkRank($p_rank)
	{
		// if user rank is lower than $p_rank --> redirected to login page
		$rankValidated = false;
		if($p_rank !=0)
		{
			if($_SESSION['rank'] != null)
			{
				if($_SESSION['rank'] >= $p_rank)
				{
					$rankValidated = true;
				}
			}
		}else
		{
			$rankValidated = true;
		}
		if(!$rankValidated)
		{
			header("Location: connect.php");
			die();
		}
	}
	
	function formatName($p_name)
	{
		// Remplace ou supprime les caracteres speciaux
		// $result = strtolower($p_name);
		
		$result = str_replace("'", "´",$p_name);
		$result = preg_replace('/[^a-zA-Z0-9A-zÀ-ÿ<>\s\/.,;´:\-]/', '_', $result);
		return $result;
	}
	
	function boxModified($p_boxName, $p_date=null)
	{
		// Add or modified time-tag for this box with current date
		// Format example : #mod:20230904 
		$date = date("Ymd");
		if($p_date != null)
			$date = $p_date;
		global $DB_user;
		global $DB_location;
		global $DB_password;
		global $DB_structure;
		global $DB_table_box;
		$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
		mysqli_select_db($conMySql,$DB_structure);
		
		$query = mysqli_query($conMySql,"SELECT * FROM ".$DB_table_box." where name='$p_boxName'");
		$data = mysqli_fetch_array($query);
		
		if($data!=null)
		{
			$desc = $data['data'];
			$search = strpos($desc, "#mod:");
			if($search === false)
			{
				// Create the tag
				$desc = $desc."\n#mod:".$date."\n";
			}else
			{
				$newDesc = substr_replace($desc, "#mod:".$date, $search,13);
				$desc = $newDesc;
			}
			// Saving description
			mysqli_query($conMySql,"UPDATE ".$DB_table_box." SET data='$desc' where name='$p_boxName'");
		}
		mysqli_close($conMySql);
	}
	
	function generateBoxName()
	{
		global $boxGenerateNameLength;
		global $boxGenerationMaxTry;
		global $DB_user;
		global $DB_location;
		global $DB_password;
		global $DB_structure;
		global $DB_table_box; 
		
		$valid = false;
		
		$alphabet = array_merge(range(2, 9),range('A', 'Z'));
		$tries = 0;
		while(!$valid)
		{
			$name = "";
			// generate name
			for($i=0;$i<$boxGenerateNameLength;$i++)
			{
				$name = $name.$alphabet[rand(0,count($alphabet)-1)];
			}
			
			// Checking if name exists

			$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
			mysqli_select_db($conMySql,$DB_structure);
			
			$query = mysqli_query($conMySql,"SELECT * FROM ".$DB_table_box." where name='$name'");
			$donnees = mysqli_fetch_array($query);
			mysqli_close($conMySql);
			if($donnees==null)
			{
				$valid = true;
				return $name;
			}
			$tries += 1;
			if($tries>$boxGenerationMaxTry)
			{
				return "";
			}
		}
	}
?>