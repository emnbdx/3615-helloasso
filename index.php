<?php
require "vendor/autoload.php";

use MiniPavi\MiniPaviCli;
use Dotenv\Dotenv;
use App\HelloAssoClient;
use App\CheckoutLink;
use App\VideotexQr;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

error_reporting(E_ERROR);
ini_set('display_errors', 0);

function buildAccueilPage()
{
    $vdt = '';

    $vdt .= VDT_BGBLUE . MiniPaviCli::repeatChar(' ', 40);
    $vdt .= MiniPaviCli::setPos(1, 3);
    $vdt .= VDT_BGBLUE . MiniPaviCli::repeatChar(' ', 40);

    $vdt .= MiniPaviCli::setPos(6, 4);
    $vdt .= VDT_BGBLUE . VDT_TXTWHITE . VDT_SZDBLHW . "HELLOASSO";

    $vdt .= MiniPaviCli::setPos(1, 6);
    $vdt .= VDT_BGBLUE . VDT_SZNORM . MiniPaviCli::repeatChar(' ', 40);
    $vdt .= MiniPaviCli::setPos(1, 7);
    $vdt .= VDT_BGBLUE . MiniPaviCli::repeatChar(' ', 40);

    $vdt .= MiniPaviCli::setPos(1, 10);
    $vdt .= VDT_SZNORM . VDT_BGBLACK . VDT_TXTCYAN;
    $vdt .= MiniPaviCli::writeCentered(10, "La solution de paiement", VDT_TXTCYAN);

    $vdt .= MiniPaviCli::writeCentered(12, "pour les associations", VDT_TXTCYAN);

    $vdt .= MiniPaviCli::setPos(1, 15);
    $vdt .= VDT_G1 . VDT_TXTGREEN;
    $vdt .= MiniPaviCli::repeatChar(chr(0x7E), 40);

    $vdt .= MiniPaviCli::setPos(1, 17);
    $vdt .= VDT_G0 . VDT_SZDBLH . VDT_TXTYELLOW;
    $vdt .= MiniPaviCli::writeCentered(17, "3615 HELLOASSO", VDT_TXTYELLOW . VDT_SZDBLH);

    $vdt .= MiniPaviCli::setPos(1, 20);
    $vdt .= VDT_SZNORM . VDT_TXTWHITE;
    $vdt .= MiniPaviCli::writeCentered(20, "Adhesions - Dons - Billetterie", VDT_TXTWHITE);

    $vdt .= MiniPaviCli::setPos(1, 22);
    $vdt .= VDT_TXTMAGENTA;
    $vdt .= MiniPaviCli::writeCentered(22, "100% gratuit pour les associations", VDT_TXTMAGENTA);

    return $vdt;
}

function buildMenuPage()
{
    $vdt = '';

    $vdt .= VDT_BGBLUE . MiniPaviCli::repeatChar(' ', 40);
    $vdt .= MiniPaviCli::setPos(1, 3);
    $vdt .= VDT_BGBLUE . VDT_TXTWHITE . VDT_SZDBLH;
    $vdt .= MiniPaviCli::writeCentered(3, "HELLOASSO", VDT_BGBLUE . VDT_TXTWHITE . VDT_SZDBLH);
    $vdt .= MiniPaviCli::setPos(1, 5);
    $vdt .= VDT_BGBLUE . VDT_SZNORM . MiniPaviCli::repeatChar(' ', 40);

    $vdt .= MiniPaviCli::setPos(1, 8);
    $vdt .= VDT_SZNORM . VDT_BGBLACK . VDT_TXTWHITE;
    $vdt .= " " . VDT_FDINV . " 1 " . VDT_FDNORM . VDT_TXTCYAN . MiniPaviCli::toG2(" Rechercher un événement");

    $vdt .= MiniPaviCli::setPos(1, 11);
    $vdt .= VDT_TXTWHITE . " " . VDT_FDINV . " 2 " . VDT_FDNORM . VDT_TXTCYAN . MiniPaviCli::toG2(" Événements aujourd'hui");

    $vdt .= MiniPaviCli::setPos(1, 14);
    $vdt .= VDT_TXTWHITE . " " . VDT_FDINV . " 3 " . VDT_FDNORM . VDT_TXTCYAN . " A propos de HelloAsso";

    $vdt .= MiniPaviCli::setPos(1, 17);
    $vdt .= VDT_TXTWHITE . " " . VDT_FDINV . " 4 " . VDT_FDNORM . VDT_TXTCYAN . MiniPaviCli::toG2(" Envoyer de l'argent");

    return $vdt;
}

