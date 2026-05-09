# Amélioration du Système d'Objectifs IMC - Résumé des Modifications

## Vue d'ensemble
Cette amélioration permet aux utilisateurs de sélectionner une **catégorie IMC cible** spécifique lorsqu'ils choisissent l'objectif "Atteindre un IMC idéal". Le système propose ensuite des régimes et activités sportives adaptées en comparant l'IMC actuel avec l'IMC cible.

---

## 📊 Modifications Apportées

### 1. **Base de Données** (`base.sql`)

#### Nouvelles Tables :

**`categories_imc`** - Catégories d'IMC pour la sélection de l'objectif
```sql
CREATE TABLE categories_imc (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100),
    imc_min FLOAT,
    imc_max FLOAT,
    description TEXT,
    ordre INT
);
```

Catégories incluses :
- Maigreur (0-18.4)
- Normal (18.5-24.9)
- Surpoids (25-29.9)
- Obésité légère (30-34.9)
- Obésité modérée (35-39.9)
- Obésité sévère (40+)

**`activites_objectifs`** - Relation entre activités sportives et objectifs
```sql
CREATE TABLE activites_objectifs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    activite_id INT,
    objectif_id INT,
    niveau_priorite INT COMMENT '1=haute, 2=moyenne, 3=basse',
    FOREIGN KEY (activite_id) REFERENCES activites_sportives(id),
    FOREIGN KEY (objectif_id) REFERENCES objectifs(id)
);
```

---

### 2. **Modèles** (`app/Models/`)

#### Nouveau modèle : `IMCCategoryModel.php`
Gère les catégories IMC avec les méthodes :
- `allOrdered()` - Récupère toutes les catégories ordonnées
- `findByIMC(float $imc)` - Trouve la catégorie correspondant à une valeur IMC
- `getById(int $id)` - Récupère une catégorie par son ID

#### Nouveau modèle : `ActiviteObjectifModel.php`
Gère la relation entre activités et objectifs :
- `getActivitesByObjectif(int $objectifId)` - Activités pour un objectif donné
- `getActivitesByIMCComparison(float $userIMC, float $targetIMC)` - Activités basées sur la comparaison IMC

#### Modifications : `RegimeModel.php`
Nouvelles méthodes :
- `getSuggestedByIMCComparison(float $userIMC, float $targetIMC)` - Régimes basés sur comparaison IMC
- `determineObjectifFromIMCComparison()` - Détermine l'objectif selon la comparaison

---

### 3. **Contrôleurs** (`app/Controllers/`)

#### Modifications : `Dashboard.php`

**Imports ajoutés :**
```php
use App\Models\IMCCategoryModel;
use App\Models\ActiviteObjectifModel;
```

**Nouvelle méthode :**
```php
public function updateIMCTarget()
```
- Traite la sélection d'une catégorie IMC cible
- Stocke les données en session (imc_target, imc_target_category_id, imc_target_category_name)
- Réinitialise l'objectif à "equilibre"

**Méthode modifiée : `index()`**
- Ajoute les catégories IMC et la catégorie IMC actuelle de l'utilisateur aux données de la vue

**Méthode modifiée : `regimes()`**
- Détecte si l'utilisateur a une catégorie IMC cible sélectionnée
- Utilise `getSuggestedByIMCComparison()` pour obtenir les régimes adaptés
- Récupère les activités sportives recommandées via `ActiviteObjectifModel`
- Transmet les informations IMC comparatives à la vue

---

### 4. **Routes** (`app/Config/Routes.php`)

Nouvelle route ajoutée :
```php
$routes->post('dashboard/imc-target', 'Dashboard::updateIMCTarget');
```

---

### 5. **Vues** 

#### Modification : `app/Views/dashboard_user.php`

**Changements :**
- Le bouton "IMC idéal" est maintenant un bouton d'action (au lieu de soumission de formulaire)
- Affiche dynamiquement les catégories IMC disponibles au clic
- Interface de sélection responsive en grille 2 colonnes
- Affiche le nom de la catégorie sélectionnée

**JavaScript ajouté :**
```javascript
function toggleIMCCategories()  // Bascule l'affichage des catégories
```

#### Modification : `app/Views/regimes_sugges.php`

**Nouvelles sections :**

1. **Comparaison IMC** (si IMC cible sélectionnée)
   - Affiche IMC actuel vs IMC cible côte à côte
   - Message personnalisé selon la différence IMC
   - Calcul approximatif du poids à perdre/gagner

