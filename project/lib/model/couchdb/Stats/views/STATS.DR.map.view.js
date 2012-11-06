function(doc) {
if (doc.type == "DR" && doc.campagne == "2012") {
  emit([doc.validee != null, doc.modifiee != null, doc.validee, doc.etape], 1);
}
}