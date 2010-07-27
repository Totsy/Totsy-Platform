SELECT c.id AS "_id",
c.first_name AS "firstname",
c.last_name AS "lastname",
0 AS "affiliate",
c.login AS "username",
c.email AS "email",
1 AS "legacy",
c.crypted_password AS "password",
c.password_salt AS "salt",
c.created_at AS "created_orig",
c.updated_at AS "updated_orig",
c.last_login_at AS "lastlogin",
c.last_login_ip AS "lastip",
c.login_count AS "logincounter",
1 AS "active"

FROM customers c

WHERE first_name != ''

ORDER BY c.id
-- limit output
--LIMIT 200
;