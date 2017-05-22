<?php

/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 20-May-17
 * Time: 00:25
 */
abstract class BaseController {
  /**
   * @var DbTable|Station $model
   */
  protected  $model;
  protected $respondStatus = 200;

  public function getRespondStatus() {
    return $this->respondStatus;
  }
  abstract public function processRequest($method, $uriElement);
  public function loadModel($modelName) {}
}