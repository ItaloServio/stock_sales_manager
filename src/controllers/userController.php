<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \App\Database;
use \App\Entities\User;

class userController {

	public function getUsers(Request $req, Response $res, $args) : Response {

		$em = Database::manager();

		// Buscando usuários da classe User:
		$userRepository = $em->getRepository(User::class);
		$findedUser = $userRepository->find(1);

		echo "<pre>"; var_dump($findedUser); exit;		


		// $User = new User();
		// $json = json_encode([
		// 	'status' => $User->getName()
		// ]);
		// $html = file_get_contents(__DIR__ . '/../../public/index.html');
		//$res->getBody()->write($json);
		//return $res;
	}

}