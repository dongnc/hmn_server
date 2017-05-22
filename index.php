<?php
/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 15-May-17
 * Time: 11:10
 */
define('ROOT_PATH', __DIR__);
require_once(ROOT_PATH . "/config/config.php");

spl_autoload_register('autoLoadClass');

//autoload for model and libs
function autoLoadClass($class) {
/*  if (strpos($class, "Controller") > 0) {
    $file = ROOT_PATH . '/controller/' . $class . '.php';
  } elseif (strpos($class, "Model") > 0) {
    $file = ROOT_PATH . '/model/' . $class . '.php';
  } elseif (strpos($class, "View") > 0) {
    $file = ROOT_PATH . '/model/' . $class . '.php';
  } else*/
  $file = ROOT_PATH . '/libs/' . $class . '.class.php';
  if (file_exists($file)) {
    require_once $file;
  }
  else {
    $file = ROOT_PATH . '/model/' . $class . '.php';
    if (file_exists($file)) {
      require_once $file;
    }
  }
}

/*** a new registry object ***/
//$registry = new Registry();

/*** load the router ***/
//$registry->router = new router($registry);

try {
  $API = new API($_SERVER['REQUEST_URI']);
  echo $API->processAPI();
} catch (Exception $e) {
  echo json_encode(Array('error' => $e->getMessage()));
}
