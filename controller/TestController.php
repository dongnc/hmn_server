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
    echo "shortestPath\n";
    print_r($graph->shortestPath(1, 21));
    echo "leastTransfer\n";
    print_r($graph->leastTransfer(78, 110));



    /*Test code end here */
    return '';
  }
}