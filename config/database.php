<?php
/**
 * @return PDO Objeto de conexao com o banco de dados
 */
function conectarBanco() {
    try {
        // Cria nova conexao PDO com MySQL
        // host=localhost: servidor local
        // dbname=biblioteca_db: nome do banco de dados
        // charset=utf8mb4: suporte completo a caracteres especiais e emojis
        $pdo = new PDO(
            "mysql:host=localhost;
            dbname=mercado_db;
            charset=utf8mb4",
            "root",      
            ""           
        );

        // Configura PDO para lancar excecoes em caso de erro
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Define que os resultados das consultas serao arrays associativos
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Desabilita emulacao de prepared statements para maior seguranca
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        return $pdo;
    } catch (PDOException $e) {
        // Em caso de erro, exibe mensagem e encerra execucao
        die("Erro ao conectar ao banco de dados: " . $e->getMessage());
    }
}

/**
 * @param string 
 * @return string 
 */
function sanitizar($dados) {
    $dados = trim($dados);              // Remove espacos no inicio e fim
    $dados = stripslashes($dados);      // Remove barras invertidas
    $dados = htmlspecialchars($dados);  // Converte caracteres especiais para HTML
    return $dados;
}

function verificarLogin() {
    // Verifica se existe a variavel de sessao 'id_usuario'
    if (!isset($_SESSION['id_usuario'])) {
        // Se nao existir, redireciona para o login
        header("Location: index.php?action=login");
        exit;
    }
}
