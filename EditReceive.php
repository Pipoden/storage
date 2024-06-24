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
	
		if ($_SESSION['rank'] > $rankNeeded_add)
		{
			// *** turn image ***
			if(isset($_GET['turn']))
			{
				// Connection to the database
				$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
				mysqli_select_db($conMySql,$DB_structure);
				
				$id = mysqli_real_escape_string($conMySql,htmlspecialchars($_GET['id']));
				echo("id=".$id);
				$picName = "";
				// search pic id
				$namecheck = mysqli_query($conMySql,"SELECT * FROM ".$DB_table_item." WHERE id='$id'");
				while ($donnees = mysqli_fetch_array($namecheck))
				{
					$picName = $donnees[3];
				}
				mysqli_close($conMySql);
				// Universal
				echo("<br>ext is jpeg<br>");
				$filename = $path_upload.$picName;
				// From : https://www.craiglotter.co.za/2017/10/30/php-jpeg-library-reports-unrecoverable-error-not-a-jpeg-file-starts-with-0x89-0x50-error-solved/
				$image = false;
				$image_data = file_get_contents($filename);
				try {
					  $image = imagecreatefromstring($image_data);
				} catch (Exception $ex) {
					  $image = false;
				}
				if ($image !== false)
				{
					$rotate = imagerotate($image, 90, 0);
					imagejpeg($rotate, $path_upload.'/New_'.$picName);
					unlink($path_upload.$picName);
					rename($path_upload.'/New_'.$picName, $path_upload.$picName);
				}
				
				echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />';
				echo '<meta http-equiv = "refresh" content = "0; url = edit.php?id='.$id.'" />';
				echo '<link rel="stylesheet" media="screen" type="text/css" title="Design" href="main.css" />';
				echo '</head>';
				echo '<body>';
				

			}else
			{
				$now = time();
				$uploaddir = $path_upload;
				
				$uploadfile = $uploaddir.$now."_". basename($_FILES['file']['name']);
				$uploadfile = preg_replace('/[^a-zA-Z0-9_.\/]/', '', $uploadfile);
				
				if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) 
				{
					if(true)
					{
						// resize of the image ( From: https://stackoverflow.com/questions/18805497/php-resize-image-on-upload)
						$maxDim = $uploadedPictureMaxSize;
						$file_name = $uploadfile; // $_FILES['myFile']['tmp_name'];
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
					$picName = $now."_".mysqli_real_escape_string($conMySql,htmlspecialchars($_FILES['file']['name']));
					$picName = preg_replace('/[^a-zA-Z0-9_.\/]/', '', $picName);

					echo($picName); // 1692283202_Capture d’écran 2023-06-29 181500.pngpiposqd
					// securisation des IDs
					$objId = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['id']));
					$objName = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['name']));
					$objDesc = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['desc']));
					$objBox = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['box']));
					
					// Replace special char
					
					$objName = formatName($objName); //preg_replace('/[^a-zA-Z0-9A-z<>\s\/.,;´:\-&éèàêôûîïüâ]/', '_', $objName);
					$objDesc = formatName($objDesc); //preg_replace('/[^a-zA-Z0-9A-z<>\s\/.,;´:\-&éèàêôûîïüâ]/', '_', $objDesc);
					$objBox = formatName($objBox); //preg_replace('/[^a-zA-Z0-9A-z<>\s\/.,;´:\-&éèàêôûîïüâ]/', '_', $objBox);
					
					
					

					mysqli_query($conMySql,"update ".$DB_table_item." SET name='$objName', description='$objDesc', packageId='$objBox', picture='$picName' WHERE id='$objId';");
					mysqli_close($conMySql);
					echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />';
					echo '<meta http-equiv = "refresh" content = "1; url = view.php?id='.$objBox.'" />';
					echo '<link rel="stylesheet" media="screen" type="text/css" title="Design" href="main.css" />';
					echo '</head>';
					echo '<body>';
					
				} else 
				{
					// Add the data without the pictures
					// ajout a la BDD
					// Connection to the database
					$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
					mysqli_select_db($conMySql,$DB_structure);
					
					// securisation des IDs
					$date = date("Ymd");
					$objId = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['id']));
					$objName = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['name']));
					$objDesc = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['desc']));
					$objBox = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['box']));
					
					// Replace special char
					
					$objName = formatName($objName); //preg_replace('/[^a-zA-Z0-9A-z<>\s\/.,;´:\-&éèàêôûîïüâ" *"]/', '_', $objName);
					$objDesc = formatName($objDesc); //preg_replace('/[^a-zA-Z0-9A-z<>\s\/.,;´:\-&éèàêôûîïüâ" *"]/', '_', $objDesc);
					$objBox = formatName($objBox); //preg_replace('/[^a-zA-Z0-9A-z<>\s\/.,;´:\-&éèàêôûîïüâ" *"]/', '_', $objBox);

					mysqli_query($conMySql,"update ".$DB_table_item." SET name='$objName', description='$objDesc', packageId='$objBox' WHERE id='$objId';");
					mysqli_close($conMySql);
					echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />';
					echo '<meta http-equiv = "refresh" content = "1; url = view.php?name='.$objBox.'" />';
					echo '<link rel="stylesheet" media="screen" type="text/css" title="Design" href="main.css" />';
					echo '</head>';
					echo '<body>';
				}
			}
		}
	?>
	</body>
</html>
