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
    /* $line = new Line();
     $list = $line->getLineList();
     foreach ($list as $line) {
       echo "<area href=\"#\" data=\"line_" . $line['id'] . "\" shape=\"circle\" coords=\" " . $line['coords1'] . "\"/>"  . PHP_EOL ;
       echo "<area href=\"#\" data=\"line_" . $line['id'] . "\" shape=\"circle\" coords=\" " . $line['coords2'] . "\"/>" . PHP_EOL;
     }*/

    $graph = new Graph();
    var_dump($graph->isLineChanged(163,112));

    /*Test code end here */
    return '';
  }
}
