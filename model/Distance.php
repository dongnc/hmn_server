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
    if ($stationId2 < $stationId1) {
      $temp = $stationId1;
      $stationId1 = $stationId2;
      $stationId2 = $temp;
    }
    $sttm = "SELECT distance FROM " . $this->table . " WHERE stationId1 = " . $stationId1 . " AND stationId2 = " . $stationId2;
    $result = $this->getOne($sttm);
    if ($result != false)
      return $result['distance'];
    else return false;
  }

  function getDistanceList() {
    return $this->getList("SELECT * FROM " . $this->table);
  }

  /* rearrange table such that stationId1 < stationId2 */
  function rearrange() {
    $distanceList = $this->getDistanceList();
    foreach ($distanceList as $row) {
      if ($row['stationId1'] > $row['stationId2']) {
        $this->setFieldValueById('stationId1', $row['stationId2'], $row['id']);
        $this->setFieldValueById('stationId2', $row['stationId1'], $row['id']);
      }
    }
  }
}