# 3615 HELLOASSO

Service Minitel pour [HelloAsso](https://www.helloasso.com) via [MiniPavi](https://www.minipavi.fr/).

Recherchez des événements associatifs et consultez leurs détails, directement depuis un Minitel.

## Fonctionnalités

- **Recherche par nom** — Trouver un événement par mot-clé
- **Événements aujourd'hui** — Découvrir les événements du jour dans une ville
- **Fiche détaillée** — Dates, lieu, tarifs, organisateur

## Prérequis

- PHP 8.1+
- Composer
- Un compte développeur [HelloAsso](https://dev.helloasso.com/) (client_id / client_secret)
- Un serveur [MiniPavi](https://www.minipavi.fr/) ou un émulateur Minitel

## Installation

```bash
git clone https://github.com/emnbdx/3615-helloasso.git
cd 3615-helloasso
composer install
cp .env.example .env
```

Renseignez le fichier `.env` :

```
API_URL=https://api.helloasso.com
API_AUTH_URL=https://api.helloasso.com/oauth2/token
CLIENT_ID=votre_client_id
CLIENT_SECRET=votre_client_secret
```

## Déploiement

Pointez votre serveur web (Apache) vers la racine du projet. Le fichier `index.php` est le point d'entrée du service MiniPavi.

## Stack

- [MiniPavi-CLI](https://github.com/ludosevilla/minipavi-cli) — Framework Minitel
- [HelloAsso PHP SDK](https://github.com/helloasso/helloasso-php) — Client API HelloAsso
- [league/oauth2-client](https://github.com/thephpleague/oauth2-client) — OAuth2
- [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv) — Variables d'environnement

## Licence

MIT
