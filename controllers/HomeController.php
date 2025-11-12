<?php

class HomeController {

    public function index() {
        verificarLogin();

        // Busca o nome do usuario da sessao
        // Este nome sera exibido no cabecalho da pagina
        $usuario_nome = $_SESSION['usuario_nome'];

        // Carrega a view da pagina inicial (menu principal)
        // A variavel $usuario_nome estara disponivel na view
        require_once '../views/home.php';
    }
}
