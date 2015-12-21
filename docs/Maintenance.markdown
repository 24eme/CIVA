Maintenance
===========

Tiers
-----

### Export des modifications de tiers

L'export des modifications permet au CIVA de mettre à jour leur base DB2

Elle survient la plupart du temps juste avant que le CIVA nous fournisse le fichier TIERS à importer

Le dernier numéro de séquence se trouve dans ce fichier :

 > cat data/export/tiers/tiers_modifications.num_sequence

Export des modifications depuis le dernier numéro de séquence :

 > php symfony export:tiers-modifications-csv [numero_sequence] # Pour tester
 
 > php symfony export:tiers-modifications-csv [numero_sequence] --flag_revision=true > /tmp/tiers-modifications.csv # Renvoi le csv et flague les révisions

Stocker le numéro de séquence courant pour exporter les modifications la prochaine fois :

 > curl -s -X GET "http://localhost:5984/civa_prod" | grep -Eo '"update_seq":[0-9]+,' | sed 's/"update_seq"://' | sed 's/,//' > data/export/tiers/tiers_modifications.num_sequence

### Désactivation des Tiers inactifs

La désactivation des tiers inactifs se fait depuis un fichier TIERS ne contenant que les tiers inactifs.

#### 1. Désactivation des Tiers inactifs

> php symfony tiers:desactivation path_to_fichier_db2

#### 2. Mise à jour des comptes

Il faut ensuite mettre à jour les comptes : se référer au chapitre "Mise à jour des comptes".

### Migration de CVI

/!\ La migration de CVI doit toujours être effectuer avant l'import du fichier TIERS

Le tableau que le CIVA est de la forme suivante : 

Ancien CVI  Nouveau CVI	 Nom         Prénom	 Commune    Récup. Histo DR	   Récup MDP si Cpt créé
6837400440	6837400011	 BRAUNEISEN  Eric	 TURCKHEIM	OUI	        	   OUI

Pour chacune des migrations, lancer la commande suivante :

 > php symfony compte:migration [cvi_actuelle] [nouveau_cvi] [nom_recoltant] [commune]

Juste pour comprendre, cette tâche effectue les actions suivantes :

1. Duplique le document 'REC-[CVI]' en 'REC-[NOUVEAU_CVI]' + mise à jour du nom et de la commune
2. Duplique le compte 'COMPTE-[CVI]' en 'COMPTE-[NOUVEAU_CVI]' + mise à jour des tiers du nom et de la commune
3. Crée un document lien symbolique avec le nouveau CVI de chacune des DRs du cvi actuel 

Si le champs "Récup. Histo DR" est à "NON", il faut supprimer dans couchdb toutes les DRs du nouveau_cvi.

Si le champs "Récup MDP" est à "NON" :
1. Rechercher le document COMPTE-[nouveau_cvi] sur couchdb
2. Changer le champ 'mot_de_passe' et lui attribuer la valeur '{TEXT}0000' où "0000" doit être remplacer par un nombre inventé.
3. Changer le champ 'statut' à 'NOUVEAU'

### Création et Mise à jour des tiers depuis DB2

Cette procédure est valable pour la création et la mise à jour des tiers Récolatant et Metteur en marché

Ainsi que la mise à jour (et pas la création) des acheteurs.

L'import se fait en 2 ou 3 étapes.

#### 1. Import des tiers

Le fichier n'a pas besoin d'être complet, ce qui permet des ajouts ponctuels.

 > php symfony import:Tiers path_to_fichier_db2

Ce n'est pas indispensable, mais l'usage est de stocker le fichier TIERS dans le projet : 

* Pour un fichier complet : data/import/Tiers/Tiers-YYYYMMDD
* Pour un fichier partiel : data/import/Tiers/maj/Tiers-maj-YYYYMMDD

**Logs de sortie**

 > INFO;CREATION;...

 > INFO;MODIFICATION;...

Rien de particulier à relever ce n'est qu'informatif.

#### 2. Mise à jour des comptes

Il faut ensuite mettre à jour les comptes : se référer au chapitre "Mise à jour des comptes".

#### 3. Flaguer le numéro de séquence 

Enfin dans le cas d'un import complet uniquement, il convient de stocker le numéro de séquence afin de pouvoir exporter les modifications pour la prochaine fois.

Stocker le numéro de séquence courant :

 > curl -s -X GET "http://localhost:5984/civa_prod" | grep -Eo '"update_seq":[0-9]+,' | sed 's/"update_seq"://' | sed 's/,//' > data/export/tiers/tiers_modifications.num_sequence

### Mise à jour des comptes

 > php symfony compte:update

**Logs de sortie**

 > INFO;Création du compte;...

 > INFO;Modification du compte;...

 > INFO;Tiers mis à jour;...

 > INFO;Le compte a été activé;...

 > INFO;Le compte a été désactivé;...

 > INFO;Inscrit ne possédant pas d'email;... #Ce n'est pas grave, Dominique a déjà été mis au courant le problème se resolvera humainement

Rien de particulier à relever ce n'est qu'informatif.

DR
--

### En général

#### Export PDF

Valable pour la création et la mise à jour.

Tache de génération des PDFs :

 > php symfony export:dr-pdf

Cette tache en executé toute les nuits grâce à un cron.

