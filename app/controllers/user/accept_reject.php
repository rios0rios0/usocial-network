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
	$accepted = isset($_POST["accepted"]) ? $_POST["accepted"] : "";
	//
	if (($id02 !== "") && ($accepted !== "")) {
		$id01 = $session->user->id;
		if (intval($accepted) === 0) {
			$sql = "DELETE F FROM friend AS F
					WHERE (F.id_user_accepted = :id01 AND F.id_user_requested = :id02)";
			$query = $conn->prepare($sql);
			$query->execute(array(':id01' => $id01, ':id02' => $id02));
		} else {
			$sql = "UPDATE friend AS F SET F.accepted = :accepted
            		WHERE (F.id_user_accepted = :id01 AND F.id_user_requested = :id02)";
			$query = $conn->prepare($sql);
			$query->execute(array(':accepted' => $accepted, ':id01' => $id01, ':id02' => $id02));
		}
		if ($query->rowCount() > 0) {
			$out["id"] = $id02;
			$out["message"] = "Successful updated friendship.";
		} else {
			$out["error"] = true;
			$out["message"] = "Error on update.";
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