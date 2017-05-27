<?php

/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 25-May-17
 * Time: 18:23
 */
class NavController extends BaseController {

  public function __construct() {
    $this->loadModel('Graph');
  }

  public function processRequest($method, $uriElement) {
    switch ($method) {
      case 'GET':
        switch (count($uriElement)) {
          case 2: //API: /nav/xyz/xyz
            if (is_numeric($uriElement[0]) && is_numeric($uriElement[1])) {
              $startStation = $uriElement[0];
              $endStation = $uriElement[1];
              $graph = new Graph();
              return $graph->shortestPath($startStation, $endStation);
            } else return $this->badRequestResponse();

          case 3: //API: /nav/xyz/xyz/xyz
            if (is_numeric($uriElement[0]) && is_numeric($uriElement[1])) {
              $startStation = $uriElement[0];
              $endStation = $uriElement[1];
              $graph = new Graph();
              switch ($uriElement[2]) {
                case 'shortest':
                  return $graph->shortestPath($startStation, $endStation);

                case 'leasttransfer':
                  return $graph->leastTransfer($startStation, $endStation);

                default:
                  return $this->badRequestResponse();
              }
            } else return $this->badRequestResponse();
        }
        break;

      default:
        $this->respondStatus = 400;
        return "Unsupported method";
    }
  }

}