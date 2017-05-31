<?php

/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 22-May-17
 * Time: 16:48
 */
class LineController extends BaseController {
  public function __construct() {
    $this->loadModel('Line');
  }

  public function processRequest($method, $uriElement) {
    switch ($method) {
      case 'GET':
        switch (count($uriElement)) {
          case 0: //API: /lines
            return $this->getLineList();

          case 1: //API: /lines/xyz
            if (is_numeric($uriElement[0])) {
              $lineId = $uriElement[0];
              return $this->model->getLineInfo($lineId);
            } else return $this->badRequestResponse();

          case 2: //API: /lines/xyz/xyz
            if (is_numeric($uriElement[0])) {
              $lineId = $uriElement[0];
              switch ($uriElement[1]) {
                case "name":
                  return $this->model->getFieldValueById('name', $lineId);

                case "route":
                  return $this->model->getRoute($lineId);

                default:
                  return $this->badRequestResponse();
              }
            } else return $this->badRequestResponse();

          case 3: //API: /lines/xyz/xyz/xyz
            if (is_numeric($uriElement[0])) {
              $lineId = $uriElement[0];
              switch ($uriElement[1]) {
                case "nexttrain":
                  if (is_numeric($uriElement[2])) {
                    $arrivalTime = new ArrivalTime();
                    return $arrivalTime->getNextTrainArrivalTime($lineId, $uriElement[2]);
                  } else return $this->badRequestResponse();

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

  public function getLineList() {
    return $this->model->getLineList();
  }


}