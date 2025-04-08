<?php

date_default_timezone_set('UTC');

// === SUAS CREDENCIAIS ===
$appId = '18395680234';       // ID do app afiliado
$secret = '2ME7D7LNF6M6WMOMR7TGJUC7XRGHDS5D';      // chave secreta
$timestamp = time();

// === PAYLOAD GRAPHQL: busca por ofertas de "smartphone" ===
$graphql = [
    "query" => "query {
    productOfferV2(){
        nodes {
            productName
            itemId
            commissionRate
            commission
            price
            sales
            imageUrl
            shopName
            productLink
            offerLink
            periodStartTime
            periodEndTime
            priceMin
            priceMax
            productCatIds
            ratingStar
            priceDiscountRate
            shopId
            shopType
            sellerCommissionRate
            shopeeCommissionRate
        }
        pageInfo{
            page
            limit
            hasNextPage
            scrollId
        }
    }
    }"
];

// === GERA A ASSINATURA ===
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

// === RESULTADO ===
$data = json_decode($response, true);
echo "RESULTADO:\n <pre>";
var_dump($data);
