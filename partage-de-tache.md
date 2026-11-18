# Répartition des tâches — HAProxy Manager

## 1. Présentation du projet

### Objectif

Créer une petite interface Web permettant de gérer HAProxy sans devoir passer systématiquement par le terminal.

L'application doit permettre de :

- Consulter l'état de HAProxy.
- Afficher les serveurs Web configurés.
- Ajouter, modifier et supprimer des serveurs.
- Activer ou désactiver temporairement un serveur.
- Démarrer, arrêter et redémarrer HAProxy.
- Recharger une configuration après validation.

### Technologies

| Partie | Technologie |
|---|---|
| Frontend | HTML, CSS, JavaScript natif |
| Backend | PHP natif |
| Communication | API HTTP + JSON |
| Service administré | HAProxy sous Linux |

### Architecture

- PC 1 : serveur Web 1.
- PC 2 : serveur Web 2.
- PC 3 : HAProxy et interface d'administration.

HAProxy écoute sur le port `80`. L'interface Web utilise un autre port, par exemple `8080`.

---

## 2. Johny — Gestion des serveurs

### Objectif

Développer la partie qui permet de gérer les serveurs Web enregistrés dans la configuration HAProxy (`haproxy.cfg`).

### A. Frontend à réaliser

Créer une page de gestion des serveurs avec :

- [ ] Un tableau affichant les serveurs configurés.
- [ ] Un bouton pour ajouter un serveur.
- [ ] Un formulaire pour modifier un serveur.
- [ ] Un bouton pour supprimer un serveur.
- [ ] Des confirmations avant les opérations importantes.
- [ ] Des messages de succès et d'erreur.

### Exemple d'interface attendue

| Nom | Adresse IP | Port | État | Actions |
|---|---|---:|---|---|
| web1 | 192.168.1.101 | 80 | UP | Modifier · Supprimer |
| web2 | 192.168.1.103 | 80 | DOWN | Modifier · Supprimer |

Les adresses IP sont des exemples.

### Exemple de formulaire d'ajout

| Champ | Exemple |
|---|---|
| Nom du serveur | `web3` |
| Adresse IP | `192.168.1.104` |
| Port | `80` |

Bouton : **Ajouter le serveur**

### B. Backend à réaliser

- [ ] Lire les serveurs déclarés dans `haproxy.cfg`.
- [ ] Ajouter un serveur dans la configuration.
- [ ] Modifier les informations d'un serveur.
- [ ] Supprimer un serveur de la configuration.
- [ ] Vérifier le nom, l'adresse IP et le port reçus.
- [ ] Sauvegarder l'ancienne configuration avant modification.
- [ ] Vérifier la syntaxe avec `haproxy -c`.
- [ ] Appliquer la nouvelle configuration uniquement si elle est valide.

### C. API à développer

| Méthode | Route | Fonction |
|---|---|---|
| GET | `/api/servers` | Récupérer les serveurs |
| POST | `/api/servers` | Ajouter un serveur |
| PUT | `/api/servers/{id}` | Modifier un serveur |
| DELETE | `/api/servers/{id}` | Supprimer un serveur |

Les formats des requêtes et réponses sont déjà définis dans le document **Normes de développement**.

### D. Fichiers concernés

- `views/servers.php`
- `public/assets/js/servers.js`
- `public/assets/css/servers.css`
- `app/Controllers/ServerController.php`
- `app/Services/HAProxyConfig.php`

### Résultat attendu

L'utilisateur peut gérer les serveurs depuis l'interface Web. Les modifications sont enregistrées dans `haproxy.cfg`, vérifiées puis appliquées à HAProxy.

---

## 3. Ny Antema — Gestion du service HAProxy

### Objectif

Développer la partie qui permet de contrôler le service HAProxy directement depuis l'interface Web.

### A. Frontend à réaliser

Créer une page de gestion du service haproxy avec :

- [ ] L'état actuel de HAProxy.
- [ ] Un bouton pour démarrer le service.
- [ ] Un bouton pour arrêter le service.
- [ ] Un bouton pour redémarrer le service.
- [ ] Un bouton pour recharger la configuration.
- [ ] Des confirmations avant les opérations importantes.
- [ ] Des messages indiquant le résultat de chaque action.

### Exemple d'interface attendue

**État du service : ACTIF**

| Action | Bouton | Fonction |
|---|---|---|
| Démarrer | Start | Démarrer HAProxy |
| Arrêter | Stop | Arrêter HAProxy |
| Redémarrer | Restart | Redémarrer HAProxy |
| Recharger | Reload | Recharger la configuration |

Exemple de retour après une action :

> HAProxy a été redémarré avec succès.

### B. Backend à réaliser

- [ ] Récupérer l'état réel du service HAProxy.
- [ ] Exécuter les commandes autorisées avec `systemctl`.
- [ ] Gérer le démarrage du service.
- [ ] Gérer son arrêt.
- [ ] Gérer son redémarrage.
- [ ] Gérer le rechargement de la configuration.
- [ ] Retourner le résultat des opérations.
- [ ] Gérer les erreurs et les permissions nécessaires.

### C. API à développer

| Méthode | Route | Fonction |
|---|---|---|
| GET | `/api/service/status` | Consulter l'état du service |
| POST | `/api/service/start` | Démarrer HAProxy |
| POST | `/api/service/stop` | Arrêter HAProxy |
| POST | `/api/service/restart` | Redémarrer HAProxy |
| POST | `/api/service/reload` | Recharger la configuration |

Les formats des requêtes et réponses sont déjà définis dans le document **Normes de développement**.

### D. Fichiers concernés

- `views/service.php`
- `public/assets/js/service.js`
- `public/assets/css/service.css`
- `app/Controllers/ServiceController.php`
- `app/Services/SystemService.php`

