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
    	    	    	    	    	    						var produitHash = "/declaration/"+certification+"/"+genre+"/"+appellation+"/"+mention+"/"+lieu+"/"+couleur+"/"+cepage;
                                              if(produit.retiraisons.length){
                                                for(retiraison in produit.retiraisons){
						                                      var r = produit.retiraisons[retiraison];
                                                  var periode = r.date.replace('-','').substring(0,6);
                                                  emit([doc.vendeur_identifiant, periode, "VRAC", doc._id ],[produitHash, "sorties", "vrac", r.volume]);
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
    if (doc.type == "DR") {
      if(doc.validee){
        var etb = "ETABLISSEMENT-"+doc.cvi;
        var periode = doc.validee.replace('-','').substring(0,6);
        emit([etb, periode, "DR", doc._id ], [null, "entrees", "recolte", null]);
        }
    }
    if(doc.type == "DS"){
      if(doc.validee){
	       var etb = "ETABLISSEMENT-"+doc.declarant.cvi;
	       emit([etb, doc.periode, "DS", doc._id ], [null, "stocks_debut", "initial", null]);
	       emit([etb, doc.periode, "DS", doc._id ], [null, "stocks_fin", "final", null]);
    }
  }

}
