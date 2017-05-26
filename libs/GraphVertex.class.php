<?php

/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 25-May-17
 * Time: 21:45
 */
class GraphVertex {
  private $id;
  private $distance;
  /**
   * @var boolean
   */
  private $isMutual;
  private $line;

  /**
   * @return bool
   */
  public function isMutual() {
    return $this->isMutual;
  }

  /**
   * @return mixed
   */
  public function getLine() {
    return $this->line;
  }

  /**
   * @param mixed $line
   */
  public function setLine($line) {
    $this->line = $line;
  }

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
  public function getDistance() {
    return $this->distance;
  }

  /**
   * @param mixed $distance
   */
  public function setDistance($distance) {
    $this->distance = $distance;
  }
}