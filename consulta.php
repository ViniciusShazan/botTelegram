<?php

date_default_timezone_set('America/Sao_Paulo');
header('Content-Type: application/json');

// === VERIFICA MÉTODO ===
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido. Use POST.']);
    exit;
}

// === RECEBE JSON ===
$dados = json_decode(file_get_contents('php://input'), true);

// === VALIDA CAMPOS OBRIGATÓRIOS ===
$campos = ['app_id', 'app_secret', 'bot_token', 'chat_id'];
foreach ($campos as $campo) {
    if (empty($dados[$campo])) {
        http_response_code(400);
        echo json_encode(['erro' => "Faltando campo obrigatório: {$campo}"]);
        exit;
    }
}

$appId = $dados['app_id'];
$secret = $dados['app_secret'];
$botToken = $dados['bot_token'];
$chatId = $dados['chat_id'];
$timestamp = time();

// === MONTAR GRAPHQL ===
$graphql = [
    "query" => "query {
        productOfferV2(
            listType: 2,
            matchId: 0,
            page: 1,
            limit: 5
        ) {
            nodes {
                productName
                offerLink
                imageUrl
                priceMin
                priceMax
                ratingStar
                sales
                priceDiscountRate
                commissionRate
            }
        }
    }"
];

// === PREPARA PAYLOAD E ASSINATURA ===
$payload = json_encode($graphql, JSON_UNESCAPED_SLASHES);
$stringToSign = $appId . $timestamp . $payload . $secret;
$signature = hash('sha256', $stringToSign);

$headers = [
    "Authorization:SHA256 Credential={$appId}, Timestamp={$timestamp}, Signature={$signature}",
    "Content-Type: application/json"
];


// === ENVIA PARA API DA SHOPEE
$ch = curl_init('https://open-api.affiliate.shopee.com.br/graphql');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

// === VERIFICA RESPOSTA DA SHOPEE
if (!isset($data['data']['productOfferV2']['nodes']) || empty($data['data']['productOfferV2']['nodes'])) {
    http_response_code(500);
    echo json_encode(['erro' => 'Nenhum produto retornado pela Shopee', 'resposta' => $data]);
    exit;
}

$produtos = $data['data']['productOfferV2']['nodes'];

// === FUNÇÃO PARA ENVIAR AO TELEGRAM
function enviarMensagemTelegram($botToken, $chatId, $mensagem, $imagemUrl) {
    $url = "https://api.telegram.org/bot{$botToken}/sendPhoto";
    $dados = [
        'chat_id' => $chatId,
        'photo' => $imagemUrl,
        'caption' => $mensagem,
        'parse_mode' => 'Markdown',
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dados);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $resposta = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    return [
        'http_code' => $info['http_code'],
        'resposta' => json_decode($resposta, true)
    ];
}


// === ENVIA PRODUTOS PARA TELEGRAM E COLETA RESULTADOS
$resultados = [];

foreach ($produtos as $item) {
    $nome = $item['productName'];
    $preco = number_format($item['priceMin'], 2, ',', '.');
    $avaliacao = $item['ratingStar'];
    $desconto = $item['priceDiscountRate'] ?? 0;
    $link = $item['offerLink'];
    $comissao = round($item['commissionRate'] * 100, 2);

    $mensagem = "**{$nome}** em promoção! 🔥\n\n";
    $mensagem .= "**R$ {$preco}** com até *{$desconto}%* de desconto\n";
    $mensagem .= "⭐ Avaliação: {$avaliacao}/5\n";
    $mensagem .= "💸 Comissão: {$comissao}%\n\n";
    $mensagem .= "[👉 Compre agora com nosso link afiliado!]({$link})\n\n";
    $mensagem .= "📢 Junte-se ao nosso canal para mais ofertas:\n";
    $mensagem .= "https://t.me/pantpromoshopee";

    $imagemUrl = $item['imageUrl'];
    $res = enviarMensagemTelegram($botToken, $chatId, $mensagem, $imagemUrl);
    $resultados[] = [
        'produto' => $nome,
        'enviado' => $res['http_code'] === 200,
        'telegram_resposta' => $res['resposta']
    ];

    sleep(1.5); // evitar flood
}

echo json_encode([
    'status' => 'ok',
    'total_enviados' => count($resultados),
    'detalhes' => $resultados
], JSON_PRETTY_PRINT);
