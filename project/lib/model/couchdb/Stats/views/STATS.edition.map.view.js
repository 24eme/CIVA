function(doc) {
    if (doc.type == "DR" && doc.campagne == "2011" && doc.utilisateurs&& doc.utilisateurs.edition) {
      for (u  in doc.utilisateurs.edition) {
         emit([u], 1);
        break;
        }
    }   
}