<?php

/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 21-May-17
 * Time: 10:19
 */
class StationController extends BaseController {
  public function __construct() {
    $this->loadModel('Station');
  }

  public function processRequest($method, $uriElement) {
    switch ($method) {
      case 'GET':
        //API: /staions
        if (!array_key_exists(0, $uriElement)) {
          return $this->getStationList();
        } //API: /stations/*
        else {
          //API: /stations/:id/*
          if (is_numeric($uriElement[0])) {
            $stationId = $uriElement[0];
            //API: /stations/:id/
            if (!array_key_exists(1, $uriElement)) {
              return $this->model->getStationInfo($stationId);
            } //API: /stations/:id/something/*
            else {
              //API: /stations/:id/something/
              if (!array_key_exists(2, $uriElement)) {
                switch ($uriElement[1]) {
                  case "arrivaltimeinfo":
                    $arrivalTime = new ArrivalTime();
                    return $arrivalTime->getArrivalTimeInfo($stationId);
                    break;
                }
              } else {
                //API: /stations/:id/something/something
                if (!array_key_exists(3, $uriElement)) {
                  switch ($uriElement[1]) {
                    case "nexttrain":
                      if (is_numeric($uriElement[2])) {
                        $arrivalTime = new ArrivalTime();
                        return $arrivalTime->getNextTrainArrivalTime($stationId, $uriElement[2]);
                      } else {
                        $this->respondStatus = 400;
                        return "Invalid request";
                      }
                      break;
                  }
                } else {

                }
              }
            }
          }
        }

        break;
      default:
        $this->respondStatus = 400;
        return "Unsupported method";
    }
  }

  public function getStationList() {
    return $this->model->getStationList();
  }

  public function loadModel($modelName) {
    if (!file_exists(ROOT_PATH . "/model/" . $modelName . ".php")) {
      die("Controller file not found!<br>");
    }
    require_once ROOT_PATH . "/model/" . $modelName . ".php";
    $this->model = new $modelName;
  }
}