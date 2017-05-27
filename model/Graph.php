<?php

/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 25-May-17
 * Time: 18:34
 */
class Graph {
  private $vertexCount;
  /**
   * @var int $adjList
   */
  private $adjList;

  function __construct() {
    $station = new Station();
    $distance = new Distance();
    $distanceList = $distance->getDistanceList();
    $this->adjList = array_fill(1, $station->getMaxId(), 0);
    foreach ($distanceList as $row) {
      $this->addEdge($row['stationId1'], $row['stationId2'], $row['distance']);
    }
    //print_r($this->adjList);
  }

  function addEdge($v1, $v2, $weight) {
    $vertexTemp1 = new GraphVertex();
    $vertexTemp1->setId($v2);
    $vertexTemp1->setDistance($weight);
    if (!is_array($this->adjList[$v1]))
      $this->adjList[$v1] = array();
    array_push($this->adjList[$v1], $vertexTemp1);
    //also add to adj list of station v2
    $vertexTemp2 = new GraphVertex();
    $vertexTemp2->setId($v1);
    $vertexTemp2->setDistance($weight);
    if (!is_array($this->adjList[$v2]))
      $this->adjList[$v2] = array();
    array_push($this->adjList[$v2], $vertexTemp2);
  }

  function shortestPath($startVertex, $endVertex) {
    $station = new Station();
    $maxId = $station->getMaxId();
    $distances = array_fill(1, $maxId, 999999);
    $previous = array_fill(1, $maxId, -1);
    $unvisited = array_fill(1, $maxId, 1);
    $distances[$startVertex] = 0;

    $whileCount = 0;
    //while (!$this->isAllVisited($unvisited)) {
    while ($unvisited[$endVertex] == 1) {
      /*$endVertex is set as visited IFF its distance form $startVertex is min from all other distances
      that means there is no other path which have distances < current distance
      */
      $cur = $this->getMinValueIndex($distances, $unvisited);
      $unvisited[$cur] = 0;
      $curAdjList = $this->getVertexAdjList($cur);
      foreach ($curAdjList as $neighbor) {
        /**
         * @var GraphVertex $neighbor
         */
        $testDistance = $distances[$cur] + $neighbor->getDistance();
        if ($testDistance < $distances[$neighbor->getId()]) {
          $distances[$neighbor->getId()] = $testDistance;
          $previous[$neighbor->getId()] = $cur;
        }
      }
      $whileCount++;
      if ($whileCount > $maxId) return false;
    }
    //echo "$whileCount loops\n";
    $systemConfig = new SystemConfig();
    $fpd = $systemConfig->getFieldValueByKey('value', 'farePerDistance');
    $return = array();
    $return['distance'] = $distances[$endVertex];
    $return['fare'] = $fpd * $return['distance'];
    $return['route'] = $this->analyseRoute($this->generateRoute($startVertex, $endVertex, $previous));
    return $return;
  }

  function leastTransfer($startVertex, $endVertex) {
    $station = new Station();
    $maxId = $station->getMaxId();
    $distances = array_fill(1, $maxId, 999999);
    $lineCount = array_fill(1, $maxId, 999999);
    $previous = array_fill(1, $maxId, -1);
    $unvisited = array_fill(1, $maxId, 1);
    $distances[$startVertex] = 0;
    $lineCount[$startVertex] = 1;

    $whileCount = 0;
    while (!$this->isAllVisited($unvisited)) {
      /* Because we consider 2 conditions: distance and number of line, a visited vertex may be updated
      with smaller distance. So we must consider all verties in graph
      */
      $cur = $this->getMinValueIndex($lineCount, $unvisited);
      $unvisited[$cur] = 0;
      $curAdjList = $this->getVertexAdjList($cur);
      foreach ($curAdjList as $neighbor) {
        /**
         * @var GraphVertex $neighbor
         */
        $nbId = $neighbor->getId();
        if ($previous[$cur] != -1 && $this->isLineChanged($previous[$cur], $nbId))
          $testLineCount = $lineCount[$cur] + 1;
        else $testLineCount = $lineCount[$cur];
        $testDistance = $distances[$cur] + $neighbor->getDistance();

        if ($testLineCount < $lineCount[$nbId] ||
          ($testLineCount == $lineCount[$nbId] && $testDistance < $distances[$nbId])) {
          $lineCount[$nbId] = $testLineCount;
          $distances[$nbId] = $testDistance;
          $previous[$nbId] = $cur;
        }
      }
      $whileCount++;
    }
    //echo "$whileCount loops\n";
    $systemConfig = new SystemConfig();
    $fpd = $systemConfig->getFieldValueByKey('value', 'farePerDistance');
    $return = array();
    $return['distance'] = $distances[$endVertex];
    $return['fare'] = $fpd * $return['distance'];
    $return['route'] = $this->analyseRoute($this->generateRoute($startVertex, $endVertex, $previous));
    return $return;
  }

  function getVertexAdjList($vertex) {
    return $this->adjList[$vertex];
  }

  function isAllVisited($array) {
    //print_r($array);
    foreach ($array as $ele) {
      if ($ele == 1)
        return false;
      else continue;
    }
    return true;
  }

  function getMinValueIndex($distances, $unvisited) {
    $min = 999999;
    $minIndex = 0;
    foreach ($distances as $index => $value) {
      if ($min > $value && $unvisited[$index] == 1) {
        $min = $value;
        $minIndex = $index;
      }
    }
    return $minIndex;
  }

  function generateRoute($startVertex, $endVertex, $previous) {
    $route = array();
    array_push($route, $endVertex);
    $prevVertex = $endVertex;
    while ($prevVertex != $startVertex) {
      $prevPrevVertex = $previous[$prevVertex];
      $route[] = $prevPrevVertex;
      $prevVertex = $prevPrevVertex;
    }
    $route = array_reverse($route);
    return $route;
  }

  //route is: ...- v1 - v1.5 - v2 - .....
  function isLineChanged($v1, $v2) {
    $belongTo = new BelongTo();
    $lineV1 = $belongTo->getLines($v1);
    $lineV2 = $belongTo->getLines($v2);
    foreach ($lineV1 as $line1) {
      foreach ($lineV2 as $line2) {
        if ($line1 == $line2) return false;
      }
    }
    return true;
  }

  function arrayIndexToStationName($array) {
    $namedArray = array();
    $station = new Station();
    foreach ($array as $id => $value) {
      $namedArray[$station->getFieldValueByKey('name', $id)] = $value;
    }
    return $namedArray;
  }

  function analyseRoute($route) {
    $imax = count($route)-3;
    $analysedRoute = array();
    $station = new Station();
    foreach ($route as $index => $stationId) {
      $analysedRoute[$index]['stationId'] = $stationId;
      $analysedRoute[$index]['stationName'] = $station->getFieldValueById('name', $stationId);
      $analysedRoute[$index]['isLineChanged'] = 0;
    }
    for ($i=0;$i<=$imax;$i++) {
      if ($this->isLineChanged($route[$i], $route[$i+2])) {
        $analysedRoute[$i+1]['isLineChanged'] = 1;
      }
    }
    return $analysedRoute;
  }

}