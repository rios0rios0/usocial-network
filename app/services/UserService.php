<?php

class UserService
{
	private $conn;

	public function __construct()
	{
		$this->conn = DatabaseConnection::getInstance();
	}

	public function get($id)
	{
		$sql = "SELECT
					U.id,
       				U.username,
       				U.password,
       				U.email,
       				U.photo
				FROM user AS U
				WHERE U.id = :id";
		$query = $this->conn->prepare($sql);
		$query->execute(array(':id' => $id));
		return $this->prepare($query->fetchObject());
	}

	public function list($id_user_exclude, $username = null)
	{
		$username_clause = "";
		$params = array(
			':id_user_exclude_1' => $id_user_exclude,
			':id_user_exclude_2' => $id_user_exclude,
			':id_user_exclude_3' => $id_user_exclude,
		);
		if ($username !== null) {
			$username_clause = "AND U.username LIKE :username";
			$params[':username'] = '%' . $username . '%';
		}
		$sql = "SELECT
					U.id,
			        U.username,
			        U.email,
	                U.photo,
	                IF(F1.id_user_accepted IS NOT NULL, 1, 0)			AS invited,
					IF(F2.id_user_requested IS NOT NULL, 1, 0)			AS requester,
					IF((F1.accepted <> 0) OR (F2.accepted <> 0), 1, 0)	AS friend
				FROM user AS U
					LEFT JOIN friend AS F1
				        ON U.id = F1.id_user_accepted
							AND F1.id_user_requested = :id_user_exclude_1
					LEFT JOIN friend AS F2
		                ON U.id = F2.id_user_requested
							AND F2.id_user_accepted = :id_user_exclude_2
				WHERE U.id <> :id_user_exclude_3
					$username_clause
				ORDER BY U.username ASC";
		$query = $this->conn->prepare($sql);
		$query->execute($params);
		return $this->prepare($query->fetchAll(PDO::FETCH_CLASS));
	}

	public function list_friends($id_user, $friend = 1, $limit = null)
	{
		$limit_clause = (!is_null($limit) ? "LIMIT " . (int)$limit : "");
		$condition_clause = (($friend === 0) ? "AND F.id_user_accepted = :id_user_5" : "");
		$params = array(
			':id_user_1' => $id_user,
			':id_user_2' => $id_user,
			':id_user_3' => $id_user,
			':id_user_4' => $id_user,
			':id_user_6' => $id_user,
			':friend' => $friend,
		);
		if ($friend === 0) {
			$params[':id_user_5'] = $id_user;
		}
		$sql = "SELECT
       				U.id,
				   	U.username,
				   	U.email,
				   	U.photo,
       				IF(F.id_user_accepted <> :id_user_1, 1, 0)	AS invited,
					IF(F.id_user_requested <> :id_user_2, 1, 0)	AS requester,
					F.accepted									AS friend
				FROM friend AS F
					 LEFT JOIN user U
							   ON U.id = (CASE
											  WHEN F.id_user_requested = :id_user_3
												  THEN F.id_user_accepted
											  ELSE F.id_user_requested END)
				WHERE (F.id_user_accepted = :id_user_4
						OR F.id_user_requested = :id_user_6)
				  	AND F.accepted = :friend
					$condition_clause
				ORDER BY U.username ASC
				$limit_clause";
		$query = $this->conn->prepare($sql);
		$query->execute($params);
		return $this->prepare($query->fetchAll(PDO::FETCH_CLASS));
	}

	private function prepare($users)
	{
		$url = RoutesManagement::base_url() . "app/controllers/user/index.php?id=";
		if (is_array($users)) {
			foreach ($users as $key => $value) {
				$users[$key]->photo = $this->get_photo($value->photo);
				$users[$key]->url = $url . $value->id;
			}
		} else {
			if (!is_null($users)) {
				$users->photo = $this->get_photo($users->photo);
				$users->url = $url . $users->id;
			}
		}
		return $users;
	}

	public function get_photo($abs_path)
	{
		return ((!is_null($abs_path) && getimagesize($abs_path))
			? $abs_path : (RoutesManagement::base_url() . "resources/images/user.png"));
	}
}