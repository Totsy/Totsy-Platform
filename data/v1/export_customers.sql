SELECT id AS "_id",
first_name AS "firstname",
last_name AS "lastname",
login AS "username",
"email",
1 AS "legacy",
crypted_password AS "password",
password_salt AS "salt",
created_at AS "created",
updated_at AS "updated",
last_login_at AS "lastlogin",
last_login_ip AS "lastip",
login_count AS "logincounter",
1 AS "active"


FROM customers

WHERE first_name != ''

ORDER BY id
--LIMIT 10
;