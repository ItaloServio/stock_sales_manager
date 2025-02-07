<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \App\Services\Helper;
use \App\Services\Auth;
use \App\Database\Database;
use \App\Entities\Category;
use Exception;

class CategoryController
{

  public function adminCategories(Request $req, Response $res, $args): Response
  {
    if (Auth::hasSession() && (Auth::getSession())->getAdmin() === 1) {
      return Helper::render('adminCategories', $req, $res);
    } else {
      return Helper::render('login', $req, $res);
    }
  }

  public function getAll(Request $req, Response $res, $args): Response
  {
    $arr = [];
    try {
      $em = Database::manager();
      $categoryRepository = $em->getRepository(Category::class);
      $categories = $categoryRepository->findAll();
      foreach ($categories as $value) {
        $arr[] = [
          'id' => $value->getId(),
          'name' => $value->getName()
        ];
      }
      $arr = [
        'status' => true,
        'categories' => $arr
      ];
    } catch (Exception $e) {
      $arr = [
        'status' => false,
        'message' => 'Ocorreu um erro ao buscar as categorias no banco',
        'error' => $e->getMessage()
      ];
    }
    $res->getBody()->write(json_encode($arr));
    return $res;
  }

  public function get(Request $req, Response $res, $args): Response
  {
    $arr = [];
    try {
      $em = Database::manager();
      $categoryRepository = $em->getRepository(Category::class);
      $categories = $categoryRepository->findAll();
      foreach ($categories as $value) {
        if (count($value->getProducts()) > 0) {
          $arr[] = [
            'id' => $value->getId(),
            'name' => $value->getName()
          ];
        }
      }
      $arr = [
        'status' => true,
        'categories' => $arr
      ];
    } catch (Exception $e) {
      $arr = [
        'status' => false,
        'message' => 'Ocorreu um erro ao buscar as categorias no banco',
        'error' => $e->getMessage()
      ];
    }
    $res->getBody()->write(json_encode($arr));
    return $res;
  }

  public function create(Request $req, Response $res, $args): Response
  {
    if (Auth::hasSession() && (Auth::getSession())->getAdmin() === 1) {
      $data = $req->getParsedBody();
      $em = Database::manager();
      $categoryRepository = $em->getRepository(Category::class);
      try {
        if (is_null($categoryRepository->findOneBy(['name' => $data["name"]]))) {
          if ($data["id"] === "-1") {
            $Category = new Category();
          } else {
            $Category = $categoryRepository->findOneBy(['id' => $data["id"]]);
          }
          $Category->setName($data["name"]);
          $em->persist($Category);
          $em->flush();

          $arr = [
            'status' => true,
            'message' => 'sucesso',
            'category' => [
              'id' => $Category->getId(),
              'name' => $Category->getName()
            ]
          ];
        } else {
          $arr = [
            'status' => false,
            'message' => 'Nome já Existe',
          ];
        }
      } catch (Exception $e) {
        $em->getConnection()->rollBack();
        $arr = [
          'status' => false,
          'message' => 'Ocorreu um erro ao criar a categoria',
          'error' => $e->getMessage()
        ];
      }
      $res->getBody()->write(json_encode($arr));
      return $res;
    }
  }

  public function delete(Request $req, Response $res, $args): Response
  {
    $id = $args['id'];
    $arr = [];
    try {
      $em = Database::manager();
      $categoryRepository = $em->getRepository(Category::class);
      $Category = $categoryRepository->find($id);
      $em->remove($Category);
      $em->flush();

      $arr = [
        'status' => true
      ];
    } catch (Exception $e) {
      $arr = [
        'status' => false,
        'message' => 'Ocorreu um erro ao remover a categoria',
        'error' => $e->getMessage()
      ];
    }
    $res->getBody()->write(json_encode($arr));
    return $res;
  }
}
