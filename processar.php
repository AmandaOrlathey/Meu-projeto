<?php
// Define o caminho onde o arquivo JSON está salvo //
$caminhoArquivo = 'dados/produtos.json';

// Verifica se a requisição veio de um formulário via método POST //
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // -------------------------------------------------------------//
    // ETAPA 1: Captura e higienização dos dados do formulário//
    // -------------------------------------------------------------//
    $nome           = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
    $categoria      = filter_input(INPUT_POST, 'categoria', FILTER_SANITIZE_SPECIAL_CHARS);
    $marca          = filter_input(INPUT_POST, 'marca', FILTER_SANITIZE_SPECIAL_CHARS);
    $preco          = filter_input(INPUT_POST, 'preco', FILTER_VALIDATE_FLOAT);
    $quantidade     = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);
    $fabricanteNome = filter_input(INPUT_POST, 'fabricante_nome', FILTER_SANITIZE_SPECIAL_CHARS);
    $fabricantePais = filter_input(INPUT_POST, 'fabricante_pais', FILTER_SANITIZE_SPECIAL_CHARS);

    // Validação básica: garante que todos os campos foram preenchidos corretamente//
    if ($nome && $categoria && $marca && $preco !== false && $quantidade !== false && $fabricanteNome && $fabricantePais) {

        // -------------------------------------------------------------//
        // ETAPA 2: Criação do Array Associativo (com sub-array de Fabricante)//
        // -------------------------------------------------------------//
        $novoProduto = [
            'nome'       => $nome,
            'categoria'  => $categoria,
            'marca'      => $marca,
            'preco'      => (float) $preco,
            'quantidade' => (int) $quantidade,
            'fabricante' => [ // Sub-array dentro do produto//
                'nome' => $fabricanteNome,
                'pais' => $fabricantePais
            ]
        ];

        // -------------------------------------------------------------//
        // ETAPA 3: Manipulação do arquivo JSON//
        // -------------------------------------------------------------//
        
        // 1. Lê o conteúdo texto do arquivo JSON existente
        $conteudoJson = file_get_contents($caminhoArquivo);

        // 2. Converte a string JSON para um array PHP (o 'true' força ser array associativo)
        $produtos = json_decode($conteudoJson, true);

        // Caso o arquivo esteja vazio ou corrompido, garante que $produtos é um array
        if (!is_array($produtos)) {
            $produtos = [];
        }

        // 3. Adiciona o novo produto no final do array de produtos
        $produtos[] = $novoProduto;

        // 4. Converte o array PHP de volta para texto no formato JSON
        // - JSON_PRETTY_PRINT: Deixa o código formatado e legível
        // - JSON_UNESCAPED_UNICODE: Mantém acentos e caracteres especiais
        $jsonAtualizado = json_encode($produtos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // 5. Salva a nova string JSON substituindo o conteúdo anterior no arquivo
        file_put_contents($caminhoArquivo, $jsonAtualizado);
    }
}

// Redireciona o usuário de volta para a página principal após salvar
header('Location: index.php');
exit();