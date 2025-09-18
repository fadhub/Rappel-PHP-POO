<?php
declare(strict_types=1);

class Article {
    public readonly int $id;
    private string $title;
    private string $slug;
    private array $tags = [];

    public function __construct(int $id, string $title, array $tags = []) {
        if ($id <= 0) throw new InvalidArgumentException("id > 0 requis.");
        $this->id = $id;
        $this->setTitle($title);
        $this->tags = $tags;
    }

    public static function fromTitle(int $id, string $title): static {
        return new static($id, $title);
    }

    public function title(): string { return $this->title; }
    public function slug(): string { return $this->slug; }
    public function tags(): array { return $this->tags; }

    public function setTitle(string $title): void {
        $title = trim($title);
        if ($title === '') throw new InvalidArgumentException("Titre requis.");
        $this->title = $title;
        $this->slug = static::slugify($title);
    }

    public function addTag(string $tag): void {
        $t = trim($tag);
        if ($t === '') throw new InvalidArgumentException("Tag vide.");
        $this->tags[] = $t;
    }

    protected static function slugify(string $value): string {
        $s = strtolower($value);
        $s = preg_replace('/[^a-z0-9]+/i', '-', $s) ?? '';
        return trim($s, '-');
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'tags' => $this->tags,
        ];
    }
}

class FeaturedArticle extends Article {
    protected static function slugify(string $value): string {
        return 'featured-' . parent::slugify($value);
    }
}

class ArticleRepository {
    private array $articles = []; // Stockage en mémoire (slug => Article)
    private int $count = 0;      // Compteur d'articles

    public function save(Article $article): void {
        $slug = $article->slug();
        if (isset($this->articles[$slug])) {
            throw new DomainException("Slug '$slug' déjà utilisé.");
        }
        $this->articles[$slug] = $article;
        $this->count++;
    }

    public function count(): int {
        return $this->count;
    }

    public function findBySlug(string $slug): ?Article {
        return $this->articles[$slug] ?? null;
    }
}

// Démo pour le challenge
$repo = new ArticleRepository();

try {
    $a = Article::fromTitle(1, 'Encapsulation & visibilité en PHP');
    $a->addTag('php');
    $repo->save($a);

    $b = FeaturedArticle::fromTitle(2, 'Lire moins, comprendre plus');
    $b->addTag('best');
    $repo->save($b);

    // Test d'unicité du slug
    $c = Article::fromTitle(3, 'Encapsulation & visibilité en PHP'); // Même titre => même slug
    $repo->save($c); // Lève une exception
} catch (DomainException $e) {
    echo "Erreur : " . $e->getMessage() . PHP_EOL;
}

echo "Nombre d'articles : " . $repo->count() . PHP_EOL; // Affiche 2

// Afficher les articles stockés
$articles = [
    $repo->findBySlug('encapsulation-visibilite-en-php'),
    $repo->findBySlug('featured-lire-moins-comprendre-plus'),
];
$arrayForJson = array_map(fn(?Article $article) => $article ? $article->toArray() : null, $articles);
print_r(array_filter($arrayForJson)); // Filtrer les null