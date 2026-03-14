<?php

class PostService
{
	private $conn;

	public function __construct()
	{
		$this->conn = DatabaseConnection::getInstance();
	}

	public function list($id_user, $id_session_user = 0)
	{
		$sql = "SELECT
					P.id,
       				P.id_user,
       				P.html_text,
       				P.photo,
       				P.created,
       				COALESCE((SELECT 'true' FROM `like` AS L WHERE L.id_post = P.id AND L.id_user = :id_session_user), 'false') AS liked,
       				P.modified,
       				(SELECT COUNT(*) FROM `like` AS L WHERE L.id_post = P.id) AS n_likes,
       				(SELECT COUNT(*) FROM comment AS C WHERE C.id_post = P.id) AS n_comments
				FROM post AS P
				WHERE P.id_user = :id_user
				ORDER BY P.created DESC";
		$query = $this->conn->prepare($sql);
		$query->execute(array(':id_user' => $id_user, ':id_session_user' => $id_session_user));
		return $query->fetchAll(PDO::FETCH_CLASS);
	}

	public function timeline($id_user)
	{
		$sql = "SELECT
       				P.id,
				   	P.id_user,
				   	P.html_text,
				   	P.photo,
				   	P.created,
       				COALESCE((SELECT 'true' FROM `like` AS L WHERE L.id_post = P.id AND L.id_user = :id_user_1), 'false') AS liked,
				   	P.modified,
				   	(SELECT COUNT(*) FROM `like` AS L WHERE L.id_post = P.id)  AS n_likes,
				   	(SELECT COUNT(*) FROM comment AS C WHERE C.id_post = P.id) AS n_comments
				FROM post AS P
				WHERE P.id_user = :id_user_2
				   OR P.id_user IN (SELECT IF(F.id_user_requested <> :id_user_3, F.id_user_requested, F.id_user_accepted) AS id
									FROM friend AS F
									WHERE F.accepted = 1
									  AND (F.id_user_requested = :id_user_4 OR F.id_user_accepted = :id_user_5))
				ORDER BY P.created DESC";
		$query = $this->conn->prepare($sql);
		$query->execute(array(
			':id_user_1' => $id_user,
			':id_user_2' => $id_user,
			':id_user_3' => $id_user,
			':id_user_4' => $id_user,
			':id_user_5' => $id_user,
		));
		return $query->fetchAll(PDO::FETCH_CLASS);
	}

	public function list_comments($id_post)
	{
		$sql = "SELECT
					C.id,
       				C.id_user,
       				C.id_post,
       				C.html_text
				FROM comment AS C
				WHERE C.id_post = :id_post
				ORDER BY C.created DESC";
		$query = $this->conn->prepare($sql);
		$query->execute(array(':id_post' => $id_post));
		return $query->fetchAll(PDO::FETCH_CLASS);
	}

	public function prepare($posts, $user_service)
	{
		foreach ($posts as $k1 => $v1) {
			$posts[$k1]->photo = $this->get_photo($posts[$k1]->photo);
			$posts[$k1]->user = $user_service->get($v1->id_user);
			$comments = $this->list_comments($v1->id);
			foreach ($comments as $k2 => $v2) {
				$comments[$k2]->user = $user_service->get($v2->id_user);
			}
			$posts[$k1]->comments = $comments;
		}
		return $posts;
	}

	private function get_photo($abs_path)
	{
		return ((!is_null($abs_path) && getimagesize($abs_path))
			? $abs_path : (RoutesManagement::base_url() . "resources/images/post.png"));
	}
}