# Projet : Film Collection

## Installation

### 1. Cloner le projet

```
git clone https://github.com/YetAnotherDevWannabe/Film_Collection.git
```

### 2. Créer un fichier ```.env.local``` à la racine du projet

### 3. Modifier les paramètres d'environnement dans le fichier ```.env.local``` :

- Copier le texte ci-dessous dans le fichier ```.env.local```
```
###> Accès BDD à modifier ###
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"

###> Clés Google reCAPTCHA à changer ###
GOOGLE_RECAPTCHA_SITE_KEY=XXXXXXXXXXXXXXXXXXXXXXXXXXXX
GOOGLE_RECAPTCHA_PRIVATE_KEY=
```

- Changer ```db_user```, ```db_password``` et ```db_name```
- Modifier les clés Google reCAPTCHA publique et privée

### 4. Préparer Symfony

- Déplacer le terminal dans le projet cloné
	```
	cd Film_Collection
	```

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


### 5. Démarrer le server web de Symfony

```
symfony serve
```


### 6. Les fixtures (Users / Collections / Films / Commentaires) :

- Charger les fixtures avec la commande suivante

```
symfony console doctrine:fixtures:load
```

Les fixtures vont créer :

* Un compte admin  (email: 'admin@email.com', password: 'Aa111111!')
* Un compte user  (email: 'w@w.w', password: 'Ww111111!')
* 12 Films (10 complet et 1 sans note / poster / trailer)
* 13 Collections (pas de films rattachés pour le moment)