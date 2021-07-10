<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use App\Models\M_Auth;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;

class User extends BaseController
{
	use ResponseTrait;
	protected $m_auth;

	public function __construct()
	{
		$this->m_auth = new M_Auth();
	}

	public function login()
	{
		$username = htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8');
		$password = htmlentities($_POST['password'], ENT_QUOTES, 'UTF-8');
		$result = $this->m_auth->login($username, $password);

		if ($result->data) {
			// Set JWT Token
			$payload = [
				"iss" => "http://localhost/",
				"aud" => "http://localhost/",
				"iat" => time(),
				"nbf" => time(),
				"exp" => time() + (60 * 60 * 6), // for 6 hours
				"data" => [
					"username" => $result->data->username,
					"role" => $result->data->role
				]
			];
			$token = JWT::encode($payload, $this->m_auth->private_key_jwt());
			$result->response['token'] = $token;

			// Cookie Remember me for 6 hours
			if (isset($_POST['remember_me'])) {
				$token = '';
				for ($i = 0; $i < 16; $i++) {
					$string = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
					$token .= $string[rand(0, strlen($string) - 1)];
				}
				$expired = time() + (60 * 60 * 6);
				setcookie('token', $token, $expired, '/skul/', '', false, true);
			}

			// Set session
			session()->set([
				"username" => $result->data->username,
				"photo" => $result->data->photo,
				"fullname" => $result->data->fullname,
				"email" => $result->data->email,
				"role" => $result->data->role,
				"token" => $token,
				"hasLogin" => true
			]);
		}

		return $this->respond($result->response);
	}

}