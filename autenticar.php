<?php
session_start();

require_once __DIR__ . '/src/conexao-bd.php';
require_once __DIR__ . '/src/Repositorio/UsuarioRepositorio.php';
// O include da Model não é obrigatório aqui, mas não causa problema
require_once __DIR__ . '/src/Modelo/Usuario.php';

// Permitir somente POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

// Campos obrigatórios
if ($email === '' || $senha === '') {
    header('Location: login.php?erro=campos');
    exit;
}

$repo = new UsuarioRepositorio($pdo);
$usuario = $repo->buscarPorEmail($email);

// Verifica senha
if (!$usuario || !password_verify($senha, $usuario->getSenha())) {
    header('Location: login.php?erro=credenciais');
    exit;
}

// Evita sessão antiga suja
session_regenerate_id(true);

// Salva o objeto Usuario na sessão
$_SESSION['usuario'] = $usuario;

// Redireciona conforme perfil
if ($usuario->getPerfil() === 'Admin') {
    header('Location: admin.php');
    exit;
} else {
    header('Location: usuario/usuario-tela.php'); // Caminho correto
    exit;
}
