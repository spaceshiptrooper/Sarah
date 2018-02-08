<?php
namespace Sarah;

error_reporting(E_ALL);
ini_set('display_errors', 1);

use \Sarah\PDO\PdoClass;
use \Sarah\Sarah\SarahClass;
use \stdClass;
use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;

class Sarah {

	public function __construct($config = []) {

		if(!defined('DS')) {

			define('DS', '/');

		}

		if(!defined('SARAH_ROOT')) {

			define('SARAH_ROOT', realpath(__DIR__) . DS);

		}

		if(!defined('REQUEST_METHOD')) {

			define('REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);

		}

		require_once(SARAH_ROOT . '../../phpmailer/phpmailer/src/Exception.php');
		require_once(SARAH_ROOT . '../../phpmailer/phpmailer/src/PHPMailer.php');
		require_once(SARAH_ROOT . '../../phpmailer/phpmailer/src/SMTP.php');

		if(PHP_VERSION < '5.4') {

			require_once(SARAH_ROOT . '../../ircmaxell/password-compat/lib/password.php');

		}

		$this->checkSession();
		$this->pdo($config);
		$this->verifyArray($config);

	}

	private function checkSession() {

		if(session_status() == PHP_SESSION_NONE) {
			session_start();
		}

	}

	private function verifyArray($config = []) {

		if(empty($config)) {

			return $this->returnHeader(404, 'The array for the constructor cannot be empty. Please go back and re-do it again.');

		} else {

			if(!isset($config['SMTP_HOST'])) {

				return $this->returnHeader(500, 'The SMTP host was not supplied in the array passed in the constructor. Please go back and re-add it in.');

			} elseif(!isset($config['SMTP_EMAIL'])) {

				return $this->returnHeader(500, 'The SMTP email was not supplied in the array passed in the constructor. Please go back and re-add it in.');

			} elseif(!isset($config['SMTP_PASSWORD'])) {

				return $this->returnHeader(500, 'The SMTP password was not supplied in the array passed in the constructor. Please go back and re-add it in.');

			} elseif(!isset($config['SMTP_PORT'])) {

				return $this->returnHeader(500, 'The SMTP port was not supplied in the array passed in the constructor. Please go back and re-add it in.');

			} elseif(!isset($config['SMTP_FROM'])) {

				return $this->returnHeader(500, 'The SMTP from header was not supplied in the array passed in the constructor. Please go back and re-add it in.');

			} elseif(!isset($config['SMTP_SECURE'])) {

				return $this->returnHeader(500, 'The SMTP secured preference was not supplied in the array passed in the constructor. Please go back and re-add it in.');

			} elseif(!isset($config['CALLBACK_URL'])) {

				return $this->returnHeader(500, 'The call back URL was not supplied in the array passed in the constructor. Please go back and re-add it in.');

			} else {

				$this->callSarah($config);

			}

		}

	}

	private function pdo($config) {

		if(!isset($config['DATABASE'])) {

			return $this->returnHeader(500, 'The database key was not supplied in the array passed in the constructor. Please go back and re-add it in.');

		} elseif(empty($config['DATABASE'])) {

			return $this->returnHeader(500, 'The database key cannot be empty. Please go back and re-add it in.');

		} elseif(!isset($config['DATABASE']['TYPE'])) {

			return $this->returnHeader(500, 'The database type was not supplied in the database key array passed in the constructor. Please go back and re-add it in.');

		} elseif(!isset($config['DATABASE']['DB_HOST'])) {

			return $this->returnHeader(500, 'The database host was not supplied in the database key array passed in the constructor. Please go back and re-add it in.');

		} elseif(!isset($config['DATABASE']['DB_USERNAME'])) {

			return $this->returnHeader(500, 'The database username was not supplied in the database key array passed in the constructor. Please go back and re-add it in.');

		} elseif(!isset($config['DATABASE']['DB_PASSWORD'])) {

			return $this->returnHeader(500, 'The database password was not supplied in the database key array passed in the constructor. Please go back and re-add it in.');

		} elseif(!isset($config['DATABASE']['DB_DATABASE'])) {

			return $this->returnHeader(500, 'The database was not supplied in the database key array passed in the constructor. Please go back and re-add it in.');

		} elseif(!isset($config['DATABASE']['COST'])) {

			return $this->returnHeader(500, 'The cost was not supplied in the database key array passed in the constructor. Please go back and re-add it in.');

		} else {

			require_once(SARAH_ROOT . 'Pdo.php');
			$this->pdo = new PdoClass($config);

		}

	}

	private function callSarah($config) {

		require_once(SARAH_ROOT . 'Sarah.php');
		$this->sarah = new SarahClass($config, $this->pdo);

	}

	protected function returnHeader($code, $message) {

		if(isset($this->config['SUCCESS_DIE']) AND $this->config['SUCCESS_DIE'] == true AND $code == 200) {

			header('HTTP/1.0 ' . $code . ' ' . $message);

		} else {

			header('Content-Type: application/json; charset=utf8');
			header('HTTP/1.0 ' . $code . ' ' . $message);

			$object = new stdClass();
			$object->code = $code;
			$object->message = $message;

			$obj = json_encode($object, JSON_PRETTY_PRINT);

			print_r($obj);
			die();

		}

	}

}
