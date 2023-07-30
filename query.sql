-- Balance for each user (rounded to two decimal places)
SELECT u.name AS user_name, ROUND(SUM(mp.balance), 2) AS total_balance
FROM user u
         JOIN mobile_phone mp ON u.id = mp.user_id
GROUP BY u.id, u.name;

-- Count phone numbers per operator
SELECT SUBSTRING(number, 5, 2) AS operator_code, COUNT(*) AS phone_count
FROM mobile_phone
GROUP BY SUBSTRING(number, 5, 2);

-- Count phones for each user
SELECT u.name AS user_name, COUNT(mp.id) AS phone_count
FROM user u
         LEFT JOIN mobile_phone mp ON u.id = mp.user_id
GROUP BY u.id, u.name;

-- Retrieve the names of the top 10 users with the highest balance (rounded to two decimal places)
SELECT u.name AS user_name, ROUND(MAX(mp.balance), 2) AS max_balance
FROM user u
         JOIN mobile_phone mp ON u.id = mp.user_id
GROUP BY u.id, u.name
ORDER BY max_balance DESC
LIMIT 10;
