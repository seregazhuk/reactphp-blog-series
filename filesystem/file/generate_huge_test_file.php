<?php

$file = fopen('test.txt', 'w');
$counter = 0;
while($counter <= 5000000) {
    fwrite($file, "Hello world\n");
    $counter++;
}

fclose($file);