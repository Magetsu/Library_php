<?php

define ("LOGGING", false);

function printlog($log) {
    
    if (LOGGING) {     
        echo $log;
    }
    
}

function get_logging() {
    
    return LOGGING;
}

?>