<?php

//$result =exec("C:/Python27/python.exe get_files.py C:/Python27");
//$command = escapeshellcmd('/usr/custom/test.py');
/*
$command = escapeshellcmd('C:/Users/Priit/PhpstormProjects/i244_projekt/get_files.py C:\Python27');
$output = shell_exec($command);
echo $output;

//echo $result;
$result_array = json_decode($output);
*/
$command = escapeshellcmd('C:/Users/Priit/PhpstormProjects/i244_projekt/multiply.py  4');
$output = shell_exec($command);
echo $output;

?>