<?php
// This is the consumer's functions.php

    // simple logging function if user has writing permission in the log directory
    function fileLog($message, $stampStart, $stampEnd) { // $stampStart + $stampEnd = booleans requiring to print prefix and suffix to the log (see below)
        // if verbose is not true => don't allow debug logs
        if (!$GLOBALS['verbose']) {return;}

        $fh = fopen("../logs/consumerlog.txt", "a");

        // if required by $stampStart => print a prefix with date and time
        if ($stampStart) {fwrite($fh, date("Y/m/d H:i:s\n"));}

        fwrite($fh, $message);

        // if required by $stampEnd => print a suffix with 2 new lines
        if ($stampEnd) {fwrite($fh, "\n\n");}

        fclose($fh);
    }
    function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_-/\@';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 16; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }
?>