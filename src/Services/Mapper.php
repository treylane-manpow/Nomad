<?php

namespace App\Services;

use App\Entity\Deal;
use App\Entity\DisclosedInvestor;
use App\Entity\HunterInvestors;
use App\Entity\LeadSource;
use App\Entity\Office;
use App\Entity\Payroll;
use App\Entity\User;

class Mapper{

    public static function get(){
        return [
            'deal'  =>  [
                'query' => 'Deal.sql',
                'type'  => Deal::class,
                'salesforce_name'   =>  'pba__Listing__c'
            ],
            'leadsource'    =>  [
                'query' =>  'LeadSource.sql',
                'type'  =>  LeadSource::class
            ],
            'user'  =>  [
                'query' =>  'User.sql',
                'type'  =>  User::class,
                'salesforce_name'   =>  'User'
            ],
            'option'    =>  [
                'query' =>  'Option.sql',
                'type'  =>  LeadSource::class,
                'salesforce_name'   =>  'LeadSource'
            ],
            'investor'  =>  [
                'query' =>  'DisclosedInvestor.sql',
                'type'  =>  DisclosedInvestor::class,
                'salesforce_name'   =>  'Contact'
            ],
            'hunter'  =>  [
                'query' =>  'Hunter.sql',
                'type'  =>  HunterInvestors::class,
                'salesforce_name'   =>  'Contact'
            ],
            'office'    =>  [
                'query' =>  'Office.sql',
                'type'  =>  Office::class,
                'salesforce_name'   =>  'Office__c'
            ],
            'payroll'   =>  [
                'query' =>  'Payroll.sql',
                'type'  =>  Payroll::class,
                'salesforce_name'   =>  'Payroll__c'
            ]
        ];
    }
}