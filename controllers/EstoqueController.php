<?php

class EstoqueController {
    private $produtoModel;
    private $movimentacaoModel;

    public function __construct() {
        $this->produtoModel = new Produto();
        $this->movimentacaoModel = new Movimentacao();
    }

    public function index() {
        verificarLogin();

        $produtos = $this->produtoModel->buscarTodos();

        $produtos = $this->produtoModel->ordenarPorNome($produtos);

        // Busca as ultimas 20 movimentacoes (entrada/saida)
        $historico = $this->movimentacaoModel->historico(20);

        $mensagem = isset($_SESSION['flash']['mensagem']) ? $_SESSION['flash']['mensagem'] : null;
        $tipoMensagem = isset($_SESSION['flash']['tipo_mensagem']) ? $_SESSION['flash']['tipo_mensagem'] : 'info';
        $alertaEstoque = isset($_SESSION['flash']['alerta_estoque']) ? $_SESSION['flash']['alerta_estoque'] : null;

        unset($_SESSION['flash']['mensagem']);
        unset($_SESSION['flash']['tipo_mensagem']);
        unset($_SESSION['flash']['alerta_estoque']);


        $usuario_nome = $_SESSION['usuario_nome'];

        require_once '../views/estoque/index.php';
    }

    public function registrar() {
        verificarLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=estoque");
            exit;
        }

        $dados = [
            'id_produto' => $_POST['id_produto'] ?? 0,
            'tipo' => $_POST['tipo'] ?? '',  // 'entrada' ou 'saida'
            'quantidade' => $_POST['quantidade'] ?? 0,
            'data_movimentacao' => $_POST['data_movimentacao'] ?? '',
            'observacao' => $_POST['observacao'] ?? ''
        ];

        $resultado = $this->movimentacaoModel->registrar($dados, $_SESSION['id_usuario']);

        $_SESSION['flash']['mensagem'] = $resultado['mensagem'];
        $_SESSION['flash']['tipo_mensagem'] = $resultado['sucesso'] ? 'success' : 'error';

        if (!empty($resultado['alerta'])) {
            $_SESSION['flash']['alerta_estoque'] = $resultado['alerta'];
        }

        header("Location: index.php?action=estoque");
    }
}
