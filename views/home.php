<?php
$usuario_nome = $usuario_nome ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Biblioteca</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">

    <div class="header">
        <h1>Sistema de Gestao de Biblioteca</h1>
        <div class="user-info">

            <span>Bem-vindo, <?php echo htmlspecialchars($usuario_nome); ?></span>

            <a href="index.php?action=logout" class="btn-logout">Sair</a>
        </div>
    </div>

    <div class="menu-grid">
        <a href="index.php?action=produtos" class="menu-card">
            <h2>Cadastro de Produtos</h2>
            <p>Gerencie o catalogo de produtos da biblioteca. Adicione, edite ou remova produtos do sistema.</p>
        </a>

        <a href="index.php?action=estoque" class="menu-card">
            <h2>Gestao de Estoque</h2>
            <p>Controle a entrada e saida de produtos. Registre emprestimos, devolucoes e compras.</p>
        </a>
    </div>
</div>

</body>
</html>
