<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once '../config/database.php';

require_once '../models/Usuarios.php';
require_once '../models/Produtos.php';
require_once '../models/Movimentacoes.php';

require_once '../controllers/LoginController.php';
require_once '../controllers/HomeController.php';
require_once '../controllers/ProdutosController.php';
require_once '../controllers/EstoqueController.php';

$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'login':
        $controller = new LoginController();
        $controller->mostrarLogin();
        break;

    case 'autenticar':
        $controller = new LoginController();
        $controller->autenticar();
        break;

    case 'logout':
        $controller = new LoginController();
        $controller->logout();
        break;

    case 'home':
        $controller = new HomeController();
        $controller->index();
        break;

    case 'produtos':
        $controller = new ProdutosController();
        $controller->listar();
        break;

    case 'produtos-cadastrar':
        $controller = new ProdutosController();
        $controller->cadastrar();
        break;

    case 'produtos-editar':
        $controller = new ProdutosController();
        $controller->editar();
        break;

    case 'produtos-excluir':
        $controller = new ProdutosController();
        $controller->excluir();
        break;

    case 'estoque':
        $controller = new EstoqueController();
        $controller->index();
        break;

    case 'estoque-registrar':
        $controller = new EstoqueController();
        $controller->registrar();
        break;

    default:
        header("Location: index.php");
        exit;
}
