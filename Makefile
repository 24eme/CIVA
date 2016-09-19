all: project/cache project/log project/config/app.yml project/config/databases.yml project/bin/config.inc project/web/civa_dev.php  .views/etablissements.json .views/mouvements.json .views/societe.json .views/compte.json .views/drm.json project/data/latex

project/cache:
	mkdir project/cache
	chmod g+sw,o+w project/cache

project/data/latex:
	mkdir project/data/latex
	chmod g+sw,o+w project/data/latex

project/log:
	mkdir project/log
	chmod g+sw,o+w project/log

project/config/app.yml:
	cp project/config/app.yml.example project/config/app.yml

project/config/databases.yml:
	cp project/config/databases.yml.example project/config/databases.yml
	mkdir -p .views

project/bin/config.inc:
	cp project/bin/config.example.inc project/bin/config.inc

project/web/civa_dev.php:
	cp project/web/civa_dev.php.example project/web/civa_dev.php

.views/etablissements.json: project/config/databases.yml project/plugins/acVinEtablissementPlugin/lib/model/views/etablissement.findByCvi.reduce.view.js project/plugins/acVinEtablissementPlugin/lib/model/views/etablissement.region.reduce.view.js project/plugins/acVinEtablissementPlugin/lib/model/views/etablissement.all.map.view.js project/plugins/acVinEtablissementPlugin/lib/model/views/etablissement.findByCvi.map.view.js project/plugins/acVinEtablissementPlugin/lib/model/views/etablissement.all.reduce.view.js project/plugins/acVinEtablissementPlugin/lib/model/views/etablissement.douane.reduce.view.js project/plugins/acVinEtablissementPlugin/lib/model/views/etablissement.region.map.view.js project/plugins/acVinEtablissementPlugin/lib/model/views/etablissement.douane.map.view.js
	perl bin/generate_views.pl project/config/databases.yml project/plugins/acVinEtablissementPlugin/lib/model/views/etablissement.findByCvi.reduce.view.js project/plugins/acVinEtablissementPlugin/lib/model/views/etablissement.region.reduce.view.js project/plugins/acVinEtablissementPlugin/lib/model/views/etablissement.all.map.view.js project/plugins/acVinEtablissementPlugin/lib/model/views/etablissement.findByCvi.map.view.js project/plugins/acVinEtablissementPlugin/lib/model/views/etablissement.all.reduce.view.js project/plugins/acVinEtablissementPlugin/lib/model/views/etablissement.douane.reduce.view.js project/plugins/acVinEtablissementPlugin/lib/model/views/etablissement.region.map.view.js project/plugins/acVinEtablissementPlugin/lib/model/views/etablissement.douane.map.view.js > $@ || rm >@

.views/mouvements.json: project/config/databases.yml project/plugins/acVinDocumentPlugin/lib/Mouvement/views/mouvement.consultation.map.view.js project/plugins/acVinDocumentPlugin/lib/Mouvement/views/mouvement.consultation.reduce.view.js
	perl bin/generate_views.pl project/config/databases.yml project/plugins/acVinDocumentPlugin/lib/Mouvement/views/mouvement.consultation.map.view.js project/plugins/acVinDocumentPlugin/lib/Mouvement/views/mouvement.consultation.reduce.view.js > $@ || rm >@

.views/societe.json: project/config/databases.yml project/plugins/acVinSocietePlugin/lib/model/views/societe.all.reduce.view.js project/plugins/acVinSocietePlugin/lib/model/views/societe.all.map.view.js project/plugins/acVinSocietePlugin/lib/model/views/societe.export.map.view.js
	perl bin/generate_views.pl project/config/databases.yml project/plugins/acVinSocietePlugin/lib/model/views/societe.all.reduce.view.js project/plugins/acVinSocietePlugin/lib/model/views/societe.all.map.view.js project/plugins/acVinSocietePlugin/lib/model/views/societe.export.map.view.js > $@ || rm >@

.views/compte.json: project/config/databases.yml project/plugins/acVinComptePlugin/lib/model/views/compte.all.reduce.view.js project/plugins/acVinComptePlugin/lib/model/views/compte.all.map.view.js
	perl bin/generate_views.pl project/config/databases.yml project/plugins/acVinComptePlugin/lib/model/views/compte.all.reduce.view.js project/plugins/acVinComptePlugin/lib/model/views/compte.all.map.view.js > $@ || rm >@

.views/drm.json: project/config/databases.yml project/plugins/acVinDRMPlugin/lib/model/DRM/views/drm.stocks.map.view.js project/plugins/acVinDRMPlugin/lib/model/DRM/views/drm.all.reduce.view.js project/plugins/acVinDRMPlugin/lib/model/DRM/views/drm.all.map.view.js project/plugins/acVinDRMPlugin/lib/model/DRM/views/drm.produits.map.view.js project/plugins/acVinDRMPlugin/lib/model/DRM/views/drm.stocks.reduce.view.js project/plugins/acVinDRMPlugin/lib/model/DRM/views/drm.derniere.reduce.view.js project/plugins/acVinDRMPlugin/lib/model/DRM/views/drm.produits.reduce.view.js project/plugins/acVinDRMPlugin/lib/model/DRM/views/drm.derniere.map.view.js
	perl bin/generate_views.pl project/config/databases.yml project/plugins/acVinDRMPlugin/lib/model/DRM/views/drm.stocks.map.view.js project/plugins/acVinDRMPlugin/lib/model/DRM/views/drm.all.reduce.view.js project/plugins/acVinDRMPlugin/lib/model/DRM/views/drm.all.map.view.js project/plugins/acVinDRMPlugin/lib/model/DRM/views/drm.produits.map.view.js project/plugins/acVinDRMPlugin/lib/model/DRM/views/drm.stocks.reduce.view.js project/plugins/acVinDRMPlugin/lib/model/DRM/views/drm.derniere.reduce.view.js project/plugins/acVinDRMPlugin/lib/model/DRM/views/drm.produits.reduce.view.js project/plugins/acVinDRMPlugin/lib/model/DRM/views/drm.derniere.map.view.js > $@   rm >@

clean:
	rm -f .views/*
