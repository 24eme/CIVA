function(doc) {
  if (doc.type && doc.type == "Export") {
    emit([doc.destinataire, doc.identifiant, doc.nom, doc.cle], 1);
  }
}