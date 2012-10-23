function(doc) {  
    if (!doc.type || doc.type != 'CSV') {
        return;
    }  

    emit([doc.campagne+'', doc.cvi+'', doc._id], 1);
}