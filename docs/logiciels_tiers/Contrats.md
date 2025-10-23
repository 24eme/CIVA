# Import des contrats CIVA via un fichier

Ce document permet de décrire le fonctionnement pour importer des contrats en masse sur la plateforme du CIVA (https://declaration.vinsalsace.pro/).

## Format du fichier

Le fichier pour importer les contrat doit être au format `CSV` et encodé en `UTF-8`.

Les différentes colonnes du fichier CSV et les valeurs attendues sont décrites dans ce tableau.

| Nom du champs                              | Type / Format        | Exemple / Liste                                   | Lecture / Ecriture | Commentaire                                                                                                               |
|:-------------------------------------------|:---------------------|:--------------------------------------------------|:-------------------|:--------------------------------------------------------------------------------------------------------------------------|
| Type                                       | Constantes           | CONTRAT                                           | Lecture / Ecriture |                                                                                                                           |
| Campagne                                   | YYYY-YYYY            | 2025-2026                                         | Lecture / Ecriture | Campagne du contrat ou de début de contrat pour les contrats pluriannuels cadres                                          |
| Type de contrat                            | Constantes           | ANNUEL,PLURIANNUEL_CADRE,PLURIANNUEL_APPLICATION  | Lecture / Ecriture |                                                                                                                           |
| Durée du contrat pluriannuel               | Nombre entier        | 3                                                 | Lecture / Ecriture | En nombre d'année, à indiquer uniquement pour les contrats cadres                                                         |
| Numéro interne du contrat                  | Identifiant          | 123456789                                         | Lecture / Ecriture | Numéro interne libre devant être unique                                                                                   |
| Numéro du contrat cadre référent           | Identifiant          | 987654321                                         | Lecture / Ecriture | Numéro interne, Identifiant CIVA ou numéro visa du contrat cadre                                                          |
| Type de vente                              | Constantes           | VIN_VRAC,VIN_BOUTEILLE,RAISIN,MOUT                | Lecture / Ecriture |                                                                                                                           |
| Acheteur CVI                               | Identifiant          | 7523700800                                        | Lecture / Ecriture | Numéro CVI ou Identifiant CIVA                                                                                            |
| Acheteur Nom                               | Texte Simple         | NEGOCE NOM                                        | Lecture / Ecriture |                                                                                                                           |
| Acheteur Assujeti TVA                      | Booléeen             | 0 ou 1                                            | Lecture / Ecriture |                                                                                                                           |
| Vendeur CVI                                | Identifiant          | 7523700100                                        | Lecture / Ecriture | Numéro CVI ou Identifiant CIVA                                                                                            |
| Vendeur Nom                                | Texte Simple         | PRODUCTEUR NOM                                    | Lecture / Ecriture |                                                                                                                           |
| Vendeur Assujeti TVA                       | Booléeen             | 0 ou 1                                            | Lecture / Ecriture |                                                                                                                           |
| Courtier Identifiant                       | Identifiant          | 810720557                                         | Lecture / Ecriture | SIREN, SIRET, N°Carte Pro ou Identifiant CIVA                                                                             |
| Courtier Nom                               | Texte Simple         | Nom du courtier                                   | Lecture / Ecriture |                                                                                                                           |
| Certification                              | Texte Simple         | AOC                                               | Lecture / Ecriture |                                                                                                                           |
| Genre                                      | Texte Simple         |                                                   | Lecture / Ecriture |                                                                                                                           |
| Appellation                                | Texte Simple         | Alsace Grand Cru                                  | Lecture / Ecriture |                                                                                                                           |
| Mention                                    | Texte Simple         |                                                   | Lecture / Ecriture |                                                                                                                           |
| Lieu                                       | Texte Simple         | Kirchberg de Barr                                 | Lecture / Ecriture |                                                                                                                           |
| Couleur                                    | Texte Simple         | Blanc                                             | Lecture / Ecriture |                                                                                                                           |
| Cepage                                     | Texte Simple         | Riesling                                          | Lecture / Ecriture |                                                                                                                           |
| Code INAO                                  | Texte Simple         | 1B021S 4                                          | Lecture / Ecriture |                                                                                                                           |
| Libelle Produit                            | Texte Simple         | AOC Alsace Grand Cru Kirchberg de Barr Riesling   | Lecture / Ecriture |                                                                                                                           |
| Mention                                    | Constantes           | BIO,HVE                                           | Lecture / Ecriture |                                                                                                                           |
| VT/SGN                                     | Constantes           | VT,SGN                                            | Lecture / Ecriture |                                                                                                                           |
| Dénomination                               | Texte Simple         | Vieille vigne                                     | Lecture / Ecriture |                                                                                                                           |
| Millésime                                  | Année au format YYYY | 2025                                              | Lecture / Ecriture |                                                                                                                           |
| Quantite                                   | Nombre               | 200.7                                             | Lecture / Ecriture |                                                                                                                           |
| Quantite type                              | Constantes           | ares,hl                                           | Lecture / Ecriture |                                                                                                                           |
| Prix Unitaire                              | Nombre               | 24.52                                             | Lecture / Ecriture |                                                                                                                           |
| Unite prix                                 | Constantes           | €/hl,€/kg,€/ha,€/bouteille                        | Lecture / Ecriture |                                                                                                                           |
| Frais annexes vendeur                      | Texte Multiligne     | 5.00€ HT/hl de frais de courtage                  | Lecture / Ecriture | Pas utilisé pour les contrats PLURIANNUEL_APPLICATION car repris automatiquement                                          |
| Primes diverses acheteur                   | Texte Multiligne     | 5.00€ HT/Hl d'apport global                       | Lecture / Ecriture | Pas utilisé pour les contrats PLURIANNUEL_APPLICATION car repris automatiquement                                          |
| Clause réserve propriété                   | Booléeen             | 0 ou 1                                            | Lecture / Ecriture | Pas utilisé pour les contrats PLURIANNUEL_APPLICATION car repris automatiquement                                          |
| Délai paiement                             | Texte Simple         | Délai légal : 30 jours après la date de livraison | Lecture / Ecriture | Pas utilisé pour les contrats PLURIANNUEL_APPLICATION car repris automatiquement                                          |
| Clause de résiliation                      | Texte Multiligne     | Voir dans les annexes                             | Lecture / Ecriture | Pas utilisé pour les contrats PLURIANNUEL_APPLICATION car repris automatiquement                                          |
| Mandat facturation                         | Booléeen             | 0 ou 1                                            | Lecture / Ecriture | Pas utilisé pour les contrats PLURIANNUEL_APPLICATION car repris automatiquement                                          |
| Critères et modalités d’évolution des prix | Texte Multiligne     | Voir dans les annexes                             | Lecture / Ecriture | Uniquement pour les contrats PLURIANNUEL_CADRE                                                                            |
| Suivi qualitatif                           | Booléeen             | 0 ou 1                                            | Lecture / Ecriture | Uniquement pour les contrats VIN_VRAC et non utilisé pour les contrats PLURIANNUEL_APPLICATION car repris automatiquement |
| Délai maximum de retiraison                | Texte Simple         | Voir dans les annexes                             | Lecture / Ecriture | Uniquement pour les contrats MOUT  et non utilisé pour les contrats PLURIANNUEL_APPLICATION car repris automatiquement    |
| Autres clauses particulières               | Texte Multiligne     | Voir dans les annexes                             | Lecture / Ecriture | Pas utilisé pour les contrats PLURIANNUEL_APPLICATION car repris automatiquement                                          |
| Créateur                                   | Constantes           | ACHETEUR,VENDEUR,COURTIER                         | Lecture            | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Date de saisie                             | Date au format Y-m-d |                                                   | Lecture            | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Date de signature vendeur                  | Date au format Y-m-d |                                                   | Lecture            | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Date de signature acheteur                 | Date au format Y-m-d |                                                   | Lecture            | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Date de signature courtier                 | Date au format Y-m-d |                                                   | Lecture            | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Date de validation                         | Date au format Y-m-d |                                                   | Lecture            | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Date de cloture                            | Date au format Y-m-d |                                                   | Lecture            | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Statut                                     | Constantes           |                                                   | Lecture            | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Vendeur Identifiant CIVA                   | Identifiant          |                                                   | Lecture            | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Acheteur Identifiant CIVA                  | Identifiant          |                                                   | Lecture            | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Courtier Identifiant CIVA                  | Identifiant          |                                                   | Lecture            | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Identifiant CIVA                           | Identifiant          |                                                   | Lecture            | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Numéro de visa CIVA                        | Identifiant          |                                                   | Lecture            | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |

> [!TIP]
> La reconnaissance du produit est réalisé via plusieurs méthode :
> - Via les colonnes Certification, Genre, Appellation, Mention, Lieu, Couleur et Cépage
> - Via le code INAO du produit
> - Via le libellé produit
>
> Il s'agit des même méthodes de reconnaissance que pour l'import de données des récoltes et des DRM

> [!NOTE]
> Seul les colonnes en écriture sont à remplir.
>
> Les colonnes qui sont uniquement en lecture seront remplies par le CIVA pour une restitution des donnes des contrats via un export sur le même format.

## Import des contrats pluriannuel cadre et annuel classique

Le fichier CSV pourra être deposé via un formulaire sur la page d'accueil de l'espace contrat de la plateforme du CIVA (https://declaration.vinsalsace.pro/).

Sur cette interface il sera aussi possible d'importer un fichier PDF contenant les annexes du contrat qui sera joint à chacun des contrats importés.

Une fois les vérifications de données effectuées, les contrats seront créés comme des projets qu'il faudra ensuite aller valider et envoyer au vendeur un par un.

## Générer les contrats pluriannuel d'application de la nouvelle campagne

Comme pour l'import des contrats pluriannuel cadre le fichier csv contenant les contrats pluriannuel d'application à générer pourra être deposé via un formulaire sur la page d'accueil de l'espace contrat de la plateforme du CIVA (https://declaration.vinsalsace.pro/).

Le format reste le même mais il n'y a pas besoin de resaisir toutes les colonnes.

Une fois déposé et vérifié, les contrats d'application seront générés et automatiquement envoyé au vendeur par mail pour validation.

> [!NOTE]
> Il sera possible de télécharger depuis l'espace contrat de la plateforme du CIVA un export ces nouveaux contrats d'application à générer pour la nouvelle campagne, afin de pouvoir le compléter (produits, surface, prix, etc ...) puis le déposer.
