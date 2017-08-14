<?php

namespace Iges\Entity;

use Iges\Parser\IgesExtract;
use Iges\Computation\IgesComputation;

class IgesLoop {

  public $Loop_ID;
  public $Normal;
  public $Edge_Count;
  public $Loop_Concavity;
  public $Loop_Type;
  public $Edge_List; // ARRAY
  public $Face_Pointers;
  public $loops;

  static $face;

  function __construct() {
    $this->Edge_List = array();
    $this->loops = array();
  }

  // Extract loops
  public function looptract($dsection, $psection, $edge, $vertexlist, $dim) {
    $xtract = new IgesExtract();
    static $p = 1;

    foreach ( $dsection as $value ) {
      if ($value->EntityType == 508) // && $set == false)
      {
        $pentry = $psection [$value->PointerData];

        $arr = $xtract->multiexplode ( array (
          ",",
          ";"
        ), $pentry );

        $edge504 = ($edge->getEdge504 ());
        $Edge_List = array ();

        $edgetuple = $arr [1];

        for($j = 2, $id = 1; $j < count ( $arr ); $j ++, $id ++)
        {
          if (($j + 1) >= count ( $arr ) && ($j + 2) >= count ( $arr )) {
            break;
          }

          $type = $arr [$j];
          $pointer = trim ( $arr [$j + 1] );
          $index = array ();

          $in = trim ( $arr [$j + 2] );

          $Edge_List [$id] = new IgesEdgeList ();
          $Edge_List [$id] = $edge504 [$pointer] [$in]->Edge_List;

          if ($id == $edgetuple)
          $id = 1;

          $j += 4;
        }

        if ($Edge_List == null) {
          echo "string";
          continue;
        }

        $this->loops [$value->PointerData] = new IgesLoop ();
        $this->loops [$value->PointerData]->Edge_List = array ();
        $this->loops [$value->PointerData]->Edge_List = $Edge_List;
        $this->loops [$value->PointerData]->Edge_Count = count ( $Edge_List );
        $this->loops [$value->PointerData]->Loop_ID = $p;
        $this->loops [$value->PointerData]->Loop_Type = null;

        foreach ( $Edge_List as $edl ) {
          $i = 1;
          if ($edl->Edge_Type == "Arc") {
            $this->loops [$value->PointerData]->Loop_Type = "BEND";
            break;
          }

          ++ $i;
        }

        if ($this->loops [$value->PointerData]->Loop_Type != "BEND" || $this->loops [$value->PointerData]->Loop_Type == null) {
          $this->loops [$value->PointerData]->Loop_Type = "FACE";
          $fx = new IgesComputation ( );
          $this->loops [$value->PointerData]->Normal = $fx->computeNormal ($this->loops [$value->PointerData]->Edge_List );
        } else
        $this->loops [$value->PointerData]->Normal = "bend";

        $p ++;
      }
    }

    self::$face = new IgesFace();
    // var_dump($edge->getEdgeList());
    $rbsurface = self::$face->facetract($dsection, $psection, $this->loops, $vertexlist, $edge->getEdgeList(), $dim);

    // var_dump($this->loops);
  }

  public function getLoops() {
    // var_dump(self::$face->getFaceList());
    return self::$face->getFaceList();
  }
}
