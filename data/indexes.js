db.menus.ensureIndex({title: 1}, {unique: true});
db.lists.ensureIndex({name: 1}, {unique: true});
db.users.ensureIndex({email: 1}, {unique: true, dropDups : true});
