function(doc) {
  if (!(doc.type && doc.type == "DR" && doc.cvi.match('^(67|68)') && doc.validee && doc.modifiee )) {
    
    return;
  }
  
  if (doc.jeunes_vignes && doc.jeunes_vignes > 0) {
    emit([doc.campagne, doc.cvi, doc.jeunes_vignes], 1);
  }
  
}