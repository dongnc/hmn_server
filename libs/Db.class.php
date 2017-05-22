<?php

/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 27-Apr-17
 * Time: 11:34
 */

class Db {
  /**
   * @var PDO $conn
   */
  //private $conn;
  public $conn;
  private static $_instance;

  static function getInstance(){
    if(self::$_instance !== null){
      return self::$_instance;
    }
    self::$_instance = new Db();
    return self::$_instance;
  }

  function dbConnect() {
    global $config;
    $dbms = $config['dbms'];
    $db_hostname = $config['db_hostname'];
    $db_name = $config['db_name'];
    $db_username = $config['db_username'];
    $db_password = $config['db_password'];
    if (!$this->conn) {
      try {
        $this->conn = new PDO("$dbms:host=$db_hostname;dbname=$db_name;charset=utf8", $db_username, $db_password);
        //$this->conn = new PDO("mysql:host=localhost;dbname=hmn;charset=utf8", "root", "");
        $this->conn->exec("set names utf8"); //for PHP < 5.3.6
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (PDOException $e) {
        echo "Fail to connect database: " . $e->getMessage();
      }
    }
  }

  function dbDisconnect() {
    if ($this->conn) {
      $this->conn = null;;
    }
  }

  function dbInsertOne($table, $data) {
    $this->dbConnect();
    $field_list = '';
    $value_list = '';
    // Lặp qua data
    foreach ($data as $key => $value) {
      $field_list .= ",$key";
      $value_list .= "," . $this->conn->quote($value);
    }
    // Vì sau vòng lặp các biến $field_list và $value_list sẽ thừa một dấu , nên ta sẽ dùng hàm trim để xóa đi
    $sql = $this->conn->prepare('INSERT INTO ' . $table . '(' . trim($field_list, ',') . ') VALUES (' . trim($value_list, ',') . ')');
    $sql->execute();
  }

  function dbUpdate($table, $data, $where) {
    $this->dbConnect();
    $sql = '';
    // Lặp qua data
    foreach ($data as $key => $value) {
      $sql .= "$key = " . $this->conn->quote($value) . ",";
    }
    // Vì sau vòng lặp biến $sql sẽ thừa một dấu , nên ta sẽ dùng hàm trim để xóa đi
    echo 'UPDATE ' . $table . ' SET ' . trim($sql, ',') . ' WHERE ' . $where;
    $sql =  $this->conn->prepare('UPDATE ' . $table . ' SET ' . trim($sql, ',') . ' WHERE ' . $where);
    $sql->execute();
  }

  function dbDelete($table, $where) {
    $this->dbConnect();
    $sql = $this->conn->prepare("DELETE FROM $table WHERE $where");
    $sql->execute();
  }

  function getList($sttm) {
    $this->dbConnect();
    $sql = $this->conn->prepare($sttm);
    $isSuccess = $sql->execute();
    if (!$isSuccess) {
      die ('Wrong query');
    }
    $return = array();
    // Lặp qua kết quả để đưa vào mảng
    while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
      $return[] = $row;
    }
    // Xóa kết quả khỏi bộ nhớ
    $sql->closeCursor();
    return $return;
  }

  function getOne($sttm) {
    $this->dbConnect();
    $sql = $this->conn->prepare($sttm);
    $isSuccess = $sql->execute();
    if (!$isSuccess) {
      die ('Wrong query');
    }
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    // Xóa kết quả khỏi bộ nhớ
    $sql->closeCursor();
    if ($row) {
      return $row;
    }
    return false;
  }
}