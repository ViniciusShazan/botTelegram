<?php

// Definindo time zone BR
date_default_timezone_set('America/Sao_Paulo');

// === CONFIGURAÇÃO ===
$appId  = '18395680234'; // ex: 123456
$secret = '2ME7D7LNF6M6WMOMR7TGJUC7XRGHDS5D';  // ex: abcdefghijklmnopqrstuvwxyz
$urlOriginal = 'https://shopee.com.br/Apple-Iphone-11-128GB-Local-Set-i.52377417.6309028319';
$subIds = ['telegram', 'promo', 'grupo', 'test1', 'test2'];

$timestamp = time();

// === PAYLOAD GRAPHQL ===
$urlOriginal = 'https://shopee.com.br/Apple-Iphone-11-128GB-Local-Set-i.52377417.6309028319';
$subIds = ['telegram', 'promo', 'grupo', 'bot', 'shopee'];

$query = [
    "query" => "mutation {
        generateShortLink(input: {
            originUrl: \"{$urlOriginal}\",
            subIds: [\"" . implode('","', $subIds) . "\"]
        }) {
            shortLink
        }
    }"
];

$payload = json_encode($query, JSON_UNESCAPED_SLASHES); // IMPORTANTE: sem escapes desnecessários

// === STRING PARA ASSINAR ===
$stringParaAssinar = $appId . $timestamp . $payload . $secret;

// === GERA A ASSINATURA ===
$signature = hash('sha256', $stringParaAssinar);

// === MONTA HEADERS ===
$headers = [
    "Authorization:SHA256 Credential={$appId}, Timestamp={$timestamp}, Signature={$signature}",
    "Content-Type: application/json"
];

// === ENVIA REQUISIÇÃO ===
$ch = curl_init('https://open-api.affiliate.shopee.com.br/graphql');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
$response = curl_exec($ch);
curl_close($ch);

// === MOSTRA A RESPOSTA ===
$data = json_decode($response, true);
print_r($data);