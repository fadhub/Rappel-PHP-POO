<?php
declare(strict_types=1);

// Fonction pour créer un slug à partir d'un titre
function slugify(string $title): string {
    // Convertit en minuscules, remplace les espaces par des tirets, et supprime les caractères spéciaux
    return strtolower(preg_replace('/[^a-z0-9]+/', '-', trim($title)));
}
$articles = [
    ['id' => 1, 'title' => 'Intro à Laravel', 'views' => 120, 'author' => 'Amina', 'category' => 'php', 'published' => true],
    ['id' => 2, 'title' => 'Les bases de PHP', 'views' => 200, 'author' => 'Karim', 'category' => 'php', 'published' => false],
    ['id' => 3, 'title' => 'Laravel avancé', 'views' => 300, 'author' => 'Leila', 'category' => 'laravel', 'published' => true],
    ['id' => 4, 'title' => 'Debugging PHP', 'views' => 80, 'author' => 'Omar', 'category' => 'php', 'published' => true]
];

// Étape 1 : Filtrer les articles publiés (published === true)
// On utilise array_filter pour ne garder que les articles où 'published' est true
// array_values réindexe le tableau pour avoir des clés numériques continues
$published = array_values(array_filter($articles, function($article) {
    return $article['published'] === true; // Garde uniquement les articles publiés
}));

// Étape 2 : Normaliser les articles avec array_map
// On crée un nouveau tableau avec seulement les champs demandés : id, slug, views, author, category
$normalized = array_map(function($article) {
    return [
        'id' => $article['id'], // Garde l'ID
        'slug' => slugify($article['title']), // Crée un slug à partir du titre
        'views' => $article['views'], // Garde le nombre de vues
        'author' => $article['author'], // Garde l'auteur
        'category' => $article['category'] // Garde la catégorie
    ];
}, $published);

// Étape 3 : Trier par nombre de vues (descendant)
// On utilise usort pour trier $normalized selon le champ 'views' en ordre décroissant
usort($normalized, function($a, $b) {
    return $b['views'] <=> $a['views']; // Compare les vues, $b avant $a pour ordre décroissant
});

// Étape 4 : Calculer un résumé avec array_reduce
// On calcule : nombre total d'articles, somme des vues, et nombre d'articles par catégorie
$summary = array_reduce($published, function($accumulator, $article) {
    // Incrémente le compteur d'articles
    $accumulator['count'] = ($accumulator['count'] ?? 0) + 1;
    // Ajoute les vues de l'article à la somme totale
    $accumulator['views_sum'] = ($accumulator['views_sum'] ?? 0) + $article['views'];
    // Incrémente le compteur pour la catégorie de l'article
    $category = $article['category'];
    $accumulator['by_category'][$category] = ($accumulator['by_category'][$category] ?? 0) + 1;
    return $accumulator;
}, ['count' => 0, 'views_sum' => 0, 'by_category' => []]); // Initialisation du tableau de résumé

// Étape 5 : Afficher les résultats
// Affiche le tableau normalisé trié
echo "Tableau normalisé trié par vues :\n";
print_r($normalized);

// Affiche le résumé
echo "Résumé des statistiques :\n";
print_r($summary);