-- YOU NEED TO DO THIS FIRST:
--
-- CREATE VIEW order_taxes AS SELECT order_id, sum(tax_amount) FROM order_products GROUP BY order_id;
-- CREATE VIEW order_subtotal AS SELECT order_id, SUM(quantity * price) AS "subtotal" FROM order_products GROUP BY order_id
;
--
-- 1) psql -Aqt -d totsy -f export_orders.sql -o orders.json -F ","
-- 2) mongoimport -d totsytest -c orders --drop --file orders.json
SELECT '{order_id:"'||o.id||'"' AS "order_id",
 'legacy:true' AS "legacy",
 'total:'||COALESCE(os.subtotal, 0) + COALESCE(ot.sum, 0) + TRUNC(CAST(o.shipping_price AS decimal) / 100, 2) AS "total",
 'subtotal:'||COALESCE(os.subtotal, 0) AS "subtotal",
 'tax:'||COALESCE(ot.sum, 0) AS "tax",
 'handling:'||TRUNC(CAST(o.shipping_price AS decimal) / 100, 2) AS "handling",
 'user_id:"'||o.customer_id||'"' AS "user_id",
-- 'MISSING' AS "card_type",
-- 'MISSING' AS "card_number",
 'date_created:{ "$date" : '||round(COALESCE(EXTRACT(EPOCH FROM o.created_at),1))||'000}' AS "date_created",
-- 'MISSING' AS "authKey",
 'billing:{firstname:"'||bi.first_name||'"' AS "billing.firstname",
 'lastname:"'||bi.last_name||'"' AS "billing.lastname",
 'address:"'||bi.billing_address1||'"' AS "billing.address",
 'address2:"'||bi.billing_address2||'"' AS "billing.address_2",
 'state:"'||bi.billing_state||'"' AS "billing.state",
 'zip:"'||bi.billing_zip||'"' AS "billing.zip",
 'country:"'||bi.billing_country||'"' AS "billing.country",
 'phone:"'||COALESCE(bi.phone,'')||'"' AS "billing.telephone",
 'user_id:"'||bi.customer_id||'"}' AS "billing.user_id",
-- 'shipping:'||o.shipping_price AS "shipping",
 'shippingMethod:"'||o.shipping_method||'"' AS "shippingMethod", 
 'giftMessage:""' AS "giftMessage",
-- 'MISSING' AS "items",
 'legacy_billinginfo_id:'||o.billinginfo_id||'}'
FROM orders o
LEFT JOIN billinginfos bi ON o.billinginfo_id = bi.id
LEFT JOIN order_taxes ot ON o.id = ot.order_id
LEFT JOIN order_subtotal os ON o.id = os.order_id
WHERE si_client_id = 7 
AND o.created_at > '2009-10-06 00:00:00'
AND billinginfo_id IS NOT NULL
AND os.subtotal != 0
ORDER BY o.id
-- debugging
--LIMIT 10
