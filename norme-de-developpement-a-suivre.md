# Normes de développement et de collaboration — HAProxy Manager

Ce document constitue la référence technique commune aux trois membres. Il définit les règles à respecter pendant le développement et l'intégration.

## 1. Technologies retenues

| Partie | Technologie |
|---|---|
| Frontend | HTML, CSS, JavaScript natif |
| Backend | PHP natif |
| Communication | API HTTP + JSON |
| Service administré | HAProxy sous Linux |

Le projet doit rester simple. Aucun framework ou système supplémentaire n'est nécessaire si les fonctionnalités du TP peuvent être réalisées avec les technologies retenues.

## 2. Organisation du dépôt GitHub

### Nom du dépôt

`gestion-haproxy-trinome`

### Structure commune du projet

```text
gestion-haproxy-trinome/
├── app/
│   ├── Controllers/
│   │   ├── DashboardController.php
│   │   ├── ServerController.php
│   │   └── ServiceController.php
│   ├── Services/
│   │   ├── HAProxyConfig.php
│   │   ├── HAProxyRuntime.php
│   │   └── SystemService.php
│   └── Core/
│       ├── Router.php
│       └── Response.php
├── config/
│   └── app.php
├── public/
│   ├── index.php
│   └── assets/
│       ├── css/
│       │   ├── main.css
│       │   ├── dashboard.css
│       │   ├── servers.css
│       │   └── service.css
│       └── js/
│           ├── api.js
│           ├── main.js
│           ├── dashboard.js
│           ├── servers.js
│           └── service.js
├── views/
│   ├── layouts/
│   │   └── main.php
│   ├── dashboard.php
│   ├── servers.php
│   └── service.php
├── .gitignore
└── README.md
```

Les fichiers communs sont créés ensemble au début du projet. Les autres fichiers sont développés par le membre responsable de chaque module.

## 3. Workflow Git

### Branches à utiliser

| Branche | Responsable | Utilisation |
|---|---|---|
| `main` | Tous | Version stable du projet |
| `feature/server-management` | Johny | Gestion des serveurs |
| `feature/service-management` | Ny Antema | Gestion du service HAProxy |
| `feature/dashboard-runtime` | Miranto | Tableau de bord et Runtime API |

### Règles obligatoires

- [ ] Ne jamais développer directement sur `main`.
- [ ] Chaque membre travaille sur sa branche.
- [ ] Récupérer les dernières modifications avant de commencer une session.
- [ ] Faire des commits réguliers.
- [ ] Utiliser des messages de commit compréhensibles.
- [ ] Envoyer régulièrement sa branche sur GitHub.
- [ ] Créer une Pull Request vers `main`.
- [ ] Faire vérifier les modifications avant leur fusion.
- [ ] Ne fusionner que du code testé.

### Exemple de workflow

```bash
# Cloner le dépôt
git clone https://github.com/Johny-olivier/gestion-haproxy-trinome.git

cd gestion-haproxy-trinome

# Se placer sur sa branche
git switch feature/server-management

# Récupérer les dernières modifications
git pull

# Ajouter les fichiers modifiés
git add .

# Créer un commit
git commit -m "feat(servers): ajouter la création des serveurs"

# Envoyer les modifications sur GitHub
git push -u origin feature/server-management
```

Ensuite, créer une Pull Request sur GitHub vers `main`.

### Conventions des commits

| Préfixe | Utilisation | Exemple |
|---|---|---|
| `feat` | Nouvelle fonctionnalité | `feat(servers): ajouter la création des serveurs` |
| `fix` | Correction d'un bug | `fix(api): gérer les JSON invalide` |
| `style` | Modification visuelle | `style(dashboard): ajuster l'espacement` |
| `refactor` | Réorganisation du code | `refactor(config): simplifier le parser` |
| `docs` | Documentation | `docs: mettre à jour le README` |

## 4. Communication entre frontend et backend

### Principe de fonctionnement

Le frontend ne communique jamais directement avec HAProxy ou le système Linux.

Le fonctionnement est toujours le suivant :

1. L'utilisateur effectue une action dans l'interface.
2. JavaScript envoie une requête HTTP à l'API PHP avec `fetch()`.
3. PHP reçoit et vérifie les données.
4. PHP exécute l'opération autorisée.
5. PHP renvoie une réponse JSON.
6. JavaScript actualise l'interface.

### Format JSON obligatoire

Toutes les API doivent respecter le même format.

**En cas de succès :**

```json
{
  "success": true,
  "message": "Serveur ajouté avec succès.",
  "data": {
    "id": "web3"
  }
}
```

