<?php
session_start();


// Checking if "config.php" exists
$filename = 'config.php';
if (!file_exists($filename))
{
    echo "Please rename the file 'config.php.rename' to 'config.php'<br>";
	echo "and edit the file 'config.php' to match your configuration";
	exit();
}else
{
	echo "<span style='color:#00a000';>OK</span> : The file 'config.php' exists.<br>";
}
require_once("config.php");

// Checking the connection to the database
$newUser = false;
// Connection to the database
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
mysqli_select_db($conMySql,$DB_structure);
printf("<span style='color:#00a000';>OK</span> : Connected to Database = %s\n", mysqli_get_host_info($conMySql));
echo("<br>");

// *** Checking tables / creating them if not exists ***
// USER Table
$query = mysqli_query($conMySql,"SHOW TABLES LIKE '%".$DB_table_user."%';");
$data = mysqli_fetch_array($query);
if($data == null)
{

	echo("Data base USER '".$DB_table_user."' not found...<br>");
	$sql_create = "CREATE TABLE ".$DB_structure.".".$DB_table_user." (`id` INT NOT NULL AUTO_INCREMENT , `login` TEXT , `md5` TEXT, `rank` TEXT, `mail` TEXT, `ip` TEXT, PRIMARY KEY (`id`)); ";
	mysqli_query($conMySql,$sql_create);
	echo("USER table '".$DB_table_user."' created.<br>");
	$passcrypt = MD5("1234");
	mysqli_query($conMySql,"INSERT INTO ".$DB_table_user." VALUES(NULL, 'admin','$passcrypt','5','admin@admin.com','0.0.0.0');");
	echo("Created Administrator account (admin:1234)<br>");
	// setting session
	$_SESSION['name'] = 'admin';
	$_SESSION['rank'] = '5';
}else
{
	echo("<span style='color:#00a000';>OK</span> : The USER table '".$DB_table_user."' exists...<br>");
	// Check of default login ( warning )
	$warning = false;
	$queryAdmin = mysqli_query($conMySql,"SELECT * FROM ".$DB_table_user." WHERE login='admin';");
	$data2 = mysqli_fetch_array($queryAdmin);
	{
		if($data2['md5'] == MD5("1234"))
		{
			$warning = true;
		}
	}
	if($warning)
	{
		echo("<span style='color:#FFa000';>WARNING</span> : The default login/password are still in use. Please change them.<br>");
		// Todo : form to change them !
	}
}
// ITEM Table
$query = mysqli_query($conMySql,"SHOW TABLES LIKE '%".$DB_table_item."%';");
$data = mysqli_fetch_array($query);
if($data == null)
{

	echo("Data base ITEM '".$DB_table_item."' not found...<br>");
	$sql_create = "CREATE TABLE ".$DB_table_item." (id int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL, name text, description text, picture text, packageId text, date text);";
	mysqli_query($conMySql,$sql_create);
	echo("ITEM table '".$DB_table_item."' created.<br>");
}else
{
	echo("<span style='color:#00a000';>OK</span> : The ITEM table '".$DB_table_item."' exists...<br>");
}

// BOX Table
$query = mysqli_query($conMySql,"SHOW TABLES LIKE '%".$DB_table_box."%';");
$data = mysqli_fetch_array($query);
if($data == null)
{

	echo("Data base BOX '".$DB_table_box."' not found...<br>");
	$sql_create = "CREATE TABLE ".$DB_table_box." (id int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL, name text, description text, photo text, data text);";
	mysqli_query($conMySql,$sql_create);
	echo("BOX table '".$DB_table_box."' created.<br>");
}else
{
	echo("<span style='color:#00a000';>OK</span> : The BOX table '".$DB_table_box."' exists...<br>");
}
mysqli_close($conMySql);

// Affichage / modification des utilisateurs

if($_SESSION['rank'] >= 5)
{
	$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
	mysqli_select_db($conMySql,$DB_structure);
	
	// Checking if formulaire was send
	if(ISSET($_POST['id']))
	{
		$id =  mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['id']));
		$login =  mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['login']));
		$password = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['password']));
		$rank = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['rank']));
		$mail = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['mail']));
		$ip = mysqli_real_escape_string($conMySql,htmlspecialchars($_POST['ip']));
		
		// on prevoit de ne pas s'autobloquer
		$old_login = "";
		$query = mysqli_query($conMySql,"SELECT * FROM ".$DB_table_user);
		while($data = mysqli_fetch_array($query))
		{
			if($_SESSION['name'] == $data['login'])
			{
				if($data['id'] == $id)
				{
					$old_login = $data['login'];
				}
			}
		}

		// update login
		mysqli_query($conMySql,"update ".$DB_table_user." SET login='$login' WHERE id='$id';");
		if($old_login != "")
		{
			$_SESSION['name'] = $login;
		}
		// update password
		if($password != "")
		{
			$md5 = MD5($password);
			mysqli_query($conMySql,"update ".$DB_table_user." SET md5='$md5' WHERE id='$id';");
		}
		// update rank
		if($old_login == "")	// can't modify current admin rank
		{
			$rank =  intval($rank);
			if($rank < 0) $rank = 0;
			if($rank > 5) $rank = 5;
			mysqli_query($conMySql,"update ".$DB_table_user." SET rank='$rank' WHERE id='$id';");
		}
		// update mail & ip
		mysqli_query($conMySql,"update ".$DB_table_user." SET mail='$mail' WHERE id='$id';");
		mysqli_query($conMySql,"update ".$DB_table_user." SET ip='$ip' WHERE id='$id';");
		
		
					
		
		
	}
	
	echo("<br><br>");
	echo("Current user : ".$_SESSION['name']." - Rank : ".$_SESSION['rank']."<br>");
	
	
	echo("List of users:<br>");	
	$query = mysqli_query($conMySql,"SELECT * FROM ".$DB_table_user.";");
	while($data = mysqli_fetch_array($query))
	{
		echo '<form name="user_'.$data['id'].'" action="admin.php" method="post" enctype="multipart/form-data">'."\r\n";
		echo '<input name="id" type="hidden" size="15" value="'.$data['id'].'">';
		echo '#'.$data['id'].' - Login: ';
		echo '<input name="login" type="text" size="15" value="'.$data['login'].'">';
		echo ' - Password: ';
		echo '<input name="password" type="text" size="15" value="">';
		echo ' - Rank: ';
		echo '<input name="rank" type="text" size="5" value="'.$data['rank'].'">';
		echo ' - Mail: ';
		echo '<input name="mail" type="text" size="20" value="'.$data['mail'].'">';
		echo ' - IP: ';
		echo '<input name="ip" type="text" size="20" value="'.$data['ip'].'">';
		
		echo '<input type="submit" value="Modify" />'."<br>\r\n";
		echo '</form>';
	}
	mysqli_close($conMySql);
}
?>