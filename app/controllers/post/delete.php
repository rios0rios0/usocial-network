<?php
require_once "../../../core/session/SessionManagement.php";
require_once "../../../core/routes/RoutesManagement.php";
require_once "../../../core/db/DatabaseConnection.php";
$session = SessionManagement::getInstance();
if ($session->logged()) {
	$conn = DatabaseConnection::getInstance();
	$out = array("error" => false);
	//
	$id_post = isset($_POST["id"]) ? $_POST["id"] : "";
	//
	if ($id_post !== "") {
		$sql = "DELETE P FROM post AS P WHERE P.id = :id_post";
		$query = $conn->prepare($sql);
		$query->execute(array(':id_post' => $id_post));
		if ($query->rowCount() > 0) {
			$out["message"] = "Successful deleted post.";
		} else {
			$out["error"] = true;
			$out["message"] = "Error on deletion.";
		}
	} else {
		$out["error"] = true;
		$out["message"] = "All fields is required.";
	}
	DatabaseConnection::close();
	header("Content-type: application/json");
	echo json_encode($out);
	die();
} else {
	RoutesManagement::redirect("/app/");
}