<?php

/**
 * HTML escape helper function
 */
function e($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

?>
