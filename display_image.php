<?php
// display_image.php
$question_id = $_GET['question_id'];
$image_path = "uploads/" . $question_id . ".jpg"; // Adjust file extension if necessary

if (file_exists($image_path)) {
    header("Content-type: image/jpeg"); // Adjust the content type if using a different image format
    readfile($image_path);
} else {
    // Display an error message or a placeholder if the image does not exist
    header("Content-type: text/plain");
    echo "Image not found.";
}
?>