### Avant l'ouverture de la télédéclaration

#### Export email des télédéclarant (pour envoie email)

 > bash bin/export_email_teledeclarant [annee_recolte]

#### Export des non télédéclarant (pour envoie postal)

 > bash bin/export_recoltant_non_teledeclarant [annee_recolte]

### Juste après la fermeture de la télédéclaration

#### Changer la date de validation des DRs supérieur à celle voulu

Sur toute une campagne :

 > bash bin/dr_change_date.sh YYYY yyyy-mm-dd

Unitairement :

 > php symfony dr:changeDate id_doc yyyy-mm-dd

#### Egaliser la date de modification sur la date de validation

Sur toute une campagne :

 > bash bin/dr_egalise_date_modification.sh YYYY

Unitairement :

 > php symfony dr:date-modification id_doc

#### Remettre le compte CVI en utilisateurs (éditeur et validateur) pour les DRs en télédéclaration validé par COMPTE-admin-civa

Sur toute une campagne :

 > bash bin/dr_change_utilisateur_compte.sh YYYY COMPTE-admin-civa

Unitairement :

 > php symfony dr:changeUtilisateurCompte id_doc COMPTE-admin-civa

### Exports de fin de campagne

#### Export XML

##### Douane

 > php symfony export:dr-xml <campagne> Douane

Le fichier se génére dans data/export/dr/xml/DR-[campagne]-Douane.xml

##### CIVA

 > php symfony export:dr-xml <campagne> Civa

Le fichier se génére dans data/export/dr/xml/DR-[campagne]-Civa.xml

##### Export CSV des compléments d'usages industriels pour les Grands Crus

Dans les XML Douane et Civa ne figurent pas les colonnes totaux des Grands Crus, on perd donc les lies saisies. 

Cet export permet de compléter les usages industriels des Grands Crus dans les fichiers XML.

 > bash bin/export_drs_usages_industriels_grdcru_complement_xml_civa.sh [campagne]

##### Debugguer le XML

L'export XML Civa et Douane se fait dans la même classe ExportDRXml.class.php. 

Chacun des deux XML a des particularités et des aggrégas différents.

Voici une liste de leurs spécifités.

XML Global:

* Les colonnes qui figurent sont les colonnes détails et les colonnes de totaux Lieu/couleur
* Les Grands Crus et les Vins de table n'ont pas de colonne total
* La colonne rebêche figure dans la colonne Total dans une balise <colonneAss>

XML Douane:

* Les colonnes détails sont agrégés par NORMAL/VT/SGN
* Les colonnes de details des Crémants sont aggrégés par couleur.
* Si un seul cépage dans une appellation, pas de colonne détail que celle de total
* Les colonnes d'assemblage ne figure pas

XML CIVA:

* Toutes les colonnes détail même si plusieurs pour un même cépage.
* Si il n'y a qu'une seul détail pour un cépage ont met le volume revendiqué et les usages industriels du cépage

Pour débugguer, il existe une tache qui permet de sortir le XML d'une seule DR avec indentation du code XML et transformation du code douane en libellé produit.

> php symfony:export-xml-debug [doc_id] [Douane|Civa]

#### Statistiques de récolte

##### Récolte

 > php symfony dr:stats-recolte [campagne]

##### Par mairie

 > php symfony dr:stats-recolte-mairie [campagne]

#### Export des Ventes

##### Ventes de raisins

 > bash bin/export_drs_ventes_raisins.sh [campagne]

##### Ventes de moûts

 > bash bin/export_drs_ventes_mouts.sh [campagne]

#### Export des superficie de jeunes vignes

 > bash bin/export_drs_jeunes_vignes.sh [campagne]

### Générer les DRs automatiques

php symfony DR:creationFromAcheteur --year=[campagne] [--dry-run=true]

### Exporter csv des DR concernant un acheteur

php symfony export:dr-acheteur-csv [campagne] [cvi]

Contrat Vrac
------------

### Export Contrat Vrac

 > php symfony export:vrac --trace [folder_path*] [date_begin*] [date_end]

* folder_path* : obligatoire, emplacement des fichiers générés par l'export (ex.: /tmp/export)
* date_begin* : obligatoire, export des contrat à partir de date_begin (format: AAAA-MM-DD)
* date_end : optionnel, export des contrat jusqu'à date_end (format: AAAA-MM-DD)

Si l'option date_end n'est pas spécifiée, elle sera à égale à la date d'hier (date du jour - 1 jour).
date_begin, doit correspondre à la date du dernier export fait, ex.: 

si un export à été fait le 01/01/2014 alors le prochain export par exemple le 10/01/2014 doit ressembler à :

 > php symfony export:vrac --trace /tmp/export20140110 2014-01-01

Cette export comprendra tous les contrats du 01/01/2014 au 09/01/2014 (date du jour - 1 jour).

DS
--

### Mise à disposition des PDF

Valable pour la création et la mise à jour.

Tache de génération des PDFs :

 > php symfony export:ds-pdf

### Déclarants et envoi du mail d'ouverture

Obtenir la liste des déclarants

 > bash bin/send_mail_ouverture_ds.sh 201512 --dryrun=true

Envoi du mail d'ouverture

 > bash bin/send_mail_ouverture_ds.sh 201512