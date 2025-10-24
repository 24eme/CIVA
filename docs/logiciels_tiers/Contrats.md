# Création des contrats CIVA via un fichier

Ce document permet de décrire le fonctionnement pour importer des contrats en masse sur la plateforme du CIVA (https://declaration.vinsalsace.pro/).

## Format du fichier

Le fichier pour importer les contrat doit être au format `CSV` et encodé en `UTF-8`.

Les différentes colonnes du fichier CSV et les valeurs attendues sont décrites dans ce tableau.

| Nom du champs                              | Type / Format        | Exemple / Liste                                   | Lecture / Écriture | Commentaire                                                                                                               |
|:-------------------------------------------|:---------------------|:--------------------------------------------------|:-------------------|:--------------------------------------------------------------------------------------------------------------------------|
| Type                                       | Constante            | CONTRAT                                           | Écriture           | Doit toujours contenir CONTRAT                                                                                            |
| Campagne                                   | YYYY-YYYY            | 2025-2026                                         | Écriture           | Campagne du contrat ou de début de contrat pour les contrats pluriannuels cadres                                          |
| Type de contrat                            | Constantes           | [ANNUEL,PLURIANNUEL_CADRE,PLURIANNUEL_APPLICATION]  | Écriture           |                                                                                                                           |
| Durée du contrat pluriannuel               | Nombre entier        | [3,10]                                                 | Écriture           | En nombre d'année, à indiquer uniquement pour les contrats PLURIANNUEL_CADRE                                              |
| Numéro interne du contrat                  | Identifiant          | 123456789                                         | Écriture           | Cet identifiant est l'identifiant interne du contrat dans votre système.                                                                                   |
| Numéro du contrat cadre référent           | Identifiant          | 987654321                                         | Écriture           | Uniquement pour les PLURIANNUEL_APPLICATION. Au choix, il est soit le Numéro interne, l'Identifiant CIVA ou le numéro visa du contrat cadre                                                          |
| Type de vente                              | Constantes           | [VIN_VRAC,VIN_BOUTEILLE,RAISIN,MOUT]                | Écriture           |                                                                                                                           |
| Acheteur CVI                               | Identifiant          | 7523700800                                        | Écriture           | Numéro CVI ou Identifiant CIVA                                                                                            |
| Acheteur Nom                               | Texte Simple         | NEGOCE NOM                                        | Écriture           |                                                                                                                           |
| Acheteur Assujeti TVA                      | Booléeen             | [0,1,NON,OUI]                                            | Écriture           |                                                                                                                           |
| Vendeur CVI                                | Identifiant          | 7523700100                                        | Écriture           | Numéro CVI ou Identifiant CIVA                                                                                            |
| Vendeur Nom                                | Texte Simple         | PRODUCTEUR NOM                                    | Écriture           |                                                                                                                           |
| Vendeur Assujeti TVA                       | Booléeen             | [0,1,NON,OUI]                                            | Écriture           |                                                                                                                           |
| Courtier Identifiant                       | Identifiant          | 810720557                                         | Écriture           | SIREN, SIRET, N°Carte Pro ou Identifiant CIVA                                                                             |
| Courtier Nom                               | Texte Simple         | Nom du courtier                                   | Écriture           |                                                                                                                           |
| Certification                              | Texte Simple         | AOC                                               | Écriture           |                                                                                                                           |
| Genre                                      | Texte Simple         |                                                   | Écriture           |                                                                                                                           |
| Appellation                                | Texte Simple         | Alsace Grand Cru                                  | Écriture           |                                                                                                                           |
| Mention                                    | Texte Simple         |                                                   | Écriture           |                                                                                                                           |
| Lieu                                       | Texte Simple         | Kirchberg de Barr                                 | Écriture           |                                                                                                                           |
| Couleur                                    | Texte Simple         | Blanc                                             | Écriture           |                                                                                                                           |
| Cepage                                     | Texte Simple         | Riesling                                          | Écriture           |                                                                                                                           |
| Code INAO                                  | Texte Simple         | 1B021S 4                                          | Écriture           |                                                                                                                           |
| Libelle Produit                            | Texte Simple         | AOC Alsace Grand Cru Kirchberg de Barr Riesling   | Écriture           |                                                                                                                           |
| Label                                      | Constantes           | [BIO,HVE3]                                          | Écriture           |                                                                                                                           |
| VT/SGN                                     | Constantes           | [VT,SGN]                                            | Écriture           |                                                                                                                           |
| Dénomination                               | Texte Simple         | Vieille vigne                                     | Écriture           |                                                                                                                           |
| Millésime                                  | Année au format YYYY | 2025                                              | Écriture           | Pas utilisé pour les contrats PLURIANNUEL_CADRE                                                                           |
| Quantite                                   | Nombre               | 200.7                                             | Écriture           |                                                                                                                           |
| Quantite type                              | Constantes           | [ARES,HL]                                           | Écriture           |                                                                                                                           |
| Prix Unitaire                              | Nombre               | 24.52                                             | Écriture           |                                                                                                                           |
| Unite prix                                 | Constantes           | [EUR_HL,EUR_KG,EUR_HA,EUR_BOUTEILLE]                        | Écriture           |                                                                                                                           |
| Frais annexes vendeur                      | Texte Multiligne     | 5.00€ HT/hl de frais de courtage                  | Écriture           | Non utilisé pour les contrats PLURIANNUEL_APPLICATION car repris automatiquement                                          |
| Primes diverses acheteur                   | Texte Multiligne     | 5.00€ HT/Hl d'apport global                       | Écriture           | Non utilisé pour les contrats PLURIANNUEL_APPLICATION car repris automatiquement                                          |
| Clause réserve propriété                   | Booléeen             | [0,1,NON,OUI]                                            | Écriture           | Non utilisé pour les contrats PLURIANNUEL_APPLICATION car repris automatiquement                                          |
| Délai paiement                             | Texte Simple         | Délai légal : 30 jours après la date de livraison | Écriture           | Non utilisé pour les contrats PLURIANNUEL_APPLICATION car repris automatiquement                                          |
| Clause de résiliation                      | Texte Multiligne     |                                                   | Écriture           | Non utilisé pour les contrats PLURIANNUEL_APPLICATION car repris automatiquement                                          |
| Mandat facturation                         | Booléeen             | [0,1,NON,OUI]                                            | Écriture           | Non utilisé pour les contrats PLURIANNUEL_APPLICATION car repris automatiquement                                          |
| Critères et modalités d’évolution des prix | Texte Multiligne     |                                                   | Écriture           | Uniquement pour les contrats PLURIANNUEL_CADRE                                                                            |
| Suivi qualitatif                           | Booléeen             | [0,1,NON,OUI]                                            | Écriture           | Uniquement pour les contrats VIN_VRAC et non utilisé pour les contrats PLURIANNUEL_APPLICATION car repris automatiquement |
| Délai maximum de retiraison                | Texte Simple         |                                                   | Écriture           | Uniquement pour les contrats MOUT  et non utilisé pour les contrats PLURIANNUEL_APPLICATION car repris automatiquement    |
| Autres clauses particulières               | Texte Multiligne     |                                                   | Écriture           | Pas utilisé pour les contrats PLURIANNUEL_APPLICATION car repris automatiquement                                          |
| Créateur                                   | Constantes / Identifiant  |                          | Lecture seule      | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Date de saisie                             | Date au format Y-m-d |                                                   | Lecture seule      | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Date de signature vendeur                  | Date au format Y-m-d |                                                   | Lecture seule      | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Date de signature acheteur                 | Date au format Y-m-d |                                                   | Lecture seule      | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Date de signature courtier                 | Date au format Y-m-d |                                                   | Lecture seule      | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Date de validation                         | Date au format Y-m-d |                                                   | Lecture seule      | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Date de cloture                            | Date au format Y-m-d |                                                   | Lecture seule      | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Statut                                     | Constantes           |                                                   | Lecture seule      | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Vendeur Identifiant CIVA                   | Identifiant          |                                                   | Lecture seule      | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Acheteur Identifiant CIVA                  | Identifiant          |                                                   | Lecture seule      | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Courtier Identifiant CIVA                  | Identifiant          |                                                   | Lecture seule      | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Identifiant CIVA                           | Identifiant          |                                                   | Lecture seule      | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |
| Numéro de visa CIVA                        | Identifiant          |                                                   | Lecture seule      | Cette colonne n'a pas besoin d'être présente pour l'import                                                                |

> [!TIP]
> La reconnaissance du produit est réalisée via l'une de ces 3 méthodes :
> - Via les colonnes Certification, Genre, Appellation, Mention, Lieu, Couleur et Cépage
> - Via le code INAO du produit
> - Via le libellé produit
>
> Il s'agit des même méthodes de reconnaissance que pour l'import de données des récoltes et des DRM basé sur le [catalogue produit](catalogue_produits.csv)

> [!WARNING]
> Chaque ligne du CSV représente un produit d'un contrat, il peut donc avoir plusieurs lignes pour un même contrat, les valeurs des colonnes relative au contrat devront donc être répétées dans chacune des lignes


> [!NOTE]
> Seul les colonnes en écriture sont à remplir.
>
> Les colonnes qui sont en lecture seule seront remplies par le CIVA pour une restitution des donnes des contrats via un export sur le même format.

## Import des contrats pluriannuel cadre et annuel classique

Le fichier CSV pourra être deposé via un formulaire sur la page d'accueil de l'espace contrat de la plateforme du CIVA (https://declaration.vinsalsace.pro/).

Sur cette interface il sera aussi possible d'importer un fichier PDF contenant les annexes du contrat qui sera joint à chacun des contrats importés.

Une fois les vérifications de données effectuées, les contrats seront créés comme des projets qu'il faudra ensuite aller valider et envoyer au vendeur un par un.

[Voir un exemple de CSV de contrats pluriannuel cadre](exemple_contrats_pluriannuel_cadre.csv)

## Générer les contrats pluriannuel d'application de la nouvelle campagne

Comme pour l'import des contrats pluriannuel cadre le fichier csv contenant les contrats pluriannuel d'application à générer pourra être deposé via un formulaire sur la page d'accueil de l'espace contrat de la plateforme du CIVA (https://declaration.vinsalsace.pro/).

Le format reste le même mais il n'y a pas besoin de resaisir toutes les colonnes.

Une fois déposé et vérifié, les contrats d'application seront générés et automatiquement envoyé au vendeur par mail pour validation.

> [!NOTE]
> Il sera possible de télécharger depuis l'espace contrat de la plateforme du CIVA un export de ces nouveaux contrats d'application à générer pour la nouvelle campagne au même format CSV. Ainsi ce fichier pourra servir de base pour être compléter (produits, surface, prix, etc ...) avant d'être déposé sur la plateforme pour import.

[Voir un exemple de CSV de contrats pluriannuel d'application](exemple_contrats_pluriannuel_application.csv)
