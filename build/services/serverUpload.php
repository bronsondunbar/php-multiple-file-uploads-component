<?php

require "../includes/PHPMailerAutoload.php";

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
	$userName = ucwords($userName);

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

	$folderName = $userName;
	$folderName = strtolower($folderName);
	$folderName = str_replace(" ", "-", $folderName);
	$folderName = preg_replace('/[^A-Za-z0-9\-]/', '', $folderName);

	$currentURL = $_SERVER['DOCUMENT_ROOT'];
	$folderPath = $currentURL . $rootFolder . $folderName;

	if (file_exists($folderPath)) {

	  	$newFolder = $folderPath;
	  	$int = 0;
	  	$newPath = $newFolder;

	  	while (is_dir($newPath)) {

	    	$newPath = $newFolder . "-0" . ++$int;

	  	}

	  	mkdir($newPath);

		if ($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_FILES['files'])) {

			$uploadFolder = $newPath;

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

					if (move_uploaded_file($_FILES["files"]["tmp_name"][$i], $uploadFolder . "/" . $_FILES["files"]["name"][$i])) {
						$uploaded++;
			    		$uploadedFiles .= $uploadFolder . "/" . $_FILES["files"]["name"][$i] . "<br />";
			    		$uploadedFiles = str_replace($_SERVER['DOCUMENT_ROOT'], $_SERVER['SERVER_NAME'], $uploadedFiles);
			    	}

				}

		    }

		    $responseData["uploadReport"] = $uploaded . " file(s) have been uploaded. " . $skipped . " file(s) skipped. <br />" . $skippedFiles;

		    $sendEmail = new PHPMailer;

		    $sendEmail->SMTPDebug = 3;

		    $sendEmail->setFrom($userEmail, "File(s) uploaded");
		    $sendEmail->addAddress($yourEmail, "File(s) uploaded");
		    $sendEmail->Subject = "Submission";
		    $sendEmail->Body = "Below is link(s) to the file(s) that have been uploaded. <br />" . $uploadedFiles ;
		    $sendEmail->AltBody = "To view the message, please use an HTML compatible email viewer!";
		    $sendEmail->IsHTML(true);

		    $sendEmail->send();

		} else {

			$responseData["uploadError"] = "<i class='fa fa-times' aria-hidden='true'></i> No files were selected.";

		}

	} else {

	  	mkdir($folderPath);

		if ($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_FILES['files'])) {

			$uploadFolder = $folderPath;

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

					if (move_uploaded_file($_FILES["files"]["tmp_name"][$i], $uploadFolder . "/" . $_FILES["files"]["name"][$i])) {
						$uploaded++;
			    		$uploadedFiles .= $uploadFolder . "/" . $_FILES["files"]["name"][$i] . "<br />";
			    		$uploadedFiles = str_replace($_SERVER['DOCUMENT_ROOT'], $_SERVER['SERVER_NAME'], $uploadedFiles);
			    	}

				}

		    }

		    $responseData["uploadReport"] = $uploaded . " file(s) have been uploaded. " . $skipped . " file(s) skipped. <br />" . $skippedFiles;

		    $sendEmail = new PHPMailer;

		    $sendEmail->SMTPDebug = 3;

		    $sendEmail->setFrom($userEmail, "File(s) uploaded");
		    $sendEmail->addAddress($yourEmail, "File(s) uploaded");
		    $sendEmail->Subject = "Submission";
		    $sendEmail->Body = "Below is link(s) to the file(s) that have been uploaded. <br />" . $uploadedFiles;
		    $sendEmail->AltBody = "To view the message, please use an HTML compatible email viewer!";
		    $sendEmail->IsHTML(true);

		    $sendEmail->send();

		} else {

			$responseData["uploadError"] = "<i class='fa fa-times' aria-hidden='true'></i> No files were selected.";

		}

	}

}

echo json_encode($responseData);

?>