<?php

/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 30-Apr-17
 * Time: 23:00
 */

class ArrivalTime extends DbTable {
  function __construct() {
    parent::__construct();
    $this->table = 'arrivalTime';
    $this->pk = 'id';
  }

  function printArrivalTimeList($arrivalTimeList) {
    echo '<table style="border-collapse: collapse">';
    echo '<tr>';
    echo '<th>StationId</th>';
    echo '<th>TrainId</th>';
    echo '<th>destStaionId</th>';
    echo '<th>Time</th>';
    echo '</tr>';
    foreach ($arrivalTimeList as $row) {
      echo '<tr>';
      foreach ($row as $key => $field) {
        echo '<td style="border: 1px solid black; padding: 5px;">';
        if ($key == 'time')
          echo $field . ' = ' . gmdate("H:i:s", $field);
        else echo $field;
        echo '</td>';
      }
      echo '</tr>';
    }
    echo '</table>';
  }

  function calculateArrivalTime($lineId) {
    // input variables
    $line = new Line();
    $systemConfig = new SystemConfig();
    $distance = new Distance();
    $train = new Train();
    $trainCount = $line->getFieldValueById('trainCount', $lineId);
    if ($trainCount % 2 != 0) exit("Number of trains cannot be odd!");
    $startTime = $line->getFieldValueById('startTime', $lineId);
    $startTime = strtotime("1970-01-01 $startTime UTC"); // convert time string to second
    $waitTime = $line->getFieldValueById('waitTime', $lineId);
    $waitTime = strtotime("1970-01-01 $waitTime UTC");
    $endTime = $line->getFieldValueById('endTime', $lineId);
    $endTime = strtotime("1970-01-01 $endTime UTC");
    $avgSpeed = $systemConfig->getFieldValueByKey('value', 'avgSpeed');
    $route = $line->getRoute($lineId);
    $stationCount = count($route);
    $startStation = $route[0];
    $endStation = $route[$stationCount - 1];
    $distanceTimes = array();
    for ($i = 0; $i <= $stationCount - 2; $i++) { //there are stationCount-1 distances
      $distanceTimes[$i] = $distance->getDistance($route[$i], $route[$i + 1]) * 3600 / $avgSpeed; // km/second
    }
    echo '<h1>' . $lineId . '</h1>';
    $oneDirectTime = array_sum($distanceTimes) + $waitTime * ($stationCount - 2);
    echo 'oneDirectTime = ' . $oneDirectTime . ' = ' . gmdate("H:i:s", $oneDirectTime) . '<br>';
    $circleTime = 2 * $oneDirectTime + 2 * $waitTime;
    $frequency = (2 * $oneDirectTime + 2 * $waitTime) / $trainCount - $waitTime;
    echo "frequency = " . $frequency . " = " . gmdate("H:i:s", $frequency) . "<br>";
    //$line->setFieldValueById('frequency', gmdate("H:i:s", $frequency), $lineId);
    $arrivalTimeList = array();
    $trainList = $train->getTrainListByLineId($lineId);
    if ($trainList == false || count($trainList) != $trainCount)
      exit("Number of train in database does not match!");

    //-------------------------------------------------foreach first-half trains
    $prevTrainArrivalTime = 0;
    for ($i = 0; $i <= $trainCount / 2 - 1; $i++) {
      $arrivalTimeList[] = array(
        'stationId' => $startStation,
        'trainId' => $trainList[$i],
        'destStationId' => $endStation,
        'time' => $i == 0 ? $startTime - $waitTime : $prevTrainArrivalTime + $frequency + $waitTime
        // first train arrivalTime depends on startTime, others' depend on first train's
      );
      $prevTrainArrivalTime = end($arrivalTimeList)['time']; //for next train
      $arrivalTime = 0;
      do {
        for ($j = 1; $j <= $stationCount - 1; $j++) { //j is station
          $prevStationArrivalTime = end($arrivalTimeList)['time'];
          $arrivalTime = $prevStationArrivalTime + $distanceTimes[$j - 1] + $waitTime;
          $arrivalTimeList[] = array(
            'stationId' => $route[$j],
            'trainId' => $trainList[$i],
            'destStationId' => ($j==$stationCount - 1) ? $startStation : $endStation,
            'time' => $prevStationArrivalTime + $distanceTimes[$j - 1] + $waitTime
          );
        }
        //invert direction
        for ($j = $stationCount - 2; $j >= 0; $j--) {
          $prevStationArrivalTime = end($arrivalTimeList)['time'];
          $arrivalTimeList[] = array(
            'stationId' => $route[$j],
            'trainId' => $trainList[$i],
            'destStationId' => $j == 0 ? $endStation : $startStation,
            'time' => $prevStationArrivalTime + $distanceTimes[$j] + $waitTime
          );
          $arrivalTime = end($arrivalTimeList)['time'];
        }
      } while ($arrivalTime + $circleTime <= $endTime);
    }

    //-------------------------------------------------foreach last-half trains
    $prevTrainArrivalTime = 0;
    for ($i = $trainCount / 2; $i <= $trainCount - 1; $i++) {
      $arrivalTimeList[] = array(
        'stationId' => $endStation,
        'trainId' => $trainList[$i],
        'destStationId' => $startStation,
        'time' => $i == $trainCount / 2 ? $startTime - $waitTime : $prevTrainArrivalTime + $frequency + $waitTime
      );
      $prevTrainArrivalTime = end($arrivalTimeList)['time']; //for next train
      $arrivalTime = 0;
      do {
        for ($j = $stationCount - 2; $j >= 0; $j--) {
          $prevStationArrivalTime = end($arrivalTimeList)['time'];
          $arrivalTimeList[] = array(
            'stationId' => $route[$j],
            'trainId' => $trainList[$i],
            'destStationId' => $j == 0 ? $endStation : $startStation,
            'time' => $prevStationArrivalTime + $distanceTimes[$j] + $waitTime
          );
          $arrivalTime = end($arrivalTimeList)['time'];
        }
        for ($j = 1; $j <= $stationCount - 1; $j++) { //j is station
          $prevStationArrivalTime = end($arrivalTimeList)['time'];
          $arrivalTime = $prevStationArrivalTime + $distanceTimes[$j - 1] + $waitTime;
          $arrivalTimeList[] = array(
            'stationId' => $route[$j],
            'trainId' => $trainList[$i],
            'destStationId' => ($j==$stationCount - 1) ? $startStation : $endStation,
            'time' => $prevStationArrivalTime + $distanceTimes[$j - 1] + $waitTime
          );
        }
      } while ($arrivalTime + $circleTime <= $endTime);
    }
    //$this->printArrivalTimeList($arrivalTimeList);
    //echo count($arrivalTimeList);
    //$this->insertArrivalTime($arrivalTimeList);
  }

