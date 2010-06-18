 select count(*), email from customers group by email having count(*) > 1;
