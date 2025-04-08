<<<<<<< HEAD

<?php
header('Content-Type: application/json');

// Verifica se o método da requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido. Use POST.']);
    exit;
}

// Recebe os dados do corpo da requisição
$dados = json_decode(file_get_contents('php://input'), true);

// Valida os dados
$camposObrigatorios = ['nome', 'preco', 'cupom', 'codigos', 'link', 'openrouter_key', 'bot_token', 'chat_id'];
foreach ($camposObrigatorios as $campo) {
    if (empty($dados[$campo])) {
        http_response_code(400);
        echo json_encode(['erro' => "Dados incompletos. Campo obrigatório faltando: {$campo}"]);
        exit;
    }
}

// Função para enviar mensagem ao Telegram
function enviarMensagemTelegram($botToken, $chatId, $texto) {
    // Envia a mensagem
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $dados = [
        'chat_id' => $chatId,
        'text' => $texto,
        'parse_mode' => 'Markdown'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dados);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $resposta = curl_exec($ch);
    curl_close($ch);

    return $resposta;
}

// Monta o texto da mensagem
$texto = "**{$dados['nome']}** em excelente preço! Uma das mais vendidas!\n\n";
$texto .= "**R$ {$dados['preco']}** com cupom da loja **{$dados['cupom']}** + código **{$dados['codigos']}** + X moedas no APP\n\n";
$texto .= "[{$dados['link']}]({$dados['link']})\n";
$texto .= "[{$dados['link']}]({$dados['link']})\n";
$texto .= "[{$dados['link']}]({$dados['link']})\n\n";
$texto .= "**PREÇO FINAL JÁ COM IMPOSTOS**\n\n";
$texto .= "Chame seus amigos para economizar com a gente!\n";
$texto .= "- Junte-se ao nosso canal no Telegram:\n";
$texto .= "https://t.me/pantpromoshopee\n\n";
$texto .= "Não perca essa oportunidade!";

// Envia a mensagem para o Telegram
$resposta = enviarMensagemTelegram($dados['bot_token'], $dados['chat_id'], $texto);

// Retorna a resposta
echo $resposta;
=======

<?php
header('Content-Type: application/json');

// Verifica se o método da requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido. Use POST.']);
    exit;
}

// Recebe os dados do corpo da requisição
$dados = json_decode(file_get_contents('php://input'), true);

// Valida os dados
$camposObrigatorios = ['nome', 'preco', 'cupom', 'codigos', 'link', 'openrouter_key', 'bot_token', 'chat_id'];
foreach ($camposObrigatorios as $campo) {
    if (empty($dados[$campo])) {
        http_response_code(400);
        echo json_encode(['erro' => "Dados incompletos. Campo obrigatório faltando: {$campo}"]);
        exit;
    }
}

// Função para enviar mensagem ao Telegram
function enviarMensagemTelegram($botToken, $chatId, $texto) {
    // Envia a mensagem
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $dados = [
        'chat_id' => $chatId,
        'text' => $texto,
        'parse_mode' => 'Markdown'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dados);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $resposta = curl_exec($ch);
    curl_close($ch);

    return $resposta;
}

// Monta o texto da mensagem
$texto = "**{$dados['nome']}** em excelente preço! Uma das mais vendidas!\n\n";
$texto .= "**R$ {$dados['preco']}** com cupom da loja **{$dados['cupom']}** + código **{$dados['codigos']}** + X moedas no APP\n\n";
$texto .= "[{$dados['link']}]({$dados['link']})\n";
$texto .= "[{$dados['link']}]({$dados['link']})\n";
$texto .= "[{$dados['link']}]({$dados['link']})\n\n";
$texto .= "**PREÇO FINAL JÁ COM IMPOSTOS**\n\n";
$texto .= "Chame seus amigos para economizar com a gente!\n";
$texto .= "- Junte-se ao nosso canal no Telegram:\n";
$texto .= "https://t.me/pantpromoshopee\n\n";
$texto .= "Não perca essa oportunidade!";

// Envia a mensagem para o Telegram
$resposta = enviarMensagemTelegram($dados['bot_token'], $dados['chat_id'], $texto);

// Retorna a resposta
echo $resposta;
>>>>>>> 5131c9c (Short link)
?>