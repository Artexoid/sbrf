<?php
/**
 * Created by PhpStorm.
 * User: Artexoid
 * Date: 27.04.16
 * Time: 19:40
 */

namespace QFive\Artexoid\SBRF;


function ifNoEmpty($value, $defaultValue = null)
{
    if (!empty($value)) {
        return $value;
    }
    return $defaultValue;
}

function sendRedirect($url)
{
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: " . $url);
}