function buildSearchPage()
{
    $vdt = '';

    $vdt .= VDT_BGBLUE . MiniPaviCli::repeatChar(' ', 40);
    $vdt .= MiniPaviCli::setPos(1, 3);
    $vdt .= VDT_BGBLUE . VDT_TXTWHITE . VDT_SZDBLH;
    $vdt .= MiniPaviCli::writeCentered(3, "RECHERCHE", VDT_BGBLUE . VDT_TXTWHITE . VDT_SZDBLH);
    $vdt .= MiniPaviCli::setPos(1, 5);
    $vdt .= VDT_BGBLUE . VDT_SZNORM . MiniPaviCli::repeatChar(' ', 40);

    $vdt .= MiniPaviCli::setPos(1, 8);
    $vdt .= VDT_SZNORM . VDT_BGBLACK . VDT_TXTCYAN;
    $vdt .= MiniPaviCli::toG2(" Nom de l'événement:");

    $vdt .= MiniPaviCli::setPos(1, 10);
    $vdt .= VDT_TXTWHITE . " > " . VDT_BGBLUE . MiniPaviCli::repeatChar('.', 35);

    return $vdt;
}

function formatDate($dateStr)
{
    if ($dateStr instanceof \DateTimeInterface) {
        return $dateStr->format('d/m/Y');
    }
    if ($dateStr === null || $dateStr === '') {
        return '';
    }
    try {
        $dt = new \DateTime((string) $dateStr);
        return $dt->format('d/m/Y');
    } catch (\Throwable $t) {
        return mb_substr((string) $dateStr, 0, 10);
    }
}

function formatFormType($type)
{
    $types = [
        'Event' => 'Evenement',
        'Membership' => 'Adhesion',
        'Donation' => 'Don',
        'PaymentForm' => 'Paiement',
        'CrowdFunding' => 'Crowdfunding',
        'Shop' => 'Boutique',
    ];
    return $types[$type] ?? $type ?? '';
}

function buildResultsPage($forms, $page)
{
    $vdt = '';

    $vdt .= VDT_BGBLUE . MiniPaviCli::repeatChar(' ', 40);
    $vdt .= MiniPaviCli::setPos(1, 3);
    $vdt .= VDT_BGBLUE . VDT_TXTWHITE . VDT_SZDBLH;
    $vdt .= MiniPaviCli::writeCentered(3, "RESULTATS", VDT_BGBLUE . VDT_TXTWHITE . VDT_SZDBLH);
    $vdt .= MiniPaviCli::setPos(1, 5);
    $vdt .= VDT_BGBLUE . VDT_SZNORM . MiniPaviCli::repeatChar(' ', 40);

    $total = count($forms);
    $perPage = 5;
    $start = $page * $perPage;
    $pageForms = array_slice($forms, $start, $perPage);

    $vdt .= MiniPaviCli::setPos(1, 6);
    $vdt .= VDT_SZNORM . VDT_BGBLACK . VDT_TXTMAGENTA;
    $vdt .= " " . $total . MiniPaviCli::toG2(" résultat(s)");

    $line = 8;
    foreach ($pageForms as $i => $form) {
        $num = $start + $i + 1;
        $title = mb_substr($form['title'] ?: $form['formSlug'], 0, 32);

        $vdt .= MiniPaviCli::setPos(1, $line);
        $vdt .= VDT_TXTWHITE . " " . VDT_FDINV . " " . $num . " " . VDT_FDNORM . " ";
        $vdt .= VDT_TXTCYAN . MiniPaviCli::toG2($title);

        if (!empty($form['orgName'])) {
            $vdt .= MiniPaviCli::setPos(5, $line + 1);
            $vdt .= VDT_TXTYELLOW . MiniPaviCli::toG2(mb_substr($form['orgName'], 0, 34));
        }

        $line += 3;
    }

    return $vdt;
}

