<?php

/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 30-Apr-17
 * Time: 22:36
 */

class Distance extends DbTable {
  function __construct() {
    parent::__construct();
    $this->table = 'distance';
    $this->pk = 'id';
  }

  function getDistance($stationId1, $stationId2) {
    $sttm = "SELECT distance FROM " . $this->table . " WHERE stationId1 = " . $stationId1 . " AND stationId2 = " . $stationId2;
    $result = $this->getOne($sttm);
    if ($result != false)
      return $result['distance'];
    else return false;
  }


}