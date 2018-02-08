<?php
namespace Sarah\Sarah;

error_reporting(E_ALL);
ini_set('display_errors', 1);

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;
use \Sarah\Sarah;

class SarahClass extends Sarah {

	public function __construct($config, $pdo) {

		$this->config = $config;
		$this->pdo = $pdo;

		if(isset($_GET['theme'])) {

			$this->changeThemes($_GET['theme']);

		}

		if(isset($_GET['two_factor_authentication']) AND isset($_GET['email'])) {

			$this->verifyTwoFactorAuthentication($_GET['two_factor_authentication'], $_GET['email']);
			die();

		}

		$this->link = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	}

	public function setUser($user) {

		$this->user = $user;

		return $this->user;

	}

	public function setCharacterAmount($length = 0) {

		if($length > 0) {

			$rand_id = '';
			for($i=1; $i<=$length; $i++) {

				mt_srand((double) microtime() * 1000000);
				$num = mt_rand(1, 36);
				$rand_id .= $this->assignRandValue($num);

			}

		}

		$this->twofa_key = $rand_id;

		return $this->twofa_key;

	}

	private function assignRandValue($num = 0) {

		switch($num) {

			case '1': $rand_value = 'a'; break;
			case '2': $rand_value = 'b'; break;
			case '3': $rand_value = 'c'; break;
			case '4': $rand_value = 'd'; break;
			case '5': $rand_value = 'e'; break;
			case '6': $rand_value = 'f'; break;
			case '7': $rand_value = 'g'; break;
			case '8': $rand_value = 'h'; break;
			case '9': $rand_value = 'i'; break;
			case '10' : $rand_value = 'j'; break;
			case '11' : $rand_value = 'k'; break;
			case '12' : $rand_value = 'l'; break;
			case '13' : $rand_value = 'm'; break;
			case '14' : $rand_value = 'n'; break;
			case '15' : $rand_value = 'o'; break;
			case '16' : $rand_value = 'p'; break;
			case '17' : $rand_value = 'q'; break;
			case '18' : $rand_value = 'r'; break;
			case '19' : $rand_value = 's'; break;
			case '20' : $rand_value = 't'; break;
			case '21' : $rand_value = 'u'; break;
			case '22' : $rand_value = 'v'; break;
			case '23' : $rand_value = 'w'; break;
			case '24' : $rand_value = 'x'; break;
			case '25' : $rand_value = 'y'; break;
			case '26' : $rand_value = 'z'; break;
			case '27' : $rand_value = '0'; break;
			case '28' : $rand_value = '1'; break;
			case '29' : $rand_value = '2'; break;
			case '30' : $rand_value = '3'; break;
			case '31' : $rand_value = '4'; break;
			case '32' : $rand_value = '5'; break;
			case '33' : $rand_value = '6'; break;
			case '34' : $rand_value = '7'; break;
			case '35' : $rand_value = '8'; break;
			case '36' : $rand_value = '9'; break;

		}

		return $rand_value;

	}

	public function sendMail($array = []) {

		if(empty($array)) {

			return $this->returnHeader(500, 'The sendMail() method cannot contain an empty array. Please go back and apply the correct keys and values to the empty array.');

		} else {

			if(!isset($array['email_subject'])) {

				return $this->returnHeader(500, 'Please provide an email subject in the array.');

			} elseif(!isset($array['user_id'])) {

				return $this->returnHeader(500, 'Please provide a user id in the array.');

			} elseif(!isset($array['first_name'])) {

				return $this->returnHeader(500, 'Please provide a first name in the array.');

			} elseif(!isset($array['email'])) {

				return $this->returnHeader(500, 'Please provide an email in the array.');

			} else {

				$subject = $array['email_subject'];
				$user_id = $array['user_id'];
				$first_name = $array['first_name'];
				$email = $array['email'];

				$this->constructMail($subject, $user_id, $first_name, $email);

			}

		}

	}

