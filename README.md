# DeployLab

DeployLab est une mini-application Symfony pedagogique pour travailler une strategie de deploiement entre local, staging et production.

L'application simule un outil interne de suivi de demandes de mise en production. Elle contient volontairement une base fonctionnelle, mais pas une industrialisation production complete.

## Stack

- PHP 8.3 ou 8.4
- Symfony 7.3
- Doctrine ORM
- PostgreSQL 16
- Twig
- Symfony Security, Forms et Validator
- AssetMapper
- PHPUnit

## Prerequis

- PHP 8.3+
- Composer 2
- PostgreSQL 16, ou Docker avec Docker Compose
- Symfony CLI facultatif

## Installation locale sans Docker

```bash
composer install
cp .env.example .env
```

Adapter `DATABASE_URL` dans `.env`, puis creer la base et charger les donnees :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
symfony server:start
```

Sans Symfony CLI :

```bash
php -S 127.0.0.1:8000 -t public
```

## Installation avec Docker Compose

```bash
docker compose up --build
```

Dans un autre terminal :

```bash
docker compose exec php php bin/console doctrine:migrations:migrate
docker compose exec php php bin/console doctrine:fixtures:load
```

L'application est disponible sur `http://localhost:8000`.

## Comptes de test

- `admin@deploylab.test` / `password`
- `user@deploylab.test` / `password`

## Routes principales

- `/login` : connexion
- `/` : dashboard
- `/applications` : liste des applications
- `/deployments` : liste des demandes de deploiement
- `/health` : healthcheck JSON avec test rapide de connexion base de donnees

## Commandes utiles

```bash
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
php bin/console cache:clear
php bin/console debug:router
php bin/phpunit
```

Avec le Makefile :

```bash
make install
make start
make migrate
make fixtures
make test
```

## Tests

```bash
php bin/phpunit
```

Les tests fournis sont volontairement simples. Ils servent de point de depart pour etendre la couverture fonctionnelle, notamment sur les controles d'acces et les parcours de creation.

## Travail attendu des etudiants

Le projet est fonctionnel en local, mais il n'est pas pret pour la production. Les etudiants doivent proposer et justifier :

- une configuration par environnement : dev, staging, prod
- un Dockerfile de production
- un docker-compose ou une stack adaptee a la production
- une strategie de variables d'environnement et secrets
- une pipeline CI/CD
- une strategie de migration de base de donnees
- une strategie de rollback
- une strategie de logs
- une strategie de sauvegarde PostgreSQL
- une strategie HTTPS / reverse proxy
- une strategie de monitoring / healthcheck
- une documentation de mise en production

Points volontairement perfectibles : pas de reverse proxy de production, pas de registry Docker, pas de HTTPS, pas de monitoring complet, pas de sauvegarde automatisee, pas de gestion avancee du cache ou des logs, pas de strategie de rollback implementee.