### Résultat attendu

L'utilisateur peut contrôler HAProxy depuis le navigateur sans devoir lancer manuellement les commandes dans le terminal.

---

## 4. Miranto — Tableau de bord et supervision

### Objectif

Développer le tableau de bord qui présente l'état général de HAProxy et permet de désactiver ou réactiver temporairement les serveurs.

### A. Frontend à réaliser

Créer un tableau de bord présentant :

- [ ] L'état général de HAProxy.
- [ ] Le nombre de serveurs configurés.
- [ ] Le nombre de serveurs disponibles.
- [ ] Le nombre de serveurs indisponibles.
- [ ] La liste des serveurs avec leur état.
- [ ] Les actions pour désactiver et réactiver un serveur.
- [ ] Un moyen d'actualiser les informations.

### Exemple de tableau de bord attendu

**État général**

| Indicateur | Valeur |
|---|---:|
| Service HAProxy | Actif |
| Serveurs configurés | 2 |
| Serveurs disponibles | 1 |
| Serveurs indisponibles | 1 |

**État des serveurs**

| Serveur | État | Requêtes | Action |
|---|---|---:|---|
| web1 | UP | 125 | Désactiver |
| web2 | DOWN | 0 | — |

Les valeurs sont illustratives. Les informations doivent être récupérées depuis HAProxy.

### B. Backend à réaliser

- [ ] Récupérer les informations nécessaires au tableau de bord.
- [ ] Récupérer l'état réel des serveurs via la Runtime API HAProxy.
- [ ] Récupérer les statistiques utiles.
- [ ] Désactiver temporairement un serveur.
- [ ] Réactiver un serveur désactivé.
- [ ] Gérer les erreurs de communication avec la socket HAProxy.

### C. API à développer

| Méthode | Route | Fonction |
|---|---|---|
| GET | `/api/dashboard` | Récupérer les données du tableau de bord |
| GET | `/api/runtime/servers` | Récupérer l'état des serveurs |
| POST | `/api/runtime/servers/{id}/disable` | Désactiver un serveur |
| POST | `/api/runtime/servers/{id}/enable` | Réactiver un serveur |

Les formats des requêtes et réponses sont déjà définis dans le document **Normes de développement**.

### D. Fichiers concernés

- `views/dashboard.php`
- `public/assets/js/dashboard.js`
- `public/assets/css/dashboard.css`
- `app/Controllers/DashboardController.php`
- `app/Services/HAProxyRuntime.php`

### Résultat attendu

Le tableau de bord affiche les informations réelles de HAProxy et permet de retirer temporairement un serveur de la répartition du trafic, puis de le réactiver.

---

## 5. Tâches communes aux trois membres

Ces éléments sont développés une seule fois et réutilisés par les trois membres.

### À préparer ensemble avant le développement

- [x] Créer le dépôt GitHub (https://github.com/Johny-olivier/gestion-haproxy-trinome)
- [ ] Ajouter les trois membres comme collaborateurs (en attente des comptes de deux autres membres)
- [ ] Créer l'arborescence commune du projet.
- [ ] Mettre en place le système de routage PHP.
- [ ] Créer le système commun de réponses JSON.
- [ ] Créer la fonction JavaScript commune pour les appels API.
- [ ] Créer les styles et éléments communs de l'interface.
- [ ] Vérifier que les trois membres peuvent lancer le projet.

### Fichiers communs à créer

| Fichier | Rôle |
|---|---|
| `app/Core/Router.php` | Gérer les routes de l'application |
| `app/Core/Response.php` | Uniformiser les réponses JSON |
| `public/assets/js/api.js` | Centraliser les appels API avec `fetch()` |
| `public/assets/js/main.js` | Regrouper les fonctions JavaScript communes |
| `public/assets/css/main.css` | Regrouper les styles communs |
| `views/layouts/main.php` | Fournir la structure commune des pages |

**Important :** aucun membre ne doit recréer individuellement ces fonctionnalités dans son module.

---

## 6. Règles communes de collaboration

- [ ] Chaque membre travaille principalement sur les fichiers de son module.
- [ ] Les fichiers communs ne sont modifiés qu'après concertation.
- [ ] Le Johny est responsable des modifications persistantes de `haproxy.cfg`.
- [ ] Le Ny Antema est responsable des commandes `systemctl`.
- [ ] Le Miranto est responsable de la Runtime API.
- [ ] Chaque membre teste son module avant l'intégration.
- [ ] Les trois membres réalisent ensemble les tests finaux.

### Attention aux opérations sur les serveurs

| Opération | Signification |
|---|---|
| Désactiver un serveur | Le conserver dans la configuration, mais ne plus lui envoyer de nouvelles requêtes. |
| Réactiver un serveur | Lui permettre de recevoir à nouveau des requêtes. |
| Supprimer un serveur | Retirer sa déclaration de `haproxy.cfg`. |
| Arrêter HAProxy | Arrêter complètement le service de répartition de charge. |

La désactivation via Runtime API est temporaire. Elle peut être perdue après un rechargement ou un redémarrage.

---

## 7. Critères de réussite du projet

- [ ] Les trois pages de l'application fonctionnent.
- [ ] La liste des serveurs s'affiche correctement.
- [ ] L'ajout d'un serveur fonctionne.
- [ ] La modification d'un serveur fonctionne.
- [ ] La suppression d'un serveur fonctionne.
- [ ] La désactivation et la réactivation fonctionnent.
- [ ] L'état réel du service HAProxy s'affiche.
- [ ] Les actions Start, Stop, Restart et Reload fonctionnent.
- [ ] Les deux serveurs Web sont accessibles via HAProxy.
- [ ] Les requêtes sont réparties selon Round Robin.