function(doc) {
    if (doc.type == "Vrac"){
        for(certification in doc.declaration) {
            if (certification.match(/^certification/g)) {
                for(genre in doc.declaration[certification]) {
                    if (genre.match(/^genre/g)) {
                        for(appellation in doc.declaration[certification][genre]) {
                            if (appellation.match(/^appellation/g)) {
                                var code_appellation = appellation.replace("appellation_","");
                                for(mention in doc.declaration[certification][genre][appellation]) {
                                    if (mention.match(/^mention/g)) {
                                        for(lieu in doc.declaration[certification][genre][appellation][mention]) {
                                            if (lieu.match(/^lieu/g)) {
                                                for(couleur in doc.declaration[certification][genre][appellation][mention][lieu]) {
                                                    if (couleur.match(/^couleur/g)) {
                                                        for(cepage in doc.declaration[certification][genre][appellation][mention][lieu][couleur]) {
                                                            if (cepage.match(/^cepage/g)) {
                                                                var code_cepage = cepage.replace("cepage_","");
                                                                var numero_cepage = null;
                                                                for(detail in doc.declaration[certification][genre][appellation][mention][lieu][couleur][cepage].detail) {
                                                                    var produit = doc.declaration[certification][genre][appellation][mention][lieu][couleur][cepage].detail[detail];
                                                                    if (produit.actif) {
                                                                        if(doc.valide.date_validation){
                                                                            var produitHash = "/declaration/"+certification+"/"+genre+"/"+appellation+"/"+mention+"/"+lieu+"/"+couleur+"/"+cepage;
                                                                            var vendeurNom = "";
                                                                            if(doc.vendeur.intitule){ vendeurNom = doc.vendeur.intitule+" "; }
                                                                            vendeurNom = vendeurNom + doc.vendeur.raison_sociale;

                                                                            var acheteurNom = "";
                                                                            if(doc.acheteur.intitule){ acheteurNom = doc.acheteur.intitule+" "; }
                                                                            acheteurNom = acheteurNom + doc.acheteur.raison_sociale;

                                                                            var statut = "NONSOLDE";

                                                                            if(doc.valide.statut == "CLOTURE" && doc.type_contrat != "BOUTEILLE") {
                                                                                statut = "SOLDE";
                                                                            }

                                                                            var now = new Date();
                                                                            var annee_limite = (now.getFullYear() - 2);
                                                                            var annee_contrat = doc.valide.date_validation.split("-")[0];

                                                                            if(annee_contrat < annee_limite) {
                                                                                statut = "SOLDE";
                                                                            }

                                                                            emit([statut, doc.vendeur_identifiant, produitHash, "VIN_" + doc.type_contrat],[doc.numero_visa, acheteurNom, produit.volume_propose, produit.volume_enleve, vendeurNom]);
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
}
