<?php

  session_start();
  
  $image_x=150;//������ �����������
  $image_y=40;
  $min_angle=-30;//���� �������
  $max_angle=30;
  $min_size=14;//������ ������
  $max_size=18;
  $fonts = array('comic.ttf', 'ROCK.TTF', 'PAPYRUS.TTF', 'ONYX.TTF', 'ITCKRIST.TTF');//������ ������
  $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';//����� ��������
  $length = mt_rand(5, 7);//����� �����
  $captcha = '';
  for ($i =0; $i < $length; $i++)
  {
    $captcha .= $chars[mt_rand(0,strlen($chars)-1)];//��������� �����
  }
  $_SESSION["captcha"]=$captcha;//������� � ������

  $im = imagecreatetruecolor($image_x, $image_y);//������� �����������
  $background = imagecolorallocate($im,255,255,255);
  imagefill($im, 0,0, $background);//�������� ��� ����� ������
  
  $step=round($image_x/(strlen($captcha)+2));//��� ��������
  $sx=0;
  for($i=0;$i<strlen($captcha);$i++)
  {
    $letter=$captcha[$i];
    $sx += $step + (rand(-round($step/5),round($step/5))); //��������� ���������� x
    $sy=$image_y-round($image_y/3)+rand(-round($image_y/5),round($image_y/5)); //��������� ���������� �
    $sa=rand($min_angle,$max_angle); //��������� ���� ��������
    $ss=rand($min_size,$max_size); //��������� ������
    $sf=$fonts[rand(0,count($fonts)-1)]; //��������� �����
    $sc=imagecolorallocate($im, 100+rand(-100,100), 100+rand(-100,100), 100+rand(100,100)); // ��������� ���� 0-200
    imagettftext($im, $ss, $sa, $sx, $sy, $sc, $sf, $letter);
  }
  header("Content-type: image/png");
  header("Pragma: no-cache");//��� ���������� �����������
  
  imagepng($im);//������� �� � ���� ������� �������� �� ��������� ����� � �.�.
  imagedestroy($im);
?>