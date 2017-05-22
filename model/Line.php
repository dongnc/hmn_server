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

  function getRoute($lineId) {
    $result = parent::getFieldValueById('route', $lineId);
    if ($result != false) {
      return explode('-', $result);
    }
    else return false;
  }
}