**En cas d'erreur :**

```json
{
  "success": false,
  "message": "Adresse IP invalide.",
  "data": null
}
```

### Règles à respecter

- [ ] Toutes les réponses API doivent être au format JSON.
- [ ] `success` indique si l'opération a réussi.
- [ ] `message` contient une explication lisible.
- [ ] `data` contient les données retournées ou `null`.
- [ ] Utiliser un code HTTP adapté au résultat.
- [ ] Respecter les noms des routes et des champs définis ci-dessous.

### Contrat API commun

Les routes suivantes sont déjà définies. Elles constituent le contrat commun : les membres doivent les implémenter telles quelles, sans redéfinir les noms ou les méthodes.

| Méthode | Route | Fonction |
|---|---|---|
| GET | `/api/servers` | Récupérer les serveurs |
| POST | `/api/servers` | Ajouter un serveur |
| PUT | `/api/servers/{id}` | Modifier un serveur |
| DELETE | `/api/servers/{id}` | Supprimer un serveur |
| GET | `/api/service/status` | Consulter l'état du service |
| POST | `/api/service/start` | Démarrer HAProxy |
| POST | `/api/service/stop` | Arrêter HAProxy |
| POST | `/api/service/restart` | Redémarrer HAProxy |
| POST | `/api/service/reload` | Recharger la configuration |
| GET | `/api/dashboard` | Récupérer les données du tableau de bord |
| GET | `/api/runtime/servers` | Récupérer l'état dynamique des serveurs |
| POST | `/api/runtime/servers/{id}/disable` | Désactiver temporairement un serveur |
| POST | `/api/runtime/servers/{id}/enable` | Réactiver un serveur |

### Exemple de requête : ajouter un serveur

**Route :**

```http
POST /api/servers
Content-Type: application/json
```

**Données envoyées :**

```json
{
  "id": "web3",
  "name": "web3",
  "address": "192.168.1.104",
  "port": 80
}
```

**Réponse attendue :**

```json
{
  "success": true,
  "message": "Serveur ajouté avec succès.",
  "data": {
    "id": "web3"
  }
}
```

Les adresses IP sont des exemples.

## 5. Fonctions communes à développer une seule fois

Certains éléments seront utilisés par les trois membres. Il ne faut donc pas les coder plusieurs fois.

| Fichier | Responsabilité | Utilisateurs |
|---|---|---|
| `app/Core/Router.php` | Gestion des routes | Moi Johny le ferait |
| `app/Core/Response.php` | Réponses JSON standardisées | Moi Johny le ferait |
| `public/assets/js/api.js` | Appels API avec `fetch()` | Moi Johny le ferait |
| `public/assets/js/main.js` | Fonctions JavaScript communes | Moi Johny le ferait |
| `public/assets/css/main.css` | Styles communs | Moi Johny le ferait |
| `views/layouts/main.php` | Structure commune des pages | Moi Johny le ferait |

### Exemple de fonction JavaScript commune

Cette fonction sera créée une seule fois dans `public/assets/js/api.js` :

```javascript
async function apiRequest(url, options = {}) {
    const response = await fetch(url, {
        ...options,
        headers: {
            "Content-Type": "application/json",
            ...(options.headers || {})
        }
    });

    const result = await response.json();

    if (!response.ok || !result.success) {
        throw new Error(result.message || "Une erreur est survenue.");
    }

    return result;
}
```

Les trois membres pourront ensuite l'utiliser dans leurs propres fichiers JavaScript.

**Exemple d'utilisation :**

```javascript
const result = await apiRequest("/api/servers");

console.log(result.data);
```

## 6. Conventions de programmation

### PHP

- [ ] Utiliser `PascalCase` pour les classes : `ServerController`.
- [ ] Utiliser `camelCase` pour les méthodes et variables : `getServers()`.
- [ ] Donner une responsabilité claire à chaque classe.
- [ ] Vérifier et valider les données reçues.
- [ ] Séparer la logique métier de l'affichage HTML.
- [ ] Respecter le format JSON commun.

### JavaScript

- [ ] Utiliser `camelCase` pour les fonctions et variables.
- [ ] Utiliser `const` par défaut et `let` lorsque nécessaire.
- [ ] Utiliser `api.js` pour les appels API.
- [ ] Afficher les erreurs retournées par le backend.
- [ ] Récupérer les informations des serveurs depuis l'API.

### HTML et CSS

- [ ] Utiliser des noms de classes explicites.
- [ ] Réutiliser les styles communs.
- [ ] Garder une présentation cohérente entre les pages.
- [ ] Éviter de dupliquer les mêmes composants.

