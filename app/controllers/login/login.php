<?php
require_once "../../../core/session/SessionManagement.php";
require_once "../../../core/routes/RoutesManagement.php";
require_once "../../../core/db/DatabaseConnection.php";
$session = SessionManagement::getInstance();
if ($session->logged()) {
	RoutesManagement::redirect("/app/controllers/home/");
} else {
	$conn = DatabaseConnection::getInstance();
	$out = array("error" => false);
	//
	$username = isset($_POST["username"]) ? $_POST["username"] : "";
	$password = isset($_POST["password"]) ? $_POST["password"] : "";
	//
	if ($username === "") {
		$out["error"] = true;
		$out["message"] = "Username is required.";
	} else if ($password === "") {
		$out["error"] = true;
		$out["message"] = "Password is required.";
	} else {
		$sql = "SELECT U.id, U.username FROM user AS U WHERE U.username=:username AND U.password=:password";
		$query = $conn->prepare($sql);
		$query->execute(array(':username' => $username, ':password' => $password));
		if ($query->rowCount() > 0) {
			$session->user = $query->fetchObject();
			$session->logged_user = true;
			$out["message"] = "Login successful.";
		} else {
			$out["error"] = true;
			$out["message"] = "Login failed. User not found...";
		}
	}
	DatabaseConnection::close();
	header("Content-type: application/json");
	echo json_encode($out);
	die();
}