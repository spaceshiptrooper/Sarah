<?php
namespace Sarah\PDO;

error_reporting(E_ALL);
ini_set('display_errors', 1);

use \PDO;
use \PDOException;

class PdoClass {

	public function __construct($config) {

		$this->config = $config;

		$this->connection();

	}

	private function connection() {

		$options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);
		$this->db = new PDO($this->config['DATABASE']['TYPE'] . ':host=' . $this->config['DATABASE']['DB_HOST'] . ';dbname=' . $this->config['DATABASE']['DB_DATABASE'], $this->config['DATABASE']['DB_USERNAME'], $this->config['DATABASE']['DB_PASSWORD'], $options);

	}

	public function insertTwoFactorAuthentication($user_id, $email, $authkey, $hash, $ip, $agent, $timestamp, $status) {

		$cost = [
			'cost' => $this->config['DATABASE']['COST']
		];
		$newauthkey = password_hash($authkey, PASSWORD_DEFAULT, $cost);

		$sql = 'INSERT INTO two_factor_authentication (user_id, email, authkey, hash, ip, agent, timestamp, status) VALUES (:user_id, :email, :authkey, :hash, :ip, :agent, :timestamp, :status);';
		$prepare = $this->db->prepare($sql);
		$parameters = [
			':user_id' => $user_id,
			':email' => $email,
			':authkey' => $newauthkey,
			':hash' => $hash,
			':ip' => $ip,
			':agent' => $agent,
			':timestamp' => $timestamp,
			':status' => $status,
		];

		$prepare->execute($parameters);

		if($prepare->rowCount()) {

			return true;

		} else {

			return false;

		}

	}

	public function verifyTwoFactorAuthentication($hash, $email) {

		$status = (int) 0;

		$sql = 'SELECT id AS total FROM two_factor_authentication WHERE email = :email AND hash = :hash AND status = :status LIMIT 1';
		$prepare = $this->db->prepare($sql);
		$parameters = [
			':email' => $email,
			':hash' => $hash,
			':status' => $status
		];

		$prepare->execute($parameters);

		if($prepare->rowCount()) {

			return true;

		} else {

			return false;

		}

	}

	public function verifyCurrentAuthentication($hash, $email) {

		$status = (int) 0;

		$sql = 'SELECT id, timestamp FROM two_factor_authentication WHERE email = :email AND hash = :hash AND status = :status LIMIT 1';
		$prepare = $this->db->prepare($sql);
		$parameters = [
			':email' => $email,
			':hash' => $hash,
			':status' => $status
		];

		$prepare->execute($parameters);

		if($prepare->rowCount()) {

			while($row = $prepare->fetch(PDO::FETCH_ASSOC)) {

				return $row;

			}

		} else {

			return false;

		}

	}

	public function verifyTwoFAKey($authkey, $email, $hash) {

		$sql = 'SELECT id, authkey FROM two_factor_authentication WHERE email = :email AND hash = :hash LIMIT 1';
		$prepare = $this->db->prepare($sql);
		$parameters = [
			':email' => $email,
			':hash' => $hash
		];

		$prepare->execute($parameters);

		if($prepare->rowCount()) {

			while($row = $prepare->fetch(PDO::FETCH_ASSOC)) {

				$getAuthKey = $row['authkey'];

				if(password_verify($authkey, $getAuthKey)) {

					$sql = 'UPDATE two_factor_authentication SET status = :status WHERE email = :email AND hash = :hash';
					$prepare = $this->db->prepare($sql);
					$parameters = [
						':status' => 1,
						':email' => $email,
						':hash' => $hash
					];
					$prepare->execute($parameters);

					if($prepare->rowCount()) {

						return true;

					} else {

						return false;

					}

				} else {

					return false;

				}

			}

		} else {

			return false;

		}

	}

	public function deleteAuthentication($hash, $email) {

		$status = (int) 0;

		$sql = 'SELECT id, timestamp FROM two_factor_authentication WHERE email = :email AND hash = :hash AND status = :status LIMIT 1';
		$prepare = $this->db->prepare($sql);
		$parameters = [
			':email' => $email,
			':hash' => $hash,
			':status' => $status
		];

		$prepare->execute($parameters);

		if($prepare->rowCount()) {

			$sql = "DELETE FROM two_factor_authentication WHERE email = :email AND hash = :hash AND status = :status LIMIT 1";
			$prepare = $this->db->prepare($sql);
			$parameters = [
				':email' => $email,
				':hash' => $hash,
				':status' => $status
			];

			$prepare->execute($parameters);

			if($prepare->rowCount()) {

				return true;

			} else {

				return false;

			}

		} else {

			return false;

		}

	}

}
