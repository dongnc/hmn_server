<?php

/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 20-May-17
 * Time: 00:25
 */
abstract class BaseController {
  /**
   * @var DbTable|Station|Line $model
   */
  protected  $model;
  protected $respondStatus = 200;

  public function getRespondStatus() {
    return $this->respondStatus;
  }
  abstract public function processRequest($method, $uriElement);

  public function loadModel($modelName) {
    if (!file_exists(ROOT_PATH . "/model/" . $modelName . ".php")) {
      die("Controller file not found!<br>");
    }
    require_once ROOT_PATH . "/model/" . $modelName . ".php";
    $this->model = new $modelName;
  }

  public function badRequestResponse() {
    $this->respondStatus = 400;
    return "Invalid request";
  }
}