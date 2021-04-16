<?php

namespace App\Entities;


use Doctrine\ORM\Mapping as ORM;

/**
 * Orderproduct
 *
 * @ORM\Table(name="orderProduct")
 * @ORM\Entity
 */
class Orderproduct
{
  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   */
  private $id;

  /**
   * @var int
   *
   * @ORM\Column(name="productId", type="integer", nullable=false)
   */
  private $productid;

  /**
   * @var int
   *
   * @ORM\Column(name="orderId", type="integer", nullable=false)
   */
  private $orderid;

  /**
   * @var int
   *
   * @ORM\Column(name="qtd", type="integer", nullable=false, options={"default"="1"})
   */
  private $qtd = 1;

  /*
   * Getters
   */
  public function getId()
  {
    return $this->id;
  }

  public function getProductid()
  {
    return $this->productid;
  }

  public function getOrderid()
  {
    return $this->orderid;
  }

  public function getQtd()
  {
    return $this->qtd;
  }
  /*
   * Setters
   */
  public function setId($p_id)
  {
    $this->id = $p_id;
  }

  public function setProductid($p_productid)
  {
    $this->productid = $p_productid;
  }

  public function setOrderid($p_orderid)
  {
    $this->orderid = $p_orderid;
  }

  public function setQtd($p_qtd)
  {
    $this->qtd = $p_qtd;
  }
}
