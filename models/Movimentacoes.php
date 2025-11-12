<?php

class Movimentacao {
    private $pdo;

    public function __construct() {
        $this->pdo = conectarBanco();
    }

    /**
     *
     * @param array $dados Dados da movimentacao (livro_id, tipo, quantidade, data, observacao)
     * @param int $usuarioId ID do usuario que esta registrando
     * @return array Resultado com 'sucesso', 'mensagem' e possivel 'alerta'
     */
    public function registrar($dados, $usuarioId) {
        $erros = $this->validar($dados);

        if (!empty($erros)) {
            return [
                'sucesso' => false,
                'mensagem' => implode('<br>', $erros)
            ];
        }

        try {
            $this->pdo->beginTransaction();
            $produtoModel = new Produto();
            $produto = $produtoModel->buscarPorId($dados['id_produto']);

            // Verifica se o livro existe
            if (!$produto) {
                throw new Exception('Produto nao encontrado');
            }

            // Calcula o novo estoque baseado no tipo de movimentacao
            $novoEstoque = $produto['estoque_atual'];

            if ($dados['tipo'] === 'entrada') {
                // ENTRADA: aumenta o estoque
                $novoEstoque += $dados['quantidade'];
            } else {
                // SAIDA: diminui o estoque

                // Valida se ha estoque suficiente para saida
                if ($dados['quantidade'] > $livro['estoque_atual']) {
                    throw new Exception('Quantidade de saida maior que estoque disponivel');
                }

                $novoEstoque -= $dados['quantidade'];
            }

            // OPERACAO 1: Atualiza o estoque do livro
            $produtoModel->atualizarEstoque($dados['id_produto'], $novoEstoque);

            // OPERACAO 2: Registra a movimentacao no historico
            $sql = "INSERT INTO movimentacoes (id_produto, id_usuario, tipo, quantidade, data_movimentacao, observacao)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $dados['id_produto'],
                $usuarioId,
                $dados['tipo'],
                $dados['quantidade'],
                $dados['data_movimentacao'],
                sanitizar($dados['observacao'])
            ]);

            $this->pdo->commit();

            $alerta = '';
            if ($dados['tipo'] === 'saida' && $novoEstoque < $produto['estoque_minimo']) {
                $deficit = $produto['estoque_minimo'] - $novoEstoque;

                // Monta mensagem de alerta
                $alerta = "ALERTA: O produto '{$produto['nome']}' esta com estoque abaixo do minimo! " .
                         "Estoque atual: {$novoEstoque} | Estoque minimo: {$produto['estoque_minimo']} | " .
                         "Deficit: {$deficit} unidades";
            }

            return [
                'sucesso' => true,
                'mensagem' => 'Movimentacao registrada com sucesso!',
                'alerta' => $alerta
            ];

        } catch (Exception $e) {
            $this->pdo->rollBack();

            return [
                'sucesso' => false,
                'mensagem' => 'Erro ao registrar movimentacao: ' . $e->getMessage()
            ];
        }
    }

    /**

     * @param int $limite Numero maximo de registros a retornar (padrao: 20)
     * @return array Array com historico de movimentacoes
     */
    public function historico($limite = 20) {
        $stmt = $this->pdo->query(
            "SELECT m.*, p.nome, p.codigo_barras, u.nome as responsavel
            FROM movimentacoes m
            INNER JOIN produtos p ON m.id_produto = p.id
            INNER JOIN usuarios u ON m.id_usuario = u.id
            ORDER BY m.data_registro DESC
            LIMIT {$limite}"
        );

        return $stmt->fetchAll();
    }

    /**
     * @param array $dados Dados a serem validados
     * @return array Array com mensagens de erro (vazio se nao houver erros)
     */
    private function validar(array $dados) {
        $erros = [];

        // Valida se um livro foi selecionado
        if (empty($dados['id_produto'])) {
            $erros[] = 'Selecione um produto';
        }

        // Valida se o tipo e 'entrada' ou 'saida'
        if (empty($dados['tipo']) || !in_array($dados['tipo'], ['entrada', 'saida'])) {
            $erros[] = 'Tipo de movimentacao invalido';
        }

        // Valida a quantidade (deve ser numero positivo)
        if (!is_numeric($dados['quantidade']) || $dados['quantidade'] <= 0) {
            $erros[] = 'Quantidade deve ser maior que zero';
        }

        // Valida se a data foi informada
        if (empty($dados['data_movimentacao'])) {
            $erros[] = 'Data da movimentacao e obrigatoria';
        }

        return $erros;
    }
}
