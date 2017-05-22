<?php

/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 30-Apr-17
 * Time: 14:14
 */

require_once(__DIR__ . "/Db.class.php");

class DbTable extends Db {
  protected $table = '';
  protected $pk = '';

  function __construct() {
    parent::dbConnect();
  }

  function __destruct() {
    parent::dbDisconnect();
  }

  function insertOne($data) {
    parent::dbInsertOne($this->table, $data);
  }

  function deleteById($id) {
    $this->dbDelete($this->table, $this->pk . '=' . (int)$id);
  }

  function updateById($data, $id) {
    $this->dbUpdate($this->table, $data, $this->pk . "=" . (int)$id);
  }

  function selectById($select, $id) {
    $sttm = "select $select from " . $this->table . " where " . $this->pk . " = " . (int)$id;
    return $this->getOne($sttm);
  }

  function getFieldValueById($field, $id) {
    $result = $this->selectById($field, $id);
    if ($result != false)
      return $result[$field];
    else return false;
  }

  function getFieldValueByKey($field, $key) {
    $sttm = "select $field from " . $this->table . " where " . $this->table . "." . $this->pk . " = '" . $key . "'";
    //echo $sttm;
    $result = $this->getOne($sttm);
    if ($result != false)
      return $result[$field];
    else return false;
  }

  function setFieldValueById($field, $value, $id) {
    $data = array($field => $value);
    $this->updateById($data,$id);
  }

  /**
   * @return string
   */
  public function getTable() {
    return $this->table;
  }
}