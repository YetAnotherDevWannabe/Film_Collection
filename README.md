# Projet : Film Collection

## Installation

### Cloner le projet

```
git clone https://github.com/YetAnotherDevWannabe/Film_Collection.git
```

### Modifier les paramètres d'environnement dans le fichier .env :

- Dé-commenter la ligne ```# DATABASE_URL="mysql:..."```
- Mettre en commentaire ('#') la ligne ```DATABASE_URL="postgresql:..."```
- Changer ```user_db```, ```password_db``` et ```db_name```

```
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
# DATABASE_URL="postgresql://db_user:db_password@127.0.0.1:5432/db_name?serverVersion=13&charset=utf8"

# Clés Google Recaptcha à changer
GOOGLE_RECAPTCHA_SITE_KEY=XXXXXXXXXXXXXXXXXXXXXXXXXXXX
GOOGLE_RECAPTCHA_PRIVATE_KEY=XXXXXXXXXXXXXXXXXXXXXXXXXXXX
```

### Déplacer le terminal dans le projet cloné

```
cd Film_Collection
```

### Tapper les commandes suivantes :

- Réinitialiser vendor et var
  ```
  composer install
  ```

- Créer la DB
  ```
  symfony console doctrine:database:create
  ```

- Analise les différences entre la DB et les Entities
  ```
  symfony console make:migration
  ```

- Synchronise la DB avec les Entities
  ```
  symfony console doctrine:migration:migrate
  ```


### Démarrer le server web de Symfony

```
symfony serve
```


### TODO : Pour créer les fixtures (Users / Collections / Commentaires) :

- Charger les fixtures avec la commande suivante

```
symfony console doctrine:fixtures:load
```

Les fixtures créeront :

* Un compte admin  (email: 'admin@email.com', password: 'Aa111111/')
* Un compte mod  (email: 'mod@email.com', password: 'Mm111111/')
	* entre 1 et 4 autre comptes mods
* 1 compte utilisateur (email 'user@email.com', password : 'Uu111111/')
	* entre 44 et 74 autre comptes utilisateurs
* entre 150 et 200 articles
* entre 0 et 10 commentaires par article