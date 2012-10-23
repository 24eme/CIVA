function(doc) {
  if (!(doc.type && doc.type == "DR" && doc.campagne == "2011" && doc.cvi.match('^(67|68)') && doc.validee && doc.modifiee )) {
    return;
  }

for (var edition_key in doc.utilisateurs.edition)
{
    if (edition_key.match("^COMPTE-civa") || edition_key.match("^COMPTE-admin")) {
        emit([doc.cvi, edition_key], 1);
    }
    return;
}


}