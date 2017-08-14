<?php

namespace Iges\Computation;

use Iges\Entity\IgesVertex;

class IgesComputation {

  private $loop;

  public function computeNormal($edgelist)
  {
    $this->loop = $edgelist;
    reset ( $this->loop );
    $edge1 = $this->loop [key ( $this->loop )];
    $edge2 = null;

    foreach ( $this->loop as $edge )
    {
      if ($edge1 == $edge)
      continue;

      if ($edge1->Start_Vertex == $edge->Start_Vertex ||
      $edge1->Terminate_Vertex == $edge->Terminate_Vertex ||
      $edge1->Start_Vertex == $edge->Terminate_Vertex ||
      $edge1->Terminate_Vertex == $edge->Start_Vertex)
      {

        $edge2 = $edge;
        break;
      }
    }

    $a = $this->computeLineVector ( $edge1 );
    $b = $this->computeLineVector ( $edge2 );

    return $this->computeCrossProduct ( $a, $b );
  }

  private function computeLineVector($line) {
    $a = new IgesVertex ();
    // echo ($line->Start_Vertex->x)."\n";
    $a->x = $line->Start_Vertex->x - $line->Terminate_Vertex->x;
    $a->y = $line->Start_Vertex->y - $line->Terminate_Vertex->y;
    $a->z = $line->Start_Vertex->z - $line->Terminate_Vertex->z;

    return $a;
  }

  private function computeCrossProduct($a, $b) {
    $normal = new IgesVertex ();

    $normal->x = ($a->y * $b->z) - ($a->z * $b->y);
    $normal->y = - (($a->x * $b->z) - ($b->x * $a->z));
    $normal->z = ($a->x * $b->y) - ($b->x * $a->y); /* */

    return $normal;
  }

  public function computeBendLength($line) {
    $a = new IgesVertex ();

    $a->x = $line->Start_Vertex->x - $line->Terminate_Vertex->x;
    $a->y = $line->Start_Vertex->y - $line->Terminate_Vertex->y;
    $a->z = $line->Start_Vertex->z - $line->Terminate_Vertex->z;

    return $this->computeEuclideanNorm ( $a );
  }


  private function computeEuclideanNorm($vector) {
    $A = $vector->x * $vector->x;
    $B = $vector->y * $vector->y;
    $C = $vector->z * $vector->z;

    // echo ($A + $B + $C);
    return sqrt ( $A + $B + $C );
  }

  private function computeDotProduct($v1, $v2) {
    $A = $v1->x * $v2->x;
    $B = $v1->y * $v2->y;
    $C = $v1->z * $v2->z;

    return ($A + $B + $C);
  }

  public function distanceBTWPlanes($n1, $n2){
    $num = ($n1->x)*($n2->x) + ($n1->y)*($n2->y) + ($n1->z)*($n2->z);
    $den = $this->computeEuclideanNorm($n1);

    // echo $num;
    $distance = $num / $den;

    return $distance;
  }

  public function computeAngle($normal1, $normal2) {

    $dotp = $this->computeDotProduct ( $normal1, $normal2 );

    $En1 = $this->computeEuclideanNorm ( $normal1 );
    $En2 = $this->computeEuclideanNorm ( $normal2 );

    if ($dotp == 0)
    $cosine = 0;
    else

    $cosine = $dotp / ($En1 * $En2);
    $radian = acos ( $cosine );

    $angle = rad2deg ( $radian );

    return $angle;
  }

  public function computeThickness($edgelist)
  {
    $this->loop = $edgelist;
    reset ( $this->loop );
    $arr = array();
    // $edge1 = $this->loop [key ( $this->loop )];
    // $edge2 = null;

    foreach ( $this->loop as $edge )
    {
      $arr[]= $this->computeBendLength($edge);
    }

    natsort($arr);

    // var_dump($arr);

    // return smallest value
    return array_shift($arr);
  }

  public function computeParallel($loop1, $loop2){
    return $this->computeDotProduct($loop1, $loop2);
  }

  public function computeConcavity($bend, $face1, $face2) {
    $concavity = null;
    $loop1 = new Loop ();
    $loop2 = new Loop ();

    $loop1 = $face1->External_Loop;
    $loop2 = $face2->External_Loop;

    $Na = $loop1->Normal;
    $Nb = $loop2->Normal;

    foreach ( $loop1->Edge_List as $edl ) {
      if ($edl->Start_Vertex != $bend->Start_Vertex && $edl->Start_Vertex != $bend->Terminate_Vertex) {
        $Pa = $edl->Start_Vertex;
        break;
      } else if ($edl->Terminate_Vertex != $bend->Terminate_Vertex && $edl->Terminate_Vertex != $bend->Start_Vertex) {
        $Pa = $edl->Terminate;
        break;
      } else
      continue;
    }

    foreach ( $loop2->Edge_List as $edl ) {
      if ($edl->Start_Vertex != $bend->Start_Vertex && $edl->Start_Vertex != $bend->Terminate_Vertex) {
        $Pb = $edl->Start_Vertex;
        break;
      } else if ($edl->Terminate_Vertex != $bend->Terminate_Vertex && $edl->Terminate_Vertex != $bend->Start_Vertex) {
        $Pb = $edl->Terminate_Vertex;
        break;
      } else
      continue;
    }

    $diff = new Vertex ();
    $diff1 = new Vertex ();

    $diff->x = $Pb->x - $Pa->x;
    $diff->y = $Pb->y - $Pa->y;
    $diff->z = $Pb->z - $Pa->z;

    $diff1->x = $Pa->x - $Pb->x;
    $diff1->y = $Pa->y - $Pb->y;
    $diff1->z = $Pa->z - $Pb->z;

    $con = $this->computeDotProduct ( $diff, $Na );
    $con1 = $this->computeDotProduct ( $diff1, $Nb );

    if (($con < 0 && $con1 < 0) || ($con > 0 && $con1 > 0))
    ;
    else
    ;

    if ($con == $con1) {
      if ($con <= 0)
      $concavity = "Convex";
      else
      $concavity = "Concave";
    }

    return $concavity;
  }

  private function toMM($value, $unit) {
    $out = 0;
    switch ($unit) {
      case 1 :
      $out = $value * 25.4;
      break;
      case 2 :
      $out = $value;
      break;
      case 4 :
      $out = $value * 304.8;
      break;
      case 5 :
      $out = $value * 1.609e6;
      break;
      case 6 :
      $out = $value * 1000;
      break;
      case 7 :
      $out = $value * 1000000;
      break;
      case 8 :
      $out = $value * 0.0254;
      break;
      case 9 :
      $out = $value * 0.001;
      break;
      case 10 :
      $out = $value * 10;
      break;
      default :
      break;
    }

    return $out;
  }

  public function computeBendingForce($bendlength, $thickness, $unit, $TS) {
    $KBS = 1.33;

    $D = 8 * $thickness;

    $bendl = $this->toMM ( $bendlength, $unit );

    $force = ($KBS * $TS * $bendlength * $thickness * $thickness) / $D;

    return $force;
  }
}
