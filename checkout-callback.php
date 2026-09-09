<?php
$outcome = $_GET['outcome'] ?? 'unknown';
$title = 'Paiement';
$message = 'Termine.';
if ($outcome === 'success') {
    $title = 'Paiement reussi';
    $message = 'Merci, votre paiement a bien ete enregistre.';
} elseif ($outcome === 'error') {
    $title = 'Erreur';
    $message = 'Une erreur est survenue lors du paiement.';
} elseif ($outcome === 'back') {
    $title = 'Retour';
    $message = 'Vous avez annule.';
}
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($title); ?></title>
</head>
<body>
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <p><?php echo htmlspecialchars($message); ?></p>
</body>
</html>
