h1. Déployer le projet symfony

h2. Faire un clone du projet (Le repository SVN est obsolète)

Depuis le répertoire /var/www :

<pre>
git clone git@gitorious.org:alsacerecolte/alsacerecolte.git declaration
ou
git clone http://git.gitorious.org/alsacerecolte/alsacerecolte.git declaration
</pre>

h2. Passer de GIT à SVN

Si vous avez récupéré le projet depuis le SVN, il est maintenant nécessaire de passer sous GIT. 

Le plus simple est de cloner le projet dans un nouveau dossier.

h2. Ajouter la conf apache pour ce projet

<pre>
sudo vim /etc/apache2/site-enables/0XX-civa-LOGIN

   <VirtualHost *:80>
      ServerName declaration.XXXX.vinsalsace.pro
      DocumentRoot "/var/www/declaration/web"
      DirectoryIndex index.php
      <Directory "/var/www/declaration/web">
        AllowOverride All
        Allow from All
      </Directory>
      Alias /sf "/var/www/declaration/lib/vendor/symfony/data/web/sf"
      <Directory "/var/www/declaration/lib/vendor/symfony/data/web/sf">
        AllowOverride All
        Allow from All
      </Directory>
    </VirtualHost>

</pre>

h2. Recharger apache

<pre>
sudo /etc/init.d/apache2 reload
</pre>

h2. Mettre ne place l'environnement symfony

* installer les librairies nécessaires

<pre>
sudo aptitude install php5-ldap php5-curl
</pre>

* Configuration de l'accès à la base de données

copier et modifier le fichier database.yml

<pre>
cp config/database.yml{.example,}
</pre>

* Configuration des paramètres de l'application

Copier et modifier le fichier app.yml

<pre>
cp config/app.yml{.example,}
</pre>

* Configuration des scripts 

Ce fichier indique aux scripts bash où trouver le couchdb

<pre>
cp bin/config.inc{.example,}
</pre>

* autoriser les logs et le caches

<pre>
sudo chown -R www-data log cache
sudo chmod -R g+sw log cache
</pre>

h2. Mettre en place un couchdb

Comme couchdb sous lenny est en version 0.8, il est préférable d'installer la version depuis squeeze. Un fois fait, un problème peut persister avec libjs-jquery. Dans ce cas, l'installer à la main.

Depuis une debian/lenny fraichement installée :

* Installer les packets lenny nécessaires :

<pre>
sudo aptitude install libcurl3 lksctp-tools wwwconfig-common erlang-base php5-cli
</pre>


* Ajouter le répository squeeze dans /etc/apt/source.list

<pre>
deb http://ftp.fr.debian.org/debian/ squeeze main contrib non-free
</pre>

* Installer couchdb

<pre>
sudo aptitude update
sudo aptitude install couchdb
</pre>

* Désactiver le répository squeeze en commentant la ligne dans /etc/apt/source.list

* Mettre à jour apt

<pre>
sudo aptitude update
</pre>

h2. Créer la base civa

Depuis l'interface d'admin :

http://declaration.XXXX.vinsalsace.pro:5984/_utils/

Créer une base appelée "civa"

h2. Charger les données

Un script d'import permet de mettre la base à zero et importer toutes les données :

<pre>
bash bin/import_all
</pre>

Pour plus d'info : [[Commandes_d'import]]

h2. Tester le fonctionnement de symfony

/!\ en production, l'application de déclaration sera servie en https

http://declaration.XXXX.vinsalsace.pro/compte