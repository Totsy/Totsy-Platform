-- 1) psql -Aqt -d totsy -f export_credits.sql -o credits.json -F ","
-- 2) mongoimport -d totsytest -c credits {--drop} --file credits.json
SELECT
 '{customer_id:"'||customer_id||'"' AS "user_id",
 'type:"Credit"' AS "type",
 'description:"Legacy Credits"' AS "description",
 'amount:'||TRUNC(CAST(SUM(remaining_amount) AS decimal), 2) AS "amount",
 'date_created:{ "$date" : '||round(COALESCE(EXTRACT(EPOCH FROM now()),1))||'000}}' AS "date_created"
FROM promotions 
WHERE customer_id IS NOT NULL 
AND remaining_amount > 0 
GROUP BY customer_id