<?php
require_once "../../../core/session/SessionManagement.php";
require_once "../../../core/routes/RoutesManagement.php";
require_once "../../../core/db/DatabaseConnection.php";
$session = SessionManagement::getInstance();
if ($session->logged()) {
	$conn = DatabaseConnection::getInstance();
	$out = array("error" => false);
	//
	$html_text = isset($_POST["html_text"]) ? $_POST["html_text"] : "";
	$url_image = isset($_POST["url_image"]) ? $_POST["url_image"] : "";
	//
	if (($html_text !== "") && ($url_image !== "")) {
		$id_user = $session->user->id;
		$html_text = str_replace('"', "'", $html_text);
		$sql = "INSERT INTO post (id_user, html_text, photo) VALUES (:id_user, :html_text, :url_image)";
		$query = $conn->prepare($sql);
		$query->execute(array(':id_user' => $id_user, ':html_text' => $html_text, ':url_image' => $url_image));
		if ($query->rowCount() > 0) {
			$out["message"] = "Successful inserted post.";
		} else {
			$out["error"] = true;
			$out["message"] = "Error on insertion.";
		}
	} else {
		$out["error"] = true;
		$out["message"] = "All fields is required.";
	}
	DatabaseConnection::close();
	RoutesManagement::redirect("/app/controllers/home/index.php");
} else {
	RoutesManagement::redirect("/app/");
}