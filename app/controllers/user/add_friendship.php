<?php
require_once "../../../core/session/SessionManagement.php";
require_once "../../../core/routes/RoutesManagement.php";
require_once "../../../core/db/DatabaseConnection.php";
$session = SessionManagement::getInstance();
if ($session->logged()) {
	$conn = DatabaseConnection::getInstance();
	$out = array("error" => false);
	//
	$id = isset($_POST["id"]) ? $_POST["id"] : "";
	//
	if ($id !== "") {
		$sql = "INSERT INTO friend (id_user_requested, id_user_accepted) VALUES (:id_requester, :id_accepted)";
		$query = $conn->prepare($sql);
		$query->execute(array(':id_requester' => $session->user->id, ':id_accepted' => $id));
		if ($query->rowCount() > 0) {
			$out["message"] = "Successful invited.";
		} else {
			$out["error"] = true;
			$out["message"] = "Error on insertion.";
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