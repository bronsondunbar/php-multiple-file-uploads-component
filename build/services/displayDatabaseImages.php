<?php

require "../includes/db.php";

$searchForImages = "SELECT * FROM uploads";
$searchForImagesResults = $conn->query($searchForImages) or die(mysqli_error($conn));

$numberOfImagesFound = mysqli_num_rows($searchForImagesResults);

if ($numberOfImagesFound > 0) {

	$getAllImages = "SELECT * FROM uploads";
	$getAllImagesResults = $conn->query($getAllImages) or die(mysqli_error($conn));

	while($row = mysqli_fetch_array($getAllImagesResults)) {

		$responseData["displaySuccess"] .= "<div class='item'><img class='img-responsive' src='data:" . $row['imageType'] . ";base64," . base64_encode($row['image']) . "'/></div>";

	}

} else {

	$responseData["displayError"] = "<div class='item'>No images to display</div>";

}

echo json_encode($responseData);

?>