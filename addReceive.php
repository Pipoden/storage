<?php
session_start(); // On démarre la session AVANT toute chose
include("functions.php");
check_cookies();
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	
		
		
	<head>
	<title><?php echo($html_title); ?></title>
	<?php
		
		//formulaire de telechargment 
		if ($_SESSION['rank'] >= $rankNeeded_add)
		{
			$now = time();
			$uploaddir = $path_upload;
			$uploadfile = "";
			$move = NULL;
			$pictureName = "";
			if($_FILES['oneClic']['name'] == NULL)
			{
				$pictureName = $_FILES['file']['name'];
				$uploadfile = $uploaddir.formatName($now."_". basename($_FILES['file']['name']));
				$move = move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile);
			}else
			{
				$pictureName = $_FILES['oneClic']['name'];
				$uploadfile = $uploaddir.formatName($now."_".basename($_FILES['oneClic']['name']));
				echo("basename: ".$uploadfile."<br>");
				$move = move_uploaded_file($_FILES['oneClic']['tmp_name'], $uploadfile);
			}
			
			
			
			if($move) 
			{
				// resize of the image ( From: https://stackoverflow.com/questions/18805497/php-resize-image-on-upload)
				if(true)
				{
					$maxDim = $uploadedPictureMaxSize;
					$file_name = $uploadfile;
					list($width, $height, $type, $attr) = getimagesize( $file_name );
					if ( $width > $maxDim || $height > $maxDim ) {
						$target_filename = $file_name;
						$ratio = $width/$height;
						if( $ratio > 1) {
							$new_width = $maxDim;
							$new_height = $maxDim/$ratio;
						} else {
							$new_width = $maxDim*$ratio;
							$new_height = $maxDim;
						}
						$src = imagecreatefromstring( file_get_contents( $file_name ) );
						$dst = imagecreatetruecolor( $new_width, $new_height );
						imagecopyresampled( $dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
						imagedestroy( $src );
						imagepng( $dst, $target_filename ); // adjust format as needed
						imagedestroy( $dst );
					}
				}
				// Connection to the database
				$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
				mysqli_select_db($conMySql,$DB_structure);
				
				// securisation des IDs
				$name = $now."_".mysqli_real_escape_string($conMySql,htmlspecialchars($pictureName));
				$name = formatName($name);
				$date = date("Ymd");
				$objName = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['name']));
				$objDesc = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['desc']));
				$objBox = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['box']));
				if($objBox=="") $objBox="UNDEF";
				$objBox = formatName($objBox);
				mysqli_query($conMySql,"INSERT INTO ".$DB_table_item." VALUES(NULL, '$objName','$objDesc','$name','$objBox','$date');");
				mysqli_close($conMySql);
				
				// write modified date in box description
				boxModified($objBox);
				
				echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />';
				if($debug_mode)
				{
					echo '<meta http-equiv = "refresh" content = "10; url = view.php?name='.$objBox.'" />';
				}else
				{
					echo '<meta http-equiv = "refresh" content = "0; url = view.php?name='.$objBox.'" />';
				}
				
				echo '<link rel="stylesheet" media="screen" type="text/css" title="Design" href="main.css" />';
				echo '</head>';
				echo '<body>';
				echo "Le fichier est valide, et a été téléchargé avec succès.";
				
			} else 
			{
				// Add the data without the pictures
				// Connection to the database
				$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
				mysqli_select_db($conMySql,$DB_structure);
				
				// securisation des IDs
				$date = date("Ymd");
				$objName = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['name']));
				$objDesc = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['desc']));
				$objBox = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['box']));
				if($objBox=="") $objBox="UNDEF";
				$objBox = formatName($objBox);
				mysqli_query($conMySql,"INSERT INTO ".$DB_table_item." VALUES(NULL, '$objName','$objDesc','','$objBox','$date');");
				mysqli_close($conMySql);
				// write modified date in box description
				boxModified($objBox);
				echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />';
				if($debug_mode)
				{
					echo '<meta http-equiv = "refresh" content = "10; url = view.php?name='.$objBox.'" />';
				}else
				{
					echo '<meta http-equiv = "refresh" content = "0; url = view.php?name='.$objBox.'" />';
				}
				echo '<link rel="stylesheet" media="screen" type="text/css" title="Design" href="main.css" />';
				echo '</head>';
				echo '<body>';
			}

			/*
			echo 'Sent file: '.$_FILES['file']['name'].'<br>';
			echo 'File size: '.$_FILES['file']['size'].' bytes'.'<br>';
			echo 'File type: '.$_FILES['file']['type'].'<br>';
			echo 'Error ? '.$_FILES['file']['error'].'<br>';
			//*/
		}
	?>
	</body>
</html>
