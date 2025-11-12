<?php

class ProdutosController {
    private $produtoModel;

    public function __construct() {
        $this->produtoModel = new Produto();
    }

    public function listar() {
        verificarLogin();

        $termoBusca = $_GET['busca'] ?? '';
        $produtos = $this->produtoModel->buscar($termoBusca);

        $mensagem = isset($_SESSION['flash']['mensagem']) ? $_SESSION['flash']['mensagem'] : null;
        $tipoMensagem = isset($_SESSION['flash']['tipo_mensagem']) ? $_SESSION['flash']['tipo_mensagem'] : 'info';
        unset($_SESSION['flash']['mensagem']);
        unset($_SESSION['flash']['tipo_mensagem']);

        $usuario_nome = $_SESSION['usuario_nome'];

        require_once '../views/produtos/listar.php';
    }

    public function cadastrar() {
        verificarLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=produtos");
            exit;
        }

        $dados = [
            'nome' => $_POST['nome'] ?? '',
            'codigo_barras' => $_POST['codigo_barras'] ?? '',
            'categoria' => $_POST['categoria'] ?? '',
            'marca' => $_POST['marca'] ?? '',
            'preco_custo' => $_POST['preco_custo'] ?? '',
            'preco_venda' => $_POST['preco_venda'] ?? '',
            'estoque_atual' => $_POST['estoque_atual'] ?? 0,
            'estoque_minimo' => $_POST['estoque_minimo'] ?? 0,
            'data_cadastro' => $_POST['data_cadastro'] ?? ''
        ];

        $resultado = $this->produtoModel->cadastrar($dados);

        $_SESSION['flash']['mensagem'] = $resultado['mensagem'];
        $_SESSION['flash']['tipo_mensagem'] = $resultado['sucesso'] ? 'success' : 'error';

        header("Location: index.php?action=produtos");
        exit;
    }

    public function editar() {
        verificarLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=produtos");
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $dados = [
            'nome' => $_POST['nome'] ?? '',
            'codigo_barras' => $_POST['codigo_barras'] ?? '',
            'categoria' => $_POST['categoria'] ?? '',
            'marca' => $_POST['marca'] ?? '',
            'preco_custo' => $_POST['preco_custo'] ?? '',
            'preco_venda' => $_POST['preco_venda'] ?? '',
            'estoque_atual' => $_POST['estoque_atual'] ?? 0,
            'estoque_minimo' => $_POST['estoque_minimo'] ?? 0,
            'data_cadastro' => $_POST['data_cadastro'] ?? ''
        ];

        $resultado = $this->produtoModel->atualizar($id, $dados);

        $_SESSION['flash']['mensagem'] = $resultado['mensagem'];
        $_SESSION['flash']['tipo_mensagem'] = $resultado['sucesso'] ? 'success' : 'error';

        header("Location: index.php?action=produtos");
        exit;
    }

    public function excluir() {
        verificarLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=produtos");
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $resultado = $this->produtoModel->excluir($id);

        $_SESSION['flash']['mensagem'] = $resultado['mensagem'];
        $_SESSION['flash']['tipo_mensagem'] = $resultado['sucesso'] ? 'success' : 'error';

        header("Location: index.php?action=produtos");
        exit;
    }
}
