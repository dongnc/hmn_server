<?php

/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 01-May-17
 * Time: 23:09
 */

class Train extends DbTable {
  function __construct() {
    parent::__construct();
    $this->table = 'train';
    $this->pk = 'id';
  }

  function getTrainListByLineId($lineId) {
    $result = $this->getList("SELECT id FROM " . $this->table . " WHERE lineId = " . $lineId);
    if ($result != false) {
      $trainList = array();
      foreach ($result as $subArray) {
        $trainList[] = $subArray['id'];
      }
      return $trainList;
    } else return false;
  }
}