<?php
declare(strict_types=1);
session_start();

const DB_HOST = 'localhost';
const DB_NAME = 'biblioteca_virtual';
const DB_USER = 'root';
const DB_PASS = ''; // padrão do XAMPP; altere se seu MySQL tiver senha

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
         PDO::ATTR_EMULATE_PREPARES => false]
    );
    return $pdo;
}
function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void {
    if (!current_user()) { flash('Entre na sua conta para continuar.', 'error'); header('Location: index.php?page=login'); exit; }
}
function flash(string $msg, string $type='success'): void { $_SESSION['flash'] = [$msg, $type]; }
function go(string $page='home'): never { header('Location: index.php?page=' . urlencode($page)); exit; }
function csrf_field(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return '<input type="hidden" name="csrf" value="' . h($_SESSION['csrf']) . '">';
}
function check_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sent = $_POST['csrf'] ?? '';
        if (!is_string($sent) || !hash_equals($_SESSION['csrf'] ?? '', $sent)) {
            http_response_code(403); exit('Solicitação inválida. Atualize a página e tente novamente.');
        }
    }
}
function post_string(string $key, int $max=5000): string {
    $v = trim((string)($_POST[$key] ?? ''));
    return mb_substr($v, 0, $max);
}
function book_by_id(int $id): ?array {
    $q = db()->prepare('SELECT * FROM books WHERE id=?'); $q->execute([$id]); return $q->fetch() ?: null;
}
function average_rating(int $bookId): float {
    $q=db()->prepare('SELECT AVG(rating) FROM book_reviews WHERE book_id=?'); $q->execute([$bookId]); return round((float)$q->fetchColumn(),1);
}
check_csrf();
