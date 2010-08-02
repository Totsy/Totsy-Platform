-- MAKE SURE YOU GOT AN INDEX ON invitation_tokens.customer_id
SELECT '{_id:"'||c.id||'"' AS "_id",
'firstname:"'||c.first_name||'"' AS "firstname",
'lastname:"'||c.last_name||'"' AS "lastname",
'affiliate:'||0 AS "affiliate",
'username:"'||c.login||'"' AS "username",
'email:"'||c.email||'"' AS "email",
'legacy:'||1 AS "legacy",
'password:"'||c.crypted_password||'"' AS "password",
'salt:"'||c.password_salt||'"' AS "salt",
'created_orig:{ "$date" : '||round(COALESCE(EXTRACT(EPOCH FROM c.created_at),1))||'000}' AS "created_orig",
'updated_orig:{ "$date" : '||round(COALESCE(EXTRACT(EPOCH FROM c.updated_at),1))||'000}' AS "updated_orig",
'lastlogin:{ "$date" : '||round(COALESCE(EXTRACT(EPOCH FROM c.last_login_at),1))||'000}' AS "lastlogin",
'lastip:"'||COALESCE(c.last_login_ip,'')||'"' AS "lastip",
'logincounter:'||COALESCE(c.login_count,0) AS "logincounter",
'active:'||1 AS "active",
'invitation_codes:["'||
array_to_string (
  array(
    SELECT code FROM invitation_tokens where customer_id = c.id
  ), '","'
)||'"]}' AS "invitation_codes"

FROM customers c

WHERE first_name != ''

ORDER BY c.id
-- limit output
--LIMIT 2000
;