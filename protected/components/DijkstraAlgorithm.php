<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DijsktraAlgorithm
 *
 * @author Syakur Rahman
 *
 * @var $initalNode Node
 * @var $targetNode Node
 * @var $visitedNodes Array(Node)
 */
define('I', 10000000); // define infinite distance
define('E', -31337);

class DijkstraAlgorithm {

    /**
     * @param int startPoint
     * @param array routes[] = array($startPoint,$endPoint,$distance)
     */
    private $startNode;
    private $endNode = null;
    private $routes = array(); // all possible routes between each two points (single direction)
    private $points = array(); // all the points on the map
    private $visitedNodes = array();
    private $currentPathNodes = array();
    private $distances = array(); // the closest distance from start point to each points
    private $pathes = array(); // path from each points to its neibor on the best path to the start point
    private $fullPathes; // path from start point to each points

    /**
     * Build Dijkstra model, find best path and closest distance from start point to each point on the map
     * @return null
     * @param object $intStartPoint
     * @param object $aRoutes
     */

    public function __construct($intStartPoint, $endNode = E) {
        $this->createGraph($intStartPoint, $endNode);
        $this->createGraph($endNode, $intStartPoint);
        $aRoutes = $this->routes;

        $this->startNode = $intStartPoint;
        if ($endNode != E) {
            $this->endNode = $endNode;
        }

        // For debugging purpose, show all routes
//        print($this->startNode . ":" . $this->endNode . "<br />");
//        foreach ($aRoutes as $route) {
//            print_r($route);
//            print("<br />");
//        }
//        die();

        foreach ($aRoutes as $aRoute) {
            if (!in_array($aRoute[0], $this->points)) {
                $this->points[] = $aRoute[0];
            }
            if (!in_array($aRoute[1], $this->points)) {
                $this->points[] = $aRoute[1];
            }
        }

        $this->visitedNodes = array($intStartPoint);
        $this->currentPathNodes = $this->array_remove($this->points, $intStartPoint);

        foreach ($this->currentPathNodes as $intPoint) {
            $this->distances[$intPoint] = I;
        }
        $this->distances[$intStartPoint] = 0;

        $this->findPath();
    }

    public function sortGraph() {
        foreach ($this->routes as $route) {
            $node_ids[] = $route[0];
        }

        array_multisort($node_ids, SORT_ASC, $this->routes);
    }

    public function createGraph($startNodeId, $endNodeId) {
        foreach (NeighboringNode::model()->findAll("NodeId=$startNodeId OR NeighboringNodeId=$startNodeId") as $node) {
            if ($startNodeId != $endNodeId) {
                // Create route
                $data = array($node->NodeId, $node->NeighboringNodeId, $node->Distance);
                if (!in_array($data, $this->routes)) {
                    $this->routes[] = $data;
                    $this->createGraph($node->NodeId, $endNodeId);
                }

                // Create singular node for forward route
                $data = array($node->NodeId, $node->NodeId, 0);
                if (!in_array($data, $this->routes)) {
                    $this->routes[] = $data;
                }

                // Create reversed route
                $reversedData = array($node->NeighboringNodeId, $node->NodeId, $node->Distance);
                if (!in_array($reversedData, $this->routes)) {
                    $this->routes[] = $reversedData;
                    $this->createGraph($node->NeighboringNodeId, $endNodeId);
                }

                // Create singular node for reversed route
                $reversedData = array($node->NeighboringNodeId, $node->NeighboringNodeId, 0);
                if (!in_array($reversedData, $this->routes)) {
                    $this->routes[] = $reversedData;
                }
            }
        }

        $this->sortGraph();
    }

    /**
     * function to get the best path
     * @return pathes for each node on the map
     */
    public function getPath() {
        foreach ($this->points as $intPoint) {
            $this->fillFullPath($intPoint, $intPoint);
        }
        if ($this->endNode !== null) {
            $bestPath = array_unique($this->fullPathes[$this->endNode]);

            return $bestPath;
        }
        return $this->fullPathes;
    }

    /**
     * function to get the closest distance
     * @return
     */
    public function getDistance() {
        if ($this->endNode !== null)
            return $this->distances[$this->endNode];
        return $this->distances;
    }

    public function getPolyline() {
        $polyline = null;
        $points = null;
        if ($this->endNode !== null) {
            foreach ($this->getPath() as $node) {
                $nodeObj = Node::model()->findByPk($node);
                $points[] = array($nodeObj->Latitude, $nodeObj->Longitude);
            }

            // For debugging purpose only
//            foreach ($points as $point) {
//                print_r($point);
//                print("<br />");
//            }

            $polyline = EGMapPolylineEncoder::encodePoints($points);
        }

        return $polyline;
    }

    /**
     * Remove specified element from array
     * @return array
     * @param array $arr : array to be processing
     * @param array $value : the element to be remove from the array
     */
    private function array_remove($arr, $value) {
        return array_values(array_diff($arr, array($value)));
    }

    /**
     * Dijkstra algorithm implementation
     * @return null
     */
    private function findPath() {
        while (!empty($this->currentPathNodes)) {
            $intShortest = I;
            foreach ($this->visitedNodes as $intRed) {
                // find possible route
                foreach ($this->routes as $aRoute) {
                    if ($intRed == $aRoute[0]) {
                        // rewrite distance
                        $intDistance = $this->distances[$intRed] + $aRoute[2];
                        if ($this->distances[$aRoute[1]] > $intDistance) {
                            $this->distances[$aRoute[1]] = $intDistance;
                            // change the path
                            if ($intRed == $this->startNode || $intRed == $aRoute[1]) {

                            } else {
                                $this->pathes[$aRoute[1]] = $intRed;
                            }
                        }

                        // find the nearest neighbor
                        if (!in_array($aRoute[1], $this->visitedNodes) && $aRoute[2] < $intShortest) {
                            $intShortest = $aRoute[2];
                            $intAddPoint = $aRoute[1];
                        }
                    }
                }
            }

            $this->visitedNodes[] = $intAddPoint;
            $this->currentPathNodes = $this->array_remove($this->currentPathNodes, $intAddPoint);
        }
    }

    /**
     * mid step function to find full path from start point to the end point.
     * @return null
     * @param int $intEndPoint
     * @param int $intMidPoint
     */
    private function fillFullPath($intEndPoint, $intMidPoint) {
        if (isset($this->pathes[$intMidPoint])) {
            $this->fullPathes[$intEndPoint][] = $this->pathes[$intMidPoint];
            $this->fillFullPath($intEndPoint, $this->pathes[$intMidPoint]);
        } else {
            $this->fullPathes[$intEndPoint][] = $this->startNode;
        }
    }

}
