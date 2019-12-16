SELECT
    payrolls.property_id 'Deal',
    payrolls.source_id 'Lead Source',
    pn.login                                                                     AS 'Primary_AA__c',
    aa2pn.login                                                                AS 'Secondary_AA__c',
    aa3pn.login                                                          AS 'Additional_AA__c',
    dapn.login                                                                    AS 'Primary_DA__c',
    da2pn.login                                                                AS 'Secondary_DA__c',
    da3pn.login                                                         AS 'Additional_DA__c',
    o.ID 'Accounting Office',
    payrolls.id 'Legacy Homeland ID',
    payrolls.type 'Type',
    aa_base_pay 'AA 1 Base',
    sec_aa_base_pay 'AA 2 Base',
    additional_aa_base_pay 'AA 3 Base',
    payrolls.acquisition_fee 'Acquisition Fee',
    payrolls.acquisition_cost 'Acquisition Price',
    payrolls.disp_admin_fee 'Admin Fee to NWA',
    payrolls.disp_cash_to_seller 'Cash to Seller',
    IF(payrolls.disp_close_date IS NOT NULL
           AND payrolls.disp_close_date NOT IN ('' , '00-00-00 00:00:00.00000'),
       DATE_FORMAT(payrolls.disp_close_date, '%Y-%m-%dT%TZ'),
       NULL) 'Close Out Date',
    DATE_FORMAT(payrolls.disp_close_date, '%Y-%m-%dT%TZ') 'Disposition Close Date',
    IF(accounting_notes IS NULL
           AND special_notes IS NULL,
       NULL,
       TRIM(CONCAT(IF(accounting_notes IS NULL,
                      '',
                      CONCAT('Accounting Notes:
                                ',
                             accounting_notes,
                             '

                             ')),
                   IF(special_notes IS NULL,
                      '',
                      CONCAT('Special Notes:
                                ',
                             special_notes,
                             ''))))) AS 'Comments',
    payrolls.commission 'Commission Fee to NWA',
    da_base_pay 'DA 1 Base',
    sec_da_base_pay 'DA 2 Base',
    additional_da_base_pay 'DA 3 Base',
    payrolls.disp_down_payment 'Down Payment in',
    (if (payrolls.option_money is NULL,
         cast('0' as int),
         cast(payrolls.option_money as int)) +
     if (payrolls.earnest_money is NULL,
         cast('0' as int),
         cast(payrolls.earnest_money as int)) +
     if (payrolls.additional_earnest_money is NULL,
         cast('0' as int),
         cast(payrolls.additional_earnest_money as int))
        ) AS 'EM and Option Out',
    payrolls.holding_company_profit 'Holding Company Profit',
    payrolls.insurance_cost 'Insurance Cost',
    payrolls.interest_expense 'Interest Expense',
    payrolls.marketing_holdback 'Marketing Holdback',
    payrolls.misc_credit 'Misc Credit',
    payrolls.misc_expense 'Misc Expense',
    IF(payrolls.payroll_date IS NOT NULL
           AND payrolls.payroll_date NOT IN ('' , '00-00-00 00:00:00.00000'),
       DATE_FORMAT(payrolls.payroll_date, '%Y-%m-%dT%TZ'),
       NULL) 'Paid Date',
    payrolls.profit_after_holdback 'Profit After Holdback',
    CONCAT(UCASE(LEFT(stage, 1)),
           SUBSTRING(stage, 2)) AS 'Stage',
    payrolls.company_fee 'Total Profit',
    payrolls.assignment_fee 'Assignment Fee',
    payrolls.wire_fee 'Wire Fee',
    payrolls.wire_in_amount 'Wire In Amount',
    IF(payrolls.wire_in_date IS NOT NULL
           AND payrolls.wire_in_date NOT IN ('' , '00-00-00 00:00:00.00000'),
       DATE_FORMAT(payrolls.wire_in_date, '%Y-%m-%dT%TZ'),
       NULL) 'Wire in Date',
    payrolls.wire_out_amount 'Wire Out Amount',
    IF(payrolls.wire_out_date IS NOT NULL
           AND payrolls.wire_out_date NOT IN ('' , '00-00-00 00:00:00.00000'),
       DATE_FORMAT(payrolls.wire_out_date, '%Y-%m-%dT%TZ'),
       NULL) 'Wire Out Date',
    payrolls.wire_variance 'Wire Variance',
    payrolls.disp_price 'Disposition Price',
    DATE_FORMAT(payrolls.date_created, '%Y-%m-%dT%TZ') 'Created Date'
FROM
    payrolls
        JOIN
    properties p ON p.id = payrolls.property_id
        JOIN
    offices o ON o.id = p.accounting_office_id
        LEFT JOIN users pn ON pn.person_id = payrolls.acq_agent_id
        LEFT JOIN users aa2pn ON aa2pn.person_id = payrolls.sec_acq_agent_id
        LEFT JOIN users aa3pn ON aa3pn.person_id = payrolls.additional_acq_agent_id
        LEFT JOIN users dapn ON dapn.person_id = payrolls.disp_agent_id
        LEFT JOIN users da2pn ON da2pn.person_id = payrolls.sec_disp_agent_id
        LEFT JOIN users da3pn ON da3pn.person_id = payrolls.additional_disp_agent_id
WHERE

        o.id = '19';
#Payrolls.Legacy_Homeland_ID__c is null
#and sqsPayroll.legacyId is null