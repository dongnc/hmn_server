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
    $db = new Db();
    $station = new Station();
    $distance = new Distance();
    $distanceList = $distance->getDistanceList();
    $this->adjList = array_fill(1, $station->getMaxId(),0);
    foreach ($distanceList as $row) {
      $this->addEdge($row['stationId1'], $row['stationId2'], $row['distance'] );
    }
    print_r($this->adjList);
  }

  function addEdge($v1, $v2, $weight) {
    echo "addEdge ($v1, $v2, $weight)" . PHP_EOL;
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
}