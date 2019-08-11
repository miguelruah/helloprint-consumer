<?php

function autoload() {
    include '../config/config.php';

    $dir    = '../include';
    $classes = scandir($dir);

    for ($i=0; $i<count($classes); $i++) {
        if( $classes[$i] != '.' && $classes[$i] != '..' ) {
            include '../include/'.$classes[$i];
        }
    }
}

?>
