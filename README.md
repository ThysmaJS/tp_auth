# **ReadMe : Guide d'Exécution du Projet**

---

## **Prérequis**

Avant de commencer, assurez-vous que votre environnement répond aux exigences suivantes :

1. **Outils et versions** :

   - PHP ≥ 8.1
   - Composer
   - MySQL
   - Symfony CLI
   - Node.js et npm (pour le frontend avec Tailwind CSS)
   - WAMP (Windows, Apache, MySQL, PHP) pour les utilisateurs Windows.

2. **Extensions PHP** :

   - `pdo_mysql`
   - `json`
   - `mbstring`

3. **Autres requis** :

   - Un terminal fonctionnel (Bash, PowerShell, etc.).

---

## **Installation et Configuration**

### **1. Clonez le projet**

Clonez le dépôt Git dans votre environnement local :

```bash
$ git clone <URL_DU_DEPOT>
$ cd tp_auth
```

### **2. Installez les dépendances**

#### Backend (Symfony) :

Installez les dépendances PHP avec Composer :

```bash
$ composer install
```

#### Frontend (Tailwind CSS) :

Installez les dépendances Node.js :

```bash
$ npm install
```

### **3. Configuration des Variables d'Environnement**

Dupliquez le fichier `.env` et modifiez les paramètres de connexion à votre base de données :

```env
DATABASE_URL="mysql://<utilisateur>:<mot_de_passe>@127.0.0.1:3306/tp_auth"
```

Ajoutez également vos clés reCAPTCHA :

```env
GOOGLE_RECAPTCHA_SITE_KEY=<votre_clé_publique>
GOOGLE_RECAPTCHA_SECRET_KEY=<votre_clé_secrète>
```

### **4. Créez la base de données**

Avant d'exécuter les migrations, créez la base de données en utilisant Doctrine :

```bash
$ php bin/console doctrine:database:create
```

Ensuite, appliquez les migrations pour créer les tables :

```bash
$ php bin/console doctrine:migrations:migrate
```

### **5. Compiler les fichiers CSS**

Générez le fichier CSS avec Tailwind CSS :

```bash
$ npx tailwindcss -i ./assets/styles/app.css -o ./public/build/app.css --watch
```

### **6. Lancez le Watch**

```bash
$ npm run watch
```

### **7. Lancez le serveur Symfony**

```bash
$ symfony server:start
```

Accédez au projet via :

```
http://127.0.0.1:8000
```

---

## **Fonctionnalités**

### **1. Authentification**

- **Inscription** :
  - Formulaire d'inscription avec validation des champs (pseudo unique, email valide, etc.).
  - Mot de passe haché avec bcrypt.
- **Connexion** :
  - Formulaire de connexion.
  - Gestion des sessions utilisateurs.
  - Blocage avec reCAPTCHA après 3 tentatives échouées.

### **2. Frontend**

- **Interface utilisateur** stylisée avec Tailwind CSS.

---

## **Commandes Utiles**

### **Vider le cache Symfony**

```bash
$ php bin/console cache:clear
```

### **Recompiler les fichiers CSS**

```bash
$ npm run dev
```

### **Mettre à jour les dépendances**

#### Composer :

```bash
$ composer update
```

#### npm :

```bash
$ npm update
```

---

## **Dépannage**

### **Problème : Base de données manquante**

Erreur : `SQLSTATE[HY000] [1049] Base 'tp_auth' inconnue`

- Vérifiez que la base est créée :
  ```bash
  $ php bin/console doctrine:database:create
  ```

### **Problème : Erreur reCAPTCHA**

- Assurez-vous que vos clés sont correctement configurées dans le fichier `.env`.
- Ajoutez `127.0.0.1` ou `localhost` comme domaine dans la console Google reCAPTCHA.

---

## **Conclusion**

Votre projet est maintenant configuré et prêt à être utilisé. Si vous avez des questions ou des problèmes, n’hésitez pas à demander de l’aide !
