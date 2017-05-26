<?php

/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 25-May-17
 * Time: 21:45
 */
class GraphVertex {
  private $id;
  private $weight;

  /**
   * @return mixed
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @param mixed $id
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * @return mixed
   */
  public function getWeight() {
    return $this->weight;
  }

  /**
   * @param mixed $weight
   */
  public function setWeight($weight) {
    $this->weight = $weight;
  }
}