<?php

/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 20-May-17
 * Time: 00:16
 */
class Router {
  private $registry;
  private $path;
  private $args = array();
  public $file;
  public $controller;
  public $action;

  function __construct($registry) {
    $this->registry = $registry;
  }
}