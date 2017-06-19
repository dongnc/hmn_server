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
        switch (count($uriElement)) {
          case 0: //API: /stations
            return $this->getStationList();

          case 1: //API: /stations/xyz
            if (is_numeric($uriElement[0])) {
              $stationId = $uriElement[0];
              return $this->model->getStationInfo($stationId);
            } else return $this->badRequestResponse();

          case 2: //API: /stations/xyz/xyz
            if (is_numeric($uriElement[0])) {
              $stationId = $uriElement[0];
              switch ($uriElement[1]) {
                case "name":
                  return $this->model->getFieldValueById('name', $stationId);

                case "arrivaltimeinfo":
                  $arrivalTime = new ArrivalTime();
                  return $arrivalTime->getArrivalTimeInfo($stationId);

                default:
                  return $this->badRequestResponse();
              }
            } else return $this->badRequestResponse();

          case 3: //API: /stations/xyz/xyz/xyz
            if (is_numeric($uriElement[0])) {
              $stationId = $uriElement[0];
              switch ($uriElement[1]) {
                case "nexttrain":
                  if (is_numeric($uriElement[2])) {
                    $arrivalTime = new ArrivalTime();
                    return $arrivalTime->getNextTrainArrivalTime($stationId, $uriElement[2]);
                  } else return $this->badRequestResponse();

                default:
                  return $this->badRequestResponse();
              }
            } else return $this->badRequestResponse();

          default:
            return $this->badRequestResponse();
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
}