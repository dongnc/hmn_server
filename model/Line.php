<?php

/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 30-Apr-17
 * Time: 15:31
 */

class Line extends DbTable {
  function __construct() {
    parent::__construct();
    $this->table = 'line';
    $this->pk = 'id';
  }

  function getLineList() {
    return $this->getList("SELECT id, code, name FROM " . $this->table . " ORDER BY id");
  }

  function getLineInfo($lineId) {
    $result =  $this->getOne("SELECT code, name, startTime, endTime, waitTime, frequency, trainCount FROM " . $this->table . " WHERE id = " . $lineId);
    if ($result != false) {
      $result['distance'] = $this->getLineDistance($lineId);
      $result['totalTime'] = $this->getLineTime($lineId);
      $result['route'] = $this->getRoute($lineId);
    }
    return $result;
  }

  function getRoute($lineId) {
    $result = parent::getFieldValueById('route', $lineId);
    if ($result != false) {
      $route = explode('-', $result);
      foreach ($route as $index => $stationId) {
        $route[$index] = intval($stationId);
      }
      return $route;
    }
    else return false;
  }

  function getLineDistanceList($lineId) {
    $distance = new Distance();
    $route = $this->getRoute($lineId);
    $stationCount = count($route);
    $distanceList = array();
    for ($i = 0; $i <= $stationCount - 2; $i++) { //there are stationCount-1 distances
      $distanceList[$i] = $distance->getDistance($route[$i], $route[$i + 1]);
    }
    return $distanceList;
  }

  function getLineDistance($lineId) {
    $distanceList = $this->getLineDistanceList($lineId);
    return array_sum($distanceList);
  }

  function getLineTime($lineId) {
    $waitTime = $this->getFieldValueById('waitTime', $lineId);
    $waitTime = strtotime("1970-01-01 $waitTime UTC");
    $systemConfig = new SystemConfig();
    $avgSpeed = $systemConfig->getFieldValueByKey('value', 'avgSpeed');
    $distanceList = $this->getLineDistanceList($lineId);
    $lineTime =  array_sum($distanceList) * 3600 / $avgSpeed + (count($distanceList)-1)*$waitTime;
    return Util::roundMinute($lineTime);
  }
}
