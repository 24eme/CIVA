function(doc) {  
    if (!doc.type || doc.type != 'CSV') {
        return;
    }

    if(!doc.cvi.match(/^(67|68)/)) {
		return;
	} 

    emit([doc.campagne+'', doc.cvi+'', doc._id], 1);
}