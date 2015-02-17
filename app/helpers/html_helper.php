<?php

function eh($string)
{
    if (!isset($string)) return;
    echo htmlspecialchars($string, ENT_QUOTES);
}

function redirect ($path, $params = array())
{
    header ("Location: " . url($path, $params));
}



