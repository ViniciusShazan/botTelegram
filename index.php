
<?php
date_default_timezone_set('America/Sao_Paulo');
header('Content-Type: application/json');

// === SUAS CREDENCIAIS ===
$appId = '18395680234';       // ID do app afiliado
$secret = '2ME7D7LNF6M6WMOMR7TGJUC7XRGHDS5D';      // chave secreta
$timestamp = time();

// === QUERY: TOP_PERFORMING (listType = 2) ===
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
            pageInfo {
                page
                limit
                hasNextPage
            }
        }
    }"
];

// === PREPARA PAYLOAD E ASSINATURA ===
$payload = json_encode($graphql, JSON_UNESCAPED_SLASHES);
$stringToSign = $appId . $timestamp . $payload . $secret;
$signature = hash('sha256', $stringToSign);

// === HEADERS ===
$headers = [
    "Authorization:SHA256 Credential={$appId}, Timestamp={$timestamp}, Signature={$signature}",
    "Content-Type: application/json"
];

// === REQUISIÇÃO CURL ===
$ch = curl_init('https://open-api.affiliate.shopee.com.br/graphql');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
$response = curl_exec($ch);
curl_close($ch);

// === TRATA E EXIBE RESULTADO ===
$data = json_decode($response, true);

echo "🔎 Produtos TOP:\n\n";

foreach ($data['data']['productOfferV2']['nodes'] as $item) {
    echo "📦 Produto: {$item['productName']}\n";
    echo "💰 Preço: R$ {$item['priceMin']} - R$ {$item['priceMax']}\n";
    echo "⭐ Avaliação: {$item['ratingStar']} ⭐\n";
    echo "📉 Desconto: " . ($item['priceDiscountRate'] ?? 0) . "%\n";
    echo "💸 Comissão: " . round($item['commissionRate'] * 100, 2) . "%\n";
    echo "🔗 Link afiliado: {$item['offerLink']}\n";
    echo "🖼️ Imagem: {$item['imageUrl']}\n";
    echo "========================\n\n";
}