-- Need to map in the appropriate lead source IDs.
-- Need to replace internal agent ids with SF IDs
-- Deal needs extra , redundant fields
-- Deal still needs peripheral agents.
-- We need to map Holding Companies into Company/Account

-- Need to deal with Holding Companies... need another migration for it.
-- Clearly, some of these have duplicate properties in an ACTIVE state.
# SELECT count(*) FROM (
SELECT DISTINCT p.id                                                                               AS 'Legacy Homeland ID',
                CONCAT(coalesce(p.streetnum, ''), ' ', coalesce(p.streetname, ''))                 AS 'Deal',
                CONCAT(coalesce(p.streetnum, ''), ' ',
                       IF(p.streetdir_prefix IS NOT NULL, concat(p.streetdir_prefix, ' '), ''),
                       coalesce(p.streetname, ''), ' ', coalesce(p.streettype, ''),
                       IF(p.streetdir_suffix IS NOT NULL, concat(' ', p.streetdir_suffix), ''))    AS 'Street',
                p.city                                                                                'City',
                p.state                                                                               'State',
                p.zipcode                                                                             'Zip/Postal Code',
                o.Id                                                                             	  'Office__c',

                pn.login                                                                     AS 'Owner_Primary_AA__c',
                IF(p.acq_agent_id is null or p.acq_agent_id = '', 'Property Dibs Bucket', 'Owned') AS 'Ownership Type',
                aa2pn.login                                                                AS 'Secondary_AA__c',
                aa3pn.login                                                          AS 'Additional_AA__c',
                dapn.login                                                                    AS 'Primary_DA__c',
                da2pn.login                                                                AS 'Secondary_DA__c',
                da3pn.login                                                         AS 'Additional_DA__c',
                p.stage_code,
                (case p.stage_code
                     when 'inspected'
                         then '0121I000000Fq69QAC'
                     when 'offer_sent'
                         then '0121I000000Fq6CQAS'
                     when 'contracted'
                         then '0121I000000Fq6CQAS'
                     When 'sold'
                         then '0121I000000Fq6BQAS'
                     when 'pre-closed'
                         then '012q00000001MClAAM'
                     when 'closed'
                         then '0121I000000Fq67QAC'
                     when 'dead'
                         then '0121I000000Fq68QAC'
                     when 'dropped'
                         then '0121I000000Fq68QAC'
                     else '0121I000000Fq6AQAS'
                    end)                                                                           AS 'Record Type ID',
                IF(stage_code in ('closed', 'dead', 'dropped'), 'false', 'true')                   AS 'Active',
                (case p.stage_code
                     when 'inspected'
                         then 'Inspected'
                     when 'offer_sent'
                         then 'Working'
                     when 'contracted'
                         then 'Available'
                     When 'sold'
                         then 'Sold'
                     when 'pre-closed'
                         then 'Closed'
                     when 'closed'
                         then 'Closed'
                     when 'dead'
                         then 'Dead'
                     when 'dropped'
                         then 'Dead'
                     else 'Prep'
                    end)                                                                           AS 'Status',
                (case p.stage_code
                     when 'new'
                         then 'New'
                     when 'cma_performed'
                         then 'CMA'
                     when 'inspected'
                         then 'Inspected'
                     when 'offer_sent'
                         then 'Offer Sent'
                     when 'contracted'
                         then 'Available'
                     When 'sold'
                         then 'Approved to Open Title'
                     when 'pre-closed'
                         then 'Pre Close'
                     when 'closed'
                         then 'Closed'
                     when 'dead'
                         then 'Dropped'
                     when 'dropped'
                         then 'Dropped'
                     else 'New'
                    end)                                                                           AS 'Property Stage',
                IF(stage_code in ('offer_sent'), true, false)                                      as 'Offer Generated',
                IF(stage_code in ('contracted'), true, false)                                      as 'Send to Dispositions',
                IF(stage_code in ('dead', 'dropped'), true, false)                                 as 'Drop',
                -- This needs to come from the picklist value... need to join to drops.
                #   IF(stage_code in ('dead', 'dropped'), 'Imported from Legacy', null) as 'Drop Reason',
                p.nwa_arv                                                                             'Agent ARV',
                p.est_arv                                                                             'Deal Scalper ARV',
                p.total_estimated_repair_cost                                                         'Repair Cost',
                p.listprice                                                                        AS 'Listing Price',
                p.salesprice                                                                       AS 'Sale Price',
                IF(disp_close_date is not null and disp_close_date not in ('', '00-00-00 00:00:00.00000'),
                   DATE_FORMAT(disp_close_date, '%Y-%m-%dT%TZ'),
                   null)                                                                           AS 'Disposition Close Date',
                # @todo
                # need to join/query on holding company ID/name
                #hc.name                                                                            as 'Holding Company',
                source_id                                                                      'Lead_Source__c',
                dd1.name	as 'Drill_Down_1__c',
                dd2.name as 'Drill_Down_2__c',
                # not a field in Salesforce
                #   ''                                                                          AS 'Lead Source ID',
                acquisition_cost                                                                   AS 'Acquisition Price',
                acquisition_fee                                                                    AS 'Acquisition Fee',
                p.disp_price                                                                       AS 'Disposition Price',
                p.seller_type                                                                      AS 'Seller Type',
                IF(acq_close_date is not null and acq_close_date not in ('', '00-00-00 00:00:00.00000'),
                   DATE_FORMAT(acq_close_date, '%Y-%m-%dT%TZ'),
                   null)                                                                              'Acquisition Close Date',
                -- This is a DATE field in SF, but TEXT in Homeland... going to have to add this to the cleanup script!
                #   acq_close_date_contract                                       'Acquisition Close Date - Contract',
                IF(closeout_type = 'double', 'Double Closeout', 'Assignment_Closeout')             AS 'Closeout Type',
                disp_down_payment                                                                  AS 'Down Payment',
                disp_cash_to_seller                                                                AS 'Cash to Seller',
                insurance_cost                                                                     AS 'Insurance Cost',
                misc_expense                                                                       AS 'Misc Expense',
                #   p.owner_name                                                                       'Owner of Property',
                misc_credit                                                                        AS 'Misc Credit',
                # @trey, let's let the automated SF process create deal and the property, which should populate these fields
                # and then we can backfill the property data later using the homeland legacy id field as the key
                #Property.Id                                                                           'Property',
                #Property.Id                                                                           'Master Property',
                #Property.Id                                                                           'HiddenProperty',
                pn.login                                                                              as 'OwnerId',
                -- ========================================
                CONCAT(coalesce(p.streetnum, ''), ' ',
                       IF(p.streetdir_prefix IS NOT NULL, concat(p.streetdir_prefix, ' '), ''),
                       coalesce(p.streetname, ''), ' ', coalesce(p.streettype, ''),
                       IF(p.streetdir_suffix IS NOT NULL, concat(' ', p.streetdir_suffix), ''))    AS 'Address',
                p.amount_behind                                                                    as 'Amount Behind',     --  , p.price
                p.cdom                                                                             as 'Cdom',
                p.comm_pct                                                                         as 'Commission Percentage',
                p.garage                                                                           as 'Garage',
                p.heatsystem                                                                       as 'Heating / Cooling', --  , p.hoa             as 'HOA'
                p.hoa_description                                                                  as 'HOA Description',
                IF(p.hoa in ('Mandatory', 'None', 'Voluntary'), p.hoa, '')                         as 'HOA',
                IF(p.hoa in ('Mandatory'), true, false)                                            as 'HOA Mandatory',
                IF(p.hoa not in ('Mandatory', 'voluntary', 1), true,
                   false)                                                                          as 'No HOA Mandatory',
                p.interest_expense                                                                 as 'Interest Expense',
                p.internal_notes                                                                   as 'Internal Notes',
                p.internal_comments                                                                as 'Internal Comments', --   MLS
                mls.mlsnum                                                                            'MLS_MLS_ID__c',
                mls.mlsnum                                                                            'MLS/ID',
                mls.salesprice                                                                     as 'MLS Sale Price',
                mls.seller_type                                                                    as 'MLS Seller Type',
                mls.listprice                                                                         'MLS List Price',
                mls.beds                                                                              'MLS Bedrooms',
                mls.liststatus                                                                        'MLS Status',
                mls.area                                                                              'MLS Area',
                mls.bathsfull                                                                         'MLS Bathrooms',
                mls.cdom                                                                              'MLS Cdom',
                mls.city                                                                              'MLS City',
                mls.bathshalf                                                                         'MLS Half Bathrooms',
                mls.state                                                                             'MLS State',
                mls.streetname                                                                        'MLS Street',
                mls.yearbuilt                                                                         'MLS Year Built',
                mls.zipcode                                                                           'MLS Zip/Postal Code',
                IF(p.stage_code in ('sold', 'contracted', 'pre-close', 'closed'), true, false)     AS 'GM Approved',
                mls.bathstotal                                                                     as 'Bathrooms_US__c',
                #@trey where do you want to join buyers
                #inv.Id                                                                             as 'Buyer',
                p.earnest_money                                                                    as 'Earnest Money',
                p.earnest_money_contract                                                           as 'Earnest Money Contract Amount',
                p.acq_escrow_agent_name                                                            as 'Escrow Agent',
                p.first_mortgage_amount                                                            as 'First Mortgage Amount',
                p.second_mortgage_amount                                                           as 'Second Mortgage Amount',
                concat(p.seller_firstname, ' ', p.seller_lastname)                                 as 'Seller2__c',
                p.wire_fee                                                                         as 'Wire Fee',
                p.wire_variance                                                                    as 'Wire Variance',
                p.yearbuilt                                                                        as 'Year_Built_Text__c',
                p.taxid                                                                            as 'Tax_ID__c',
                p.survey                                                                           as 'Survey Needed',
                IF(p.survey in ('Yes'), true, false)                                               as 'Survey Needed - Yes',
                IF(p.survey in ('No'), true, false)                                                as 'Survey Needed - No',
                mls.agentlist_email                                                                as 'MLS Listing Agent Email',
                mls.agentlist_fax                                                                  as 'MLS Listing Agent Fax',
                p.option_money                                                                     as 'Option Money',
                #mls.area                                                                           as 'pba__Area_pb__c',
                p.hoa_description                                                                  as 'MLS HOA Description',
                p.marketing_comments                                                               as 'Marketing Comments',
                mls.beds                                                                           as 'pba__Bedrooms_pb__c',
                mls.latitude                                                                       as 'pba__Latitude_pb__c',
                mls.longitude                                                                      as 'pba__Longitude_pb__c',
                # should be grabbing field and populating on property
                #mls.lotsize                                                                        as 'pba__LotSize_pb__c',
                mls.state                                                                          as 'pba__StateCode_pb__c',
                p.days_option_contract                                                             as 'Days of Option',
                (CASE p.disp_is_down_payment
                     when 'seller_held'
                         then 'Seller Held'
                     WHEN 'title_held'
                         then 'Title Held'
                     else ''
                    end)                                                                           as 'Is Down Payment',
                p.lender_name                                                                      as 'Lender',
                IF(p.stage_code in ('sold', 'contracted', 'pre-close', 'closed'), true,
                   false)                                                                          AS 'Request GM Approval',
                p.acq_title_company                                                                as 'Acquisition Title Co',
                #p.acq_title_company_address                                                        as 'Acquisition Title Co Address',
                p.yearbuilt                                                                        as 'pba__YearBuilt_pb__c',
                p.area                                                                             as 'pba__Area_pb__c',
                p.nwa_alv                                                                          as 'Agent_Est_Rent__c',
                p.yearbuilt                                                                        as 'Year_Build_text__c',
                p.bathsfull                                                                        as 'pba__FullBathrooms_pb__c',
                p.agentlist_fax                                                                    as 'pba__Listing_Agent_Fax__c',
                p.agentlist_email                                                                  as 'pba__Listing_Agent_Email__c',
                p.agentlist_phone                                                                  as 'pba__Listing_Agent_Phone__c',
                p.agentlist_firstname                                                              as 'pba__Listing_Agent_Firstname__c',
                p.agentlist_lastname                                                               as 'pba__Listing_Agent_Lastname__c',
                p.agentlist_mobilenum1                                                             as 'pba__Listing_Agent_Mobil_Phone__c',
                p.agentlist_licensen                                                               as 'pba__Listing_Agent_Licence__c',
                p.bathshalf                                                                        as 'pba__HalfBathrooms_pb__c',
                p.longitude                                                                        as 'pba__Longitude_Property__c',
                p.pof_amount                                                                       as 'POF_Amount__c',
                p.sellers_notes                                                                    as 'Seller_s_Notes__c',
                p.bathstotal                                                                       as 'Bathrooms__c',
                p.beds                                                                             as 'Bedrooms__c',
                p.legal_description                                                                as 'Legal_Description_HL__c',
                p.sqfttotal                                                                        as 'SqFt_Total__c',
                p.block                                                                            as 'Block_HL__c',
                p.lotnum                                                                           as 'Lot_HL__c',
                p.subdivision                                                                      as 'Addition_HL__c',
                p.lotdim                                                                           as 'Lot_Dimensions__c',
                p.marketing_holdback                                                               as 'Marketing_Holdback__c',
                p.company_fee                                                                      as 'Company_Fee__c',
                p.latitude                                                                         as 'pba__Latitude_Property__c',
                mls.housingtype                                                                    as 'pba__ListingType__c',
                mls.agentlist_firstname                                                            as 'MLS_pba__Listing_Agent_Firstname__c',
                mls.agentlist_lastname                                                             as 'MLS_pba__Listing_Agent_Lastname__c',
                p.holding_company_profit                                                           as 'Holding_Company_Profit__c',
                p.profit_after_holdback                                                            as 'Profit_after_Holdback__c',
                p.county                                                                           as 'pba__County_pb__c',
                p.wholesaler_comm                                                                  as 'Wholesaler_Commission__c',
                p.arv_pct                                                                          as 'arv_pct',
                p.sbl_down_payment                                                                 as 'SBL_Down_Payment__c',
                p.flat_fee_commission                                                              as 'Flat_Fee_Commission__c',
                #p.lotsize                                                                          as 'pba__LotSize_pb__c',
                p.retail_value                                                                     as 'Retail_Value__c',
                p.additional_earnest_money                                                         as 'Additional_Earnest_Money__c',
                #@todo query/join to find SF ID
                p.disp_title_company                                                                 as 'Disposition_Title_Co__c',
                p.reason_for_selling                                                               as 'Reason_for_Selling__c',
                p.listprice                                                                        as 'Asking_Price__c',
                DATE_FORMAT(p.date_updated, '%Y-%m-%dT%TZ')											as "LastModifiedDate",
                DATE_FORMAT(p.date_created, '%Y-%m-%dT%TZ')																		as "createddate",
                p.investor_id 						as 	'Buyer__c'
