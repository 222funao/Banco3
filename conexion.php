<?php

function load_environment($path)
{
    if (!is_file($path)) {
        return;
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        if (getenv($key) === false) {
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

function env_value($key, $default = null)
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}

load_environment(__DIR__ . '/.env');

$databaseUrl = env_value('DATABASE_URL');
if (!$databaseUrl) {
    http_response_code(500);
    die('DATABASE_URL no esta configurada para Banco 3');
}

$parts = parse_url($databaseUrl);
if ($parts === false || empty($parts['host']) || empty($parts['path'])) {
    http_response_code(500);
    die('DATABASE_URL de Banco 3 no es valida');
}

parse_str($parts['query'] ?? '', $options);
$host = $parts['host'];
$port = $parts['port'] ?? 5432;
$database = ltrim($parts['path'], '/');
$sslmode = $options['sslmode'] ?? 'require';
$username = urldecode($parts['user'] ?? '');
$password = urldecode($parts['pass'] ?? '');
$dsn = "pgsql:host=$host;port=$port;dbname=$database;sslmode=$sslmode";

try {
    $conn = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $exception) {
    error_log(
        'Banco 3 PostgreSQL connection failed [' .
        $exception->getCode() .
        ']: ' .
        $exception->getMessage()
    );
    http_response_code(500);
    die('Error de conexion con Banco 3');
}

class DBResult
{
    public array $rows;
    public int $position = 0;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }
}

$db_last_error = '';

function db_query(PDO $connection, string $sql)
{
    global $db_last_error;
    try {
        $statement = $connection->query($sql);
        if (preg_match('/^\s*(SELECT|WITH|SHOW|EXPLAIN)\b/i', $sql)) {
            return new DBResult($statement->fetchAll());
        }
        return true;
    } catch (PDOException $exception) {
        $db_last_error = $exception->getMessage();
        error_log(
            'Banco 3 query failed [' .
            $exception->getCode() .
            ']: ' .
            $exception->getMessage()
        );
        return false;
    }
}

function db_fetch_assoc($result)
{
    if (!$result instanceof DBResult || $result->position >= count($result->rows)) {
        return null;
    }
    return $result->rows[$result->position++];
}

function db_num_rows($result): int
{
    return $result instanceof DBResult ? count($result->rows) : 0;
}

function db_real_escape_string(PDO $connection, $value): string
{
    $quoted = $connection->quote((string) $value);
    return substr($quoted, 1, -1);
}

function db_error(PDO $connection): string
{
    global $db_last_error;
    return $db_last_error;
}

function db_begin_transaction(PDO $connection): bool
{
    return $connection->beginTransaction();
}

function db_commit(PDO $connection): bool
{
    return $connection->commit();
}

function db_rollback(PDO $connection): bool
{
    return $connection->inTransaction() ? $connection->rollBack() : true;
}

?>
