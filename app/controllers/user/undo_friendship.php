<?php
require_once "../../../core/session/SessionManagement.php";
require_once "../../../core/routes/RoutesManagement.php";
require_once "../../../core/db/DatabaseConnection.php";
$session = SessionManagement::getInstance();
if ($session->logged()) {
	$conn = DatabaseConnection::getInstance();
	$out = array("error" => false);
	//
	$id02 = isset($_POST["id"]) ? $_POST["id"] : "";
	//
	if ($id02 !== "") {
		$id01 = $session->user->id;
		$sql = "DELETE F FROM friend AS F
				WHERE ((F.id_user_requested = :id01a AND F.id_user_accepted = :id02a)
					OR (F.id_user_requested = :id02b AND F.id_user_accepted = :id01b))";
		$query = $conn->prepare($sql);
		$query->execute(array(':id01a' => $id01, ':id02a' => $id02, ':id02b' => $id02, ':id01b' => $id01));
		if ($query->rowCount() > 0) {
			$out["id"] = $id02;
			$out["message"] = "Successful undo friendship.";
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