  function insertArrivalTime($arrivalTimeList) {
    echo count($arrivalTimeList) . '<br>';
    if (empty($arrivalTimeList))
      exit ("List is empty!<br>");
    $isExist = $this->getOne("SELECT * FROM " . $this->table . " WHERE trainId = " . $arrivalTimeList[0]['trainId']);
    if ($isExist)
      exit("ArrivalTime of this train already exists!<br>");
    $arrivalTimeListCount = count($arrivalTimeList);
    $field_list = 'stationId,trainId,destStationId,time';
    $value_list = '';
    for ($i = 0; $i <= $arrivalTimeListCount - 1; $i++) { //foreach row in arrialTimeList
      //for ($i = 0; $i <= 200; $i++) { //foreach row in arrialTimeList
      $value_list .= "(";
      foreach ($arrivalTimeList[$i] as $key => $value) {
        if ($key == 'time')
          $value_list .= $this->conn->quote(gmdate("H:i:s", $value));
        else $value_list .= $this->conn->quote($value) . ",";
      }
      $value_list .= "),";
      if (($i > 0 && $i % 200 == 0) || $i == $arrivalTimeListCount - 1) {
        //echo 'INSERT INTO ' . $this->table . '(' . $field_list . ') VALUES ' . trim($value_list, ',');
        //echo '<br>';
        $sql = $this->conn->prepare('INSERT INTO ' . $this->table . '(' . $field_list . ') VALUES ' . trim($value_list, ','));
        $sql->execute();
        $value_list = '';
      }
    }
  }

  function getArrivalTimeInfo($stationId) {
    $station = new Station();
    $destStationList = $this->getDestStationIdList($stationId);
    $arrivalTimeInfo = array();
    $i = 0;
    foreach ($destStationList as $destStationId) {
      $arrivalTimeInfo[$i]['destId'] = $destStationId;
      $arrivalTimeInfo[$i]['destName'] = $station->getFieldValueById('name', $destStationId);
      $arrivalTimeInfo[$i]['firstTrain'] = Util::roundMinute($this->getFirstTrainArrivalTime($stationId, $destStationId));
      $arrivalTimeInfo[$i]['lastTrain'] = Util::roundMinute($this->getLastTrainArrivalTime($stationId, $destStationId));
      $arrivalTimeInfo[$i++]['nextTrain'] = $this->getNextTrainArrivalTime($stationId, $destStationId);
    }
    var_dump($arrivalTimeInfo);
    return $arrivalTimeInfo;
  }

  function getDestStationIdList($stationId) {
    $resultTemp = $this->getList("SELECT DISTINCT destStationId FROM " . $this->table . " WHERE stationId = " . $stationId);
    if ($resultTemp != false) {
      $result = array();
      foreach ($resultTemp as $destStation) {
        $result[] = $destStation['destStationId'];
      }
      return $result;
    } else return false;
  }

  function getFirstTrainArrivalTime($stationId, $destStationId) {
    $result = $this->getOne("SELECT MIN(time) AS firstTime FROM " . $this->table . " WHERE stationId = " . $stationId . " AND destStationId = " . $destStationId);
    if ($result != false) {
      return $result['firstTime'];
    } else return false;
  }

  function getLastTrainArrivalTime($stationId, $destStationId) {
    $result = $this->getOne("SELECT MAX(time) AS lastTime FROM " . $this->table . " WHERE stationId = " . $stationId . " AND destStationId = " . $destStationId);
    if ($result != false) {
      return $result['lastTime'];
    } else return false;
  }

  function getNextTrainArrivalTime($stationId, $destStationId) {
    $result = $this->getOne("SELECT MIN(time) AS nextTime FROM " . $this->table . " WHERE stationId = " . $stationId . " AND destStationId = " . $destStationId . " AND time > CURRENT_TIME");
    if ($result != false) {
      return $result['nextTime'];
    } else return false;
  }

  function getPrevTrainArrivalTime($stationId, $destStationId) {
    $result = $this->getOne("SELECT MAX(time) AS prevTime FROM " . $this->table . " WHERE stationId = " . $stationId . " AND destStationId = " . $destStationId . " AND time < CURRENT_TIME");
    if ($result != false) {
      return $result['prevTime'];
    } else return false;
  }
}

