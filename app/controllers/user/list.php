<?php
require_once "../../../core/session/SessionManagement.php";
require_once "../../../core/routes/RoutesManagement.php";
require_once "../../../core/views/ViewsManagement.php";
require_once "../../../core/db/DatabaseConnection.php";
require_once "../../services/UserService.php";
$session = SessionManagement::getInstance();
if ($session->logged()) {
	//
	$search = new stdClass();
	$search->action = isset($_GET["action"]) ? $_GET["action"] : "";
	$search->username = isset($_GET["username"]) ? $_GET["username"] : "";
	//
	$vm = new ViewsManagement();
	$vm->session = $session;
	$vm->search = $search;
	$user_service = new UserService();
	switch ($search->action) {
		case "friends":
			$vm->users = $user_service->list_friends($session->user->id);
			break;
		case "invitations":
			$vm->users = $user_service->list_friends($session->user->id, 0);
			break;
		default:
			$vm->users = $user_service->list($session->user->id, $search->username ?: null);
			break;
	}
	$vm->invitations = $user_service->list_friends($session->user->id, 0, 6);
	$vm->friends = $user_service->list_friends($session->user->id, 1, 6);
	if (count($vm->invitations) > 0) {
		$vm->set("panel_invitations", "/app/views/fragments/panel-invitations.php");
	}
	if (count($vm->friends) > 0) {
		$vm->set("panel_friends", "/app/views/fragments/panel-friends.php");
	}
	$vm->set("content", "/app/views/user/list.php");
	$vm->render();
} else {
	RoutesManagement::redirect("/app/");
}