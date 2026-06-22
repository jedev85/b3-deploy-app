# DeployLab

DeployLab est une mini-application Symfony pedagogique pour travailler une strategie de deploiement entre local, staging et production.

L'application simule un outil interne de suivi de demandes de mise en production. Elle contient volontairement une base fonctionnelle, mais pas une industrialisation production complete.

Le depot fournit un outillage Docker minimal pour faciliter l'execution locale et les premiers essais d'hebergement, mais il ne fournit pas de strategie d'environnements prete a l'emploi. Ces sujets font partie du travail attendu.

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
- PostgreSQL 16
- Symfony CLI facultatif

## Installation locale minimale

```bash
composer install
cp .env.example .env
```

Adapter `DATABASE_URL` dans `.env` pour pointer vers une base PostgreSQL locale que vous avez creee, puis lancer les migrations et les fixtures :

```bash
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
symfony server:start
```

Sans Symfony CLI :

```bash
php -S 127.0.0.1:8000 -t public
```

## Docker attendu

Docker n'est pas fourni comme solution finale cle en main dans ce depot. L'outillage present sert de point de depart local et de support de test, mais les etudiants doivent concevoir une strategie Docker progressive pour passer d'un usage local a une cible staging puis production.

Le travail Docker attendu doit au minimum couvrir :

- une image PHP locale capable d'executer Symfony et Composer
- les extensions PHP necessaires, notamment `pdo_pgsql`, `intl`, `zip` et `opcache`
- un service PostgreSQL local ou une connexion documentee vers une base PostgreSQL existante
- la gestion de `DATABASE_URL` selon le contexte d'execution, par exemple hote local, conteneur applicatif ou reseau Docker
- une commande claire pour installer les dependances Composer
- une commande claire pour lancer les migrations et les fixtures
- une commande claire pour lancer le serveur applicatif en local
- une distinction explicite entre Docker de developpement et Docker de production

La solution production ne doit pas etre une simple copie de l'environnement local. Elle doit traiter au minimum les sujets suivants : image optimisee, utilisateur non-root, variables d'environnement, secrets, cache Symfony, logs, reverse proxy, HTTPS, migrations, rollback et healthcheck.

## Comptes de test

- `admin@deploylab.test` / `password`
- `user@deploylab.test` / `password`

## Routes principales

- `/login` : connexion
- `/` : dashboard
- `/applications` : liste des applications
- `/deployments` : liste des demandes de deploiement
- `/health` : healthcheck JSON public compatible Render, avec test rapide de connexion base de donnees

Fonctionnalites disponibles dans les demandes :

- filtres par statut et application
- commentaires internes par demande
- journal d'activite des changements de statut
- widget Vue.js sur le dashboard pour visualiser la repartition des statuts

## Commandes utiles

```bash
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
php bin/console cache:clear
php bin/console debug:router
php bin/phpunit
```

## Tests

```bash
php bin/phpunit
```

Les tests fournis sont volontairement simples. Ils servent de point de depart pour etendre la couverture fonctionnelle, notamment sur les controles d'acces et les parcours de creation.

## Travail attendu des etudiants

Le projet fournit le code applicatif Symfony, mais il ne fournit pas l'industrialisation. Les etudiants doivent proposer, implementer et justifier :

- une strategie d'environnements complete, du local au staging puis a la production
- une configuration par environnement : dev/local, staging, prod
- un Dockerfile adapte au developpement local
- un Dockerfile de production
- un docker-compose, une stack ou une alternative adaptee a chaque contexte
- un Makefile ou une autre interface de commandes si l'equipe le juge utile
- une strategie de variables d'environnement et secrets
- une pipeline CI/CD
- une strategie de migration de base de donnees
- une strategie de rollback
- une strategie de logs
- une strategie de sauvegarde PostgreSQL
- une strategie HTTPS / reverse proxy
- une strategie de monitoring / healthcheck
- une documentation de mise en production

Points volontairement absents : pas de Docker Compose complet, pas de reverse proxy, pas de registry Docker, pas de HTTPS, pas de monitoring complet, pas de sauvegarde automatisee, pas de gestion avancee du cache ou des logs, pas de strategie de rollback implementee, pas de configuration staging/prod fournie.