	private function constructMail($subject, $user_id, $first_name, $email) {

		$hash = hash('sha256', $this->twofa_key);

		if(strpos($this->link, '?') !== false) {

			$link = $this->link . '&two_factor_authentication=' . $hash . '&email=' . urlencode($email);

		} else {

			$link = $this->link . '?two_factor_authentication=' . $hash . '&email=' . urlencode($email);

		}

		$mail = new PHPMailer;
		$mail->isSMTP();
		$mail->Host = $this->config['SMTP_HOST'];
		$mail->SMTPAuth = true;
		$mail->Username = $this->config['SMTP_EMAIL'];
		$mail->Password = $this->config['SMTP_PASSWORD'];
		$mail->SMTPSecure = $this->config['SMTP_SECURE'];
		$mail->Port = $this->config['SMTP_PORT'];
		$mail->From = $this->config['SMTP_EMAIL'];
		$mail->FromName = $this->config['SMTP_FROM'];

		$mail->addAddress($email, $first_name);

		$mail->isHTML(true);

		//////////////////////////////////////////////////////
		//													//
		//			DO NOT EDIT OR MODIFY BELOW!!!!			//
		//													//
		//////////////////////////////////////////////////////

		// We need ob_start() and ob_end_clean()
		// Because this will not output our mail template to the screen and send
		// a gross looking email template to the user in question.
		ob_start();
		require_once(SARAH_ROOT . 'template' . DS . 'mail.php');
		$mail_template = ob_get_contents();
		ob_end_clean();

		$mail->Subject = $subject;
		$mail->Body = <<<EOF
{$mail_template}
EOF;

		//////////////////////////////////////////////////////
		//													//
		//			DO NOT EDIT OR MODIFY ABOVE!!!!			//
		//													//
		//////////////////////////////////////////////////////

		if(!$mail->send()) {

			return $this->returnHeader(500, $mail->ErrorInfo);

		} else {

			if(isset($_SERVER['REMOTE_ADDR'])) {

				$ip = $_SERVER['REMOTE_ADDR'];

			} else {

				$ip = NULL;

			}

			if(isset($_SERVER['HTTP_USER_AGENT'])) {

				$agent = $_SERVER['HTTP_USER_AGENT'];

			} else {

				$agent = NULL;

			}

			$timestamp = (int) time();
			$status = (int) 0;

			$this->pdo->insertTwoFactorAuthentication($user_id, $email, $this->twofa_key, $hash, $ip, $agent, $timestamp, $status);

			return $this->returnHeader(200, 'Ok');

		}

	}

	private function verifyTwoFactorAuthentication($hash, $email) {

		$this->link = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		$verify = $this->pdo->verifyTwoFactorAuthentication($hash, $email);
		if($verify == false) {

			return $this->returnHeader(401, 'Unauthorized');

		} else {

			$verifyCurrentAuthentication = $this->pdo->verifyCurrentAuthentication($hash, $email);
			if($verifyCurrentAuthentication == false) {

				return $this->returnHeader(401, 'Unauthorized');

			} else {

				if(REQUEST_METHOD == 'POST') {

					if(!isset($_POST['key'])) {

						return $this->returnHeader(504, 'Please do not modify the page.');

					} else {

						$verifyTwoFAKey = $this->pdo->verifyTwoFAKey($_POST['key'], $email, $hash);
						if($verifyTwoFAKey == false) {

							return $this->returnHeader(401, 'Unauthorized');

						} else {

							header('HTTP/1.0 200 Ok');

							$_SESSION['CALLBACK_SUCCESS'] = hash('sha256', $_POST['key']);

							header('Location: ' . $this->config['CALLBACK_URL']);
							die();

						}

					}

				} else {

					$time = time();

					$check = $verifyCurrentAuthentication['timestamp'] + 3600 < $time;
					if($check == true) {

						$deleteAuthentication = $this->pdo->deleteAuthentication($hash, $email);
						if($deleteAuthentication == false) {

							return $this->returnHeader(401, 'Unauthorized');

						} else {

							return $this->returnHeader(200, 'Ok');

						}

					} else {

						if(strpos($this->link, '?') !== false) {

							if(strpos($this->link, '?two_factor_authentication') !== false) {

								$this->link = $this->link;

							} else {

								$this->link = $this->link . '&two_factor_authentication=' . $hash . '&email=' . urlencode($email);

							}

						} else {

							$this->link = $this->link . '?two_factor_authentication=' . $hash . '&email=' . urlencode($email);

						}

						require_once(SARAH_ROOT . 'template' . DS . 'verify.php');
						die();

					}

				}

			}

		}

	}

}
