function(doc) {
   	if (doc.type != "Etablissement") {
 
         return;
        }
     
     emit([doc.interpro, doc.statut, doc.famille, doc.id_societe, doc._id, doc.nom, doc.identifiant, doc.cvi, doc.region], [doc.raison_sociale, doc.siege.adresse, doc.siege.commune, doc.siege.code_postal, doc.no_accises, doc.carte_pro, doc.email, doc.telephone, doc.fax, null, null, doc.mois_stock_debut, doc.num_interne]);
}