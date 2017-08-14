<?php

namespace Iges\Bend;

use Iges\Computation\IgesComputation;
use Iges\Parser\IgesExtract;

class IgesBend {

  // protected static $table_name = 'bends';
  public $Bend_ID;
  public $Face1;
  public $Face2;
  public $Angle;
  public $Bend_Loop;
  public $Bend_Unit;
  public $Bend_Length;
  public $Bend_Thickness;
  public $Bend_Radius;
  public $Bend_height;
  public $Bend_force;

  static $bends;

  public function bendTract($face_list) {
    $x = new IgesExtract ();
    $fx = new IgesComputation ();
    $flag = false;
    $bends = array();

    $loops = $face_list;
    $faces = $face_list;

    $p = 0;

    if ($face_list != null)
    foreach ( $loops as $loop )
    {
      $face1 = null;
      $face2 = null;
      $bendE = null;

      $i = 0;

      if (isset($loop->External_Loop->Edge_List) && is_array($loop->External_Loop->Edge_List))
      foreach ( $loop->External_Loop->Edge_List as $bedl ) {
        if ($bedl->Edge_Type == "Line" && $loop->Bend_ID != - 1) {
          $i ++;

          $bendE = $bedl;

          foreach ( $faces as $face ) {
            if ($face->Bend_ID == -1) {
              foreach ( $face->External_Loop->Edge_List as $fedl )
              if ($bendE == $fedl) {
                $flag = true;
                if ($face1 == null)
                $face1 = $face;
                else
                $face2 = $face;
              } else
              continue;
            } else
            continue;

            if ($flag == true) {
              $flag = false;
              break;
            }
          }
        } else {
        }

        if ($i == 2) {

          $bendLength = $fx->computeBendLength ( $bendE );
          $angle = $fx->computeAngle ( $face1->External_Loop->Normal, $face2->External_Loop->Normal );

          $i = 0;

          self::$bends [$p] = new IgesBend ();
          self::$bends [$p]->Bend_ID = $loop->Bend_ID;
          self::$bends [$p]->Face1 = $face1->Face_ID;
          self::$bends [$p]->Face2 = $face2->Face_ID;
          self::$bends [$p]->Angle = $angle;
          self::$bends [$p]->Bend_Loop = $loop->External_Loop->Loop_ID;
          self::$bends [$p]->Bend_Length = $bendLength;
          $bends[$p] = self::$bends [$p];

          ++ $p;
        }
      }
    }

    return $bends;
  }
}
