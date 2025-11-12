<?php

class LoginController {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }
    public function mostrarLogin() {
        if (isset($_SESSION['id_usuario'])) {

            header("Location: index.php");
            exit;
        }

        $erro = isset($_SESSION['flash']['erro']) ? $_SESSION['flash']['erro'] : null;
        $sucesso = isset($_SESSION['flash']['sucesso']) ? $_SESSION['flash']['sucesso'] : null;

        unset($_SESSION['flash']['erro']);
        unset($_SESSION['flash']['sucesso']);

        require_once '../views/login.php';
    }

    public function autenticar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=login");
            exit;
        }

        $usuario = $_POST['usuario'] ?? '';
        $senha = $_POST['senha'] ?? '';

        $resultado = $this->usuarioModel->autenticar($usuario, $senha);

        if ($resultado['sucesso']) {
            $_SESSION['id_usuario'] = $resultado['usuario']['id'];
            $_SESSION['usuario_nome'] = $resultado['usuario']['nome'];
            $_SESSION['usuario_login'] = $resultado['usuario']['usuario'];

            header("Location: index.php");
            exit;
        } else {

            $_SESSION['flash']['erro'] = $resultado['mensagem'];

            header("Location: index.php?action=login");
            exit;
        }
    }

    public function logout() {
        session_destroy();

        session_start();

        $_SESSION['flash']['sucesso'] = 'Logout realizado com sucesso!';

        header("Location: index.php?action=login");
        exit;
    }
}
