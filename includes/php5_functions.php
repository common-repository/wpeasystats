<?php

if (!function_exists("stripos")) {
    function stripos($str, $needle, $offset = 0) {
        if (empty($needle)) return false;
        return strpos(strtolower($str), strtolower($needle), $offset);
    }
}

if (!function_exists('file_put_contents')) {
    function file_put_contents($n, $d, $flag = false) {
        $mode = $flag ? 'a' : 'w';
        $f = @fopen($n, $mode);
        if ($f === false) {
            return false;
        } else {
            if (is_array($d)) $d = implode($d);
            $bytes_written = fwrite($f, $d);
            fclose($f);
            return $bytes_written;
        }
    }
}

function wpbl_to_array(&$v) {
    if (!isset($v)) return array();
    if (isset($v) && !is_object($v) && !isset($v[0])) {
        $v = array(0 => $v);
    }
    return $v;
}


function wpbl_is_above_dir($path, $base) {
    $path = realpath($path);
    $base = realpath($base);
    return 0 !== strncmp($path, $base, strlen($base));
}

?>
