<?php
function buildArticle(array $row): array {
    $row['title']     ??= 'Sans titre';
    $row['author']    ??= 'N/A';
    $row['published'] ??= true;

    $title   = trim((string)$row['title']);
    $excerpt = isset($row['excerpt']) ? trim((string)$row['excerpt']) : null;
    $excerpt = ($excerpt === '') ? null : $excerpt;

    $views   = (int)($row['views'] ?? 0);
    $views   = max(0, $views);

    return [
        'title'     => $title,
        'excerpt'   => $excerpt,
        'views'     => $views,
        'published' => (bool)$row['published'],
        'author'    => trim((string)$row['author']),
    ];
}



// Tests pour vérifier le comportement de la fonction
$testCases = [
    // Cas 1 : Données complètes
    [
        'title' => 'Mon article',
        'excerpt' => 'Résumé de l\'article',
        'views' => '42',
        'published' => true,
        'author' => 'Jean Dupont',
    ],
    // Cas 2 : Données manquantes
    [
        'title' => 'Article incomplet',
    ],
    // Cas 3 : Données avec valeurs vides ou problématiques
    [
        'title' => 'Article vide',
        'excerpt' => '',
        'views' => -5,
        'published' => null,
        'author' => '',
    ],
];

// Exécuter et afficher les tests
foreach ($testCases as $index => $test) {
    echo "Test " . ($index + 1) . ":\n";
    echo "Entrée : " . print_r($test, true) . "\n";
    echo "Sortie : " . print_r(buildArticle($test), true) . "\n\n";
}
?>
