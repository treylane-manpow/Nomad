SELECT
    u.id 'Legacy_Homeland_ID__c',
    u.login as 'Username',
    u.login as 'Email',
    first_name 'FirstName',
    last_name 'LastName',
    'en_US' as 'LocaleSidKey',
    'UTF-8' as 'EmailEncodingKey',
    pr.role as 'UserRoleId',
    (CASE pr.role
         WHEN 'acquisition_agent' THEN '00e1I000000RwUr'
         WHEN 'disposition_agent' THEN '00e1I000000RwUs'
         WHEN 'office_broker' THEN '00e1I000000S1Ni'
         WHEN 'office_coordinator' THEN '00e1I000000S1Nk'
         WHEN 'funding_manager' THEN '00e1I000000RxTE'
         WHEN 'accounting' THEN '00e1I000000S1Ng'
         ELSE '00e1I000000S1Nh' -- 'Corporate'
        END) as 'ProfileId'
        ,u.active           as 'IsActive'
        , o.name
FROM users u
         JOIN persons p ON p.id = u.person_id
         JOIN contact_info ci ON ci.id = p.contact_info_id
         JOIN person_roles pr ON pr.person_id = (select person_id from person_roles WHERE
        person_id=p.id
                                                                                      AND office_id=19
                                                 Order BY date_created DESC LIMIT 1)
         JOIN offices o ON o.id = pr.office_id
         JOIN markets m on m.id = o.market_id
WHERE
        o.name = 'Oklahoma City'
  AND pr.role IN ('acquisition_agent', 'disposition_agent')
  AND concat(first_name, ' ', last_name) NOT IN ('Kristina Eisenhauer')
GROUP BY Username;