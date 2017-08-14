<?php

namespace Iges\Entity;

use Iges\Parser\IgesExtract;

class IgesVertex extends IgesPoint {

  private static $vertexlist;

  function __construct () {
    // self::$vertexlist = array();
  }

  // Extraction of vertextes from the vertex list
  public function vertract($dsection, $psection) {
    $counter = 1;
    $xtract = new IgesExtract();
    self::$vertexlist = array();

    foreach ( $dsection as $value )
    {
      if ($value->EntityType == 502)
      {
        // var_dump($dsection);
        $pentry = $psection [$value->PointerData];

        $arr = $xtract->multiexplode ( array (
          ",",
          ";"
        ), $pentry );

        for($j = 2, $id = 1; $j < count ( $arr ); $j ++, $id ++) {
          $vt = new IgesVertex ();
          if (($j + 1) >= count ( $arr ) && ($j + 2) >= count ( $arr )) {
            break;
          }

          if ($arr [$j] == 0.)
          $arr [$j] = 0.0;
          else if ($arr [$j] == 1.)
          $arr [$j] = 1.0;
          else
          ;

          $vt->x = trim ( $xtract->removeD ( $arr [$j] ) );

          if ($arr [$j + 1] == 0.)
          $arr [$j + 1] = 0.0;
          else if ($arr [$j + 1] == 1.)
          $arr [$j + 1] = 1.0;
          else
          ;
          $vt->y = trim ( $xtract->removeD ( $arr [$j + 1] ) );

          if ($arr [$j + 2] == 0.)
          $arr [$j + 2] = 0.0;
          else if ($arr [$j + 2] == 1.)
          $arr [$j + 2] = 1.0;
          else
          ;

          $vt->z = trim ( $xtract->removeD ( $arr [$j + 2] ) );

          self::$vertexlist [$id] = new IgesVertexList ();

          self::$vertexlist [$id]->Vertex_ID = $id;
          self::$vertexlist [$id]->Vertex_Count = $arr [1];
          self::$vertexlist [$id]->Vertex = $vt;

          $vertexlist [$id] = self::$vertexlist [$id];
          $j = $j + 2;
        }
      }
    }

    return $vertexlist;
  }

  public function getVertexList() {
    return self::$vertexlist;
  }
}
