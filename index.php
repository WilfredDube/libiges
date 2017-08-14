<?php
require 'vendor/autoload.php';

use Iges\YourClass;
use Iges\OtherClass;
use Iges\Entity\IgesEdge;//IgesPoint;
use Iges\Entity\Bspline\IgesBsplineCurve;
use Iges\Bend\IgesBend;
use Iges\Computation\IgesComputation;
use Iges\Parser\IgesParser;
use Iges\Entity\IgesVertex;
use Iges\Parser\IgesExtract;
use Iges\Entity\IgesLoop;

echo YourClass::method1(1);

$x = new YourClass();
echo $x->method1(2);

// $y = new OtherClass();
echo OtherClass::method1(1);

$parser = new IgesParser("/var/www/html/FXtract/uploads/mina/mina.igs");

$total = $parser->count_dline();

$parser->get_back();
$dline1 = $parser->get_line();
$dline1 = $parser->jump_to_dsection($dline1);
$dline2 = $parser->get_line();

$parser->parse_d_entry($dline1, $dline2);

for ($i = 1; strpos($dline1, 'D') == true; $i++)
{
  $dline1 = $parser->get_line();
  $dline2 = $parser->get_line();

  if ($dline2 == null)
    break;

  $parser->parse_d_entry($dline1, $dline2);
}

$psection = $parser->param_section();
$gsection = $parser->global_section();
$dsection = $parser->getDsection();

$edgetype = array();
$rbspline = new IgesBSplineCurve();
$edgetype = $rbspline->rbsplineCurveTract($dsection, $psection);

$vt = new IgesVertex();
$vtlist = $vt->vertract($dsection, $psection);
// $vtlist = $vt->getVeddrtexList();

$edge = new IgesEdge();
$edgelist = $edge->edgetract($dsection, $psection, $edgetype, $vt);

$x = new IgesExtract();
$dim = $x->getDimensions($gsection);
$loops = new IgesLoop();
$loops->looptract($dsection, $psection, $edge, $vtlist, $dim);

$bends = new IgesBend();
$bendz = $bends->bendTract($loops->getLoops());

// var_dump($bendz);

// var_dump($loops);
// var_dump($loops->getLoops());

// var_dump($edge->getEdgeList());
// if (!empty($vlist))
// var_dump($vtlist);

// var_dump($dsection);
// var_dump($edgetype);
 ?>
