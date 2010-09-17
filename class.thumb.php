<?php

class Thumb	{

  function Thumb()	{ }

  function resize_image($path,$thumbPath,$maxsize)	{

  $this->PATH = $path;
  $this->SIZE = $maxsize;

  list($imageWidth, $imageHeight, $imageType) = GetImageSize($this->PATH);

/*
  switch($imageType) {
    case 1 : header("Content-type: image/gif"); break;
    case 2 : header("Content-type: image/jpeg"); break;
    case 3 : header("Content-type: image/png"); break;
    case 6 : header("Content-type: image/x-ms-bmp"); break;
    case 7 : 
    case 8 : header("Content-type: image/tiff"); break;
  }
*/

  if($imageWidth < $imageHeight)	{
    $thumbWidth = round($imageWidth/$imageHeight * $this->SIZE);
    $thumbHeight = $this->SIZE;
  }
  else	{
    $thumbWidth = $this->SIZE;
    $thumbHeight = round($imageHeight/$imageWidth * $this->SIZE);
  }

  switch($imageType)	{
    case 1 : $image = ImageCreateFromGIF($this->PATH); break;
    case 2 : $image = ImageCreateFromJPEG($this->PATH); break;
    case 3 : $image = ImageCreateFromPNG($this->PATH); break;
  }

  $thumb = ImageCreateTrueColor($thumbWidth,$thumbHeight);
  //ImageCopyResampled($thumb,$image,0,0,0,0,$thumbWidth,$thumbHeight,$imageWidth,$imageHeight);
  ImageCopyResized($thumb,$image,0,0,0,0,$thumbWidth,$thumbHeight,$imageWidth,$imageHeight);

  switch($imageType)	{
    case 1 :
      ImageGIF($thumb,$thumbPath); 
      break;
    case 2 : 
      ImageJPEG($thumb,$thumbPath); 
      break;
    case 3 :
      ImagePNG($thumb,$thumbPath); 
      break;

  ImageDestroy($image);
  ImageDestroy($thumb);

  }

  }

}

?>
