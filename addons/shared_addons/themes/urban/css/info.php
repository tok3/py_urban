<?php
ini_set('zlib.output_compression', 'On');

 $timezone = 'America/Adak';
  date_default_timezone_set($timezone);  
echo date('H:i:s', time()).'<br>';
 $timezone = 'Europe/Berlin';
  date_default_timezone_set($timezone);  
echo date('H:i:s', time());
echo phpinfo();