## 7. Sécurité minimale

L'application peut modifier la configuration HAProxy et exécuter des commandes système. Il faut donc respecter ces règles :

- [ ] Ne jamais construire une commande système directement à partir d'une saisie utilisateur.
- [ ] Ne jamais donner un accès `sudo` général à l'utilisateur PHP (`www-data`).
- [ ] Autoriser uniquement les opérations prévues dans le projet.
- [ ] Vérifier les noms de serveurs, les adresses IP et les ports.
- [ ] Contrôler les permissions avant les opérations système.
- [ ] Ne pas exposer les informations sensibles dans les réponses API.
- [ ] Limiter l'accès à l'interface aux personnes autorisées sur le réseau de test.

Les commandes système doivent être exécutées de manière sécurisée, avec des arguments séparés et une liste blanche d'actions autorisées.

## 8. Règles spécifiques à HAProxy

### Modification de `haproxy.cfg`

Johny est responsable des modifications persistantes du fichier de configuration.

Avant chaque modification :

1. Sauvegarder l'ancienne configuration.
2. Préparer la nouvelle configuration.
3. Vérifier sa syntaxe avec `haproxy -c`.
4. Remplacer le fichier uniquement si la validation réussit.
5. Recharger HAProxy.
6. En cas d'échec, conserver la dernière configuration valide.

### Runtime API

La Runtime API est utilisée pour les actions dynamiques sur les serveurs.

- Désactiver un serveur ne le supprime pas de `haproxy.cfg`.
- Réactiver un serveur lui permet de recevoir à nouveau des requêtes.
- Une modification effectuée uniquement via Runtime API peut être perdue après un rechargement ou un redémarrage.

### Commandes systemd

| Commande | Signification |
|---|---|
| `start` | Démarrer HAProxy |
| `stop` | Arrêter HAProxy |
| `restart` | Redémarrer HAProxy |
| `reload` | Recharger la configuration |

Le démarrage automatique avec `enable` ou `disable` est différent du démarrage et de l'arrêt immédiats. Ces options ne sont pas prioritaires pour le projet.

## 9. Tests et intégration

### Tests individuels

- [ ] Johny : tester l'ajout, la modification et la suppression des serveurs.
- [ ] Ny Antema : tester l'état, le démarrage, l'arrêt et le redémarrage de HAProxy.
- [ ] Miranto : tester l'état UP/DOWN et la désactivation/réactivation des serveurs.

### Tests communs

- [ ] Vérifier que les trois pages fonctionnent.
- [ ] Vérifier que les API retournent les bons formats JSON.
- [ ] Vérifier que les deux serveurs Web réels sont accessibles.
- [ ] Vérifier que HAProxy peut communiquer avec les deux serveurs.
- [ ] Envoyer plusieurs requêtes HTTP et vérifier Round Robin.
- [ ] Vérifier qu'une configuration invalide ne remplace pas la dernière configuration valide.

## 10. Organisation finale

### Avant le développement

- [x] Créer le dépôt GitHub.
- [ ] Ajouter les trois membres.
- [ ] Créer les branches.
- [ ] Préparer l'arborescence.
- [ ] Développer les fonctions communes.
- [ ] Utiliser le contrat API déjà défini dans ce document.

### Pendant le développement

- [ ] Chaque membre travaille sur son module.
- [ ] Chaque membre teste son travail.
- [ ] Les modifications des fichiers communs sont discutées.
- [ ] Les commits sont réguliers.
- [ ] Les Pull Requests sont vérifiées avant fusion.

### Avant la démonstration

- [ ] Fusionner les trois modules dans `main`.
- [ ] Tester l'application sur le PC HAProxy.
- [ ] Vérifier les permissions et les ports.
- [ ] Préparer les deux serveurs Web.
- [ ] Vérifier le fonctionnement de Round Robin.

## 11. Scénario de démonstration

1. Ouvrir le tableau de bord.
2. Montrer que HAProxy est actif.
3. Afficher les deux serveurs Web.
4. Ajouter ou modifier un serveur.
5. Appliquer la configuration.
6. Envoyer plusieurs requêtes et montrer Round Robin.
7. Désactiver temporairement un serveur.
8. Montrer que l'autre serveur continue de répondre.
9. Réactiver le serveur.
10. Redémarrer HAProxy depuis l'interface.

**Règle générale :** les trois membres développent des modules différents, mais utilisent les mêmes conventions, les mêmes fonctions communes et le même contrat API.