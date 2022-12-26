function (doc) {
  if (doc.type.startWith('SV1')) {
    if (doc.declarant.cvi.startWith('67') || doc.declarant.cvi.startWith('68')) {
      emit([doc.campagne, doc.valide.statut == 'VALIDE', doc.valide.date_saisie])
    }
  }
}