FROM properties p
         JOIN offices o ON o.id = p.accounting_office_id
         LEFT JOIN options dd1 on p.source_dd1_id = dd1.id
         LEFT JOIN options dd2 on p.source_dd2_id = dd2.id
         LEFT JOIN users pn ON pn.person_id = p.acq_agent_id
         LEFT JOIN users aa2pn ON aa2pn.person_id = p.sec_acq_agent_id
         LEFT JOIN users aa3pn ON aa3pn.person_id = p.additional_acq_agent_id
         LEFT JOIN users dapn ON dapn.id = p.disp_agent_id
         LEFT JOIN users da2pn ON da2pn.person_id = p.sec_disp_agent_id
         LEFT JOIN users da3pn ON da3pn.person_id = p.additional_disp_agent_id
         LEFT JOIN title_companies tc ON p.acq_title_company = tc.id
    # don't need join on users anymore, just going to use person IDs
    # left join users u on u.person_id = pn.id
    # left join users aa2u on aa2u.person_id = aa2pn.id
    # left join users aa3u on aa3u.person_id = aa3pn.id
    # left join users du on du.person_id = dapn.id
    # left join users da2u on da2u.person_id = da2pn.id
    # left join users da3u on da3u.person_id = da3pn.id
         LEFT JOIN mls_properties mls ON mls.id = p.mls_property_id
     # migration table no longer exists
     # left join migration.User su on u.login = su.Username and su.Id is not null
     #left join migration.User aa2su on aa2u.login = aa2su.Username and aa2su.Id is not null
     # left join migration.User aa3su on aa3u.login = aa3su.Username and aa3su.Id is not null
     # left join migration.User dasu on du.login = dasu.Username and dasu.Id is not null
     #left join migration.User da2su on da2u.login = da2su.Username and da2su.Id is not null
     # left join migration.User da3su on da3u.login = da3su.Username and da3su.Id is not null
     # join migration.Property on p.id = Property.Legacy_Homeland_ID__c and Property.Id is not null
     # join migration.Office on o.name = Office.Name and Office.Id is not null
     # left join holding_companies as hc on p.holding_company_id = hc.Legacy_Homeland_ID__c
     # left join migration.Investor as inv on p.investor_id = inv.Legacy_Homeland_ID__c
     # left join title_companies as tcdisposition
     # on tcdisposition.Legacy_Homeland_ID__c = p.disp_title_company

     --  left join migration.Listing on p.id = Listing.Legacy_Homeland_ID__c
     --   left join migration.sqsListing on sqsListing.legacyId = p.id
WHERE
--    Listing.Legacy_Homeland_ID__c is null
--  and sqsListing.legacyId is null
-- This block ensures that we do not select any records that have non-migrated agents.
-- AND
#  su.Username <=> u.login
#and aa2su.Username <=> aa2u.login
#and aa3su.Username <=> aa3u.login
#and dasu.Username <=> du.login
#and da2su.Username <=> da2u.login
#and da3su.Username <=> da3u.login
o.id = '19'

order by p.date_updated;
# AND p.date_updated > '2018-06-01'
#and p.apn_parcel IS NOT NULL
# LIMIT 5;
# SHOW full processlist ;
#   where stage_code = 'closed' and disp_close_date > NOW() - interval 3 month
#   and o.name = 'Dallas'
-- ORDER BY p.id DESC
# ) tbl
-- GRANT ALL PRIVILEGES ON migration.* TO 'treylane'@'%';


-- DELETE FROM migration.Listing
-- ALTER Table `migration`.`Listing`
-- ADD INDEX `idIndex`  (`Legacy_Homeland_ID__c`);