function buildDetailPage($form)
{
    $vdt = '';

    $vdt .= VDT_BGBLUE . MiniPaviCli::repeatChar(' ', 40);
    $vdt .= MiniPaviCli::setPos(1, 3);
    $vdt .= VDT_BGBLUE . VDT_TXTWHITE . VDT_SZDBLH;
    $type = formatFormType($form['formType']);
    $vdt .= MiniPaviCli::writeCentered(3, mb_strtoupper($type), VDT_BGBLUE . VDT_TXTWHITE . VDT_SZDBLH);
    $vdt .= MiniPaviCli::setPos(1, 5);
    $vdt .= VDT_BGBLUE . VDT_SZNORM . MiniPaviCli::repeatChar(' ', 40);

    $title = $form['title'] ?: '???';
    $vdt .= MiniPaviCli::setPos(1, 7);
    $vdt .= VDT_SZNORM . VDT_BGBLACK . VDT_TXTWHITE . VDT_SZDBLH;
    $vdt .= " " . MiniPaviCli::toG2(mb_substr($title, 0, 19));

    $line = 10;

    $vdt .= MiniPaviCli::setPos(1, $line);
    $vdt .= VDT_SZNORM . VDT_TXTCYAN . " Par: ";
    $vdt .= VDT_TXTYELLOW . MiniPaviCli::toG2(mb_substr($form['orgName'] ?: 'N/A', 0, 32));
    $line++;

    $startDate = formatDate($form['startDate']);
    $endDate = formatDate($form['endDate']);
    if ($startDate) {
        $line++;
        $vdt .= MiniPaviCli::setPos(1, $line);
        $vdt .= VDT_TXTCYAN . " Du " . VDT_TXTYELLOW . $startDate;
        if ($endDate) {
            $vdt .= VDT_TXTCYAN . " au " . VDT_TXTYELLOW . $endDate;
        }
        $line++;
    }

    if (!empty($form['place'])) {
        $place = $form['place'];
        $lieu = $place['name'] ?: '';
        if ($place['city']) {
            $lieu .= ($lieu ? ' - ' : '') . $place['city'];
        }
        if ($lieu) {
            $line++;
            $vdt .= MiniPaviCli::setPos(1, $line);
            $vdt .= VDT_TXTCYAN . " Lieu: " . VDT_TXTYELLOW . MiniPaviCli::toG2(mb_substr($lieu, 0, 32));
            $line++;
        }
    }

    if (!empty($form['tarifs'])) {
        $line++;
        $vdt .= MiniPaviCli::setPos(1, $line);
        $vdt .= VDT_TXTCYAN . " Tarifs:";
        $line++;
        foreach ($form['tarifs'] as $tarif) {
            if ($line > 21) break;
            $vdt .= MiniPaviCli::setPos(2, $line);
            $label = mb_substr($tarif['label'], 0, 26);
            $price = number_format($tarif['price'], 2, ',', '') . MiniPaviCli::toG2('€');
            $vdt .= VDT_TXTWHITE . MiniPaviCli::toG2($label) . " " . VDT_TXTYELLOW . $price;
            $line++;
        }
    } elseif (!empty($form['description'])) {
        $line++;
        $desc = wordwrap(mb_substr($form['description'], 0, 200), 38, "\n");
        $lines = explode("\n", $desc);
        foreach ($lines as $dline) {
            if ($line > 21) break;
            $vdt .= MiniPaviCli::setPos(2, $line);
            $vdt .= VDT_TXTWHITE . MiniPaviCli::toG2(trim($dline));
            $line++;
        }
    }

    if (!empty($form['url'])) {
        $vdt .= MiniPaviCli::setPos(1, 22);
        $vdt .= VDT_TXTGREEN . " " . MiniPaviCli::toG2(mb_substr($form['url'], 0, 38));
    }

    return $vdt;
}

function requestBaseUrl()
{
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        $prot = 'https';
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
        $prot = 'https';
    } elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        $prot = 'https';
    } elseif (isset($_SERVER['SERVER_PORT']) && intval($_SERVER['SERVER_PORT']) === 443) {
        $prot = 'https';
    } else {
        $prot = 'http';
    }
    $path = str_replace('\\', '/', dirname($_SERVER['PHP_SELF'] ?? '/'));
    if ($path === '/' || $path === '.') {
        $path = '';
    }
    return rtrim($prot . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $path, '/');
}

function buildPayQrPage($shortUrl, $amountCents)
{
    $vdt = '';

    $vdt .= VDT_BGBLUE . MiniPaviCli::repeatChar(' ', 40);
    $vdt .= MiniPaviCli::setPos(1, 1);
    $vdt .= VDT_BGBLUE . VDT_TXTWHITE . VDT_SZNORM;
    $title = 'PAIEMENT';
    if ($amountCents > 0) {
        $title .= '  ' . number_format($amountCents / 100, 2, ',', ' ') . ' EUR';
    }
    $vdt .= MiniPaviCli::writeCentered(1, $title, VDT_BGBLUE . VDT_TXTWHITE);

    $vdt .= VideotexQr::render($shortUrl, 1, 3, 40, 19);

    $vdt .= MiniPaviCli::setPos(1, 23);
    $vdt .= VDT_G0 . VDT_SZNORM . VDT_BGBLACK . VDT_TXTCYAN;
    $vdt .= MiniPaviCli::writeCentered(23, "Scannez pour payer", VDT_TXTCYAN);

    return $vdt;
}

