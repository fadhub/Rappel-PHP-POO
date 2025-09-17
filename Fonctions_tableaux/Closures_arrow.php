<?php
declare(strict_types=1);

$tax = 0.2;

// Closure (capture explicite via "use")
$ttc = function (float $ht) use ($tax): float {
    return $ht * (1 + $tax);
};

// Arrow function (capture auto par valeur)
$ttc2 = fn(float $ht): float => $ht * (1 + $tax);