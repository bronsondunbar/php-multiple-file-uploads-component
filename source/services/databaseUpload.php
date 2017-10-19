<?php

require "../includes/PHPMailerAutoload.php";
require "../includes/db.php";

$captcha = "";
$captchaError = "";
$yourEmail = "your@email.com";

$userName = "";
$userEmail = "";

$rootFolder = "/components/php-multiple-file-uploads/";

$responseData["debug"] = $_FILES["files"];

if(isset($_POST["g-recaptcha-response"])){

	$captcha=$_POST["g-recaptcha-response"];

}

if (empty($_POST["name"])) {

	$responseData["nameError"] = "<i class='fa fa-times' aria-hidden='true'></i> Please type your name.";

} else {

	$userName = trim($_POST["name"]);
	$userName = strtolower($userName);
	$userName = str_replace(" ", "-", $userName);
	$userName = preg_replace('/[^A-Za-z0-9\-]/', '', $userName);

}

if (empty($_POST["email"])) {

	$responseData["emailError"] = "<i class='fa fa-times' aria-hidden='true'></i> Please type your email.";

} else {

	$userEmail = trim($_POST["email"]);

}

$secret = "PRIVATE_KEY";
$ip = $_SERVER['REMOTE_ADDR'];
$response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$captcha."&remoteip=".$ip);
$responseKeys = json_decode($response,true);

if(intval($responseKeys["success"]) !== 1) {

	$responseData["captchaError"] = "<i class='fa fa-times' aria-hidden='true'></i> Please verify that you are human.";

} else {

	if ($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_FILES['files'])) {

		$extensions = array("image/jpeg", "image/jpg", "image/png");
	  	$max_size = 1024*5000;

	  	$uploaded = 0;
		$skippedFiles = "";

		$count = count($_FILES["files"]["name"]);

		for ($i=0; $i < count($_FILES["files"]["name"]); $i++) {

			$skipped = 0;

			if ($_FILES["files"]["size"][$i] >= $max_size) {
	    		$skippedFiles .= $_FILES["files"]["name"][$i] . " Skipped because of file size.";
	    	}

			if (!in_array($_FILES["files"]["type"][$i], $extensions)) {
				$skippedFiles .= $_FILES["files"]["name"][$i] . " Skipped because of file type. <br />";
			}

			if (!in_array($_FILES["files"]["type"][$i], $extensions) || $_FILES["files"]["size"][$i] >= $max_size) {
				$skipped++;
			}

			if ($skipped == 0) {

				$servername = "sql16.flk1.host-h.net";
				$username = "bronscytmw_5";
				$password = "MyZdiZeivJ8";
				$dbname = "bronscytmw_db4";

				$conn = new mysqli($servername, $username, $password, $dbname);

			  	if ($conn->connect_error) {

			    	$responseData["databaseError"] = "<i class='fa fa-times' aria-hidden='true'></i> There has been a database error. Please try again.";

			  	}

				$imageType = $_FILES['files']['type'][$i];
				$imagePath = $_FILES['files']['tmp_name'][$i];
				$imageSize = $_FILES['files']['size'][$i];
			    $imageName = $_FILES['files']['name'][$i];

			    $imageContent = file_get_contents($imagePath);
			    $imageContent = mysqli_real_escape_string($conn, $imageContent);

        		$sql = "INSERT INTO uploads (imageType, image, imageSize, imageName, imageOwner) VALUES ('{$imageType}', '{$imageContent}', '{$imageSize}', '{$imageName}', '{$userName}')";

				$result = $conn->query($sql);

				if ($result > 0) {
					$uploaded++;
				}

			}

	    }

	    $responseData["uploadReport"] = $uploaded . " file(s) have been uploaded. " . $skipped . " file(s) skipped. <br />" . $skippedFiles;

	} else {

		$responseData["uploadError"] = "<i class='fa fa-times' aria-hidden='true'></i> No files were selected.";

	}

}

echo json_encode($responseData);

$conn->close();

?>