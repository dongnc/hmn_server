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
    $this->adjList = array_fill(1, $station->getMaxId(),0);
    foreach ($distanceList as $row) {
      $this->addEdge($row['stationId1'], $row['stationId2'], $row['distance'] );
    }
    //print_r($this->adjList);
  }

  function addEdge($v1, $v2, $weight) {
    $vertexTemp1= new GraphVertex();
    $vertexTemp1->setId($v2);
    $vertexTemp1->setWeight($weight);
    if (!is_array($this->adjList[$v1]))
      $this->adjList[$v1] = array();
    array_push($this->adjList[$v1],$vertexTemp1);
    //also add to adj list of station v2
    $vertexTemp2= new GraphVertex();
    $vertexTemp2->setId($v1);
    $vertexTemp2->setWeight($weight);
    if (!is_array($this->adjList[$v2]))
      $this->adjList[$v2] = array();
    array_push($this->adjList[$v2],$vertexTemp2);
  }

  function shortestPath($startVertex, $endVertex) {
    $station = new Station();
    $maxId = $station->getMaxId();
    $distances = array_fill(1, $maxId,999999);
    $previous = array_fill(1, $maxId,-1);
    $unvisited = array_fill(1, $maxId,1);
    $distances[$startVertex] = 0;

    while (!$this->isAllVisited($unvisited)) {
      $cur = $this->getMinValueIndex($distances, $unvisited);
      $unvisited[$cur] = 0;
      $curAdjList = $this->getVertexAdjList($cur);
      foreach ($curAdjList as $neighbor) {
        /**
         * @var GraphVertex $neighbor
         */
        $testDistance = $distances[$cur] + $neighbor->getWeight();
        if ($testDistance < $distances[$neighbor->getId()]) {
          $distances[$neighbor->getId()] = $testDistance;
          $previous[$neighbor->getId()] = $cur;
        }
      }
    }
    return ($this->generateRoute($startVertex, $endVertex, $previous));
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
    $namedRoute = array();
    $station = new Station();
    foreach ($route as $index => $stationId) {
      $namedRoute[$index] = $station->getFieldValueById('name', $stationId);
    }
    return $namedRoute;
  }

}