<?php
require_once "../../../core/session/SessionManagement.php";
require_once "../../../core/routes/RoutesManagement.php";
require_once "../../../core/db/DatabaseConnection.php";
require_once "../../services/UserService.php";
$session = SessionManagement::getInstance();
if ($session->logged()) {
	RoutesManagement::redirect("/app/controllers/home/");
} else {
	$conn = DatabaseConnection::getInstance();
	$out = array("error" => false);
	//
	$firstName = isset($_POST["first_name"]) ? $_POST["first_name"] : "";
	$lastName = isset($_POST["last_name"]) ? $_POST["last_name"] : "";
	$photo = isset($_POST["photo"]) ? $_POST["photo"] : "";
	$email = isset($_POST["email"]) ? $_POST["email"] : "";
	$password = isset($_POST["password"]) ? $_POST["password"] : "";
	$confirmPassword = $_POST["confirm_password"] ? $_POST["confirm_password"] : "";
	//
	if (($firstName !== "") && ($lastName !== "") && ($photo !== "")
		&& ($email !== "") && ($password !== "") && ($confirmPassword !== "")) {
		if ($password === $confirmPassword) {
			$user_service = new UserService();
			if ($photo === $user_service->get_photo($photo)) {
				$username = trim(strtolower($firstName . $lastName)) . rand(0, 100);
				$sql = "INSERT INTO user (username, password, email, photo) VALUES (:username, :password, :email, :photo)";
				$query = $conn->prepare($sql);
				$query->execute(array(':username' => $username, ':password' => $password, ':email' => $email, ':photo' => $photo));
				if ($query->rowCount() > 0) {
					$out["message"] = "Sign Up successful. Your username is <strong>" . htmlspecialchars($username, ENT_QUOTES, 'UTF-8') . "</strong>.";
				} else {
					$out["error"] = true;
					$out["message"] = "Sign Up failed. Database error, contact administrator...";
				}
			} else {
				$out["error"] = true;
				$out["message"] = "Enter a valid photo link to your photo.";
			}
		} else {
			$out["error"] = true;
			$out["message"] = "Password and confirmation is required. Must be equal.";
		}
	} else {
		$out["error"] = true;
		$out["message"] = "All fields is required.";
	}
	DatabaseConnection::close();
	header("Content-type: application/json");
	echo json_encode($out);
	die();
}