<?php
declare(strict_types=1);

function riskyDivide(int $a, int $b): float {
  if ($b === 0) {
    throw new InvalidArgumentException('Division par zéro interdite.');
  }
  return $a / $b;
}

try {
  echo riskyDivide(10, 0);
} catch (InvalidArgumentException $e) {
  echo "[WARN] " . $e->getMessage() . PHP_EOL;
} finally {
  echo "Toujours exécuté (libération de ressources, etc.)." . PHP_EOL;
}
