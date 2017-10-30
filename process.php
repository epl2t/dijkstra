<?php

class Matrix
{
    public $matrix;
}

class ProcessMap extends Matrix
{
    public $startVertex;
    public $finishVertex;
    private $mapHeight;
    private $mapWidth;

    public function __construct ($map)
    {
        $this->map=$map;
    }

    public function createMatrixFromArray()
    {
        $this->mapHeight = count($this->map);
        $this->mapWidth = count($this->map[0]);

        for ($i = 0; $i < $this->mapHeight; $i++) {
            for ($j = 0; $j < $this->mapWidth; $j++) {
                $vertexNumber = $this->vertexNumber($j,$i);
                if ($this->map[$i][$j] == 1) {
                    $this->startVertex=$vertexNumber;
                }
                if ($this->map[$i][$j] == 2) {
                    $this->finishVertex=$vertexNumber;
                }
                if (($nearVertex = $this->vertexExists($j - 1, $i - 1)) !== false && $this->map[$i][$j]!=9) {
                    $this->matrix[$vertexNumber][$nearVertex] = 1.2;
                }
                if (($nearVertex = $this->vertexExists($j, $i - 1)) !== false && $this->map[$i][$j]!=9) {
                    $this->matrix[$vertexNumber][$nearVertex] = 1;
                }
                if (($nearVertex = $this->vertexExists($j + 1, $i - 1)) !== false && $this->map[$i][$j]!=9) {
                    $this->matrix[$vertexNumber][$nearVertex] = 1.2;
                }
                if (($nearVertex = $this->vertexExists($j - 1, $i + 1)) !== false && $this->map[$i][$j]!=9) {
                    $this->matrix[$vertexNumber][$nearVertex] = 1.2;
                }
                if (($nearVertex = $this->vertexExists($j, $i + 1)) !== false && $this->map[$i][$j]!=9) {
                    $this->matrix[$vertexNumber][$nearVertex] = 1;
                }
                if (($nearVertex = $this->vertexExists($j + 1, $i + 1)) !== false && $this->map[$i][$j]!=9) {
                    $this->matrix[$vertexNumber][$nearVertex] = 1.2;
                }
                if (($nearVertex = $this->vertexExists($j - 1, $i)) !== false && $this->map[$i][$j]!=9) {
                    $this->matrix[$vertexNumber][$nearVertex] = 1;
                }
                if (($nearVertex = $this->vertexExists($j + 1, $i)) !== false && $this->map[$i][$j]!=9) {
                    $this->matrix[$vertexNumber][$nearVertex] = 1;
                }
            }
        }
    }

    private function vertexExists($x, $y)
    {
        if ($x >= 0 && $x < $this->mapWidth && $y >= 0 && $y < $this->mapHeight && $this->map[$y][$x] != 9) {
            return $this->vertexNumber ($x,$y);
        }
        return false;
    }

    private function vertexNumber ($x,$y)
    {
        return $y*$this->mapWidth+$x;
    }
}

class dijkstra
{
    private $matrix;
    static $mapHeight;
    static $mapWidth;

    public function __construct ($matrix)
    {
        $this->matrix=$matrix;
    }

    public function createRoute($startVertex=false,$finishVertex=false)
    {
        $startVertex=$startVertex===false?$this->matrix->startVertex:$startVertex;
        $finishVertex=$finishVertex===false?$this->matrix->finishVertex:$finishVertex;
        $visitedVertex=[];
        $vertexWeights[$startVertex]=0;
        $route[$startVertex]=[$startVertex];
        $vertexToVisit[$startVertex]=$startVertex;
        do
        {
            $vertex = reset($vertexToVisit);
            unset ($vertexToVisit[$vertex]);
            $vertexRoute=$route[$vertex];
            foreach ($this->matrix->matrix[$vertex] as $checkVertex=>$distance)
            {
                if (!in_array($checkVertex,$visitedVertex)) {
                    if (!isset($vertexWeights[$checkVertex]) || $vertexWeights[$checkVertex] > ($vertexWeights[$vertex] + $distance)) {
                        $vertexWeights[$checkVertex] = $vertexWeights[$vertex] + $distance;
                        $route[$checkVertex]=array_merge($vertexRoute,[$checkVertex]);
                    }
                    if (!in_array($checkVertex,$vertexToVisit)) {
                        $vertexToVisit[$checkVertex] = $checkVertex;
                    }
                }
            }
            $visitedVertex[$vertex]=$vertex;
        }
        while (count($vertexToVisit)>0);
        return ($route[$finishVertex]);
    }
}

$start_time=microtime(true);
header('Content-Type:text/json;charset=UTF-8');

if (!isset($_POST['data']) || !is_array($_POST['data']))
{
    die(json_encode(['result'=>'ko']));
}

$matrix=new ProcessMap($_POST['data']);
$matrix->createMatrixFromArray();
$dijkstra=new Dijkstra($matrix);
$route=$dijkstra->createRoute();
die(json_encode(['result'=>'ok','route'=>$route,'time'=>microtime(true)-$start_time]));

