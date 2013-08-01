function(doc) {
    if (doc.type == "DR" && doc.utilisateurs&& doc.utilisateurs.edition) {
      for (u  in doc.utilisateurs.edition) {
         emit([doc.campagne, u], 1);
        break;
        }
    }   
}