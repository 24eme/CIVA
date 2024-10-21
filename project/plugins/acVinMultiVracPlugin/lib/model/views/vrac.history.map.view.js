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
    vendeurNom = doc.vendeur.raison_sociale;

    var acheteurNom = "";
    acheteurNom = doc.acheteur.raison_sociale;

    var mercuriales = "M";
	if (doc.vendeur_type == 'caves_cooperatives') {
		mercuriales = "C";
	}
	if (doc.vendeur_type == 'negociants') {
		mercuriales = "X";
	}
	if (doc.acheteur_type == 'recoltants') {
		mercuriales = "V";
	}
	if (doc.interne) {
		mercuriales = "I";
	}

  var pluriannuel = doc.contrat_pluriannuel ? "PLURIANNUEL" : "ANNUEL"
  var clause_reserve = doc.clause_reserve_propriete ? "OUI" : "NON"

  var duree = null, mode = null, prix_unite = null, createur = null;

  if (doc.prix_unite) {
    prix_unite = doc.prix_unite
  } else if (doc.type_contrat === "BOUTEILLE") {
    prix_unite = "EUR_BOUTEILLE"
  } else {
    prix_unite = "EUR_HL"
  }

  if (doc.createur_identifiant === doc.vendeur_identifiant) {
    createur = "VENDEUR"
  } else if (doc.createur_identifiant === doc.acheteur_identifiant) {
    createur = "ACHETEUR"
  } else {
    createur = "COURTIER"
  }

  if (pluriannuel === "PLURIANNUEL") {
    var campagne_start = doc.campagne.substr(0, 4)
    duree = campagne_start + ' à ' + (campagne_start + 2)
    mode = doc.contrat_pluriannuel_mode_surface ? "ARES" : "HL"
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
                                                                        var mentionKey = "DEFAUT"
                                                                        if(produit.vtsgn) {
                                                                            mentionKey = produit.vtsgn;
                                                                        }
                                                                        
                                                                        var genreKey = "TRANQ";
                                                                        if(appellation.match(/CREMANT/)) {
                                                                            genreKey = "EFF";
                                                                        }
                                                                        var lieuKey = lieu.replace("lieu", "");
                                                                        if(!lieuKey) {
                                                                            lieuKey = "DEFAUT";
                                                                        }
                                                                        
                                                                        var couleurKey = couleur.replace("couleur", "").toLowerCase();
                                                                        if(!couleurKey) {
                                                                            couleurKey = "DEFAUT";
                                                                        }
                                                                        
                                                                        
                                                                        var produitHash = "/declaration/certifications/AOC_ALSACE/genres/"+genreKey+"/appellations/"+appellation.replace('appellation_', '')+"/mentions/"+mentionKey+"/lieux/"+lieuKey+"/couleurs/"+couleurKey+"/cepages/"+cepage.replace("cepage_", "");
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

                                                                        var dateRetiraison = null;
                                                                        for(keyRetiraison in produit.retiraisons) {
                                                                            dateRetiraison = produit.retiraisons[keyRetiraison].date;
                                                                        }

                                                                        var volume_reserve = null;
                                                                        if (produit.dont_volume_bloque !== undefined) {
                                                                          volume_reserve = produit_volume_bloque
                                                                        }

                                                                        emit(
                                                                          [teledeclare, doc.valide.date_saisie, doc._id],
                                                                          [
                                                                            doc.campagne,
                                                                            doc.valide.statut,
                                                                            doc._id,
                                                                            doc.numero_contrat,
                                                                            archive,
                                                                            doc.acheteur_identifiant,
                                                                            acheteurNom,
                                                                            doc.vendeur_identifiant,
                                                                            vendeurNom,
                                                                            doc.mandataire_identifiant,doc.mandataire.nom,
                                                                            null,
                                                                            null,
                                                                            doc.type_contrat,
                                                                            produitHash,
                                                                            produitLibelle,
                                                                            produit.volume_propose,
                                                                            produit.volume_enleve,
                                                                            prix_unitaire_hl,
                                                                            prix_unitaire_hl,
                                                                            prix_variable,
                                                                            interne,
                                                                            original,
                                                                            mercuriales,
                                                                            doc.valide.date_validation,
                                                                            doc.valide.date_validation,
                                                                            doc.valide.date_saisie,
                                                                            produit.millesime,
                                                                            null,
                                                                            produit.denomination.replace(/,/g, ""),
                                                                            null,
                                                                            null,
                                                                            null,
                                                                            doc.cepage,
                                                                            libelle_cepage,
                                                                            label,
                                                                            quantite,
                                                                            produit.prix_unitaire,
                                                                            centilisation,
                                                                            doc.acheteur_type,
                                                                            doc.vendeur_type,
                                                                            doc.valide.date_cloture,
                                                                            dateRetiraison,

                                                                            pluriannuel,
                                                                            duree,
                                                                            mode,
                                                                            prix_unite,
                                                                            volume_reserve,
                                                                            clause_reserve,
                                                                            createur
                                                                          ]
                                                                        );
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
