<?php
declare(strict_types=1);

/**
 * Générateur de seed articles
 * Usage:
 *   php seed.php storage/seeds/articles.json 5
 */

if ($argc < 3) {
    fwrite(STDERR, "Usage: php {$argv[0]} <chemin> <nbArticles>\n");
    exit(1);
}

$path = $argv[1];
$count = (int)$argv[2];

/** 
 * Valider un article 
 */
function validateArticle(array $a): void {
    if (empty($a['title']) || empty($a['slug'])) {
        throw new DomainException("Article invalide: title/slug manquant");
    }
}

/**
 * Générer N articles fake
 */
function generateArticles(int $n): array {
    $articles = [];
    for ($i = 1; $i <= $n; $i++) {
        $articles[] = [
            'title' => "Article $i",
            'slug'  => "article-$i",
        ];
    }
    return $articles;
}

/**
 * Sauvegarde atomique en JSON
 */
function atomicWrite(string $path, array $data): void {
    $tmp = $path . '.tmp';
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    if (file_put_contents($tmp, $json) === false) {
        throw new RuntimeException("Impossible d'écrire dans $tmp");
    }

    if (!rename($tmp, $path)) {
        throw new RuntimeException("Impossible de renommer $tmp vers $path");
    }
}

/**
 * Merge avec articles.extra.json
 */
function mergeExtra(array $articles): array {
    $extraPath = __DIR__ . '/articles.extra.json';
    if (!file_exists($extraPath)) {
        return $articles;
    }

    $extra = json_decode(file_get_contents($extraPath), true);
    if (!is_array($extra)) {
        fwrite(STDERR, "⚠ articles.extra.json invalide\n");
        return $articles;
    }

    // Éviter doublons par slug
    $bySlug = [];
    foreach (array_merge($articles, $extra) as $a) {
        $bySlug[$a['slug']] = $a;
    }

    return array_values($bySlug);
}

// Génération
$articles = generateArticles($count);

// Validation
foreach ($articles as $a) {
    validateArticle($a);
}

// Merge avec extra.json
$articles = mergeExtra($articles);

// Sauvegarde atomique
try {
    atomicWrite($path, $articles);
    fwrite(STDOUT, "✅ $count articles générés et sauvegardés dans $path\n");
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, "❌ Erreur: " . $e->getMessage() . "\n");
    exit(1);
}
