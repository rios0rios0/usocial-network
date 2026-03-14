<?php
require_once "../../../core/session/SessionManagement.php";
require_once "../../../core/routes/RoutesManagement.php";
require_once "../../../core/views/ViewsManagement.php";
require_once "../../../core/db/DatabaseConnection.php";
require_once "../../services/UserService.php";
require_once "../../services/PostService.php";
$session = SessionManagement::getInstance();
if ($session->logged()) {
	$conn = DatabaseConnection::getInstance();
	$out = array("error" => false);
	//
	$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
	$id = (($id !== 0) ? $id : $session->user->id);
	$photo = isset($_POST["photo"]) ? $_POST["photo"] : "";
	//
	$user_service = new UserService();
	if ($photo !== "") {
		if ($photo === $user_service->get_photo($photo)) {
			$sql = "UPDATE user AS U SET U.photo = :photo WHERE U.id = :id";
			$query = $conn->prepare($sql);
			$query->execute(array(':photo' => $photo, ':id' => $session->user->id));
			if ($query->rowCount() > 0) {
				DatabaseConnection::close();
				RoutesManagement::redirect("/app/controllers/user/index.php");
			} else {
				$out["error"] = true;
				$out["message"] = "Error on update user photo.";
			}
			DatabaseConnection::close();
			header("Content-type: application/json");
			echo json_encode($out);
			die();
		} else {
			RoutesManagement::redirect("/app/controllers/user/index.php");
		}
	}
	//
	$vm = new ViewsManagement();
	$vm->session = $session;
	$vm->user = $user_service->get($id);
	$vm->user->photo_link = ((strpos($vm->user->photo, RoutesManagement::base_url()) !== false) ? "" : $vm->user->photo);
	$vm->invitations = $user_service->list_friends($session->user->id, 0, 6);
	$vm->friends = $user_service->list_friends($session->user->id, 1, 6);
	if (count($vm->invitations) > 0) {
		$vm->set("panel_invitations", "/app/views/fragments/panel-invitations.php");
	}
	if (count($vm->friends) > 0) {
		$vm->set("panel_friends", "/app/views/fragments/panel-friends.php");
	}
	//
	$post_service = new PostService();
	// nosemgrep: php.lang.security.injection.tainted-callable.tainted-callable
	$vm->posts = $post_service->prepare($post_service->list($id, $session->user->id), $user_service);
	if (count($vm->posts) > 0) {
		$vm->set("panel_posts", "/app/views/fragments/panel-posts.php");
	}
	//
	$vm->set("content", "/app/views/user/index.php");
	$vm->render();
} else {
	RoutesManagement::redirect("/app/");
}