function payQrInputCmd()
{
    return MiniPaviCli::createInputTxtCmd(40, 24, 1, MSK_SOMMAIRE | MSK_REPETITION | MSK_RETOUR | MSK_ENVOI, false, ' ', '');
}

function buildAboutPage()
{
    $vdt = '';

    $vdt .= VDT_BGBLUE . MiniPaviCli::repeatChar(' ', 40);
    $vdt .= MiniPaviCli::setPos(1, 3);
    $vdt .= VDT_BGBLUE . VDT_TXTWHITE . VDT_SZDBLH;
    $vdt .= MiniPaviCli::writeCentered(3, "A PROPOS", VDT_BGBLUE . VDT_TXTWHITE . VDT_SZDBLH);
    $vdt .= MiniPaviCli::setPos(1, 5);
    $vdt .= VDT_BGBLUE . VDT_SZNORM . MiniPaviCli::repeatChar(' ', 40);

    $vdt .= MiniPaviCli::setPos(1, 7);
    $vdt .= VDT_SZNORM . VDT_BGBLACK . VDT_TXTCYAN;
    $vdt .= " HelloAsso est la solution de";
    $vdt .= MiniPaviCli::setPos(1, 8);
    $vdt .= " paiement 100% gratuite pour les";
    $vdt .= MiniPaviCli::setPos(1, 9);
    $vdt .= " associations.";

    $vdt .= MiniPaviCli::setPos(1, 11);
    $vdt .= VDT_TXTYELLOW . " Fonctionnalites:";

    $vdt .= MiniPaviCli::setPos(1, 13);
    $vdt .= VDT_TXTWHITE . "  - Adhesions en ligne";
    $vdt .= MiniPaviCli::setPos(1, 14);
    $vdt .= "  - Collecte de dons";
    $vdt .= MiniPaviCli::setPos(1, 15);
    $vdt .= "  - Billetterie";
    $vdt .= MiniPaviCli::setPos(1, 16);
    $vdt .= "  - Crowdfunding";
    $vdt .= MiniPaviCli::setPos(1, 17);
    $vdt .= "  - Boutique en ligne";

    $vdt .= MiniPaviCli::setPos(1, 19);
    $vdt .= VDT_TXTGREEN . " Plus d'infos: www.helloasso.com";

    $vdt .= MiniPaviCli::setPos(1, 21);
    $vdt .= VDT_TXTMAGENTA . " Depuis 2010, HelloAsso accompagne";
    $vdt .= MiniPaviCli::setPos(1, 22);
    $vdt .= " plus de 400 000 associations.";

    return $vdt;
}

