<?php

/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 25-May-17
 * Time: 18:14
 */

class TestController {
  public function processRequest() {
    /* Test code goes here */
    $graph = new Graph();
    $graph->shortestPath(1, 132);



    /*Test code end here */
    return '';
  }
}