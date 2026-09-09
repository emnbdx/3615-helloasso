<?php

require __DIR__ . '/vendor/autoload.php';

use App\CheckoutLink;

$token = (string) ($_GET['t'] ?? '');
$url = CheckoutLink::resolve($token);
if ($url === null) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Lien de paiement introuvable ou expire.';
    exit;
}

header('Location: ' . $url, true, 302);
exit;
