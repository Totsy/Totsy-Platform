SELECT c.id AS "_id",
c.first_name AS "firstname",
c.last_name AS "lastname",
0 AS "affiliate",
c.login AS "username",
c.email AS "email",
1 AS "legacy",
c.crypted_password AS "password",
c.password_salt AS "salt",
c.created_at AS "created",
c.updated_at AS "updated",
c.last_login_at AS "lastlogin",
c.last_login_ip AS "lastip",
c.login_count AS "logincounter",
1 AS "active",
it.code AS "invitation_code"

FROM customers c,
invitation_tokens it

WHERE first_name != ''
AND it.customer_id = c.id
-- skip crap data
AND c.id > 3000

ORDER BY c.id
-- limit output
LIMIT 20
;