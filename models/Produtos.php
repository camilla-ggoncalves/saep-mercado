<?php
class Produto {
    private $pdo;

    public function __construct() {
        $this->pdo = conectarBanco();
    }

    /**
     * @return array Array de produtos
     */
    public function buscarTodos() {
        $stmt = $this->pdo->query("SELECT * FROM produtos ORDER BY nome");
        return $stmt->fetchAll();
    }

    /**
     * @param string $termo Termo de busca (opcional)
     * @return array Array de produtos encontrados
     */
    public function buscar($termo = "") {
        if (!empty($termo)) {

            $termoBusca = "%" . $termo . "%";
            $stmt = $this->pdo->prepare(
                "SELECT * FROM produtos
                WHERE nome LIKE ?
                OR codigo_barras LIKE ?
                OR marca LIKE ?
                OR categoria LIKE ?
                ORDER BY nome"
            );
            $stmt->execute([$termoBusca, $termoBusca, $termoBusca, $termoBusca]);
        } else {
            $stmt = $this->pdo->query("SELECT * FROM produtos ORDER BY nome");
        }

        return $stmt->fetchAll();
    }

    /**
     * @param int $id ID do produto
     * @return array|false Dados do produto ou false se nao encontrado
     */
    public function buscarPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * @param array $dados Array associativo com os dados do produto
     * @return array Resultado da operacao com 'sucesso' e 'mensagem'
     */
    public function cadastrar($dados) {
        $erros = $this->validar($dados);

        if (!empty($erros)) {
            return [
                'sucesso' => false,
                'mensagem' => implode('<br>', $erros)
            ];
        }

        try {
            $sql = "INSERT INTO produtos (nome, codigo_barras, categoria, marca, preco_custo, preco_venda, estoque_atual, estoque_minimo, data_cadastro)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                sanitizar($dados['nome']),
                sanitizar($dados['codigo_barras']),
                sanitizar($dados['categoria']),
                sanitizar($dados['marca']),
                $dados['preco_custo'],
                sanitizar($dados['preco_venda']),
                $dados['estoque_atual'],
                $dados['estoque_minimo']
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Produto cadastrado com sucesso!'
            ];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return [
                    'sucesso' => false,
                    'mensagem' => 'Código de barras ja cadastrado no sistema.'
                ];
            }
            return [
                'sucesso' => false,
                'mensagem' => 'Erro ao cadastrar produto: ' . $e->getMessage()
            ];
        }
    }

    /**
     * @param int $id ID do produto a ser atualizado
     * @param array $dados Novos dados do prpduto
     * @return array Resultado da operacao
     */
    public function atualizar($id, $dados) {
        $erros = $this->validar($dados);

        if (!empty($erros)) {
            return [
                'sucesso' => false,
                'mensagem' => implode('<br>', $erros)
            ];
        }

        try {
            $sql = "UPDATE produtos
                    SET nome = ?,
                        codigo_barras = ?,
                        categoria = ?,
                        marca = ?,
                        preco_custo = ?,
                        preco_venda = ?,
                        estoque_atual = ?,
                        estoque_minimo = ?
                    WHERE id = ?";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                sanitizar($dados['nome']),
                sanitizar($dados['codigo_barras']),
                sanitizar($dados['categoria']),
                sanitizar($dados['marca']),
                $dados['preco_custo'],
                sanitizar($dados['preco_venda']),
                $dados['estoque_atual'],
                $dados['estoque_minimo'],
                $id
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Produto atualizado com sucesso!'
            ];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return [
                    'sucesso' => false,
                    'mensagem' => 'ISBN ja cadastrado no sistema.'
                ];
            }
            return [
                'sucesso' => false,
                'mensagem' => 'Erro ao atualizar produyo: ' . $e->getMessage()
            ];
        }
    }

    /**
     *
     * @param int $id ID do produto a ser excluido
     * @return array Resultado da operacao
     */
    public function excluir($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM produtos WHERE id = ?");
            $stmt->execute([$id]);

            return [
                'sucesso' => true,
                'mensagem' => 'Produto excluido com sucesso!'
            ];
        } catch (PDOException $e) {
            return [
                'sucesso' => false,
                'mensagem' => 'Erro ao excluir produto: ' . $e->getMessage()
            ];
        }
    }

    /**
     * @param array $produtos Array de produtos a ser ordenado
     * @return array Array ordenado alfabeticamente por nome
     */
    public function ordenarPorNome(array $produtos) {
        $n = count($produtos);
        for ($i = 0; $i < $n - 1; $i++) {
            for ($j = 0; $j < $n - $i - 1; $j++) {

                if (strcasecmp($produtos[$j]["nome"], $produtos[$j + 1]["nome"]) > 0) {

                    $temp = $produtos[$j];              
                    $produtos[$j] = $produtos[$j + 1];    
                    $produtos[$j + 1] = $temp;          
                }
            }
        }

        return $produtos;
    }

    /**
     *
     * @param int $id ID do produto
     * @param int $novoEstoque Nova quantidade em estoque
     * @return bool True se atualizou com sucesso
     */
    public function atualizarEstoque($id, $novoEstoque) {
        $stmt = $this->pdo->prepare("UPDATE produtos SET estoque_atual = ? WHERE id = ?");
        return $stmt->execute([$novoEstoque, $id]);
    }

    /**
     * @param array $dados Dados a serem validados
     * @return array Array com mensagens de erro (vazio se nao houver erros)
     */
    private function validar(array $dados) {
        $erros = [];

        // Valida campo Nome
        if (empty($dados['nome'])) {
            $erros[] = 'Nome é obrigatório';
        }

        // Valida campo autor
        if (empty($dados['codigo_barras'])) {
            $erros[] = 'Código de barras e obrigatório';
        }

        // Valida campo ISBN
        if (empty($dados['marca'])) {
            $erros[] = 'Marca é obrigatória';
        }

        // Valida campo categoria
        if (empty($dados['categoria'])) {
            $erros[] = 'Categoria é obrigatória';
        }


        if (!is_numeric($dados['estoque_atual']) || $dados['estoque_atual'] < 0) {
            $erros[] = 'Estoque atual deve ser um numero valido';
        }

        if (!is_numeric($dados['estoque_minimo']) || $dados['estoque_minimo'] < 0) {
            $erros[] = 'Estoque minimo deve ser um numero valido';
        }

        return $erros;
    }
}
