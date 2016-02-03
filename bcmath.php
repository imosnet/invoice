<?php

if (!function_exists('bcadd')) {

    trigger_error('BCMath not installed. Using fallback', E_USER_WARNING);

    function bcadd($a, $b) { return $a + $b; }
    function bcsub($a, $b) { return $a - $b; }
    function bcmul($a, $b) { return $a * $b; }
    function bcdiv($a, $b) { return $a / $b; }
    function bccomp($a, $b) { return $a == $b ? 0 : ($a < $b ? -1 : 1); }
    function bcmod($a, $b) { return $a % $b; }
    function bcpow($a, $b) { return pow($a, $b); }
    function bcpowmod($a, $b, $mod) { return bcmod(bcpow($a, $b), $mod); }
    function bcsqrt($a) { return sqrt($a); }

    function bcscale(){ throw new Exception('Not implemented'); }

}

