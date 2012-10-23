function(doc) {

 if (doc.type && doc.type == "DR" && doc.validee && doc.modifiee && !doc.import_db2)  {
    emit([doc.campagne, doc.declaration_insee, doc.cvi], 1);
  }
}