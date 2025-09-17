<?php
declare(strict_types=1);
// Définir la fonction slugify pour transformer un titre en slug (chaîne URL-friendly)
function slugify(string $text): string {
    // Remplacer tous les caractères non alphanumériques (sauf lettres et chiffres) par des tirets
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Convertir les caractères accentués (ex. "à" → "a") en leur équivalent ASCII
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Supprimer tous les caractères restants qui ne sont ni des lettres, ni des chiffres, ni des tirets
    $text = preg_replace('~[^-\w]+~', '', $text);
    // Enlever les tirets en début et fin de la chaîne pour un slug propre
    $text = trim($text, '-');
    // Convertir toute la chaîne en minuscules pour uniformité
    $text = strtolower($text);
    // Retourner la chaîne transformée (le slug final)
    return $text;
}

// Définir les données d'entrée
$articles = [
    ['id' => 1, 'title' => 'Intro à Laravel', 'views' => 120, 'author' => 'Amina', 'category' => 'php', 'published' => true],
    ['id' => 2, 'title' => 'Les bases de PHP', 'views' => 200, 'author' => 'Karim', 'category' => 'php', 'published' => false],
    ['id' => 3, 'title' => 'Laravel avancé', 'views' => 300, 'author' => 'Leila', 'category' => 'laravel', 'published' => true],
    ['id' => 4, 'title' => 'Debugging PHP', 'views' => 80, 'author' => 'Omar', 'category' => 'php', 'published' => true]
];

// Filtrer les articles publiés
$published = array_values(array_filter($articles, fn($a) => $a['published'] ?? false));

// Normaliser les données
$normalized = array_map(
    fn($a) => [
      'id'       => $a['id'],
      'slug'     => slugify($a['title']),
      'views'    => $a['views'],
      'author'   => $a['author'],
      'category' => $a['category'],
    ],
    $published
  );
// Trier par vues décroissantes
usort($normalized, fn($x, $y) => $y['views'] <=> $x['views']);

// Calculer un résumé avec array_reduce
$summary = array_reduce(
    $published,
    function(array $acc, array $a): array {
        $acc['count']      = ($acc['count'] ?? 0) + 1;
        $acc['views_sum']  = ($acc['views_sum'] ?? 0) + $a['views'];
        $cat = $a['category'];
        $acc['by_category'][$cat] = ($acc['by_category'][$cat] ?? 0) + 1;
        return $acc;
    },
    ['count'=>0, 'views_sum'=>0, 'by_category'=>[]]
  );

// Afficher les résultats
echo "Tableau normalisé trié par vues :\n";
print_r($normalized);
echo "Résumé des statistiques :\n";
print_r($summary);