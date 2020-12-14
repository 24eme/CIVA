Maintenance
===========

Tiers
-----

### Export des modifications de tiers

 > php symfony export:etablissements-modifications

### Mise à jour des tiers depuis DB2

Concaténation et conversion en utf8 des fichiers db2 des actifs et cloturés  

 > bash bin/import_tiers.sh /path/to/DB2_TIERS_ACTIF /path/to/DB2_TIERS_CLOTURE

Transformation du fichier db2 en un csv contenant les sociétés, établissements et comptes

 > php symfony tiers:db2-csv data/import/Tiers/Tiers-last > /tmp/tiers.csv

Import du csv

 > php symfony societe:import-csv /tmp/tiers.csv

 > php symfony etablissement:import-csv /tmp/tiers.csv

 > php symfony compte:import-csv /tmp/tiers.csv

Migration de CVI :

 > php symfony migration:cvi ancien_cvi nouveau_cvi

 > php symfony migration:cvi ancien_cvi nouveau_cvi true #Pour conserver le mot de passe

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

### Générer les DRs automatiques

php symfony DR:creationFromAcheteur --year=[campagne] [--dryrun=true]

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

### Mise à jour des lieux de stockages

/!\ Le fichier tableur envoyé par les douanes nécessitent un retravaille

 > php symfony import:LieuxStockages [chemin vers le csv des lieux de stockages]

### Déclarants et envoi du mail d'ouverture

Obtenir la liste des déclarants

 > bash bin/send_mail_ouverture_ds.sh [periode] --dryrun=true

Envoi du mail d'ouverture

 > bash bin/send_mail_ouverture_ds.sh [periode]
