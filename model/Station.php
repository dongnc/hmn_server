<?php

/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 10-May-17
 * Time: 15:03
 */

class Station extends DbTable {
  function __construct() {
    parent::__construct();
    $this->table = 'station';
    $this->pk = 'id';
  }

  function getStationList() {
    return $this->getList("SELECT id, code, name, shape, coords FROM " . $this->table . " ORDER BY name");
  }

  function getStationInfo($stationId) {
    $stationInfo = $this->getOne("SELECT code, name, isMutual FROM " . $this->table . " WHERE id = " . $stationId);
    $belongTo = new BelongTo();
    $stationInfo['line'] = $belongTo->getLines($stationId);
    return $stationInfo;
  }

}