<?php

/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 20-May-17
 * Time: 00:05
 */
class Registry {

  private $vars = array();

  public function __set($index, $value) {
    $this->vars[$index] = $value;
  }

  public function __get($index) {
    return $this->vars[$index];
  }

}