try {
    MiniPaviCli::start();

    if (MiniPaviCli::$fctn == 'CNX' || MiniPaviCli::$fctn == 'DIRECTCNX') {
        $step = 'accueil';
        $context = array();
        MiniPaviCli::$content = array();
    } else {
        $context = unserialize(MiniPaviCli::$context);
        $step = $context['step'];
    }

    if (MiniPaviCli::$fctn == 'FIN') {
        exit;
    }

    $vdt = '';
    $cmd = null;
    $directCall = false;

    while (true) {
        switch ($step) {
            case 'accueil':
                $vdt = MiniPaviCli::clearScreen() . PRO_MIN . PRO_LOCALECHO_OFF;
                $vdt .= MiniPaviCli::setPos(1, 2);
                $vdt .= buildAccueilPage();
                $vdt .= MiniPaviCli::setPos(1, 24);
                $vdt .= VDT_G0 . VDT_SZNORM . VDT_TXTWHITE . VDT_BGBLACK;
                $vdt .= " " . VDT_FDINV . " Suite " . VDT_FDNORM . " pour continuer" . VDT_CLRLN;
                $step = 'accueil-saisie';
                $directCall = false;
                break 2;

            case 'accueil-saisie':
                if (MiniPaviCli::$fctn == 'REPETITION') {
                    $step = 'accueil';
                    break;
                }
                if (MiniPaviCli::$fctn == 'SUITE' || MiniPaviCli::$fctn == 'ENVOI') {
                    $step = 'menu';
                    break;
                }
                break 2;

            case 'menu':
                $vdt = MiniPaviCli::clearScreen() . PRO_MIN . PRO_LOCALECHO_OFF;
                $vdt .= MiniPaviCli::setPos(1, 2);
                $vdt .= buildMenuPage();
                $vdt .= MiniPaviCli::setPos(1, 24);
                $vdt .= VDT_G0 . VDT_SZNORM . VDT_TXTWHITE . VDT_BGBLACK;
                $vdt .= " Votre choix: + " . VDT_FDINV . " Envoi " . VDT_FDNORM . VDT_CLRLN;
                $cmd = MiniPaviCli::createInputTxtCmd(14, 24, 1, MSK_ENVOI | MSK_SOMMAIRE, true, '.', '');
                $step = 'menu-saisie';
                $directCall = false;
                break 2;

            case 'menu-saisie':
                if (MiniPaviCli::$fctn == 'SOMMAIRE') {
                    $step = 'accueil';
                    break;
                }
                $choix = trim(@MiniPaviCli::$content[0] ?? '');
                if ($choix == '1') {
                    $step = 'search';
                    break;
                }
                if ($choix == '2') {
                    $step = 'search-today';
                    break;
                }
                if ($choix == '3') {
                    $step = 'about';
                    break;
                }
                if ($choix == '4') {
                    $step = 'pay-slug';
                    break;
                }
                $vdt = MiniPaviCli::writeLine0('Choix invalide!');
                $step = 'menu';
                break;

            case 'pay-slug':
                unset($context['checkout_id'], $context['checkout_url'], $context['pay_amount_cents'], $context['pay_qr_token'], $context['pay_short_url']);
                $vdt = MiniPaviCli::clearScreen() . PRO_MIN . PRO_LOCALECHO_OFF;
                $vdt .= MiniPaviCli::setPos(1, 2);
                $vdt .= VDT_BGBLUE . MiniPaviCli::repeatChar(' ', 40);
                $vdt .= MiniPaviCli::setPos(1, 3);
                $vdt .= VDT_BGBLUE . VDT_TXTWHITE . VDT_SZDBLH;
                $vdt .= MiniPaviCli::writeCentered(3, "ENVOYER DE L'ARGENT", VDT_BGBLUE . VDT_TXTWHITE . VDT_SZDBLH);
                $vdt .= MiniPaviCli::setPos(1, 5);
                $vdt .= VDT_BGBLUE . VDT_SZNORM . MiniPaviCli::repeatChar(' ', 40);
                $vdt .= MiniPaviCli::setPos(1, 8);
                $vdt .= VDT_SZNORM . VDT_BGBLACK . VDT_TXTCYAN;
                $vdt .= MiniPaviCli::toG2(" Slug asso:");
                $vdt .= MiniPaviCli::setPos(1, 10);
                $vdt .= VDT_TXTWHITE . " > " . VDT_BGBLUE . MiniPaviCli::repeatChar('.', 35);
                $vdt .= MiniPaviCli::setPos(1, 24);
                $vdt .= VDT_G0 . VDT_SZNORM . VDT_TXTWHITE . VDT_BGBLACK;
                $vdt .= " " . VDT_FDINV . " Envoi " . VDT_FDNORM . "  " . VDT_FDINV . " Sommaire " . VDT_FDNORM . VDT_CLRLN;
                $cmd = MiniPaviCli::createInputTxtCmd(4, 10, 35, MSK_ENVOI | MSK_SOMMAIRE, true, '.', '');
                $step = 'pay-slug-saisie';
                $directCall = false;
                break 2;

            case 'pay-slug-saisie':
                if (MiniPaviCli::$fctn == 'SOMMAIRE') {
                    $step = 'menu';
                    break;
                }
                $context['pay_slug'] = trim(@MiniPaviCli::$content[0] ?? '');
                if (empty($context['pay_slug'])) {
                    $vdt = MiniPaviCli::writeLine0('Slug requis!');
                    $step = 'pay-slug';
                    break;
                }
                $step = 'pay-amount';
                break;

            case 'pay-amount':
                $line0 = $vdt;
                $vdt = MiniPaviCli::clearScreen() . PRO_MIN . PRO_LOCALECHO_OFF;
                $vdt .= MiniPaviCli::setPos(1, 2);
                $vdt .= VDT_BGBLUE . MiniPaviCli::repeatChar(' ', 40);
                $vdt .= MiniPaviCli::setPos(1, 3);
                $vdt .= VDT_BGBLUE . VDT_TXTWHITE . VDT_SZDBLH;
                $vdt .= MiniPaviCli::writeCentered(3, "MONTANT (euros)", VDT_BGBLUE . VDT_TXTWHITE . VDT_SZDBLH);
                $vdt .= MiniPaviCli::setPos(1, 5);
                $vdt .= VDT_BGBLUE . VDT_SZNORM . MiniPaviCli::repeatChar(' ', 40);
                $vdt .= MiniPaviCli::setPos(1, 8);
                $vdt .= VDT_SZNORM . VDT_BGBLACK . VDT_TXTCYAN;
                $vdt .= MiniPaviCli::toG2(" Montant (ex: 10.50):");
                $vdt .= MiniPaviCli::setPos(1, 10);
                $vdt .= VDT_TXTWHITE . " > " . VDT_BGBLUE . MiniPaviCli::repeatChar('.', 35);
                $vdt .= MiniPaviCli::setPos(1, 24);
                $vdt .= VDT_G0 . VDT_SZNORM . VDT_TXTWHITE . VDT_BGBLACK;
                $vdt .= " " . VDT_FDINV . " Envoi " . VDT_FDNORM . "  " . VDT_FDINV . " Sommaire " . VDT_FDNORM . VDT_CLRLN;
                $vdt .= $line0;
                $cmd = MiniPaviCli::createInputTxtCmd(4, 10, 35, MSK_ENVOI | MSK_SOMMAIRE, true, '.', '');
                $step = 'pay-amount-saisie';
                $directCall = false;
                break 2;

            case 'pay-amount-saisie':
                if (MiniPaviCli::$fctn == 'SOMMAIRE') {
                    $step = 'menu';
                    break;
                }
                if (MiniPaviCli::$fctn != 'ENVOI') {
                    break 2;
                }
                if (!empty($context['checkout_url'])) {
                    $step = 'pay-qr';
                    break;
                }
                $amountStr = str_replace(',', '.', trim(@MiniPaviCli::$content[0] ?? ''));
                $amountEur = (float) $amountStr;
                if ($amountEur <= 0) {
                    $vdt = MiniPaviCli::writeLine0('Montant invalide!');
                    $step = 'pay-amount';
                    break;
                }
                $amountCents = (int) round($amountEur * 100);
                $baseUrl = rtrim((string) ($_ENV['CALLBACK_BASE_URL'] ?? ''), '/');
                if ($baseUrl === '') {
                    $baseUrl = requestBaseUrl();
                }
                try {
                    $client = new HelloAssoClient();
                    $checkout = $client->createCheckout(
                        $context['pay_slug'],
                        $amountCents,
                        'Don 3615 HelloAsso',
                        $baseUrl . '/checkout-callback.php?outcome=success',
                        $baseUrl . '/checkout-callback.php?outcome=error',
                        $baseUrl . '/checkout-callback.php?outcome=back'
                    );
                } catch (\Throwable $e) {
                    $checkout = ['error' => $e->getMessage()];
                }
                if (!$checkout || empty($checkout['redirect_url'])) {
                    $err = $checkout['error'] ?? 'Erreur creation checkout!';
                    $vdt = MiniPaviCli::writeLine0(mb_substr($err, 0, 39));
                    $step = 'pay-amount';
                    break;
                }
                $context['checkout_id'] = $checkout['id'];
                $context['checkout_url'] = $checkout['redirect_url'];
                $context['pay_amount_cents'] = $amountCents;
                $context['pay_qr_token'] = CheckoutLink::store($checkout['redirect_url']);
                $context['pay_short_url'] = CheckoutLink::url($baseUrl, $context['pay_qr_token']);
                $step = 'pay-qr';
                break;

            case 'pay-qr':
                $vdt = MiniPaviCli::clearScreen() . PRO_MIN . PRO_LOCALECHO_OFF;
                $shortUrl = $context['pay_short_url'] ?? CheckoutLink::url(requestBaseUrl(), $context['pay_qr_token'] ?? '');
                $vdt .= buildPayQrPage(
                    $shortUrl,
                    (int) ($context['pay_amount_cents'] ?? 0)
                );
                $vdt .= MiniPaviCli::setPos(1, 24);
                $vdt .= VDT_G0 . VDT_SZNORM . VDT_TXTWHITE . VDT_BGBLACK;
                $vdt .= " " . VDT_FDINV . " Sommaire " . VDT_FDNORM . " menu" . VDT_CLRLN;
                $cmd = payQrInputCmd();
                $step = 'pay-qr-saisie';
                $directCall = false;
                break 2;

            case 'pay-qr-saisie':
                if (MiniPaviCli::$fctn == 'SOMMAIRE' || MiniPaviCli::$fctn == 'RETOUR') {
                    $step = 'menu';
                    break;
                }
                if (MiniPaviCli::$fctn == 'REPETITION') {
                    $step = 'pay-qr';
                    break;
                }
                break 2;

            case 'search':
                $vdt = MiniPaviCli::clearScreen() . PRO_MIN . PRO_LOCALECHO_OFF;
                $vdt .= MiniPaviCli::setPos(1, 2);
                $vdt .= buildSearchPage();
                $vdt .= MiniPaviCli::setPos(1, 24);
                $vdt .= VDT_G0 . VDT_SZNORM . VDT_TXTWHITE . VDT_BGBLACK;
                $vdt .= " " . VDT_FDINV . " Envoi " . VDT_FDNORM . " rechercher  " . VDT_FDINV . " Sommaire " . VDT_FDNORM . VDT_CLRLN;
                $cmd = MiniPaviCli::createInputTxtCmd(4, 10, 35, MSK_ENVOI | MSK_SOMMAIRE, true, '.', '');
                $step = 'search-saisie';
                $directCall = false;
                break 2;

            case 'search-saisie':
                if (MiniPaviCli::$fctn == 'SOMMAIRE') {
                    $step = 'menu';
                    break;
                }
                $query = trim(@MiniPaviCli::$content[0] ?? '');
                if (empty($query)) {
                    $vdt = MiniPaviCli::writeLine0('Entrez un nom!');
                    $step = 'search';
                    break;
                }

                try {
                    $client = new HelloAssoClient();
                    $forms = $client->searchForms($query, 20);

                    $context['forms'] = $forms;
                    $context['query'] = $query;
                    $context['page'] = 0;

                    if (empty($forms)) {
                        $vdt = MiniPaviCli::writeLine0('Aucun resultat!');
                        $step = 'search';
                        break;
                    }

                    $step = 'results';
                } catch (\Throwable $e) {
                    $vdt = MiniPaviCli::writeLine0('Erreur API!');
                    $step = 'search';
                }
                break;

            case 'search-today':
                $vdt = MiniPaviCli::clearScreen() . PRO_MIN . PRO_LOCALECHO_OFF;
                $vdt .= MiniPaviCli::setPos(1, 2);
                $vdt .= VDT_BGBLUE . MiniPaviCli::repeatChar(' ', 40);
                $vdt .= MiniPaviCli::setPos(1, 3);
                $vdt .= VDT_BGBLUE . VDT_TXTWHITE . VDT_SZDBLH;
                $vdt .= MiniPaviCli::writeCentered(3, "AUJOURD'HUI", VDT_BGBLUE . VDT_TXTWHITE . VDT_SZDBLH);
                $vdt .= MiniPaviCli::setPos(1, 5);
                $vdt .= VDT_BGBLUE . VDT_SZNORM . MiniPaviCli::repeatChar(' ', 40);
                $vdt .= MiniPaviCli::setPos(1, 8);
                $vdt .= VDT_SZNORM . VDT_BGBLACK . VDT_TXTCYAN;
                $vdt .= MiniPaviCli::toG2(" Ville:");
                $vdt .= MiniPaviCli::setPos(1, 10);
                $vdt .= VDT_TXTWHITE . " > " . VDT_BGBLUE . MiniPaviCli::repeatChar('.', 35);
                $vdt .= MiniPaviCli::setPos(1, 24);
                $vdt .= VDT_G0 . VDT_SZNORM . VDT_TXTWHITE . VDT_BGBLACK;
                $vdt .= " " . VDT_FDINV . " Envoi " . VDT_FDNORM . " rechercher  " . VDT_FDINV . " Sommaire " . VDT_FDNORM . VDT_CLRLN;
                $cmd = MiniPaviCli::createInputTxtCmd(4, 10, 35, MSK_ENVOI | MSK_SOMMAIRE, true, '.', '');
                $step = 'search-today-saisie';
                $directCall = false;
                break 2;

            case 'search-today-saisie':
                if (MiniPaviCli::$fctn == 'SOMMAIRE') {
                    $step = 'menu';
                    break;
                }
                $city = trim(@MiniPaviCli::$content[0] ?? '');
                if (empty($city)) {
                    $vdt = MiniPaviCli::writeLine0('Entrez une ville!');
                    $step = 'search-today';
                    break;
                }

                try {
                    $client = new HelloAssoClient();
                    $forms = $client->searchFormsToday($city, 20);

                    $context['forms'] = $forms;
                    $context['query'] = $city;
                    $context['page'] = 0;

                    if (empty($forms)) {
                        $vdt = MiniPaviCli::writeLine0('Aucun resultat!');
                        $step = 'search-today';
                        break;
                    }

                    $step = 'results';
                } catch (\Throwable $e) {
                    $vdt = MiniPaviCli::writeLine0('Erreur API!');
                    $step = 'search-today';
                }
                break;

            case 'results':
                $forms = $context['forms'] ?? [];
                $page = $context['page'] ?? 0;

                $vdt = MiniPaviCli::clearScreen() . PRO_MIN . PRO_LOCALECHO_OFF;
                $vdt .= MiniPaviCli::setPos(1, 2);
                $vdt .= buildResultsPage($forms, $page);
                $vdt .= MiniPaviCli::setPos(1, 24);
                $vdt .= VDT_G0 . VDT_SZNORM . VDT_TXTWHITE . VDT_BGBLACK;
                $vdt .= " N+" . VDT_FDINV . "Envoi" . VDT_FDNORM . " " . VDT_FDINV . "Suite" . VDT_FDNORM . " " . VDT_FDINV . "Retour" . VDT_FDNORM . " " . VDT_FDINV . "Somm" . VDT_FDNORM . VDT_CLRLN;
                $cmd = MiniPaviCli::createInputTxtCmd(2, 24, 2, MSK_ENVOI | MSK_SUITE | MSK_RETOUR | MSK_SOMMAIRE, true, '.', '');
                $step = 'results-saisie';
                $directCall = false;
                break 2;

            case 'results-saisie':
                $forms = $context['forms'] ?? [];
                $page = $context['page'] ?? 0;
                $perPage = 5;
                $total = count($forms);
                $maxPage = max(0, ceil($total / $perPage) - 1);

                if (MiniPaviCli::$fctn == 'SOMMAIRE') {
                    $step = 'menu';
                    break;
                }
                if (MiniPaviCli::$fctn == 'RETOUR') {
                    $step = 'search';
                    break;
                }
                if (MiniPaviCli::$fctn == 'SUITE') {
                    if ($page < $maxPage) {
                        $context['page'] = $page + 1;
                    } else {
                        $vdt = MiniPaviCli::writeLine0('Derniere page');
                    }
                    $step = 'results';
                    break;
                }

                $choix = (int)trim(@MiniPaviCli::$content[0] ?? '0');
                if ($choix >= 1 && $choix <= $total) {
                    $context['selected_form'] = $forms[$choix - 1];
                    $step = 'detail';
                    break;
                }

                $vdt = MiniPaviCli::writeLine0('Choix invalide!');
                $step = 'results';
                break;

            case 'detail':
                $selected = $context['selected_form'] ?? null;
                if (!$selected) {
                    $step = 'results';
                    break;
                }

                try {
                    $client = new HelloAssoClient();
                    $form = $client->getFormDetail(
                        $selected['orgSlug'],
                        $selected['formType'],
                        $selected['formSlug']
                    );
                } catch (\Throwable $e) {
                    $form = null;
                }

                if (!$form) {
                    $vdt = MiniPaviCli::writeLine0('Impossible de charger!');
                    $step = 'results';
                    break;
                }

                $vdt = MiniPaviCli::clearScreen() . PRO_MIN . PRO_LOCALECHO_OFF;
                $vdt .= MiniPaviCli::setPos(1, 2);
                $vdt .= buildDetailPage($form);
                $vdt .= MiniPaviCli::setPos(1, 24);
                $vdt .= VDT_G0 . VDT_SZNORM . VDT_TXTWHITE . VDT_BGBLACK;
                $vdt .= " " . VDT_FDINV . " Retour " . VDT_FDNORM . " liste  " . VDT_FDINV . " Sommaire " . VDT_FDNORM . " menu" . VDT_CLRLN;
                $step = 'detail-saisie';
                $directCall = false;
                break 2;

            case 'detail-saisie':
                if (MiniPaviCli::$fctn == 'SOMMAIRE') {
                    $step = 'menu';
                    break;
                }
                if (MiniPaviCli::$fctn == 'RETOUR') {
                    $step = 'results';
                    break;
                }
                break 2;

            case 'about':
                $vdt = MiniPaviCli::clearScreen() . PRO_MIN . PRO_LOCALECHO_OFF;
                $vdt .= MiniPaviCli::setPos(1, 2);
                $vdt .= buildAboutPage();
                $vdt .= MiniPaviCli::setPos(1, 24);
                $vdt .= VDT_G0 . VDT_SZNORM . VDT_TXTWHITE . VDT_BGBLACK;
                $vdt .= " " . VDT_FDINV . " Sommaire " . VDT_FDNORM . " retour au menu" . VDT_CLRLN;
                $step = 'about-saisie';
                $directCall = false;
                break 2;

            case 'about-saisie':
                if (MiniPaviCli::$fctn == 'SOMMAIRE' || MiniPaviCli::$fctn == 'RETOUR') {
                    $step = 'menu';
                    break;
                }
                break 2;

            default:
                $vdt = MiniPaviCli::writeLine0('Etape inconnue');
                $step = 'menu';
                $directCall = false;
                break;
        }
    }

    $nextPage = requestBaseUrl() . '/' . basename($_SERVER['PHP_SELF'] ?? 'index.php');
    $context['step'] = $step;
    MiniPaviCli::send($vdt, $nextPage, serialize($context), true, $cmd, $directCall);
} catch (Exception $e) {
    throw new Exception('Erreur MiniPavi ' . $e->getMessage());
}
exit;
