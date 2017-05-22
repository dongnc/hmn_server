<?php

/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 21-May-17
 * Time: 13:34
 */
class BelongTo extends DbTable {
  function __construct() {
    parent::__construct();
    $this->table = 'belongTo';
    $this->pk = 'id';
  }

  function getLines($stationId) {
    return $this->getList("SELECT lineId FROM " . $this->table . " WHERE stationId = " . $stationId);
  }
}