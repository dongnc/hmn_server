<?php

/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 21-May-17
 * Time: 00:48
 */
class API {
  protected $method = '';
  protected $mainEndpoint = '';
  protected $uriElement = Array();
  /**
   * Property: file
   * Stores the input of the PUT request
   */
  protected $file = Null;

  /**
   * API constructor.
   * @param string $uri
   */
  public function __construct($uri) {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: *");
    header("Content-Type: application/json");

    $uri = strtolower($uri);
    $this->uriElement = explode('/', trim($uri, '/'));
    $this->mainEndpoint = array_shift($this->uriElement);

    $this->method = $_SERVER['REQUEST_METHOD'];
   /* if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
      if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
        $this->method = 'DELETE';
      } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
        $this->method = 'PUT';
      } else {
        throw new Exception("Unexpected Header");
      }
    }*/

    switch($this->method) {
      case 'DELETE':
      case 'POST':
        $this->request = $this->_cleanInputs($_POST);
        break;
      case 'GET':
        $this->request = $this->_cleanInputs($_GET);
        break;
      case 'PUT':
        $this->request = $this->_cleanInputs($_GET);
        //$this->file = file_get_contents("php://input");
        break;
      default:
        $this->response('Invalid Method', 405);
        break;
    }
  }

  public function processAPI() {
    $controller = ucfirst(rtrim($this->mainEndpoint,"s")) . 'Controller';
    //echo 'controller = ' . $controller . '<br>';
    if (!file_exists(ROOT_PATH . "/controller/" . $controller . ".php")) {
      return $this->response("Controller file not found!<br>", 404);
    }
    require_once ROOT_PATH . "/controller/" . $controller . ".php";
    if (!class_exists($controller)) {
      return $this->response("Controller class not found!<br>", 404);
    }
    /**
     * @var BaseController $controllerObject
     */
    $controllerObject = new $controller();

    /* Testing */
    if (array_key_exists(0,$this->uriElement) && $this->uriElement[count($this->uriElement) - 1] == 'decode') {
      array_pop($this->uriElement);
      $response = $controllerObject->processRequest($this->method,$this->uriElement);
      print_r($response);
      return '';
    }
    /* end testing */

    $response = $controllerObject->processRequest($this->method,$this->uriElement);
    if ($response == false)
      return $this->response("Function return false!", 400);
    $status = $controllerObject->getRespondStatus();
    return $this->response($response, $status);
  }

  private function response($data, $status = 200) {
    header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
    return json_encode($data);
  }

  private function _cleanInputs($data) {
    $clean_input = Array();
    if (is_array($data)) {
      foreach ($data as $k => $v) {
        $clean_input[$k] = $this->_cleanInputs($v);
      }
    } else {
      $clean_input = trim(strip_tags($data));
    }
    return $clean_input;
  }

  private function _requestStatus($code) {
    $status = array(
      200 => 'OK',
      400 => 'Bad Request',
      404 => 'Not Found',
      405 => 'Method Not Allowed',
      500 => 'Internal Server Error',
    );
    return ($status[$code])?$status[$code]:$status[500];
  }
  
  
}