# Documentation GitFlow – EcoRide

Ce document décrit l’organisation du versionnement Git utilisée pour le projet **EcoRide**.  
Nous avons choisi GitHub avec une stratégie **GitFlow simplifiée** pour répondre aux exigences de l’ECF et garder un historique clair.

---

## 1. Branches principales

- **main** : contient uniquement les versions stables et validées du projet.  
- **dev** : branche de développement, utilisée pour intégrer les fonctionnalités avant validation.

---

## 2. Branches secondaires (features)

Chaque nouvelle fonctionnalité est développée dans une branche dédiée :

- Nom standard : `feature/<nom-fonction>`  
  - Exemple : `feature/crud-trajet`, `feature/security-server`

Ces branches sont mergées dans `dev` une fois la fonctionnalité terminée et testée.

---

## 3. Workflow de développement

1. **Créer une branche de fonctionnalité** :

```bash
git checkout dev
git pull origin dev
git checkout -b feature/ma-fonction
```

2. **Développer et commiter** :

```bash
git add .
git commit -m "feat(trajet): ajout de la création de trajets"
```

3. **Pousser la branche** :

```bash
git push origin feature/ma-fonction
```

4. **Merge dans dev** :

```bash
git checkout dev
git pull origin dev
git merge feature/ma-fonction
git push origin dev
```

5. **Merge final dans main** (avant soutenance / release) :

```bash
git checkout main
git pull origin main
git merge dev
git push origin main
```

---

## 4. Conventions de commits

Nous utilisons des **préfixes normalisés** pour clarifier chaque commit :

- `feat:` → ajout d’une nouvelle fonctionnalité  
- `fix:` → correction d’un bug  
- `chore:` → maintenance, configuration, nettoyage  
- `docs:` → documentation  
- `style:` → mise en forme (CSS, indentation) sans impact fonctionnel  
- `refactor:` → réécriture du code sans ajout de fonctionnalité  
- `test:` → ajout ou mise à jour de tests

Exemple :

```bash
git commit -m "feat(reservation): ajout du système de crédits"
git commit -m "fix(trajet): correction bug affichage liste"
git commit -m "docs: ajout procédure de déploiement"
```

---

## 5. Bonnes pratiques

- Toujours mettre à jour `dev` avant de créer une feature.  
- Faire des commits réguliers et atomiques (un commit = une modification claire).  
- Nommer les branches de manière explicite (`feature/`, `fix/`, `hotfix/`).  
- Ne jamais travailler directement sur `main`.  
- Supprimer les branches de fonctionnalités après merge pour garder le dépôt propre.

---

## 6. Schéma simplifié

```
main ──────────────●─────────────●───────
                   │             │
                   ▼             ▼
                 merge         merge
                   ▲             ▲
dev  ─────●───────●───────●─────●────────
            │       │       │
            ▼       ▼       ▼
   feature/A   feature/B   feature/C
```

---

## 7. Conclusion

Cette organisation permet :

- D’éviter les conflits en séparant les développements.  
- De sécuriser le code livré sur `main`.  
- De garantir un suivi clair grâce aux conventions de commit.  

Le choix d’un GitFlow simplifié est adapté au projet EcoRide car il reste compréhensible, structuré et conforme aux attentes de l’ECF.

En complément, le projet utilise **Composer** uniquement pour l’autoload PSR-4, ce qui garantit une organisation propre des classes lors du développement.