2. **Logique de messages personnalisés :**
   - Si différence < 1 : "Excellent, maintenez votre poids"
   - Si IMC utilisateur < IMC cible : "Prise de poids recommandée"
   - Si IMC utilisateur > IMC cible : "Perte de poids recommandée"

3. **Activités sportives recommandées**
   - Liste des activités adaptées à l'objectif
   - Affichage des calories brûlées par heure
   - Priorité visuelle pour l'activité la plus recommandée
   - Conseil d'utilisation

---

## 🔄 Flux d'Utilisation

### Scénario 1 : Sélection d'une catégorie IMC cible

1. Utilisateur accède au Dashboard
2. Clique sur le bouton "IMC idéal"
3. Les 6 catégories IMC s'affichent dynamiquement
4. Sélectionne une catégorie (ex: "Normal")
5. Le système calcule l'IMC cible (moyenne de 18.5-24.9 = 21.7)
6. Redirection vers Dashboard avec confirmation
7. En session : `imc_target=21.7`, `imc_target_category_name="Normal"`

### Scénario 2 : Affichage des régimes adaptés

1. Utilisateur accède à "Régimes suggérés"
2. Le système compare :
   - IMC utilisateur (ex: 29.3)
   - IMC cible (ex: 21.7)
3. Résultat :
   - Objectif déterminé : "reduction" (perte de poids)
   - Régimes pour réduction affichés
   - Activités pour réduction affichées
   - Message : "Vous devez perdre environ 3.8 kg"

### Scénario 3 : Utilisation traditionnelle (sans catégorie IMC)

1. Utilisateur sélectionne "Augmenter le poids" ou "Réduire le poids"
2. Le système fonctionne comme avant
3. Affiche les régimes et activités pour cet objectif
4. Pas de comparaison IMC ni de message personnalisé

---

## 📋 Logique de Sélection des Régimes et Activités

### Par comparaison IMC :

```
Si IMC_utilisateur < IMC_cible - 1  → Objectif: AUGMENTATION
                                      Régimes: prise de poids
                                      Sports: musculation (priorité 1)
                                              
Si IMC_utilisateur > IMC_cible + 1  → Objectif: REDUCTION
                                      Régimes: perte de poids
                                      Sports: cardio (priorité 1)
                                      
Si |IMC_utilisateur - IMC_cible| < 1 → Objectif: EQUILIBRE
                                        Régimes: maintien/équilibre
                                        Sports: yoga/activités variées
```

### Activités Recommandées :

**Pour Réduction (Perte de poids) :**
1. Course à pied (priorité haute)
2. Natation (priorité haute)
3. Vélo (priorité moyenne)

**Pour Augmentation (Prise de masse) :**
1. Musculation (priorité haute)
2. Vélo (priorité moyenne)
3. Yoga (priorité moyenne)

**Pour Équilibre (Maintien) :**
1. Yoga (priorité haute)
2. Course à pied, Natation, Vélo (priorité moyenne)
3. Musculation (priorité moyenne)

---

## 🔧 Implémentation Technique

### Stockage en Session
```php
session()->set('objectif_choisi', 'equilibre');
session()->set('imc_target', 21.7);
session()->set('imc_target_category_id', 2);
session()->set('imc_target_category_name', 'Normal');
```

### Transmission aux Vues
```php
'targetIMC' => $targetIMC,
'userIMC' => $userIMC,
'sports' => $sports,
'imcCategories' => $imcCategories,
```

---

## ✅ Points d'Intégration Vérifiés

- [x] Tables créées et données insérées dans `base.sql`
- [x] Modèles créés avec logique d'IMC et de comparaison
- [x] Contrôleur mis à jour avec nouvelle route et logique
- [x] Routage configuré
- [x] Vues améliorées avec UI/UX dynamique
- [x] JavaScript pour l'affichage dynamique des catégories

---

## 📝 Notes de Déploiement

1. **Mise à jour BD :** Exécuter `base.sql` pour créer les nouvelles tables et insérer les données
2. **Aucune migration CodeIgniter requise** - Modifications directes au SQL
3. **Compatibilité :** Fonctionne avec l'approche traditionnelle (objectifs simples)
4. **Données session :** Nettoyées automatiquement au logout

---

## 🚀 Prochaines Améliorations Possibles

- [ ] Historique des objectifs IMC sélectionnés
- [ ] Graphique de progression IMC dans le dashboard
- [ ] Suggestions d'ajustement automatique basées sur la progression
- [ ] Export de plans personnalisés (régime + activités)
- [ ] Notifications de proximité avec l'IMC cible
- [ ] Intégration avec wearables pour suivi temps réel
