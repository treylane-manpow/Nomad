SELECT
    i.person_id                                                    as 'Legacy Homeland ID',
    aaU.login														 as 'owner',
    ci.address1,
    ci.address2,
    ci.city,
    ci.state_code,
    ci.zip,
    ci.country,
    DATE_FORMAT(date_meeting, '%Y-%m-%dT%TZ')                      as 'Meeting_Scheduled_Date__c',
    'Investor'                                                     as 'Contact Type',
    coalesce(ci.email, ci.email_secondary, ci.email_additional)    as 'Email',
    ci.phone_mobile                                                as 'Mobile Phone',
    ci.phone_work                                                  as 'Business Phone',
    ci.phone_home                                                  as 'Home Phone',
    ci.phone_other                                                 as 'Other Phone',
    ci.first_name                                                  as 'First Name',
    ci.last_name                                                   as 'Last Name',
-- so.ID                                                       as 'Office',
    (case
         when stage_code in ('no_contact', 'redistributed')
             then 'New'
         when stage_code in ('contact_made', 'meeting')
             then 'Working'
         when stage_code in ('disclosed', 'buyer', 'repeat_buyer')
             then 'Disclosed Investor'
         when stage_code in ('deleted', 'black_list', 'not_interested')
             then 'Dead'
         else 'Not Converted'
        end)                                                          as 'Stage',
    (case
         when stage_code in ('no_contact', 'redistributed')
             then 'New'
         when stage_code in ('contact_made', 'meeting')
             then 'Working'
         when stage_code in ('disclosed', 'buyer', 'repeat_buyer')
             then 'Disclosed Investor'
         when stage_code in ('deleted', 'black_list', 'not_interested')
             then 'Dead'
         else 'Not Converted'
        end)                                                          as 'Investor Stage',
    CONCAT(UCASE(LEFT(temperature, 1)), SUBSTRING(temperature, 2)) as 'Temperature',
    i.source_id as 'Lead Source',
    i.source_dd1_id as 'Drill Down 1',
    i.source_dd2_id as 'Drill Down 2',
    i.additional_notes,
    i.date_created
FROM persons pn
         JOIN investors i on i.person_id = pn.id

         join contact_info ci on ci.id = contact_info_id
         join offices o on o.id = accounting_office_id
         join markets m on m.id = o.market_id
         LEFT JOIN persons aa2pn ON aa2pn.id = agent_id
         LEFT JOIN contact_info aa ON aa.id = aa2pn.contact_info_id
         LEFT JOIN users aaU ON aaU.person_id = aa2pn.id
WHERE o.`Name` = 'Oklahoma City' AND stage_code NOT IN ('no_contact', 'redistributed', 'contact_made', 'meeting', 'deleted', 'black_list', 'not_interested' )
  and aaU.login = 'hunter.palacios@newwestern.com'
