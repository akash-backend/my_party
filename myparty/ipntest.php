 <?php
$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
$txt = "John Doe\n".json_encode($_REQUEST);

fwrite($myfile, $txt);
 
fclose($myfile);
?> 