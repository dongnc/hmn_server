<?php

/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 30-Apr-17
 * Time: 23:32
 */

class SystemConfig extends DbTable {
  function __construct() {
    parent::__construct();
    $this->table = 'systemConfig';
    $this->pk = 'key';
  }
}