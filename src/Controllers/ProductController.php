<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \App\Services\Helper;
use \App\Services\Auth;
use \App\DB\Database;
use \App\Entities\Category;
use \App\Entities\Product;
use Exception;

class ProductController {

  public function details(Request $req, Response $res, $args): Response {

    return Helper::render('product', $req, $res, ['productId' => $args['id']]);
  }

  public function adminProducts(Request $req, Response $res, $args): Response {
    if (Auth::hasSession() && (Auth::getSession())->getAdmin() === 1) {
      return Helper::render('adminProducts', $req, $res);
    } else {
      return Helper::render('login', $req, $res);
    }
  }

  public function getAll(Request $req, Response $res, $args): Response {
    $arr = [];
    $category = ($req->getQueryParams())['category'];

    try {
      $em = Database::manager();
      $productRepository = $em->getRepository(Product::class);

      if ($category === '0') {
        $products = $productRepository->findAll();
      } else {
        $products = $productRepository->findBy(array('categoryid' => $category));
      }
      if (!is_null($products)) {
        foreach ($products as $value) {
          $arr[] = [
            'id' => $value->getId(),
            'name' => $value->getName(),
            'desc' => $value->getDesc(),
            'qtd' => $value->getQtd(),
            'price' => $value->getPrice(),
            'imagePath' => $value->getImagePath(),
            'category' => [
              'id' => $value->getCategory()->getId(),
              'name' => $value->getCategory()->getName()
            ]
          ];
        }
        $arr = [
          'status' => true,
          'products' => $arr
        ];
      } else {
        $arr = [
          'status' => false,
          'message' => 'NIGUEM NO BANCO'
        ];
      }
    } catch (Exception $e) {
      $arr = [
        'status' => false,
        'message' => 'DEU ERRO GERAL',
        'error' => $e->getMessage()
      ];
    }
    $res->getBody()->write(json_encode($arr));
    return $res;
  }

  public function get(Request $req, Response $res, $args): Response {
    $arr = [];
    $id = $args['id'];
    try {
      $em = Database::manager();
      $productRepository = $em->getRepository(Product::class);
      $product = $productRepository->findOneBy(array('id' => $id));
      if (!is_null($product)) {
        $arr[] = [
          'id' => $product->getId(),
          'name' => $product->getName(),
          'desc' => $product->getDesc(),
          'qtd' => $product->getQtd(),
          'price' => $product->getPrice(),
          'imagePath' => $product->getImagePath(),
          'category' => [
            'id' => $product->getCategory()->getId(),
          ]
        ];
        $arr = [
          'status' => true,
          'product' => $arr
        ];
      } else {
        $arr = [
          'status' => false,
          'message' => 'NIGUEM NO BANCO'
        ];
      }
    } catch (Exception $e) {
      $arr = [
        'status' => false,
        'message' => 'DEU ERRO GERAL',
        'error' => $e->getMessage()
      ];
    }
    $res->getBody()->write(json_encode($arr));
    return $res;
  }

  public function create(Request $req, Response $res, $args): Response {
    date_default_timezone_set('America/Sao_Paulo');
    $arr = [];
    $file = $_FILES['file'];
    $data = $req->getParsedBody();
    $file['name'] = md5(date('YmdHis')) . '_' . $file['name'];

    $em = Database::manager();
    $em->getConnection()->beginTransaction();

    try {
      // Movendo arquivo:
      move_uploaded_file($file['tmp_name'], '../public/assets/img/sys/products/' . $file['name']);

      // Criando produto:
      $User = Auth::getSession();
      $categoryRepository = $em->getRepository(Category::class);
      $Category = $categoryRepository->find((int) $data['category']);

      $Product = new Product();
      $Product->setName($data['name']);
      $Product->setQtd($data['qtd']);
      $Product->setPrice($data['price']);
      $Product->setDesc($data['desc']);
      $Product->setImagePath("products/" . $file['name']);
      $Product->setCreatedBy($User->getId());
      $Product->setCategoryId($data['category']);
      $Product->setUser($User);
      $Product->setCategory($Category);

      $em->merge($Product);
      $em->flush();
      $em->getConnection()->commit();

      $arr = [
        'status' => true,
        'message' => 'Produto criado com sucesso'
      ];

    } catch (Exception $e) {
      $em->getConnection()->rollBack();
      $arr = [
        'status' => false,
        'message' => 'Falha na criação do produto',
        'error' => $e->getMessage()
      ];
    }

    $res->getBody()->write(json_encode($arr));
    return $res;
  }
}