<?php
session_start(); // On démarre la session AVANT toute chose
require_once("functions.php");
check_cookies();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	<head>
		<title><?php echo($html_title); ?></title>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link rel="stylesheet" media="screen" type="text/css" title="Design" href="./main.css" />

	</head>
	<body onload="refreshCanvas()">
		<?php
		drawNameRank();
		?>
		<script type="text/javascript" src="jquery.min.js"></script>
		<script type="text/javascript" src="qrcode.js"></script>
		<br>
		<input readonly id="text" type="text" value="http://jindo.dev.naver.com/collie" style="width:80%" /><br />
		<div id="qrcode" style="display:none;width:100px; height:100px; margin-top:15px;"></div>

		<canvas id="myCanvas" width="800" height="600"></canvas>
		
		


		<script type="text/javascript">
			
		</script>
		
		<SCRIPT>
			<?php
			// Connection to the database
				$conMySql = mysqli_connect($DB_location, $DB_user, $DB_password);
				mysqli_select_db($conMySql,$DB_structure);
				$name = mysqli_real_escape_string($conMySql,htmlspecialchars($_GET['name']));
				echo ('var BoxUrl = "'.$baseUrl.'view.php?name='.$name.'";');
				echo('var boxName = "'.$name.'";');
			?>
			var canvas = document.getElementById("myCanvas");
			var ctx = canvas.getContext("2d");
			
			// canvas size calculation
			var qrSize = <?php echo($QRCodeFontSize); ?>;
			var space = 8;	// width of the frame line
			var spacing = space*4;
			ctx.font = qrSize+"px boxNumber";
			var textWidth = Math.ceil(ctx.measureText(boxName).width);
			console.log(textWidth);
			document.getElementById("myCanvas").width = qrSize + textWidth + 12*space;
			document.getElementById("myCanvas").height = qrSize + spacing;
			
			// Get // Draw qrcode
			var qrcode = new QRCode(document.getElementById("qrcode"), 
			{
				text: BoxUrl,
				width: qrSize,
				height: qrSize,
				colorDark : "#000000",
				colorLight : "#ffffff",
				correctLevel : QRCode.CorrectLevel.H
			});
			qrcode.makeCode(BoxUrl);
			
			function refreshCanvas()
			{	
				// setup url in input bar
				document.getElementById("text").value = "<?php echo($baseUrl.'view.php?name='.$name);?>";
			
				
				// background
				ctx.fillStyle = "white";
				ctx.fillRect(0, 0, canvas.width, canvas.height);
				
				// Draw borders around QR CODE
				
				ctx.strokeStyle = "#000000";
				ctx.lineWidth = 2;	// avoid blur
				for(var i=0;i<(space);i++)
				{
					ctx.strokeRect(i, i, qrSize+spacing-2*i, qrSize+spacing-2*i);
				}
				// Draw Box name
				ctx.font = qrSize+"px boxNumber";
				ctx.fillStyle = "#000000";
				ctx.fillText(boxName, qrSize + space*5, Math.ceil(qrSize*.95));
				
				// Draw borders around name
				for(var i=0;i<(spacing/4);i++)
				{
					var x = qrSize + (space*3);
					ctx.strokeRect(x+i, i, (x+textWidth-spacing*2.5)-2*i, qrSize+(spacing-2*i));
				}	
				
				// resize canvas
				//ctx.canvas.width  = qrSize;
				//ctx.canvas.height = 200;// + spacing*4;
				
				
				
				
				var qrImg = new Image();
				var qrCanvasData = document.querySelector('canvas').toDataURL();;
				qrImg.src = qrCanvasData;
				
				ctx.drawImage(qrImg, spacing/2, spacing/2, qrSize, qrSize);

				ctx.beginPath();
				//ctx.rect(20, 40, 50, 50);
				//ctx.fillStyle = "#FF0000";
				//ctx.fill();
				ctx.closePath();
			}

		</SCRIPT>
			
		
		
		
	</body>
</html>

























