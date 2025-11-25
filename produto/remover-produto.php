<?php
session_start();

if(!isset($_SESSION['usuario'])){
    header('Location: login.php');
    exit;
}

require_once 'src/conexao-bd.php';
require_once 'src/Repositorio/ProdutoRepositorio.php';
require_once 'src/Modelo/Produto.php';

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])){
    $id = (int)$_POST['id'];
    $repo = new ProdutoRepositorio($pdo);
    $repo->remover($id);
}

header('Location: admin.php'); 
exit;
