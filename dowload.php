<?php

include('barcode-generator/src/BarcodeGenerator.php');
include('barcode-generator/src/BarcodeGeneratorPNG.php');
include('barcode-generator/src/BarcodeGeneratorSVG.php');
include('barcode-generator/src/BarcodeGeneratorJPG.php');
include('barcode-generator/src/BarcodeGeneratorHTML.php');

if ((isset($_GET['text']))){
  $text = $_GET['text'];
  createsvg($text);
  downloadfile($text);
  deletefile($text);

}

function createsvg($text){
  $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
  $base64string = base64_encode($generator->getBarcode($text, $generator::TYPE_CODE_128, 2, 60));

  $filename = $text.'_barcode.svg';
  header('Content-Type: image/svg+xml');
  file_put_contents($filename, base64_decode($base64string));
  header('Content-Disposition: attachment; filename="'.$filename);
}

function downloadfile($text){
  $fileurl = $text.'_barcode.svg';
  header("Content-type:image/svg+xml");
  header('Content-Disposition: attachment; filename=' . $fileurl);
  readfile( $fileurl );
}

function deletefile($text){
$myFile = $text.'_barcode.svg';
unlink($myFile) or die("Couldn't delete file");
}
?>