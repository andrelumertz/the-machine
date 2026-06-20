<?php
header('Content-Type: application/json; charset=utf-8');

// 1. Captura o input JSON bruto vindo do Vanilla JS
$jsonInput = file_get_contents('php://input');
$requestData = json_decode($jsonInput, true);

// Recupera o texto digitado pelo usuário
$userMessage = $requestData['message'] ?? '';

// Validação simples de segurança
if (empty(trim($userMessage))) {
    echo json_encode([
        'success' => false, 
        'response' => 'A mensagem não pode estar vazia.'
    ]);
    exit;
}

// 2. Define o endpoint local onde o seu FastAPI (app.py) está rodando
$fastapi_url = 'http://127.0.0.1:8001/api/chat';

// CRUCIAL: Monta o array associativo limpo e converte para string JSON.
// Isso garante que chegue exatamente {"message": "texto"} no Python.
$payload = json_encode([
    'message' => (string)$userMessage
]);

// 3. Inicializa e configura o disparo via cURL
$ch = curl_init($fastapi_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($payload)
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 45); // Tempo estendido para a LLM processar o RAG

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

// 4. Devolve a resposta processada para o Frontend
if ($httpCode === 200 && $response) {
    $respostaDecodificada = json_decode($response, true);

    // Só tenta casar produtos se a resposta do Python veio no formato esperado
    if (is_array($respostaDecodificada) && !empty($respostaDecodificada['success'])) {
        $textoResposta = $respostaDecodificada['response'] ?? '';
        $produtosEncontrados = encontrarProdutosMencionados($textoResposta);
        $respostaDecodificada['produtos'] = $produtosEncontrados;
    }

    echo json_encode($respostaDecodificada);
} else {
    // Caso ocorra falha de comunicação ou erro 422/500, gera um log interno e avisa o cliente elegantemente
    error_log("Erro de Integração Blackout AI - Código HTTP: {$httpCode} - Erro cURL: {$curlError}");
    echo json_encode([
        'success' => false,
        'response' => 'Desculpe a demora! Nosso barista digital está finalizando o preparo da sua resposta. Pode tentar enviar novamente?'
    ]);
}

/**
 * Procura, dentro do texto de resposta do bot, menções aos nomes de
 * produtos cadastrados no banco. Usado para anexar "cards" clicáveis
 * do cardápio junto da resposta no chat.
 *
 * @param string $texto Texto de resposta gerado pelo LLM
 * @return array Lista de produtos encontrados, cada um com nome, preço e imagem
 */
function encontrarProdutosMencionados(string $texto): array
{
    if (trim($texto) === '') {
        return [];
    }

    require_once __DIR__ . '/../conexao-bd.php';
    require_once __DIR__ . '/../Modelo/Produto.php';
    require_once __DIR__ . '/../Modelo/Repositorio/produtoRepositorio.php';

    $repositorio = new produtoRepositorio($pdo);
    $todosProdutos = array_merge(
        $repositorio->opcoesCafe(),
        $repositorio->opcoesAlmoco()
    );

    $textoComparacao = mb_strtolower($texto, 'UTF-8');
    $encontrados = [];

    foreach ($todosProdutos as $produto) {
        $nomeProduto = $produto->getNome();

        // Ignora nomes muito curtos para evitar falsos positivos
        // (ex: um produto chamado "Café" combinaria com quase tudo)
        if (mb_strlen($nomeProduto, 'UTF-8') < 4) {
            continue;
        }

        $nomeComparacao = mb_strtolower($nomeProduto, 'UTF-8');

        if (mb_strpos($textoComparacao, $nomeComparacao) !== false) {
            $encontrados[] = [
                'nome'   => $nomeProduto,
                'preco'  => $produto->getPrecoFormatado(),
                'imagem' => $produto->getImagemDiretorio(),
            ];
        }
    }

    // Limita a 4 cards para não sobrecarregar a UI do chat
    return array_slice($encontrados, 0, 4);
}