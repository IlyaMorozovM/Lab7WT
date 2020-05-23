<?php 
session_start();
$string = '';
 
for ($i = 0; $i < rand(3,10); $i++) {
  $string .= chr(rand(97, 122));
}

//$dir = 'fonts/';
$_SESSION['random_code'] = $string;

$image = imagecreatetruecolor(200, 60);
imageantialias($image, true); 
$color = imagecolorallocate($image, rand(0,255), rand(0,255),rand(0,255)); // red
$white = imagecolorallocate($image,  rand(0,255), rand(0,255),rand(0,255));
$line1 = imagecolorallocate($image,  rand(0,255), rand(0,255),rand(0,255));

 

imagefilledrectangle($image,0,0,200,100,$white);
// var_dump($dir.'Pacifico.ttf');


putenv('GDFONTPATH=' . realpath('fonts'));
$font = 'Red October.ttf';
// $font = 'Pacifico.ttf';
$font=realpath($font);
for($i=0;$i<rand(1,30);$i++){
imagettftext ($image, rand(20,40), rand(-45,45), rand(0,200),rand(0,60), imagecolorallocate($image,  rand(0,255), rand(0,255),rand(0,255)), $font, $_SESSION['random_code']);
}

for ($x=1; $x<=50; $x++)
{
    imageline($image,rand(0,200),rand(0,60),rand(0,200),rand(0,60),imagecolorallocate($image,  rand(0,255), rand(0,255),rand(0,255)));
    imagearc($image,rand(0,200),rand(0,60),rand(0,200),rand(0,60),rand(0,360),rand(0,360),imagecolorallocate($image,  rand(0,255), rand(0,255),rand(0,255)));       
  if(1==rand(0,10))  imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
}

imagettftext ($image, rand(20,40), rand(-10,10), rand(5,35), rand(30,50), $color, $font, $_SESSION['random_code']);
imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
header("Content-type: image/png");
for($i=0;$i<rand(5,30);$i++){
imageline($image,rand(0,200),rand(0,60),rand(0,200),rand(0,60),imagecolorallocate($image,  rand(0,255), rand(0,255),rand(0,255)));
imagearc($image,rand(0,200),rand(0,60),rand(0,200),rand(0,60),rand(0,360),rand(0,360),imagecolorallocate($image,  rand(0,255), rand(0,255),rand(0,255)));
}
imagepng($image);

