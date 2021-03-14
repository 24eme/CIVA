function(doc) {
    if (doc.type != "Vrac") {

        return;
    }

    var archive = doc.numero_contrat;
    if (doc.numero_archive) {
        archive = doc.numero_archive;
    }
    teledeclare = 1;
    if (doc.papier) {
        teledeclare = 0;
    }

    var prix_variable = "NON";
    if (doc.prix_variable) {
        prix_variable = "OUI";
    }

    var original = "OUI";
    if (doc.attente_original) {
          original = "NON"
    }

    var interne = "NON";
    if (doc.papier) {
        interne = "OUI";
    }

    var vendeurNom = "";
    if(doc.vendeur.intitule){ vendeurNom = doc.vendeur.intitule+" "; }
    vendeurNom = vendeurNom + doc.vendeur.raison_sociale;

    var acheteurNom = "";
    if(doc.acheteur.intitule){ acheteurNom = doc.acheteur.intitule+" "; }
    acheteurNom = acheteurNom + doc.acheteur.raison_sociale;
    
    var mercuriales = "M - Viticulteur vers Négoce";
	if (doc.vendeur_type == 'caves_cooperatives') {
		mercuriales = "C - Coopérative vers Négoce";
	}
	if (doc.vendeur_type == 'negociants') {
		mercuriales = "X - Négoce vers Négoce";
	}
	if (doc.acheteur_type == 'recoltants') {
		mercuriales = "V - Vigneron vers Vigneron";
	}
	if (doc.interne) {
		mercuriales = "I - Contrat interne";
	}

    for(certification in doc.declaration) {
        if (certification.match(/^certification/g)) {
            for(genre in doc.declaration[certification]) {
                if (genre.match(/^genre/g)) {
                    for(appellation in doc.declaration[certification][genre]) {
                        if (appellation.match(/^appellation/g)) {
                            var code_appellation = appellation.replace("appellation_","");
                            var libelle_appellation = doc.declaration[certification][genre][appellation].libelle;
                            for(mention in doc.declaration[certification][genre][appellation]) {
                                if (mention.match(/^mention/g)) {
                                    for(lieu in doc.declaration[certification][genre][appellation][mention]) {
                                        if (lieu.match(/^lieu/g)) {
                                            for(couleur in doc.declaration[certification][genre][appellation][mention][lieu]) {
                                                if (couleur.match(/^couleur/g)) {
                                                    for(cepage in doc.declaration[certification][genre][appellation][mention][lieu][couleur]) {
                                                        if (cepage.match(/^cepage/g)) {
                                                            var code_cepage = cepage.replace("cepage_","");
                                                            var libelle_cepage = doc.declaration[certification][genre][appellation][mention][lieu][couleur][cepage].libelle;
                                                            var numero_cepage = null;
                                                            for(detail in doc.declaration[certification][genre][appellation][mention][lieu][couleur][cepage].detail) {
                                                                var produit = doc.declaration[certification][genre][appellation][mention][lieu][couleur][cepage].detail[detail];
                                                                if (produit.actif) {
                                                                    if(doc.valide.date_validation){
                                                                        if(produit.vtsgn) {
                                                                            mention = produit.vtsgn;
                                                                        }
                                                                        var produitHash = "/declaration/certifications/"+certification+"/genres/"+genre+"/appellations/"+appellation+"/mentions/"+mention+"/lieux/"+lieu+"/couleurs/"+couleur+"/cepages/"+cepage;
                                                                        var produitLibelle = libelle_appellation + ' ' + libelle_cepage;
                                                                        
                                                                        var quantite = produit.volume_propose;

                                                                        if(produit.nb_bouteille) {
                                                                            quantite = produit.nb_bouteille;
                                                                        }
                                                                        
                                                                        var centilisation = 1;
                                                                        var prix_unitaire_hl = produit.prix_unitaire;
                                                                        if(produit.centilisation) {
                                                                            centilisation = produit.centilisation / 10000;
                                                                            prix_unitaire_hl = 10000 / produit.centilisation * produit.prix_unitaire;
                                                                            prix_unitaire_hl = Math.round(prix_unitaire_hl*100)/100;
                                                                        }
                                                                        
                                                                        var label = null;
                                                                        if(produit.label) {
                                                                            label = produit.label;
                                                                        }

                                                                        emit([teledeclare, doc.valide.date_saisie, doc._id], [doc.campagne, doc.valide.statut, doc._id, doc.numero_contrat, archive, doc.acheteur_identifiant, acheteurNom, doc.vendeur_identifiant, vendeurNom, doc.mandataire_identifiant,doc.mandataire.nom, null, null, doc.type_contrat, produitHash, produitLibelle, produit.volume_propose, produit.volume_enleve, prix_unitaire_hl, prix_unitaire_hl, prix_variable, interne, original, mercuriales, doc.valide.date_validation, doc.valide.date_cloture, doc.valide.date_saisie, produit.millesime, mercuriales, produit.denomination.replace(/,/g, ""), null, null, null, doc.cepage, libelle_cepage, label, quantite, produit.prix_unitaire, centilisation]);
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
