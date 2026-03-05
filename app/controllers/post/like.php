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
		$id_user = $session->user->id;
		$sql = "INSERT INTO `like` (id_user, id_post) VALUES (:id_user, :id_post)";
		try {
			$query = $conn->prepare($sql);
			$query->execute(array(':id_user' => $id_user, ':id_post' => $id_post));
			if ($query->rowCount() > 0) {
				$out["message"] = "Successful inserted like on post.";
			} else {
				$out["error"] = true;
				$out["message"] = "Error on insert like on post.";
			}
		} catch (PDOException $exception) {
			if (intval($exception->getCode()) === 23000) {
				$sql = "DELETE L FROM `like` AS L WHERE L.id_user = :id_user AND L.id_post = :id_post";
				$query = $conn->prepare($sql);
				$query->execute(array(':id_user' => $id_user, ':id_post' => $id_post));
				$out["dislike"] = true;
			}
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