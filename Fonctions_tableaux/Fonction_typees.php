<?php
declare(strict_types=1);

function prixTTC(float $ht, float $taux = 0.2): float {
    return $ht * (1 + $taux);
}

function somme(int ...$nums): int { // variadics
    return array_sum($nums);
}

echo prixTTC(100.0); // 120
echo somme(1,2,3,4); // 10
