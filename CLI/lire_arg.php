Lire les arguments bruts via $argv
<?php
// php hello.php Amina
// $argv[0] = 'hello.php', $argv[1] = 'Amina'
echo "Bonjour " . ($argv[1] ?? 'Inconnu') . PHP_EOL;
echo "salut " . ($argv[2] ?? 'Inconnu') . PHP_EOL;