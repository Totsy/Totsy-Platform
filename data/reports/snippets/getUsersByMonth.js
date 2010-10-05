db.users.group(
	{
		keyf: function(doc){
			return {
				"Month": doc.created_date.getMonth(),
			}
		},
		initial : {
			count:0
		}, 
		reduce: function(doc, prev){
			prev.count += 1;
		},
		cond: {
			"created_date" : {$gte : new Date ('September 1, 2010')}
		}
	}
);
