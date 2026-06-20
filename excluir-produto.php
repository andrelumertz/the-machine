<?php 

    session_start();
    if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
    }

    require "src/conexao-bd.php";
    require "src/Modelo/Produto.php";
    require "src/Modelo/Repositorio/produtoRepositorio.php";

$produtoRepositorio = new produtoRepositorio($pdo);
$produtoRepositorio->deletar($_POST['id']);

//cabecalho HTTP para redirecionar para a pagina de produtos apos exclusao
header("Location: admin.php");

