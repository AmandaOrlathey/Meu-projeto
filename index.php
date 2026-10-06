<?php
// Configuração do caminho relativo para o arquivo JSON
$caminhoArquivo = 'dados/produtos.json';
$mensagem = '';

// -------------------------------------------------------------------//
// 1. PROCESSAMENTO DO FORMULÁRIO (MÉTODO POST)//
// -------------------------------------------------------------------//
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Captura dos dados vindos do formulário via $_POST//
    $nome           = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
    $categoria      = filter_input(INPUT_POST, 'categoria', FILTER_SANITIZE_SPECIAL_CHARS);
    $marca          = filter_input(INPUT_POST, 'marca', FILTER_SANITIZE_SPECIAL_CHARS);
    $preco          = filter_input(INPUT_POST, 'preco', FILTER_VALIDATE_FLOAT);
    $quantidade     = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);
    $fabricanteNome = filter_input(INPUT_POST, 'fabricante_nome', FILTER_SANITIZE_SPECIAL_CHARS);
    $fabricantePais = filter_input(INPUT_POST, 'fabricante_pais', FILTER_SANITIZE_SPECIAL_CHARS);

    // Validação para garantir que todos os campos foram preenchidos//
    if ($nome && $categoria && $marca && $preco !== false && $quantidade !== false && $fabricanteNome && $fabricantePais) {
        
        
        // Informações do fabricante inseridas em um sub-array interno//
        $novoProduto = [
            'nome'       => $nome,
            'categoria'  => $categoria,
            'marca'      => $marca,
            'preco'      => (float) $preco,
            'quantidade' => (int) $quantidade,
            'fabricante' => [
                'nome' => $fabricanteNome,
                'pais' => $fabricantePais
            ]
        ];

        // REQUISITO 3: Manipulação do Arquivo JSON//
        
        // Passo 1: Ler o conteúdo do arquivo JSON//
        $conteudoJson = file_get_contents($caminhoArquivo);

        // Passo 2: Converter o JSON para um array PHP (true garante que vira array associativo)//
        $produtos = json_decode($conteudoJson, true);

        if (!is_array($produtos)) {
            $produtos = [];
        }

        // Passo 3: Adicionar o novo produto ao array//
        $produtos[] = $novoProduto;

        // Passo 4: Converter o array PHP de volta para JSON//
        $novoJson = json_encode($produtos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Passo 5: Salvar o conteúdo atualizado no arquivo JSON//
        if (file_put_contents($caminhoArquivo, $novoJson)) {
            $mensagem = '<div class="alerta sucesso">Produto cadastrado com sucesso!</div>';
        } else {
            $mensagem = '<div class="alerta erro">Erro ao salvar no arquivo JSON. Verifique as permissões.</div>';
        }

    } else {
        $mensagem = '<div class="alerta erro">Por favor, preencha todos os campos corretamente.</div>';
    }
}

// -------------------------------------------------------------------//
// 4. LEITURA DOS PRODUTOS PARA EXIBIÇÃO//
// -------------------------------------------------------------------//
$conteudoParaExibir = file_get_contents($caminhoArquivo);
$listaProdutos = json_decode($conteudoParaExibir, true);

if (!is_array($listaProdutos)) {
    $listaProdutos = [];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro e Listagem de Produtos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 0 15px;
            background-color: #f4f6f8;
            color: #333;
        }
        h1, h2 {
            color: #2c3e50;
        }
        form {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        fieldset {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
        }
        legend {
            font-weight: bold;
            color: #2c3e50;
            padding: 0 5px;
        }
        .campo {
            margin-bottom: 12px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
        }
        button:hover {
            background-color: #219150;
        }
        .alerta {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .sucesso { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .erro { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .card-produto {
            background: #ffffff;
            border-left: 5px solid #3498db;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .card-produto h3 {
            margin-top: 0;
            color: #2980b9;
        }
        .destaque-total {
            background-color: #e8f8f5;
            border: 1px solid #a3e4d7;
            padding: 8px 12px;
            border-radius: 4px;
            font-weight: bold;
            color: #16a085;
            display: inline-block;
            margin-top: 8px;
        }
    </style>
</head>
<body>

    <h1>Cadastro de Produtos</h1>

    <?= $mensagem ?>

    <!-- REQUISITO 1: Formulário de cadastro com método POST -->
    <form action="index.php" method="POST">
        <fieldset>
            <legend>Informações do Produto</legend>
            <div class="campo">
                <label for="nome">Nome do Produto:</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <div class="campo">
                <label for="categoria">Categoria:</label>
                <input type="text" id="categoria" name="categoria" required>
            </div>
            <div class="campo">
                <label for="marca">Marca:</label>
                <input type="text" id="marca" name="marca" required>
            </div>
            <div class="campo">
                <label for="preco">Preço (R$):</label>
                <input type="number" id="preco" name="preco" step="0.01" min="0" required>
            </div>
            <div class="campo">
                <label for="quantidade">Quantidade em Estoque:</label>
                <input type="number" id="quantidade" name="quantidade" min="0" required>
            </div>
        </fieldset>

        <fieldset>
            <legend>Informações do Fabricante</legend>
            <div class="campo">
                <label for="fabricante_nome">Nome do Fabricante:</label>
                <input type="text" id="fabricante_nome" name="fabricante_nome" required>
            </div>
            <div class="campo">
                <label for="fabricante_pais">País de Origem:</label>
                <input type="text" id="fabricante_pais" name="fabricante_pais" required>
            </div>
        </fieldset>

        <button type="submit">Cadastrar Produto</button>
    </form>

    <hr>

    <!-- REQUISITO 4: Exibição dos Produtos -->
    <h2>PRODUTOS CADASTRADOS</h2>

    <?php if (empty($listaProdutos)): ?>
        <p>Nenhum produto cadastrado até o momento.</p>
    <?php else: ?>
        <!-- Uso do foreach para listar todos os itens salvos -->
        <?php foreach ($listaProdutos as $produto): ?>
            <?php 
                // ⭐ DESAFIO EXTRA: Cálculo do valor total do produto em estoque//
                $valorTotalEstoque = $produto['preco'] * $produto['quantidade'];
            ?>
            <div class="card-produto">
                <h3><?= htmlspecialchars($produto['nome']) ?></h3>
                <p><strong>Categoria:</strong> <?= htmlspecialchars($produto['categoria']) ?></p>
                <p><strong>Marca:</strong> <?= htmlspecialchars($produto['marca']) ?></p>
                <p><strong>Preço:</strong> R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                <p><strong>Quantidade em estoque:</strong> <?= $produto['quantidade'] ?> unidade(s)</p>
                
                <!-- Acesso aos dados  interno do fabricante -->
                <p><strong>Fabricante:</strong> <?= htmlspecialchars($produto['fabricante']['nome']) ?> (<?= htmlspecialchars($produto['fabricante']['pais']) ?>)</p>
                
                <!-- Exibição do Desafio Extra informado-->
                <div class="destaque-total">
                    Valor total em estoque: R$ <?= number_format($valorTotalEstoque, 2, ',', '.') ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>