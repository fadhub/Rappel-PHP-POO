<?php
declare(strict_types=1);

/**
 * Classe représentant un utilisateur.
 */
class User {
    // Constructeur PHP 8 : on déclare directement les propriétés publiques
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?string $bio = null,
        public int $articlesCount = 0,
    ) {}

    /**
     * Retourne les initiales du nom de l'utilisateur.
     * Exemple : "Jean Dupont" -> "JD"
     */
    public function initials(): string {
        // On découpe le nom en mots
        $parts = preg_split('/\s+/', trim($this->name));
        // On récupère la première lettre de chaque mot
        $letters = array_map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)), $parts);
        return implode('', $letters);
    }

    /**
     * Transforme l'objet User en tableau associatif
     */
    public function toArray(): array {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'bio'           => $this->bio,
            'articlesCount' => $this->articlesCount,
            'initials'      => $this->initials(),
        ];
    }
}

/**
 * Factory pour créer un User à partir d'un tableau
 */
class UserFactory {
    public static function fromArray(array $u): User {
        // On récupère l'ID ou 1 par défaut
        $id    = max(1, (int)($u['id'] ?? 0));
        // On récupère le nom ou "Inconnu"
        $name  = trim((string)($u['name'] ?? 'Inconnu'));
        // On récupère l'email et on lève une exception si vide
        $email = trim((string)($u['email'] ?? ''));
        if ($email === '') {
            throw new InvalidArgumentException('email requis');
        }
        // Bio facultative
        $bio   = isset($u['bio']) ? (string)$u['bio'] : null;
        // Nombre d'articles par défaut 0
        $count = (int)($u['articlesCount'] ?? 0);

        // Retourne un nouvel objet User
        return new User($id, $name, $email, $bio, $count);
    }
}

// ------------------------
// Exemple d'utilisation
// ------------------------

// Données fictives pour tester le code
$data = [
    [
        'id' => 1,
        'name' => 'salma',
        'email' => 'salma@gmail.com',
        'bio' => 'Développeur PHP passionné',
        'articlesCount' => 12,
    ],
    [
        'id' => 2,
        'name' => 'fadna',
        'email' => 'fadna.lak@gmail.com',
        'articlesCount' => 5,
    ],
    [
        // Exemple avec nom manquant et bio vide
        'id' => 3,
        'email' => 'amal@gmail.com',
    ]
];

// Création des objets User et affichage
foreach ($data as $u) {
    try {
        $user = UserFactory::fromArray($u);
        print_r($user->toArray());
        echo "\n---------------------\n";
    } catch (InvalidArgumentException $e) {
        echo "Erreur: " . $e->getMessage() . "\n";
    }
}
