<?php

// 1. Inclui o autoloader
require_once __DIR__ . '/vendor/autoload.php';

// 2. Importa o "Capsule" do Eloquent
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory as ValidatorFactory;
use Illuminate\Container\Container;
use Illuminate\Validation\DatabasePresenceVerifier;

// --- 1. CARREGAR O .ENV ---
// Isso lê o arquivo .env e disponibiliza as variáveis em $_ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 3. Cria uma nova instância do Capsule
$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $_ENV['DB_HOST'],
    'database'  => $_ENV['DB_DATABASE'],
    'username'  => $_ENV['DB_USERNAME'],
    'password'  => $_ENV['DB_PASSWORD'],
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
]);

// 5. "Liga" o Eloquent globalmente
// Isso torna o Eloquent acessível de qualquer lugar
$capsule->setAsGlobal();

// 6. "Boota" (Inicializa) o Eloquent
$capsule->bootEloquent();

// --- PARTE 2: CONFIGURAR O VALIDADOR (AS NOVAS FUNCTIONS) ---
// (Este é o código que adicionamos para a Validação)
$container = new Container();
// 'singleton' espera uma "receita" (Closure)
// 'instance' aceita um "objeto pronto".
// Nós já temos o objeto ($container), então usamos 'instance'.
// $container->singleton('app', $container);
$container->instance('app', $container);

$filesystem = new Filesystem();
$loader = new FileLoader($filesystem, 'lang'); 
$translator = new Translator($loader, 'en');
$validatorFactory = new ValidatorFactory($translator, $container);

// 1. Crie o "Verificador de Presença" (o "Tradutor")
$presenceVerifier = new DatabasePresenceVerifier($capsule->getDatabaseManager());

// 2. "Apresente" o Verificador para a Fábrica de Validação.
$validatorFactory->setPresenceVerifier($presenceVerifier);

// Função "helper" global para criar um validador
function validator(array $data, array $rules, array $mensagens = [])
{
    global $validatorFactory;
    return $validatorFactory->make($data, $rules, $mensagens);
}

// Funções "helper" globais para a Sessão Flash
function session_flash($key, $value)
{
    $_SESSION[$key] = $value;
}

function session_get($key, $default = null)
{
    $value = $_SESSION[$key] ?? $default;
    unset($_SESSION[$key]); // Apaga a mensagem "flash" após ser lida
    return $value;
}

function session_has($key)
{
    return isset($_SESSION[$key]);
}