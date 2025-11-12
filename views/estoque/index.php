<?php
$mensagem = $mensagem ?? null;
$tipoMensagem = $tipoMensagem ?? 'info';
$alertaEstoque = $alertaEstoque ?? null;
$produtos = $produtos ?? [];
$historico = $historico ?? [];
$usuario_nome = $usuario_nome ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Mercado - Estoque</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Gestao de Estoque</h1>
        <div class="user-info">
            <span>Usuario: <?php echo htmlspecialchars($usuario_nome); ?></span>
            <a href="index.php?action=logout" class="btn-logout">Sair</a>
        </div>
    </div>

    <div class="card">
        <?php if ($mensagem): ?>
            <div class="alert alert-<?php echo $tipoMensagem; ?>"><?php echo htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>

        <?php if ($alertaEstoque): ?>
            <div class="alert alert-warning"><?php echo htmlspecialchars($alertaEstoque); ?></div>
        <?php endif; ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Registrar Movimentacao</h2>
            <a href="index.php" class="btn-secondary">Voltar ao Menu</a>
        </div>

        <form method="POST" action="index.php?action=estoque-registrar" style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 30px;">
            <div class="form-row">
                <div class="form-group">
                    <label for="id_produto">Selecionar Produto *</label>
                    <select id="id_produto" name="id_produto" required onchange="atualizarInfoProduto()" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="">Escolha um produto...</option>
                        <?php foreach ($produtos as $produto): ?>
                            <option value="<?php echo $produto['id']; ?>"
                                    data-estoque="<?php echo $produto['estoque_atual']; ?>"
                                    data-minimo="<?php echo $produto['estoque_minimo']; ?>"
                                    data-nome="<?php echo htmlspecialchars($produto['nome']); ?>">
                                <?php echo htmlspecialchars($produto['nome']); ?> - <?php echo htmlspecialchars($produto['codigo_barras']); ?>
                                (Estoque: <?php echo $produto['estoque_atual']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div id="infoProduto" style="background: white; padding: 15px; border-radius: 5px; margin: 15px 0; display: none;">
                <strong>Informacoes do Produto:</strong><br>
                <span id="infoTexto"></span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="tipo">Tipo de Movimentacao *</label>
                    <select id="tipo" name="tipo" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="">Escolha o tipo...</option>
                        <option value="entrada">Entrada</option>
                        <option value="saida">Saida</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantidade">Quantidade *</label>
                    <input type="number" id="quantidade" name="quantidade" min="1" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="data_movimentacao">Data da Movimentacao *</label>
                    <input type="date" id="data_movimentacao" name="data_movimentacao" required value="<?php echo date('Y-m-d'); ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>

                <div class="form-group">
                    <label for="observacao">Observacao</label>
                    <input type="text" id="observacao" name="observacao" placeholder="Opcional" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%;">Registrar Movimentacao</button>
        </form>

        <h2>produtos Cadastrados (Ordenados Alfabeticamente)</h2>
        <p style="color: #666; font-size: 14px; margin-bottom: 15px;">Lista ordenada usando algoritmo Bubble Sort</p>

        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Código de barras</th>
                    <th>Marca</th>
                    <th>Categoria</th>
                    <th>Estoque Atual</th>
                    <th>Estoque Minimo</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                    <tr style="<?php echo $produto['estoque_atual'] < $produto['estoque_minimo'] ? 'background: #fff3cd;' : ''; ?>">
                        <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                        <td><?php echo htmlspecialchars($produto['codigo_barras']); ?></td>
                        <td><?php echo htmlspecialchars($produto['categoria']); ?></td>
                        <td><strong><?php echo $produto['estoque_atual']; ?></strong></td>
                        <td><?php echo $produto['estoque_minimo']; ?></td>
                        <td>
                            <?php if ($produto['estoque_atual'] < $produto['estoque_minimo']): ?>
                                <span style="color: #dc3545; font-weight: bold;">ALERTA - Abaixo do minimo</span>
                            <?php elseif ($produto['estoque_atual'] == 0): ?>
                                <span style="color: #dc3545; font-weight: bold;">ESGOTADO</span>
                            <?php else: ?>
                                <span style="color: #28a745; font-weight: bold;">NORMAL</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>Historico de Movimentacoes (Ultimas 20)</h2>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>produto</th>
                    <th>Tipo</th>
                    <th>Quantidade</th>
                    <th>Responsavel</th>
                    <th>Observacao</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($historico) > 0): ?>
                    <?php foreach ($historico as $mov): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($mov['data_movimentacao'])); ?></td>
                            <td><?php echo htmlspecialchars($mov['nome']); ?></td>
                            <td>
                                <span style="color: <?php echo $mov['tipo'] === 'entrada' ? '#28a745' : '#dc3545'; ?>; font-weight: bold;">
                                    <?php echo strtoupper($mov['tipo']); ?>
                                </span>
                            </td>
                            <td><?php echo $mov['quantidade']; ?></td>
                            <td><?php echo htmlspecialchars($mov['responsavel']); ?></td>
                            <td><?php echo htmlspecialchars($mov['observacao']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">Nenhuma movimentacao registrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function atualizarInfoProduto() {
    const select = document.getElementById('id_produto');
    const option = select.options[select.selectedIndex];
    const infoDiv = document.getElementById('infoProduto');
    const infoTexto = document.getElementById('infoTexto');
    if (option.value) {
        const estoque = option.getAttribute('data-estoque');
        const minimo = option.getAttribute('data-minimo');
        const nome = option.getAttribute('data-nome');
        let statusCor = '#28a745';
        let statusTexto = 'NORMAL';
        if (parseInt(estoque) < parseInt(minimo)) {
            statusCor = '#dc3545';
            statusTexto = 'ALERTA - Abaixo do minimo';
        } else if (parseInt(estoque) === 0) {
            statusCor = '#dc3545';
            statusTexto = 'ESGOTADO';
        }
        infoTexto.innerHTML = `
            <strong>${nome}</strong><br>
            Estoque Atual: <strong>${estoque}</strong> unidades<br>
            Estoque Minimo: <strong>${minimo}</strong> unidades<br>
            Status: <span style="color: ${statusCor}; font-weight: bold;">${statusTexto}</span>
        `;
        infoDiv.style.display = 'block';
    } else {
        infoDiv.style.display = 'none';
    }
}
</script>
</body>
</html>
