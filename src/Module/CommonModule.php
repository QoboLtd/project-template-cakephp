<?php
namespace App\Module;

use Qobo\Utils\Module\Module;

final class CommonModule extends Module
{
    protected $config = array (
);

    protected $migration = array (
);

    protected $lists = array (
  'units_area' => 
  array (
    'm' => 
    array (
      'label' => 'm²',
      'inactive' => false,
    ),
    'ft' => 
    array (
      'label' => 'ft²',
      'inactive' => false,
    ),
  ),
  'currencies' => 
  array (
    'EUR' => 
    array (
      'label' => 'EUR',
      'inactive' => false,
    ),
    'AFN' => 
    array (
      'label' => 'AFN',
      'inactive' => true,
    ),
    'AFA' => 
    array (
      'label' => 'AFA',
      'inactive' => true,
    ),
    'ALL' => 
    array (
      'label' => 'ALL',
      'inactive' => true,
    ),
    'ALK' => 
    array (
      'label' => 'ALK',
      'inactive' => true,
    ),
    'DZD' => 
    array (
      'label' => 'DZD',
      'inactive' => true,
    ),
    'ADP' => 
    array (
      'label' => 'ADP',
      'inactive' => true,
    ),
    'AOA' => 
    array (
      'label' => 'AOA',
      'inactive' => true,
    ),
    'AOK' => 
    array (
      'label' => 'AOK',
      'inactive' => true,
    ),
    'AON' => 
    array (
      'label' => 'AON',
      'inactive' => true,
    ),
    'AOR' => 
    array (
      'label' => 'AOR',
      'inactive' => true,
    ),
    'ARA' => 
    array (
      'label' => 'ARA',
      'inactive' => true,
    ),
    'ARS' => 
    array (
      'label' => 'ARS',
      'inactive' => true,
    ),
    'ARM' => 
    array (
      'label' => 'ARM',
      'inactive' => true,
    ),
    'ARP' => 
    array (
      'label' => 'ARP',
      'inactive' => true,
    ),
    'ARL' => 
    array (
      'label' => 'ARL',
      'inactive' => true,
    ),
    'AMD' => 
    array (
      'label' => 'AMD',
      'inactive' => true,
    ),
    'AWG' => 
    array (
      'label' => 'AWG',
      'inactive' => true,
    ),
    'AUD' => 
    array (
      'label' => 'AUD',
      'inactive' => true,
    ),
    'ATS' => 
    array (
      'label' => 'ATS',
      'inactive' => true,
    ),
    'AZN' => 
    array (
      'label' => 'AZN',
      'inactive' => true,
    ),
    'AZM' => 
    array (
      'label' => 'AZM',
      'inactive' => true,
    ),
    'BSD' => 
    array (
      'label' => 'BSD',
      'inactive' => true,
    ),
    'BHD' => 
    array (
      'label' => 'BHD',
      'inactive' => true,
    ),
    'BDT' => 
    array (
      'label' => 'BDT',
      'inactive' => true,
    ),
    'BBD' => 
    array (
      'label' => 'BBD',
      'inactive' => true,
    ),
    'BYB' => 
    array (
      'label' => 'BYB',
      'inactive' => true,
    ),
    'BYR' => 
    array (
      'label' => 'BYR',
      'inactive' => true,
    ),
    'BEF' => 
    array (
      'label' => 'BEF',
      'inactive' => true,
    ),
    'BEC' => 
    array (
      'label' => 'BEC',
      'inactive' => true,
    ),
    'BEL' => 
    array (
      'label' => 'BEL',
      'inactive' => true,
    ),
    'BZD' => 
    array (
      'label' => 'BZD',
      'inactive' => true,
    ),
    'BMD' => 
    array (
      'label' => 'BMD',
      'inactive' => true,
    ),
    'BTN' => 
    array (
      'label' => 'BTN',
      'inactive' => true,
    ),
    'BOB' => 
    array (
      'label' => 'BOB',
      'inactive' => true,
    ),
    'BOL' => 
    array (
      'label' => 'BOL',
      'inactive' => true,
    ),
    'BOV' => 
    array (
      'label' => 'BOV',
      'inactive' => true,
    ),
    'BOP' => 
    array (
      'label' => 'BOP',
      'inactive' => true,
    ),
    'BAM' => 
    array (
      'label' => 'BAM',
      'inactive' => true,
    ),
    'BAD' => 
    array (
      'label' => 'BAD',
      'inactive' => true,
    ),
    'BAN' => 
    array (
      'label' => 'BAN',
      'inactive' => true,
    ),
    'BWP' => 
    array (
      'label' => 'BWP',
      'inactive' => true,
    ),
    'BRC' => 
    array (
      'label' => 'BRC',
      'inactive' => true,
    ),
    'BRZ' => 
    array (
      'label' => 'BRZ',
      'inactive' => true,
    ),
    'BRE' => 
    array (
      'label' => 'BRE',
      'inactive' => true,
    ),
    'BRR' => 
    array (
      'label' => 'BRR',
      'inactive' => true,
    ),
    'BRN' => 
    array (
      'label' => 'BRN',
      'inactive' => true,
    ),
    'BRB' => 
    array (
      'label' => 'BRB',
      'inactive' => true,
    ),
    'BRL' => 
    array (
      'label' => 'BRL',
      'inactive' => true,
    ),
    'GBP' => 
    array (
      'label' => 'GBP',
      'inactive' => false,
    ),
    'BND' => 
    array (
      'label' => 'BND',
      'inactive' => true,
    ),
    'BGL' => 
    array (
      'label' => 'BGL',
      'inactive' => true,
    ),
    'BGN' => 
    array (
      'label' => 'BGN',
      'inactive' => true,
    ),
    'BGO' => 
    array (
      'label' => 'BGO',
      'inactive' => true,
    ),
    'BGM' => 
    array (
      'label' => 'BGM',
      'inactive' => true,
    ),
    'BUK' => 
    array (
      'label' => 'BUK',
      'inactive' => true,
    ),
    'BIF' => 
    array (
      'label' => 'BIF',
      'inactive' => true,
    ),
    'KHR' => 
    array (
      'label' => 'KHR',
      'inactive' => true,
    ),
    'CAD' => 
    array (
      'label' => 'CAD',
      'inactive' => true,
    ),
    'CVE' => 
    array (
      'label' => 'CVE',
      'inactive' => true,
    ),
    'KYD' => 
    array (
      'label' => 'KYD',
      'inactive' => true,
    ),
    'XOF' => 
    array (
      'label' => 'XOF',
      'inactive' => true,
    ),
    'XAF' => 
    array (
      'label' => 'XAF',
      'inactive' => true,
    ),
    'XPF' => 
    array (
      'label' => 'XPF',
      'inactive' => true,
    ),
    'CLE' => 
    array (
      'label' => 'CLE',
      'inactive' => true,
    ),
    'CLP' => 
    array (
      'label' => 'CLP',
      'inactive' => true,
    ),
    'CLF' => 
    array (
      'label' => 'CLF',
      'inactive' => true,
    ),
    'CNX' => 
    array (
      'label' => 'CNX',
      'inactive' => true,
    ),
    'CNY' => 
    array (
      'label' => 'CNY',
      'inactive' => true,
    ),
    'COP' => 
    array (
      'label' => 'COP',
      'inactive' => true,
    ),
    'COU' => 
    array (
      'label' => 'COU',
      'inactive' => true,
    ),
    'KMF' => 
    array (
      'label' => 'KMF',
      'inactive' => true,
    ),
    'CDF' => 
    array (
      'label' => 'CDF',
      'inactive' => true,
    ),
    'CRC' => 
    array (
      'label' => 'CRC',
      'inactive' => true,
    ),
    'HRD' => 
    array (
      'label' => 'HRD',
      'inactive' => true,
    ),
    'HRK' => 
    array (
      'label' => 'HRK',
      'inactive' => true,
    ),
    'CUC' => 
    array (
      'label' => 'CUC',
      'inactive' => true,
    ),
    'CUP' => 
    array (
      'label' => 'CUP',
      'inactive' => true,
    ),
    'CYP' => 
    array (
      'label' => 'CYP',
      'inactive' => true,
    ),
    'CZK' => 
    array (
      'label' => 'CZK',
      'inactive' => true,
    ),
    'CSK' => 
    array (
      'label' => 'CSK',
      'inactive' => true,
    ),
    'DKK' => 
    array (
      'label' => 'DKK',
      'inactive' => true,
    ),
    'DJF' => 
    array (
      'label' => 'DJF',
      'inactive' => true,
    ),
    'DOP' => 
    array (
      'label' => 'DOP',
      'inactive' => true,
    ),
    'NLG' => 
    array (
      'label' => 'NLG',
      'inactive' => true,
    ),
    'XCD' => 
    array (
      'label' => 'XCD',
      'inactive' => true,
    ),
    'DDM' => 
    array (
      'label' => 'DDM',
      'inactive' => true,
    ),
    'ECS' => 
    array (
      'label' => 'ECS',
      'inactive' => true,
    ),
    'ECV' => 
    array (
      'label' => 'ECV',
      'inactive' => true,
    ),
    'EGP' => 
    array (
      'label' => 'EGP',
      'inactive' => true,
    ),
    'GQE' => 
    array (
      'label' => 'GQE',
      'inactive' => true,
    ),
    'ERN' => 
    array (
      'label' => 'ERN',
      'inactive' => true,
    ),
    'EEK' => 
    array (
      'label' => 'EEK',
      'inactive' => true,
    ),
    'ETB' => 
    array (
      'label' => 'ETB',
      'inactive' => true,
    ),
    'XEU' => 
    array (
      'label' => 'XEU',
      'inactive' => true,
    ),
    'FKP' => 
    array (
      'label' => 'FKP',
      'inactive' => true,
    ),
    'FJD' => 
    array (
      'label' => 'FJD',
      'inactive' => true,
    ),
    'FIM' => 
    array (
      'label' => 'FIM',
      'inactive' => true,
    ),
    'FRF' => 
    array (
      'label' => 'FRF',
      'inactive' => true,
    ),
    'XFO' => 
    array (
      'label' => 'XFO',
      'inactive' => true,
    ),
    'XFU' => 
    array (
      'label' => 'XFU',
      'inactive' => true,
    ),
    'GMD' => 
    array (
      'label' => 'GMD',
      'inactive' => true,
    ),
    'GEK' => 
    array (
      'label' => 'GEK',
      'inactive' => true,
    ),
    'GEL' => 
    array (
      'label' => 'GEL',
      'inactive' => true,
    ),
    'DEM' => 
    array (
      'label' => 'DEM',
      'inactive' => true,
    ),
    'GHS' => 
    array (
      'label' => 'GHS',
      'inactive' => true,
    ),
    'GHC' => 
    array (
      'label' => 'GHC',
      'inactive' => true,
    ),
    'GIP' => 
    array (
      'label' => 'GIP',
      'inactive' => true,
    ),
    'GRD' => 
    array (
      'label' => 'GRD',
      'inactive' => true,
    ),
    'GTQ' => 
    array (
      'label' => 'GTQ',
      'inactive' => true,
    ),
    'GWP' => 
    array (
      'label' => 'GWP',
      'inactive' => true,
    ),
    'GNF' => 
    array (
      'label' => 'GNF',
      'inactive' => true,
    ),
    'GNS' => 
    array (
      'label' => 'GNS',
      'inactive' => true,
    ),
    'GYD' => 
    array (
      'label' => 'GYD',
      'inactive' => true,
    ),
    'HTG' => 
    array (
      'label' => 'HTG',
      'inactive' => true,
    ),
    'HNL' => 
    array (
      'label' => 'HNL',
      'inactive' => true,
    ),
    'HKD' => 
    array (
      'label' => 'HKD',
      'inactive' => true,
    ),
    'HUF' => 
    array (
      'label' => 'HUF',
      'inactive' => true,
    ),
    'ISK' => 
    array (
      'label' => 'ISK',
      'inactive' => true,
    ),
    'ISJ' => 
    array (
      'label' => 'ISJ',
      'inactive' => true,
    ),
    'INR' => 
    array (
      'label' => 'INR',
      'inactive' => true,
    ),
    'IDR' => 
    array (
      'label' => 'IDR',
      'inactive' => true,
    ),
    'IRR' => 
    array (
      'label' => 'IRR',
      'inactive' => true,
    ),
    'IQD' => 
    array (
      'label' => 'IQD',
      'inactive' => true,
    ),
    'IEP' => 
    array (
      'label' => 'IEP',
      'inactive' => true,
    ),
    'ILS' => 
    array (
      'label' => 'ILS',
      'inactive' => true,
    ),
    'ILP' => 
    array (
      'label' => 'ILP',
      'inactive' => true,
    ),
    'ILR' => 
    array (
      'label' => 'ILR',
      'inactive' => true,
    ),
    'ITL' => 
    array (
      'label' => 'ITL',
      'inactive' => true,
    ),
    'JMD' => 
    array (
      'label' => 'JMD',
      'inactive' => true,
    ),
    'JPY' => 
    array (
      'label' => 'JPY',
      'inactive' => true,
    ),
    'JOD' => 
    array (
      'label' => 'JOD',
      'inactive' => true,
    ),
    'KZT' => 
    array (
      'label' => 'KZT',
      'inactive' => true,
    ),
    'KES' => 
    array (
      'label' => 'KES',
      'inactive' => true,
    ),
    'KWD' => 
    array (
      'label' => 'KWD',
      'inactive' => true,
    ),
    'KGS' => 
    array (
      'label' => 'KGS',
      'inactive' => true,
    ),
    'LAK' => 
    array (
      'label' => 'LAK',
      'inactive' => true,
    ),
    'LVL' => 
    array (
      'label' => 'LVL',
      'inactive' => true,
    ),
    'LVR' => 
    array (
      'label' => 'LVR',
      'inactive' => true,
    ),
    'LBP' => 
    array (
      'label' => 'LBP',
      'inactive' => true,
    ),
    'LSL' => 
    array (
      'label' => 'LSL',
      'inactive' => true,
    ),
    'LRD' => 
    array (
      'label' => 'LRD',
      'inactive' => true,
    ),
    'LYD' => 
    array (
      'label' => 'LYD',
      'inactive' => true,
    ),
    'LTL' => 
    array (
      'label' => 'LTL',
      'inactive' => true,
    ),
    'LTT' => 
    array (
      'label' => 'LTT',
      'inactive' => true,
    ),
    'LUL' => 
    array (
      'label' => 'LUL',
      'inactive' => true,
    ),
    'LUC' => 
    array (
      'label' => 'LUC',
      'inactive' => true,
    ),
    'LUF' => 
    array (
      'label' => 'LUF',
      'inactive' => true,
    ),
    'MOP' => 
    array (
      'label' => 'MOP',
      'inactive' => true,
    ),
    'MKD' => 
    array (
      'label' => 'MKD',
      'inactive' => true,
    ),
    'MKN' => 
    array (
      'label' => 'MKN',
      'inactive' => true,
    ),
    'MGA' => 
    array (
      'label' => 'MGA',
      'inactive' => true,
    ),
    'MGF' => 
    array (
      'label' => 'MGF',
      'inactive' => true,
    ),
    'MWK' => 
    array (
      'label' => 'MWK',
      'inactive' => true,
    ),
    'MYR' => 
    array (
      'label' => 'MYR',
      'inactive' => true,
    ),
    'MVR' => 
    array (
      'label' => 'MVR',
      'inactive' => true,
    ),
    'MVP' => 
    array (
      'label' => 'MVP',
      'inactive' => true,
    ),
    'MLF' => 
    array (
      'label' => 'MLF',
      'inactive' => true,
    ),
    'MTL' => 
    array (
      'label' => 'MTL',
      'inactive' => true,
    ),
    'MTP' => 
    array (
      'label' => 'MTP',
      'inactive' => true,
    ),
    'MRO' => 
    array (
      'label' => 'MRO',
      'inactive' => true,
    ),
    'MUR' => 
    array (
      'label' => 'MUR',
      'inactive' => true,
    ),
    'MXV' => 
    array (
      'label' => 'MXV',
      'inactive' => true,
    ),
    'MXN' => 
    array (
      'label' => 'MXN',
      'inactive' => true,
    ),
    'MXP' => 
    array (
      'label' => 'MXP',
      'inactive' => true,
    ),
    'MDC' => 
    array (
      'label' => 'MDC',
      'inactive' => true,
    ),
    'MDL' => 
    array (
      'label' => 'MDL',
      'inactive' => true,
    ),
    'MCF' => 
    array (
      'label' => 'MCF',
      'inactive' => true,
    ),
    'MNT' => 
    array (
      'label' => 'MNT',
      'inactive' => true,
    ),
    'MAD' => 
    array (
      'label' => 'MAD',
      'inactive' => true,
    ),
    'MAF' => 
    array (
      'label' => 'MAF',
      'inactive' => true,
    ),
    'MZE' => 
    array (
      'label' => 'MZE',
      'inactive' => true,
    ),
    'MZN' => 
    array (
      'label' => 'MZN',
      'inactive' => true,
    ),
    'MZM' => 
    array (
      'label' => 'MZM',
      'inactive' => true,
    ),
    'MMK' => 
    array (
      'label' => 'MMK',
      'inactive' => true,
    ),
    'NAD' => 
    array (
      'label' => 'NAD',
      'inactive' => true,
    ),
    'NPR' => 
    array (
      'label' => 'NPR',
      'inactive' => true,
    ),
    'ANG' => 
    array (
      'label' => 'ANG',
      'inactive' => true,
    ),
    'TWD' => 
    array (
      'label' => 'TWD',
      'inactive' => true,
    ),
    'NZD' => 
    array (
      'label' => 'NZD',
      'inactive' => true,
    ),
    'NIO' => 
    array (
      'label' => 'NIO',
      'inactive' => true,
    ),
    'NIC' => 
    array (
      'label' => 'NIC',
      'inactive' => true,
    ),
    'NGN' => 
    array (
      'label' => 'NGN',
      'inactive' => true,
    ),
    'KPW' => 
    array (
      'label' => 'KPW',
      'inactive' => true,
    ),
    'NOK' => 
    array (
      'label' => 'NOK',
      'inactive' => true,
    ),
    'OMR' => 
    array (
      'label' => 'OMR',
      'inactive' => true,
    ),
    'PKR' => 
    array (
      'label' => 'PKR',
      'inactive' => true,
    ),
    'PAB' => 
    array (
      'label' => 'PAB',
      'inactive' => true,
    ),
    'PGK' => 
    array (
      'label' => 'PGK',
      'inactive' => true,
    ),
    'PYG' => 
    array (
      'label' => 'PYG',
      'inactive' => true,
    ),
    'PEI' => 
    array (
      'label' => 'PEI',
      'inactive' => true,
    ),
    'PEN' => 
    array (
      'label' => 'PEN',
      'inactive' => true,
    ),
    'PES' => 
    array (
      'label' => 'PES',
      'inactive' => true,
    ),
    'PHP' => 
    array (
      'label' => 'PHP',
      'inactive' => true,
    ),
    'PLN' => 
    array (
      'label' => 'PLN',
      'inactive' => true,
    ),
    'PLZ' => 
    array (
      'label' => 'PLZ',
      'inactive' => true,
    ),
    'PTE' => 
    array (
      'label' => 'PTE',
      'inactive' => true,
    ),
    'GWE' => 
    array (
      'label' => 'GWE',
      'inactive' => true,
    ),
    'QAR' => 
    array (
      'label' => 'QAR',
      'inactive' => true,
    ),
    'RHD' => 
    array (
      'label' => 'RHD',
      'inactive' => true,
    ),
    'XRE' => 
    array (
      'label' => 'XRE',
      'inactive' => true,
    ),
    'RON' => 
    array (
      'label' => 'RON',
      'inactive' => true,
    ),
    'ROL' => 
    array (
      'label' => 'ROL',
      'inactive' => true,
    ),
    'RUB' => 
    array (
      'label' => 'RUB',
      'inactive' => true,
    ),
    'RUR' => 
    array (
      'label' => 'RUR',
      'inactive' => true,
    ),
    'RWF' => 
    array (
      'label' => 'RWF',
      'inactive' => true,
    ),
    'SVC' => 
    array (
      'label' => 'SVC',
      'inactive' => true,
    ),
    'WST' => 
    array (
      'label' => 'WST',
      'inactive' => true,
    ),
    'STD' => 
    array (
      'label' => 'STD',
      'inactive' => true,
    ),
    'SAR' => 
    array (
      'label' => 'SAR',
      'inactive' => true,
    ),
    'RSD' => 
    array (
      'label' => 'RSD',
      'inactive' => true,
    ),
    'CSD' => 
    array (
      'label' => 'CSD',
      'inactive' => true,
    ),
    'SCR' => 
    array (
      'label' => 'SCR',
      'inactive' => true,
    ),
    'SLL' => 
    array (
      'label' => 'SLL',
      'inactive' => true,
    ),
    'SGD' => 
    array (
      'label' => 'SGD',
      'inactive' => true,
    ),
    'SKK' => 
    array (
      'label' => 'SKK',
      'inactive' => true,
    ),
    'SIT' => 
    array (
      'label' => 'SIT',
      'inactive' => true,
    ),
    'SBD' => 
    array (
      'label' => 'SBD',
      'inactive' => true,
    ),
    'SOS' => 
    array (
      'label' => 'SOS',
      'inactive' => true,
    ),
    'ZAR' => 
    array (
      'label' => 'ZAR',
      'inactive' => true,
    ),
    'ZAL' => 
    array (
      'label' => 'ZAL',
      'inactive' => true,
    ),
    'KRH' => 
    array (
      'label' => 'KRH',
      'inactive' => true,
    ),
    'KRW' => 
    array (
      'label' => 'KRW',
      'inactive' => true,
    ),
    'KRO' => 
    array (
      'label' => 'KRO',
      'inactive' => true,
    ),
    'SSP' => 
    array (
      'label' => 'SSP',
      'inactive' => true,
    ),
    'SUR' => 
    array (
      'label' => 'SUR',
      'inactive' => true,
    ),
    'ESP' => 
    array (
      'label' => 'ESP',
      'inactive' => true,
    ),
    'ESA' => 
    array (
      'label' => 'ESA',
      'inactive' => true,
    ),
    'ESB' => 
    array (
      'label' => 'ESB',
      'inactive' => true,
    ),
    'LKR' => 
    array (
      'label' => 'LKR',
      'inactive' => true,
    ),
    'SHP' => 
    array (
      'label' => 'SHP',
      'inactive' => true,
    ),
    'SDD' => 
    array (
      'label' => 'SDD',
      'inactive' => true,
    ),
    'SDG' => 
    array (
      'label' => 'SDG',
      'inactive' => true,
    ),
    'SDP' => 
    array (
      'label' => 'SDP',
      'inactive' => true,
    ),
    'SRD' => 
    array (
      'label' => 'SRD',
      'inactive' => true,
    ),
    'SRG' => 
    array (
      'label' => 'SRG',
      'inactive' => true,
    ),
    'SZL' => 
    array (
      'label' => 'SZL',
      'inactive' => true,
    ),
    'SEK' => 
    array (
      'label' => 'SEK',
      'inactive' => true,
    ),
    'CHF' => 
    array (
      'label' => 'CHF',
      'inactive' => true,
    ),
    'SYP' => 
    array (
      'label' => 'SYP',
      'inactive' => true,
    ),
    'TJR' => 
    array (
      'label' => 'TJR',
      'inactive' => true,
    ),
    'TJS' => 
    array (
      'label' => 'TJS',
      'inactive' => true,
    ),
    'TZS' => 
    array (
      'label' => 'TZS',
      'inactive' => true,
    ),
    'THB' => 
    array (
      'label' => 'THB',
      'inactive' => true,
    ),
    'TPE' => 
    array (
      'label' => 'TPE',
      'inactive' => true,
    ),
    'TOP' => 
    array (
      'label' => 'TOP',
      'inactive' => true,
    ),
    'TTD' => 
    array (
      'label' => 'TTD',
      'inactive' => true,
    ),
    'TND' => 
    array (
      'label' => 'TND',
      'inactive' => true,
    ),
    'TRY' => 
    array (
      'label' => 'TRY',
      'inactive' => true,
    ),
    'TRL' => 
    array (
      'label' => 'TRL',
      'inactive' => true,
    ),
    'TMT' => 
    array (
      'label' => 'TMT',
      'inactive' => true,
    ),
    'TMM' => 
    array (
      'label' => 'TMM',
      'inactive' => true,
    ),
    'UGX' => 
    array (
      'label' => 'UGX',
      'inactive' => true,
    ),
    'UGS' => 
    array (
      'label' => 'UGS',
      'inactive' => true,
    ),
    'UAH' => 
    array (
      'label' => 'UAH',
      'inactive' => true,
    ),
    'UAK' => 
    array (
      'label' => 'UAK',
      'inactive' => true,
    ),
    'AED' => 
    array (
      'label' => 'AED',
      'inactive' => true,
    ),
    'UYU' => 
    array (
      'label' => 'UYU',
      'inactive' => true,
    ),
    'UYP' => 
    array (
      'label' => 'UYP',
      'inactive' => true,
    ),
    'UYI' => 
    array (
      'label' => 'UYI',
      'inactive' => true,
    ),
    'USD' => 
    array (
      'label' => 'USD',
      'inactive' => false,
    ),
    'USN' => 
    array (
      'label' => 'USN',
      'inactive' => true,
    ),
    'USS' => 
    array (
      'label' => 'USS',
      'inactive' => true,
    ),
    'UZS' => 
    array (
      'label' => 'UZS',
      'inactive' => true,
    ),
    'VUV' => 
    array (
      'label' => 'VUV',
      'inactive' => true,
    ),
    'VEF' => 
    array (
      'label' => 'VEF',
      'inactive' => true,
    ),
    'VEB' => 
    array (
      'label' => 'VEB',
      'inactive' => true,
    ),
    'VND' => 
    array (
      'label' => 'VND',
      'inactive' => true,
    ),
    'VNN' => 
    array (
      'label' => 'VNN',
      'inactive' => true,
    ),
    'CHE' => 
    array (
      'label' => 'CHE',
      'inactive' => true,
    ),
    'CHW' => 
    array (
      'label' => 'CHW',
      'inactive' => true,
    ),
    'YDD' => 
    array (
      'label' => 'YDD',
      'inactive' => true,
    ),
    'YER' => 
    array (
      'label' => 'YER',
      'inactive' => true,
    ),
    'YUN' => 
    array (
      'label' => 'YUN',
      'inactive' => true,
    ),
    'YUD' => 
    array (
      'label' => 'YUD',
      'inactive' => true,
    ),
    'YUM' => 
    array (
      'label' => 'YUM',
      'inactive' => true,
    ),
    'YUR' => 
    array (
      'label' => 'YUR',
      'inactive' => true,
    ),
    'ZRN' => 
    array (
      'label' => 'ZRN',
      'inactive' => true,
    ),
    'ZRZ' => 
    array (
      'label' => 'ZRZ',
      'inactive' => true,
    ),
    'ZMW' => 
    array (
      'label' => 'ZMW',
      'inactive' => true,
    ),
    'ZMK' => 
    array (
      'label' => 'ZMK',
      'inactive' => true,
    ),
    'ZWD' => 
    array (
      'label' => 'ZWD',
      'inactive' => true,
    ),
    'ZWR' => 
    array (
      'label' => 'ZWR',
      'inactive' => true,
    ),
    'ZWL' => 
    array (
      'label' => 'ZWL',
      'inactive' => true,
    ),
  ),
  'genders' => 
  array (
    'm' => 
    array (
      'label' => 'Male',
      'inactive' => false,
    ),
    'f' => 
    array (
      'label' => 'Female',
      'inactive' => false,
    ),
  ),
  'countries' => 
  array (
    'AF' => 
    array (
      'label' => 'Afghanistan',
      'inactive' => false,
    ),
    'AX' => 
    array (
      'label' => 'Åland Islands',
      'inactive' => false,
    ),
    'AL' => 
    array (
      'label' => 'Albania',
      'inactive' => false,
    ),
    'DZ' => 
    array (
      'label' => 'Algeria',
      'inactive' => false,
    ),
    'AS' => 
    array (
      'label' => 'American Samoa',
      'inactive' => false,
    ),
    'AD' => 
    array (
      'label' => 'Andorra',
      'inactive' => false,
    ),
    'AO' => 
    array (
      'label' => 'Angola',
      'inactive' => false,
    ),
    'AI' => 
    array (
      'label' => 'Anguilla',
      'inactive' => false,
    ),
    'AQ' => 
    array (
      'label' => 'Antarctica',
      'inactive' => false,
    ),
    'AG' => 
    array (
      'label' => 'Antigua & Barbuda',
      'inactive' => false,
    ),
    'AR' => 
    array (
      'label' => 'Argentina',
      'inactive' => false,
    ),
    'AM' => 
    array (
      'label' => 'Armenia',
      'inactive' => false,
    ),
    'AW' => 
    array (
      'label' => 'Aruba',
      'inactive' => false,
    ),
    'AC' => 
    array (
      'label' => 'Ascension Island',
      'inactive' => false,
    ),
    'AU' => 
    array (
      'label' => 'Australia',
      'inactive' => false,
    ),
    'AT' => 
    array (
      'label' => 'Austria',
      'inactive' => false,
    ),
    'AZ' => 
    array (
      'label' => 'Azerbaijan',
      'inactive' => false,
    ),
    'BS' => 
    array (
      'label' => 'Bahamas',
      'inactive' => false,
    ),
    'BH' => 
    array (
      'label' => 'Bahrain',
      'inactive' => false,
    ),
    'BD' => 
    array (
      'label' => 'Bangladesh',
      'inactive' => false,
    ),
    'BB' => 
    array (
      'label' => 'Barbados',
      'inactive' => false,
    ),
    'BY' => 
    array (
      'label' => 'Belarus',
      'inactive' => false,
    ),
    'BE' => 
    array (
      'label' => 'Belgium',
      'inactive' => false,
    ),
    'BZ' => 
    array (
      'label' => 'Belize',
      'inactive' => false,
    ),
    'BJ' => 
    array (
      'label' => 'Benin',
      'inactive' => false,
    ),
    'BM' => 
    array (
      'label' => 'Bermuda',
      'inactive' => false,
    ),
    'BT' => 
    array (
      'label' => 'Bhutan',
      'inactive' => false,
    ),
    'BO' => 
    array (
      'label' => 'Bolivia',
      'inactive' => false,
    ),
    'BA' => 
    array (
      'label' => 'Bosnia & Herzegovina',
      'inactive' => false,
    ),
    'BW' => 
    array (
      'label' => 'Botswana',
      'inactive' => false,
    ),
    'BR' => 
    array (
      'label' => 'Brazil',
      'inactive' => false,
    ),
    'IO' => 
    array (
      'label' => 'British Indian Ocean Territory',
      'inactive' => false,
    ),
    'VG' => 
    array (
      'label' => 'British Virgin Islands',
      'inactive' => false,
    ),
    'BN' => 
    array (
      'label' => 'Brunei',
      'inactive' => false,
    ),
    'BG' => 
    array (
      'label' => 'Bulgaria',
      'inactive' => false,
    ),
    'BF' => 
    array (
      'label' => 'Burkina Faso',
      'inactive' => false,
    ),
    'BI' => 
    array (
      'label' => 'Burundi',
      'inactive' => false,
    ),
    'KH' => 
    array (
      'label' => 'Cambodia',
      'inactive' => false,
    ),
    'CM' => 
    array (
      'label' => 'Cameroon',
      'inactive' => false,
    ),
    'CA' => 
    array (
      'label' => 'Canada',
      'inactive' => false,
    ),
    'IC' => 
    array (
      'label' => 'Canary Islands',
      'inactive' => false,
    ),
    'CV' => 
    array (
      'label' => 'Cape Verde',
      'inactive' => false,
    ),
    'BQ' => 
    array (
      'label' => 'Caribbean Netherlands',
      'inactive' => false,
    ),
    'KY' => 
    array (
      'label' => 'Cayman Islands',
      'inactive' => false,
    ),
    'CF' => 
    array (
      'label' => 'Central African Republic',
      'inactive' => false,
    ),
    'EA' => 
    array (
      'label' => 'Ceuta & Melilla',
      'inactive' => false,
    ),
    'TD' => 
    array (
      'label' => 'Chad',
      'inactive' => false,
    ),
    'CL' => 
    array (
      'label' => 'Chile',
      'inactive' => false,
    ),
    'CN' => 
    array (
      'label' => 'China',
      'inactive' => false,
    ),
    'CX' => 
    array (
      'label' => 'Christmas Island',
      'inactive' => false,
    ),
    'CC' => 
    array (
      'label' => 'Cocos (Keeling) Islands',
      'inactive' => false,
    ),
    'CO' => 
    array (
      'label' => 'Colombia',
      'inactive' => false,
    ),
    'KM' => 
    array (
      'label' => 'Comoros',
      'inactive' => false,
    ),
    'CG' => 
    array (
      'label' => 'Congo - Brazzaville',
      'inactive' => false,
    ),
    'CD' => 
    array (
      'label' => 'Congo - Kinshasa',
      'inactive' => false,
    ),
    'CK' => 
    array (
      'label' => 'Cook Islands',
      'inactive' => false,
    ),
    'CR' => 
    array (
      'label' => 'Costa Rica',
      'inactive' => false,
    ),
    'CI' => 
    array (
      'label' => 'Côte d’Ivoire',
      'inactive' => false,
    ),
    'HR' => 
    array (
      'label' => 'Croatia',
      'inactive' => false,
    ),
    'CU' => 
    array (
      'label' => 'Cuba',
      'inactive' => false,
    ),
    'CW' => 
    array (
      'label' => 'Curaçao',
      'inactive' => false,
    ),
    'CY' => 
    array (
      'label' => 'Cyprus',
      'inactive' => false,
    ),
    'CZ' => 
    array (
      'label' => 'Czech Republic',
      'inactive' => false,
    ),
    'DK' => 
    array (
      'label' => 'Denmark',
      'inactive' => false,
    ),
    'DG' => 
    array (
      'label' => 'Diego Garcia',
      'inactive' => false,
    ),
    'DJ' => 
    array (
      'label' => 'Djibouti',
      'inactive' => false,
    ),
    'DM' => 
    array (
      'label' => 'Dominica',
      'inactive' => false,
    ),
    'DO' => 
    array (
      'label' => 'Dominican Republic',
      'inactive' => false,
    ),
    'EC' => 
    array (
      'label' => 'Ecuador',
      'inactive' => false,
    ),
    'EG' => 
    array (
      'label' => 'Egypt',
      'inactive' => false,
    ),
    'SV' => 
    array (
      'label' => 'El Salvador',
      'inactive' => false,
    ),
    'GQ' => 
    array (
      'label' => 'Equatorial Guinea',
      'inactive' => false,
    ),
    'ER' => 
    array (
      'label' => 'Eritrea',
      'inactive' => false,
    ),
    'EE' => 
    array (
      'label' => 'Estonia',
      'inactive' => false,
    ),
    'ET' => 
    array (
      'label' => 'Ethiopia',
      'inactive' => false,
    ),
    'FK' => 
    array (
      'label' => 'Falkland Islands',
      'inactive' => false,
    ),
    'FO' => 
    array (
      'label' => 'Faroe Islands',
      'inactive' => false,
    ),
    'FJ' => 
    array (
      'label' => 'Fiji',
      'inactive' => false,
    ),
    'FI' => 
    array (
      'label' => 'Finland',
      'inactive' => false,
    ),
    'FR' => 
    array (
      'label' => 'France',
      'inactive' => false,
    ),
    'GF' => 
    array (
      'label' => 'French Guiana',
      'inactive' => false,
    ),
    'PF' => 
    array (
      'label' => 'French Polynesia',
      'inactive' => false,
    ),
    'TF' => 
    array (
      'label' => 'French Southern Territories',
      'inactive' => false,
    ),
    'GA' => 
    array (
      'label' => 'Gabon',
      'inactive' => false,
    ),
    'GM' => 
    array (
      'label' => 'Gambia',
      'inactive' => false,
    ),
    'GE' => 
    array (
      'label' => 'Georgia',
      'inactive' => false,
    ),
    'DE' => 
    array (
      'label' => 'Germany',
      'inactive' => false,
    ),
    'GH' => 
    array (
      'label' => 'Ghana',
      'inactive' => false,
    ),
    'GI' => 
    array (
      'label' => 'Gibraltar',
      'inactive' => false,
    ),
    'GR' => 
    array (
      'label' => 'Greece',
      'inactive' => false,
    ),
    'GL' => 
    array (
      'label' => 'Greenland',
      'inactive' => false,
    ),
    'GD' => 
    array (
      'label' => 'Grenada',
      'inactive' => false,
    ),
    'GP' => 
    array (
      'label' => 'Guadeloupe',
      'inactive' => false,
    ),
    'GU' => 
    array (
      'label' => 'Guam',
      'inactive' => false,
    ),
    'GT' => 
    array (
      'label' => 'Guatemala',
      'inactive' => false,
    ),
    'GG' => 
    array (
      'label' => 'Guernsey',
      'inactive' => false,
    ),
    'GN' => 
    array (
      'label' => 'Guinea',
      'inactive' => false,
    ),
    'GW' => 
    array (
      'label' => 'Guinea-Bissau',
      'inactive' => false,
    ),
    'GY' => 
    array (
      'label' => 'Guyana',
      'inactive' => false,
    ),
    'HT' => 
    array (
      'label' => 'Haiti',
      'inactive' => false,
    ),
    'HN' => 
    array (
      'label' => 'Honduras',
      'inactive' => false,
    ),
    'HK' => 
    array (
      'label' => 'Hong Kong SAR China',
      'inactive' => false,
    ),
    'HU' => 
    array (
      'label' => 'Hungary',
      'inactive' => false,
    ),
    'IS' => 
    array (
      'label' => 'Iceland',
      'inactive' => false,
    ),
    'IN' => 
    array (
      'label' => 'India',
      'inactive' => false,
    ),
    'ID' => 
    array (
      'label' => 'Indonesia',
      'inactive' => false,
    ),
    'IR' => 
    array (
      'label' => 'Iran',
      'inactive' => false,
    ),
    'IQ' => 
    array (
      'label' => 'Iraq',
      'inactive' => false,
    ),
    'IE' => 
    array (
      'label' => 'Ireland',
      'inactive' => false,
    ),
    'IM' => 
    array (
      'label' => 'Isle of Man',
      'inactive' => false,
    ),
    'IL' => 
    array (
      'label' => 'Israel',
      'inactive' => false,
    ),
    'IT' => 
    array (
      'label' => 'Italy',
      'inactive' => false,
    ),
    'JM' => 
    array (
      'label' => 'Jamaica',
      'inactive' => false,
    ),
    'JP' => 
    array (
      'label' => 'Japan',
      'inactive' => false,
    ),
    'JE' => 
    array (
      'label' => 'Jersey',
      'inactive' => false,
    ),
    'JO' => 
    array (
      'label' => 'Jordan',
      'inactive' => false,
    ),
    'KZ' => 
    array (
      'label' => 'Kazakhstan',
      'inactive' => false,
    ),
    'KE' => 
    array (
      'label' => 'Kenya',
      'inactive' => false,
    ),
    'KI' => 
    array (
      'label' => 'Kiribati',
      'inactive' => false,
    ),
    'XK' => 
    array (
      'label' => 'Kosovo',
      'inactive' => false,
    ),
    'KW' => 
    array (
      'label' => 'Kuwait',
      'inactive' => false,
    ),
    'KG' => 
    array (
      'label' => 'Kyrgyzstan',
      'inactive' => false,
    ),
    'LA' => 
    array (
      'label' => 'Laos',
      'inactive' => false,
    ),
    'LV' => 
    array (
      'label' => 'Latvia',
      'inactive' => false,
    ),
    'LB' => 
    array (
      'label' => 'Lebanon',
      'inactive' => false,
    ),
    'LS' => 
    array (
      'label' => 'Lesotho',
      'inactive' => false,
    ),
    'LR' => 
    array (
      'label' => 'Liberia',
      'inactive' => false,
    ),
    'LY' => 
    array (
      'label' => 'Libya',
      'inactive' => false,
    ),
    'LI' => 
    array (
      'label' => 'Liechtenstein',
      'inactive' => false,
    ),
    'LT' => 
    array (
      'label' => 'Lithuania',
      'inactive' => false,
    ),
    'LU' => 
    array (
      'label' => 'Luxembourg',
      'inactive' => false,
    ),
    'MO' => 
    array (
      'label' => 'Macau SAR China',
      'inactive' => false,
    ),
    'MK' => 
    array (
      'label' => 'Macedonia',
      'inactive' => false,
    ),
    'MG' => 
    array (
      'label' => 'Madagascar',
      'inactive' => false,
    ),
    'MW' => 
    array (
      'label' => 'Malawi',
      'inactive' => false,
    ),
    'MY' => 
    array (
      'label' => 'Malaysia',
      'inactive' => false,
    ),
    'MV' => 
    array (
      'label' => 'Maldives',
      'inactive' => false,
    ),
    'ML' => 
    array (
      'label' => 'Mali',
      'inactive' => false,
    ),
    'MT' => 
    array (
      'label' => 'Malta',
      'inactive' => false,
    ),
    'MH' => 
    array (
      'label' => 'Marshall Islands',
      'inactive' => false,
    ),
    'MQ' => 
    array (
      'label' => 'Martinique',
      'inactive' => false,
    ),
    'MR' => 
    array (
      'label' => 'Mauritania',
      'inactive' => false,
    ),
    'MU' => 
    array (
      'label' => 'Mauritius',
      'inactive' => false,
    ),
    'YT' => 
    array (
      'label' => 'Mayotte',
      'inactive' => false,
    ),
    'MX' => 
    array (
      'label' => 'Mexico',
      'inactive' => false,
    ),
    'FM' => 
    array (
      'label' => 'Micronesia',
      'inactive' => false,
    ),
    'MD' => 
    array (
      'label' => 'Moldova',
      'inactive' => false,
    ),
    'MC' => 
    array (
      'label' => 'Monaco',
      'inactive' => false,
    ),
    'MN' => 
    array (
      'label' => 'Mongolia',
      'inactive' => false,
    ),
    'ME' => 
    array (
      'label' => 'Montenegro',
      'inactive' => false,
    ),
    'MS' => 
    array (
      'label' => 'Montserrat',
      'inactive' => false,
    ),
    'MA' => 
    array (
      'label' => 'Morocco',
      'inactive' => false,
    ),
    'MZ' => 
    array (
      'label' => 'Mozambique',
      'inactive' => false,
    ),
    'MM' => 
    array (
      'label' => 'Myanmar (Burma)',
      'inactive' => false,
    ),
    'NA' => 
    array (
      'label' => 'Namibia',
      'inactive' => false,
    ),
    'NR' => 
    array (
      'label' => 'Nauru',
      'inactive' => false,
    ),
    'NP' => 
    array (
      'label' => 'Nepal',
      'inactive' => false,
    ),
    'NL' => 
    array (
      'label' => 'Netherlands',
      'inactive' => false,
    ),
    'NC' => 
    array (
      'label' => 'New Caledonia',
      'inactive' => false,
    ),
    'NZ' => 
    array (
      'label' => 'New Zealand',
      'inactive' => false,
    ),
    'NI' => 
    array (
      'label' => 'Nicaragua',
      'inactive' => false,
    ),
    'NE' => 
    array (
      'label' => 'Niger',
      'inactive' => false,
    ),
    'NG' => 
    array (
      'label' => 'Nigeria',
      'inactive' => false,
    ),
    'NU' => 
    array (
      'label' => 'Niue',
      'inactive' => false,
    ),
    'NF' => 
    array (
      'label' => 'Norfolk Island',
      'inactive' => false,
    ),
    'KP' => 
    array (
      'label' => 'North Korea',
      'inactive' => false,
    ),
    'MP' => 
    array (
      'label' => 'Northern Mariana Islands',
      'inactive' => false,
    ),
    'NO' => 
    array (
      'label' => 'Norway',
      'inactive' => false,
    ),
    'OM' => 
    array (
      'label' => 'Oman',
      'inactive' => false,
    ),
    'PK' => 
    array (
      'label' => 'Pakistan',
      'inactive' => false,
    ),
    'PW' => 
    array (
      'label' => 'Palau',
      'inactive' => false,
    ),
    'PS' => 
    array (
      'label' => 'Palestinian Territories',
      'inactive' => false,
    ),
    'PA' => 
    array (
      'label' => 'Panama',
      'inactive' => false,
    ),
    'PG' => 
    array (
      'label' => 'Papua New Guinea',
      'inactive' => false,
    ),
    'PY' => 
    array (
      'label' => 'Paraguay',
      'inactive' => false,
    ),
    'PE' => 
    array (
      'label' => 'Peru',
      'inactive' => false,
    ),
    'PH' => 
    array (
      'label' => 'Philippines',
      'inactive' => false,
    ),
    'PN' => 
    array (
      'label' => 'Pitcairn Islands',
      'inactive' => false,
    ),
    'PL' => 
    array (
      'label' => 'Poland',
      'inactive' => false,
    ),
    'PT' => 
    array (
      'label' => 'Portugal',
      'inactive' => false,
    ),
    'PR' => 
    array (
      'label' => 'Puerto Rico',
      'inactive' => false,
    ),
    'QA' => 
    array (
      'label' => 'Qatar',
      'inactive' => false,
    ),
    'RE' => 
    array (
      'label' => 'Réunion',
      'inactive' => false,
    ),
    'RO' => 
    array (
      'label' => 'Romania',
      'inactive' => false,
    ),
    'RU' => 
    array (
      'label' => 'Russia',
      'inactive' => false,
    ),
    'RW' => 
    array (
      'label' => 'Rwanda',
      'inactive' => false,
    ),
    'WS' => 
    array (
      'label' => 'Samoa',
      'inactive' => false,
    ),
    'SM' => 
    array (
      'label' => 'San Marino',
      'inactive' => false,
    ),
    'ST' => 
    array (
      'label' => 'São Tomé & Príncipe',
      'inactive' => false,
    ),
    'SA' => 
    array (
      'label' => 'Saudi Arabia',
      'inactive' => false,
    ),
    'SN' => 
    array (
      'label' => 'Senegal',
      'inactive' => false,
    ),
    'RS' => 
    array (
      'label' => 'Serbia',
      'inactive' => false,
    ),
    'SC' => 
    array (
      'label' => 'Seychelles',
      'inactive' => false,
    ),
    'SL' => 
    array (
      'label' => 'Sierra Leone',
      'inactive' => false,
    ),
    'SG' => 
    array (
      'label' => 'Singapore',
      'inactive' => false,
    ),
    'SX' => 
    array (
      'label' => 'Sint Maarten',
      'inactive' => false,
    ),
    'SK' => 
    array (
      'label' => 'Slovakia',
      'inactive' => false,
    ),
    'SI' => 
    array (
      'label' => 'Slovenia',
      'inactive' => false,
    ),
    'SB' => 
    array (
      'label' => 'Solomon Islands',
      'inactive' => false,
    ),
    'SO' => 
    array (
      'label' => 'Somalia',
      'inactive' => false,
    ),
    'ZA' => 
    array (
      'label' => 'South Africa',
      'inactive' => false,
    ),
    'GS' => 
    array (
      'label' => 'South Georgia & South Sandwich Islands',
      'inactive' => false,
    ),
    'KR' => 
    array (
      'label' => 'South Korea',
      'inactive' => false,
    ),
    'SS' => 
    array (
      'label' => 'South Sudan',
      'inactive' => false,
    ),
    'ES' => 
    array (
      'label' => 'Spain',
      'inactive' => false,
    ),
    'LK' => 
    array (
      'label' => 'Sri Lanka',
      'inactive' => false,
    ),
    'BL' => 
    array (
      'label' => 'St. Barthélemy',
      'inactive' => false,
    ),
    'SH' => 
    array (
      'label' => 'St. Helena',
      'inactive' => false,
    ),
    'KN' => 
    array (
      'label' => 'St. Kitts & Nevis',
      'inactive' => false,
    ),
    'LC' => 
    array (
      'label' => 'St. Lucia',
      'inactive' => false,
    ),
    'MF' => 
    array (
      'label' => 'St. Martin',
      'inactive' => false,
    ),
    'PM' => 
    array (
      'label' => 'St. Pierre & Miquelon',
      'inactive' => false,
    ),
    'VC' => 
    array (
      'label' => 'St. Vincent & Grenadines',
      'inactive' => false,
    ),
    'SD' => 
    array (
      'label' => 'Sudan',
      'inactive' => false,
    ),
    'SR' => 
    array (
      'label' => 'Suriname',
      'inactive' => false,
    ),
    'SJ' => 
    array (
      'label' => 'Svalbard & Jan Mayen',
      'inactive' => false,
    ),
    'SZ' => 
    array (
      'label' => 'Swaziland',
      'inactive' => false,
    ),
    'SE' => 
    array (
      'label' => 'Sweden',
      'inactive' => false,
    ),
    'CH' => 
    array (
      'label' => 'Switzerland',
      'inactive' => false,
    ),
    'SY' => 
    array (
      'label' => 'Syria',
      'inactive' => false,
    ),
    'TW' => 
    array (
      'label' => 'Taiwan',
      'inactive' => false,
    ),
    'TJ' => 
    array (
      'label' => 'Tajikistan',
      'inactive' => false,
    ),
    'TZ' => 
    array (
      'label' => 'Tanzania',
      'inactive' => false,
    ),
    'TH' => 
    array (
      'label' => 'Thailand',
      'inactive' => false,
    ),
    'TL' => 
    array (
      'label' => 'Timor-Leste',
      'inactive' => false,
    ),
    'TG' => 
    array (
      'label' => 'Togo',
      'inactive' => false,
    ),
    'TK' => 
    array (
      'label' => 'Tokelau',
      'inactive' => false,
    ),
    'TO' => 
    array (
      'label' => 'Tonga',
      'inactive' => false,
    ),
    'TT' => 
    array (
      'label' => 'Trinidad & Tobago',
      'inactive' => false,
    ),
    'TA' => 
    array (
      'label' => 'Tristan da Cunha',
      'inactive' => false,
    ),
    'TN' => 
    array (
      'label' => 'Tunisia',
      'inactive' => false,
    ),
    'TR' => 
    array (
      'label' => 'Turkey',
      'inactive' => false,
    ),
    'TM' => 
    array (
      'label' => 'Turkmenistan',
      'inactive' => false,
    ),
    'TC' => 
    array (
      'label' => 'Turks & Caicos Islands',
      'inactive' => false,
    ),
    'TV' => 
    array (
      'label' => 'Tuvalu',
      'inactive' => false,
    ),
    'UM' => 
    array (
      'label' => 'U.S. Outlying Islands',
      'inactive' => false,
    ),
    'VI' => 
    array (
      'label' => 'U.S. Virgin Islands',
      'inactive' => false,
    ),
    'UG' => 
    array (
      'label' => 'Uganda',
      'inactive' => false,
    ),
    'UA' => 
    array (
      'label' => 'Ukraine',
      'inactive' => false,
    ),
    'AE' => 
    array (
      'label' => 'United Arab Emirates',
      'inactive' => false,
    ),
    'GB' => 
    array (
      'label' => 'United Kingdom',
      'inactive' => false,
    ),
    'US' => 
    array (
      'label' => 'United States',
      'inactive' => false,
    ),
    'UY' => 
    array (
      'label' => 'Uruguay',
      'inactive' => false,
    ),
    'UZ' => 
    array (
      'label' => 'Uzbekistan',
      'inactive' => false,
    ),
    'VU' => 
    array (
      'label' => 'Vanuatu',
      'inactive' => false,
    ),
    'VA' => 
    array (
      'label' => 'Vatican City',
      'inactive' => false,
    ),
    'VE' => 
    array (
      'label' => 'Venezuela',
      'inactive' => false,
    ),
    'VN' => 
    array (
      'label' => 'Vietnam',
      'inactive' => false,
    ),
    'WF' => 
    array (
      'label' => 'Wallis & Futuna',
      'inactive' => false,
    ),
    'EH' => 
    array (
      'label' => 'Western Sahara',
      'inactive' => false,
    ),
    'YE' => 
    array (
      'label' => 'Yemen',
      'inactive' => false,
    ),
    'ZM' => 
    array (
      'label' => 'Zambia',
      'inactive' => false,
    ),
    'ZW' => 
    array (
      'label' => 'Zimbabwe',
      'inactive' => false,
    ),
  ),
  'languages' => 
  array (
    'ab' => 
    array (
      'label' => 'Abkhazian',
      'inactive' => false,
    ),
    'ace' => 
    array (
      'label' => 'Achinese',
      'inactive' => false,
    ),
    'ach' => 
    array (
      'label' => 'Acoli',
      'inactive' => false,
    ),
    'ada' => 
    array (
      'label' => 'Adangme',
      'inactive' => false,
    ),
    'ady' => 
    array (
      'label' => 'Adyghe',
      'inactive' => false,
    ),
    'aa' => 
    array (
      'label' => 'Afar',
      'inactive' => false,
    ),
    'afh' => 
    array (
      'label' => 'Afrihili',
      'inactive' => false,
    ),
    'af' => 
    array (
      'label' => 'Afrikaans',
      'inactive' => false,
    ),
    'agq' => 
    array (
      'label' => 'Aghem',
      'inactive' => false,
    ),
    'ain' => 
    array (
      'label' => 'Ainu',
      'inactive' => false,
    ),
    'ak' => 
    array (
      'label' => 'Akan',
      'inactive' => false,
    ),
    'akk' => 
    array (
      'label' => 'Akkadian',
      'inactive' => false,
    ),
    'bss' => 
    array (
      'label' => 'Akoose',
      'inactive' => false,
    ),
    'akz' => 
    array (
      'label' => 'Alabama',
      'inactive' => false,
    ),
    'sq' => 
    array (
      'label' => 'Albanian',
      'inactive' => false,
    ),
    'ale' => 
    array (
      'label' => 'Aleut',
      'inactive' => false,
    ),
    'arq' => 
    array (
      'label' => 'Algerian Arabic',
      'inactive' => false,
    ),
    'en_US' => 
    array (
      'label' => 'American English',
      'inactive' => false,
    ),
    'ase' => 
    array (
      'label' => 'American Sign Language',
      'inactive' => false,
    ),
    'am' => 
    array (
      'label' => 'Amharic',
      'inactive' => false,
    ),
    'egy' => 
    array (
      'label' => 'Ancient Egyptian',
      'inactive' => false,
    ),
    'grc' => 
    array (
      'label' => 'Ancient Greek',
      'inactive' => false,
    ),
    'anp' => 
    array (
      'label' => 'Angika',
      'inactive' => false,
    ),
    'njo' => 
    array (
      'label' => 'Ao Naga',
      'inactive' => false,
    ),
    'ar' => 
    array (
      'label' => 'Arabic',
      'inactive' => false,
    ),
    'an' => 
    array (
      'label' => 'Aragonese',
      'inactive' => false,
    ),
    'arc' => 
    array (
      'label' => 'Aramaic',
      'inactive' => false,
    ),
    'aro' => 
    array (
      'label' => 'Araona',
      'inactive' => false,
    ),
    'arp' => 
    array (
      'label' => 'Arapaho',
      'inactive' => false,
    ),
    'arw' => 
    array (
      'label' => 'Arawak',
      'inactive' => false,
    ),
    'hy' => 
    array (
      'label' => 'Armenian',
      'inactive' => false,
    ),
    'rup' => 
    array (
      'label' => 'Aromanian',
      'inactive' => false,
    ),
    'frp' => 
    array (
      'label' => 'Arpitan',
      'inactive' => false,
    ),
    'as' => 
    array (
      'label' => 'Assamese',
      'inactive' => false,
    ),
    'ast' => 
    array (
      'label' => 'Asturian',
      'inactive' => false,
    ),
    'asa' => 
    array (
      'label' => 'Asu',
      'inactive' => false,
    ),
    'cch' => 
    array (
      'label' => 'Atsam',
      'inactive' => false,
    ),
    'en_AU' => 
    array (
      'label' => 'Australian English',
      'inactive' => false,
    ),
    'de_AT' => 
    array (
      'label' => 'Austrian German',
      'inactive' => false,
    ),
    'av' => 
    array (
      'label' => 'Avaric',
      'inactive' => false,
    ),
    'ae' => 
    array (
      'label' => 'Avestan',
      'inactive' => false,
    ),
    'awa' => 
    array (
      'label' => 'Awadhi',
      'inactive' => false,
    ),
    'ay' => 
    array (
      'label' => 'Aymara',
      'inactive' => false,
    ),
    'az' => 
    array (
      'label' => 'Azerbaijani',
      'inactive' => false,
    ),
    'bfq' => 
    array (
      'label' => 'Badaga',
      'inactive' => false,
    ),
    'ksf' => 
    array (
      'label' => 'Bafia',
      'inactive' => false,
    ),
    'bfd' => 
    array (
      'label' => 'Bafut',
      'inactive' => false,
    ),
    'bqi' => 
    array (
      'label' => 'Bakhtiari',
      'inactive' => false,
    ),
    'ban' => 
    array (
      'label' => 'Balinese',
      'inactive' => false,
    ),
    'bal' => 
    array (
      'label' => 'Baluchi',
      'inactive' => false,
    ),
    'bm' => 
    array (
      'label' => 'Bambara',
      'inactive' => false,
    ),
    'bax' => 
    array (
      'label' => 'Bamun',
      'inactive' => false,
    ),
    'bjn' => 
    array (
      'label' => 'Banjar',
      'inactive' => false,
    ),
    'bas' => 
    array (
      'label' => 'Basaa',
      'inactive' => false,
    ),
    'ba' => 
    array (
      'label' => 'Bashkir',
      'inactive' => false,
    ),
    'eu' => 
    array (
      'label' => 'Basque',
      'inactive' => false,
    ),
    'bbc' => 
    array (
      'label' => 'Batak Toba',
      'inactive' => false,
    ),
    'bar' => 
    array (
      'label' => 'Bavarian',
      'inactive' => false,
    ),
    'bej' => 
    array (
      'label' => 'Beja',
      'inactive' => false,
    ),
    'be' => 
    array (
      'label' => 'Belarusian',
      'inactive' => false,
    ),
    'bem' => 
    array (
      'label' => 'Bemba',
      'inactive' => false,
    ),
    'bez' => 
    array (
      'label' => 'Bena',
      'inactive' => false,
    ),
    'bn' => 
    array (
      'label' => 'Bengali',
      'inactive' => false,
    ),
    'bew' => 
    array (
      'label' => 'Betawi',
      'inactive' => false,
    ),
    'bho' => 
    array (
      'label' => 'Bhojpuri',
      'inactive' => false,
    ),
    'bik' => 
    array (
      'label' => 'Bikol',
      'inactive' => false,
    ),
    'bin' => 
    array (
      'label' => 'Bini',
      'inactive' => false,
    ),
    'bpy' => 
    array (
      'label' => 'Bishnupriya',
      'inactive' => false,
    ),
    'bi' => 
    array (
      'label' => 'Bislama',
      'inactive' => false,
    ),
    'byn' => 
    array (
      'label' => 'Blin',
      'inactive' => false,
    ),
    'zbl' => 
    array (
      'label' => 'Blissymbols',
      'inactive' => false,
    ),
    'brx' => 
    array (
      'label' => 'Bodo',
      'inactive' => false,
    ),
    'bs' => 
    array (
      'label' => 'Bosnian',
      'inactive' => false,
    ),
    'brh' => 
    array (
      'label' => 'Brahui',
      'inactive' => false,
    ),
    'bra' => 
    array (
      'label' => 'Braj',
      'inactive' => false,
    ),
    'pt_BR' => 
    array (
      'label' => 'Brazilian Portuguese',
      'inactive' => false,
    ),
    'br' => 
    array (
      'label' => 'Breton',
      'inactive' => false,
    ),
    'en_GB' => 
    array (
      'label' => 'British English',
      'inactive' => false,
    ),
    'bug' => 
    array (
      'label' => 'Buginese',
      'inactive' => false,
    ),
    'bg' => 
    array (
      'label' => 'Bulgarian',
      'inactive' => false,
    ),
    'bum' => 
    array (
      'label' => 'Bulu',
      'inactive' => false,
    ),
    'bua' => 
    array (
      'label' => 'Buriat',
      'inactive' => false,
    ),
    'my' => 
    array (
      'label' => 'Burmese',
      'inactive' => false,
    ),
    'cad' => 
    array (
      'label' => 'Caddo',
      'inactive' => false,
    ),
    'frc' => 
    array (
      'label' => 'Cajun French',
      'inactive' => false,
    ),
    'en_CA' => 
    array (
      'label' => 'Canadian English',
      'inactive' => false,
    ),
    'fr_CA' => 
    array (
      'label' => 'Canadian French',
      'inactive' => false,
    ),
    'yue' => 
    array (
      'label' => 'Cantonese',
      'inactive' => false,
    ),
    'cps' => 
    array (
      'label' => 'Capiznon',
      'inactive' => false,
    ),
    'car' => 
    array (
      'label' => 'Carib',
      'inactive' => false,
    ),
    'ca' => 
    array (
      'label' => 'Catalan',
      'inactive' => false,
    ),
    'cay' => 
    array (
      'label' => 'Cayuga',
      'inactive' => false,
    ),
    'ceb' => 
    array (
      'label' => 'Cebuano',
      'inactive' => false,
    ),
    'tzm' => 
    array (
      'label' => 'Central Atlas Tamazight',
      'inactive' => false,
    ),
    'dtp' => 
    array (
      'label' => 'Central Dusun',
      'inactive' => false,
    ),
    'esu' => 
    array (
      'label' => 'Central Yupik',
      'inactive' => false,
    ),
    'shu' => 
    array (
      'label' => 'Chadian Arabic',
      'inactive' => false,
    ),
    'chg' => 
    array (
      'label' => 'Chagatai',
      'inactive' => false,
    ),
    'ch' => 
    array (
      'label' => 'Chamorro',
      'inactive' => false,
    ),
    'ce' => 
    array (
      'label' => 'Chechen',
      'inactive' => false,
    ),
    'chr' => 
    array (
      'label' => 'Cherokee',
      'inactive' => false,
    ),
    'chy' => 
    array (
      'label' => 'Cheyenne',
      'inactive' => false,
    ),
    'chb' => 
    array (
      'label' => 'Chibcha',
      'inactive' => false,
    ),
    'cgg' => 
    array (
      'label' => 'Chiga',
      'inactive' => false,
    ),
    'qug' => 
    array (
      'label' => 'Chimborazo Highland Quichua',
      'inactive' => false,
    ),
    'zh' => 
    array (
      'label' => 'Chinese',
      'inactive' => false,
    ),
    'chn' => 
    array (
      'label' => 'Chinook Jargon',
      'inactive' => false,
    ),
    'chp' => 
    array (
      'label' => 'Chipewyan',
      'inactive' => false,
    ),
    'cho' => 
    array (
      'label' => 'Choctaw',
      'inactive' => false,
    ),
    'cu' => 
    array (
      'label' => 'Church Slavic',
      'inactive' => false,
    ),
    'chk' => 
    array (
      'label' => 'Chuukese',
      'inactive' => false,
    ),
    'cv' => 
    array (
      'label' => 'Chuvash',
      'inactive' => false,
    ),
    'nwc' => 
    array (
      'label' => 'Classical Newari',
      'inactive' => false,
    ),
    'syc' => 
    array (
      'label' => 'Classical Syriac',
      'inactive' => false,
    ),
    'ksh' => 
    array (
      'label' => 'Colognian',
      'inactive' => false,
    ),
    'swb' => 
    array (
      'label' => 'Comorian',
      'inactive' => false,
    ),
    'swc' => 
    array (
      'label' => 'Congo Swahili',
      'inactive' => false,
    ),
    'cop' => 
    array (
      'label' => 'Coptic',
      'inactive' => false,
    ),
    'kw' => 
    array (
      'label' => 'Cornish',
      'inactive' => false,
    ),
    'co' => 
    array (
      'label' => 'Corsican',
      'inactive' => false,
    ),
    'cr' => 
    array (
      'label' => 'Cree',
      'inactive' => false,
    ),
    'mus' => 
    array (
      'label' => 'Creek',
      'inactive' => false,
    ),
    'crh' => 
    array (
      'label' => 'Crimean Turkish',
      'inactive' => false,
    ),
    'hr' => 
    array (
      'label' => 'Croatian',
      'inactive' => false,
    ),
    'cs' => 
    array (
      'label' => 'Czech',
      'inactive' => false,
    ),
    'dak' => 
    array (
      'label' => 'Dakota',
      'inactive' => false,
    ),
    'da' => 
    array (
      'label' => 'Danish',
      'inactive' => false,
    ),
    'dar' => 
    array (
      'label' => 'Dargwa',
      'inactive' => false,
    ),
    'dzg' => 
    array (
      'label' => 'Dazaga',
      'inactive' => false,
    ),
    'del' => 
    array (
      'label' => 'Delaware',
      'inactive' => false,
    ),
    'din' => 
    array (
      'label' => 'Dinka',
      'inactive' => false,
    ),
    'dv' => 
    array (
      'label' => 'Divehi',
      'inactive' => false,
    ),
    'doi' => 
    array (
      'label' => 'Dogri',
      'inactive' => false,
    ),
    'dgr' => 
    array (
      'label' => 'Dogrib',
      'inactive' => false,
    ),
    'dua' => 
    array (
      'label' => 'Duala',
      'inactive' => false,
    ),
    'nl' => 
    array (
      'label' => 'Dutch',
      'inactive' => false,
    ),
    'dyu' => 
    array (
      'label' => 'Dyula',
      'inactive' => false,
    ),
    'dz' => 
    array (
      'label' => 'Dzongkha',
      'inactive' => false,
    ),
    'frs' => 
    array (
      'label' => 'Eastern Frisian',
      'inactive' => false,
    ),
    'efi' => 
    array (
      'label' => 'Efik',
      'inactive' => false,
    ),
    'arz' => 
    array (
      'label' => 'Egyptian Arabic',
      'inactive' => false,
    ),
    'eka' => 
    array (
      'label' => 'Ekajuk',
      'inactive' => false,
    ),
    'elx' => 
    array (
      'label' => 'Elamite',
      'inactive' => false,
    ),
    'ebu' => 
    array (
      'label' => 'Embu',
      'inactive' => false,
    ),
    'egl' => 
    array (
      'label' => 'Emilian',
      'inactive' => false,
    ),
    'en' => 
    array (
      'label' => 'English',
      'inactive' => false,
    ),
    'myv' => 
    array (
      'label' => 'Erzya',
      'inactive' => false,
    ),
    'eo' => 
    array (
      'label' => 'Esperanto',
      'inactive' => false,
    ),
    'et' => 
    array (
      'label' => 'Estonian',
      'inactive' => false,
    ),
    'pt_PT' => 
    array (
      'label' => 'European Portuguese',
      'inactive' => false,
    ),
    'es_ES' => 
    array (
      'label' => 'European Spanish',
      'inactive' => false,
    ),
    'ee' => 
    array (
      'label' => 'Ewe',
      'inactive' => false,
    ),
    'ewo' => 
    array (
      'label' => 'Ewondo',
      'inactive' => false,
    ),
    'ext' => 
    array (
      'label' => 'Extremaduran',
      'inactive' => false,
    ),
    'fan' => 
    array (
      'label' => 'Fang',
      'inactive' => false,
    ),
    'fat' => 
    array (
      'label' => 'Fanti',
      'inactive' => false,
    ),
    'fo' => 
    array (
      'label' => 'Faroese',
      'inactive' => false,
    ),
    'hif' => 
    array (
      'label' => 'Fiji Hindi',
      'inactive' => false,
    ),
    'fj' => 
    array (
      'label' => 'Fijian',
      'inactive' => false,
    ),
    'fil' => 
    array (
      'label' => 'Filipino',
      'inactive' => false,
    ),
    'fi' => 
    array (
      'label' => 'Finnish',
      'inactive' => false,
    ),
    'nl_BE' => 
    array (
      'label' => 'Flemish',
      'inactive' => false,
    ),
    'fon' => 
    array (
      'label' => 'Fon',
      'inactive' => false,
    ),
    'gur' => 
    array (
      'label' => 'Frafra',
      'inactive' => false,
    ),
    'fr' => 
    array (
      'label' => 'French',
      'inactive' => false,
    ),
    'fur' => 
    array (
      'label' => 'Friulian',
      'inactive' => false,
    ),
    'ff' => 
    array (
      'label' => 'Fulah',
      'inactive' => false,
    ),
    'gaa' => 
    array (
      'label' => 'Ga',
      'inactive' => false,
    ),
    'gag' => 
    array (
      'label' => 'Gagauz',
      'inactive' => false,
    ),
    'gl' => 
    array (
      'label' => 'Galician',
      'inactive' => false,
    ),
    'gan' => 
    array (
      'label' => 'Gan Chinese',
      'inactive' => false,
    ),
    'lg' => 
    array (
      'label' => 'Ganda',
      'inactive' => false,
    ),
    'gay' => 
    array (
      'label' => 'Gayo',
      'inactive' => false,
    ),
    'gba' => 
    array (
      'label' => 'Gbaya',
      'inactive' => false,
    ),
    'gez' => 
    array (
      'label' => 'Geez',
      'inactive' => false,
    ),
    'ka' => 
    array (
      'label' => 'Georgian',
      'inactive' => false,
    ),
    'de' => 
    array (
      'label' => 'German',
      'inactive' => false,
    ),
    'aln' => 
    array (
      'label' => 'Gheg Albanian',
      'inactive' => false,
    ),
    'bbj' => 
    array (
      'label' => 'Ghomala',
      'inactive' => false,
    ),
    'glk' => 
    array (
      'label' => 'Gilaki',
      'inactive' => false,
    ),
    'gil' => 
    array (
      'label' => 'Gilbertese',
      'inactive' => false,
    ),
    'gom' => 
    array (
      'label' => 'Goan Konkani',
      'inactive' => false,
    ),
    'gon' => 
    array (
      'label' => 'Gondi',
      'inactive' => false,
    ),
    'gor' => 
    array (
      'label' => 'Gorontalo',
      'inactive' => false,
    ),
    'got' => 
    array (
      'label' => 'Gothic',
      'inactive' => false,
    ),
    'grb' => 
    array (
      'label' => 'Grebo',
      'inactive' => false,
    ),
    'el' => 
    array (
      'label' => 'Greek',
      'inactive' => false,
    ),
    'gn' => 
    array (
      'label' => 'Guarani',
      'inactive' => false,
    ),
    'gu' => 
    array (
      'label' => 'Gujarati',
      'inactive' => false,
    ),
    'guz' => 
    array (
      'label' => 'Gusii',
      'inactive' => false,
    ),
    'gwi' => 
    array (
      'label' => 'Gwichʼin',
      'inactive' => false,
    ),
    'hai' => 
    array (
      'label' => 'Haida',
      'inactive' => false,
    ),
    'ht' => 
    array (
      'label' => 'Haitian',
      'inactive' => false,
    ),
    'hak' => 
    array (
      'label' => 'Hakka Chinese',
      'inactive' => false,
    ),
    'ha' => 
    array (
      'label' => 'Hausa',
      'inactive' => false,
    ),
    'haw' => 
    array (
      'label' => 'Hawaiian',
      'inactive' => false,
    ),
    'he' => 
    array (
      'label' => 'Hebrew',
      'inactive' => false,
    ),
    'hz' => 
    array (
      'label' => 'Herero',
      'inactive' => false,
    ),
    'hil' => 
    array (
      'label' => 'Hiligaynon',
      'inactive' => false,
    ),
    'hi' => 
    array (
      'label' => 'Hindi',
      'inactive' => false,
    ),
    'ho' => 
    array (
      'label' => 'Hiri Motu',
      'inactive' => false,
    ),
    'hit' => 
    array (
      'label' => 'Hittite',
      'inactive' => false,
    ),
    'hmn' => 
    array (
      'label' => 'Hmong',
      'inactive' => false,
    ),
    'hu' => 
    array (
      'label' => 'Hungarian',
      'inactive' => false,
    ),
    'hup' => 
    array (
      'label' => 'Hupa',
      'inactive' => false,
    ),
    'iba' => 
    array (
      'label' => 'Iban',
      'inactive' => false,
    ),
    'ibb' => 
    array (
      'label' => 'Ibibio',
      'inactive' => false,
    ),
    'is' => 
    array (
      'label' => 'Icelandic',
      'inactive' => false,
    ),
    'io' => 
    array (
      'label' => 'Ido',
      'inactive' => false,
    ),
    'ig' => 
    array (
      'label' => 'Igbo',
      'inactive' => false,
    ),
    'ilo' => 
    array (
      'label' => 'Iloko',
      'inactive' => false,
    ),
    'smn' => 
    array (
      'label' => 'Inari Sami',
      'inactive' => false,
    ),
    'id' => 
    array (
      'label' => 'Indonesian',
      'inactive' => false,
    ),
    'izh' => 
    array (
      'label' => 'Ingrian',
      'inactive' => false,
    ),
    'inh' => 
    array (
      'label' => 'Ingush',
      'inactive' => false,
    ),
    'ia' => 
    array (
      'label' => 'Interlingua',
      'inactive' => false,
    ),
    'ie' => 
    array (
      'label' => 'Interlingue',
      'inactive' => false,
    ),
    'iu' => 
    array (
      'label' => 'Inuktitut',
      'inactive' => false,
    ),
    'ik' => 
    array (
      'label' => 'Inupiaq',
      'inactive' => false,
    ),
    'ga' => 
    array (
      'label' => 'Irish',
      'inactive' => false,
    ),
    'it' => 
    array (
      'label' => 'Italian',
      'inactive' => false,
    ),
    'jam' => 
    array (
      'label' => 'Jamaican Creole English',
      'inactive' => false,
    ),
    'ja' => 
    array (
      'label' => 'Japanese',
      'inactive' => false,
    ),
    'jv' => 
    array (
      'label' => 'Javanese',
      'inactive' => false,
    ),
    'kaj' => 
    array (
      'label' => 'Jju',
      'inactive' => false,
    ),
    'dyo' => 
    array (
      'label' => 'Jola-Fonyi',
      'inactive' => false,
    ),
    'jrb' => 
    array (
      'label' => 'Judeo-Arabic',
      'inactive' => false,
    ),
    'jpr' => 
    array (
      'label' => 'Judeo-Persian',
      'inactive' => false,
    ),
    'jut' => 
    array (
      'label' => 'Jutish',
      'inactive' => false,
    ),
    'kbd' => 
    array (
      'label' => 'Kabardian',
      'inactive' => false,
    ),
    'kea' => 
    array (
      'label' => 'Kabuverdianu',
      'inactive' => false,
    ),
    'kab' => 
    array (
      'label' => 'Kabyle',
      'inactive' => false,
    ),
    'kac' => 
    array (
      'label' => 'Kachin',
      'inactive' => false,
    ),
    'kgp' => 
    array (
      'label' => 'Kaingang',
      'inactive' => false,
    ),
    'kkj' => 
    array (
      'label' => 'Kako',
      'inactive' => false,
    ),
    'kl' => 
    array (
      'label' => 'Kalaallisut',
      'inactive' => false,
    ),
    'kln' => 
    array (
      'label' => 'Kalenjin',
      'inactive' => false,
    ),
    'xal' => 
    array (
      'label' => 'Kalmyk',
      'inactive' => false,
    ),
    'kam' => 
    array (
      'label' => 'Kamba',
      'inactive' => false,
    ),
    'kbl' => 
    array (
      'label' => 'Kanembu',
      'inactive' => false,
    ),
    'kn' => 
    array (
      'label' => 'Kannada',
      'inactive' => false,
    ),
    'kr' => 
    array (
      'label' => 'Kanuri',
      'inactive' => false,
    ),
    'kaa' => 
    array (
      'label' => 'Kara-Kalpak',
      'inactive' => false,
    ),
    'krc' => 
    array (
      'label' => 'Karachay-Balkar',
      'inactive' => false,
    ),
    'krl' => 
    array (
      'label' => 'Karelian',
      'inactive' => false,
    ),
    'ks' => 
    array (
      'label' => 'Kashmiri',
      'inactive' => false,
    ),
    'csb' => 
    array (
      'label' => 'Kashubian',
      'inactive' => false,
    ),
    'kaw' => 
    array (
      'label' => 'Kawi',
      'inactive' => false,
    ),
    'kk' => 
    array (
      'label' => 'Kazakh',
      'inactive' => false,
    ),
    'ken' => 
    array (
      'label' => 'Kenyang',
      'inactive' => false,
    ),
    'kha' => 
    array (
      'label' => 'Khasi',
      'inactive' => false,
    ),
    'km' => 
    array (
      'label' => 'Khmer',
      'inactive' => false,
    ),
    'kho' => 
    array (
      'label' => 'Khotanese',
      'inactive' => false,
    ),
    'khw' => 
    array (
      'label' => 'Khowar',
      'inactive' => false,
    ),
    'ki' => 
    array (
      'label' => 'Kikuyu',
      'inactive' => false,
    ),
    'kmb' => 
    array (
      'label' => 'Kimbundu',
      'inactive' => false,
    ),
    'krj' => 
    array (
      'label' => 'Kinaray-a',
      'inactive' => false,
    ),
    'rw' => 
    array (
      'label' => 'Kinyarwanda',
      'inactive' => false,
    ),
    'kiu' => 
    array (
      'label' => 'Kirmanjki',
      'inactive' => false,
    ),
    'tlh' => 
    array (
      'label' => 'Klingon',
      'inactive' => false,
    ),
    'bkm' => 
    array (
      'label' => 'Kom',
      'inactive' => false,
    ),
    'kv' => 
    array (
      'label' => 'Komi',
      'inactive' => false,
    ),
    'koi' => 
    array (
      'label' => 'Komi-Permyak',
      'inactive' => false,
    ),
    'kg' => 
    array (
      'label' => 'Kongo',
      'inactive' => false,
    ),
    'kok' => 
    array (
      'label' => 'Konkani',
      'inactive' => false,
    ),
    'ko' => 
    array (
      'label' => 'Korean',
      'inactive' => false,
    ),
    'kfo' => 
    array (
      'label' => 'Koro',
      'inactive' => false,
    ),
    'kos' => 
    array (
      'label' => 'Kosraean',
      'inactive' => false,
    ),
    'avk' => 
    array (
      'label' => 'Kotava',
      'inactive' => false,
    ),
    'khq' => 
    array (
      'label' => 'Koyra Chiini',
      'inactive' => false,
    ),
    'ses' => 
    array (
      'label' => 'Koyraboro Senni',
      'inactive' => false,
    ),
    'kpe' => 
    array (
      'label' => 'Kpelle',
      'inactive' => false,
    ),
    'kri' => 
    array (
      'label' => 'Krio',
      'inactive' => false,
    ),
    'kj' => 
    array (
      'label' => 'Kuanyama',
      'inactive' => false,
    ),
    'kum' => 
    array (
      'label' => 'Kumyk',
      'inactive' => false,
    ),
    'ku' => 
    array (
      'label' => 'Kurdish',
      'inactive' => false,
    ),
    'kru' => 
    array (
      'label' => 'Kurukh',
      'inactive' => false,
    ),
    'kut' => 
    array (
      'label' => 'Kutenai',
      'inactive' => false,
    ),
    'nmg' => 
    array (
      'label' => 'Kwasio',
      'inactive' => false,
    ),
    'ky' => 
    array (
      'label' => 'Kyrgyz',
      'inactive' => false,
    ),
    'quc' => 
    array (
      'label' => 'Kʼicheʼ',
      'inactive' => false,
    ),
    'lad' => 
    array (
      'label' => 'Ladino',
      'inactive' => false,
    ),
    'lah' => 
    array (
      'label' => 'Lahnda',
      'inactive' => false,
    ),
    'lkt' => 
    array (
      'label' => 'Lakota',
      'inactive' => false,
    ),
    'lam' => 
    array (
      'label' => 'Lamba',
      'inactive' => false,
    ),
    'lag' => 
    array (
      'label' => 'Langi',
      'inactive' => false,
    ),
    'lo' => 
    array (
      'label' => 'Lao',
      'inactive' => false,
    ),
    'ltg' => 
    array (
      'label' => 'Latgalian',
      'inactive' => false,
    ),
    'la' => 
    array (
      'label' => 'Latin',
      'inactive' => false,
    ),
    'es_419' => 
    array (
      'label' => 'Latin American Spanish',
      'inactive' => false,
    ),
    'lv' => 
    array (
      'label' => 'Latvian',
      'inactive' => false,
    ),
    'lzz' => 
    array (
      'label' => 'Laz',
      'inactive' => false,
    ),
    'lez' => 
    array (
      'label' => 'Lezghian',
      'inactive' => false,
    ),
    'lij' => 
    array (
      'label' => 'Ligurian',
      'inactive' => false,
    ),
    'li' => 
    array (
      'label' => 'Limburgish',
      'inactive' => false,
    ),
    'ln' => 
    array (
      'label' => 'Lingala',
      'inactive' => false,
    ),
    'lfn' => 
    array (
      'label' => 'Lingua Franca Nova',
      'inactive' => false,
    ),
    'lzh' => 
    array (
      'label' => 'Literary Chinese',
      'inactive' => false,
    ),
    'lt' => 
    array (
      'label' => 'Lithuanian',
      'inactive' => false,
    ),
    'liv' => 
    array (
      'label' => 'Livonian',
      'inactive' => false,
    ),
    'jbo' => 
    array (
      'label' => 'Lojban',
      'inactive' => false,
    ),
    'lmo' => 
    array (
      'label' => 'Lombard',
      'inactive' => false,
    ),
    'nds' => 
    array (
      'label' => 'Low German',
      'inactive' => false,
    ),
    'sli' => 
    array (
      'label' => 'Lower Silesian',
      'inactive' => false,
    ),
    'dsb' => 
    array (
      'label' => 'Lower Sorbian',
      'inactive' => false,
    ),
    'loz' => 
    array (
      'label' => 'Lozi',
      'inactive' => false,
    ),
    'lu' => 
    array (
      'label' => 'Luba-Katanga',
      'inactive' => false,
    ),
    'lua' => 
    array (
      'label' => 'Luba-Lulua',
      'inactive' => false,
    ),
    'lui' => 
    array (
      'label' => 'Luiseno',
      'inactive' => false,
    ),
    'smj' => 
    array (
      'label' => 'Lule Sami',
      'inactive' => false,
    ),
    'lun' => 
    array (
      'label' => 'Lunda',
      'inactive' => false,
    ),
    'luo' => 
    array (
      'label' => 'Luo',
      'inactive' => false,
    ),
    'lb' => 
    array (
      'label' => 'Luxembourgish',
      'inactive' => false,
    ),
    'luy' => 
    array (
      'label' => 'Luyia',
      'inactive' => false,
    ),
    'mde' => 
    array (
      'label' => 'Maba',
      'inactive' => false,
    ),
    'mk' => 
    array (
      'label' => 'Macedonian',
      'inactive' => false,
    ),
    'jmc' => 
    array (
      'label' => 'Machame',
      'inactive' => false,
    ),
    'mad' => 
    array (
      'label' => 'Madurese',
      'inactive' => false,
    ),
    'maf' => 
    array (
      'label' => 'Mafa',
      'inactive' => false,
    ),
    'mag' => 
    array (
      'label' => 'Magahi',
      'inactive' => false,
    ),
    'vmf' => 
    array (
      'label' => 'Main-Franconian',
      'inactive' => false,
    ),
    'mai' => 
    array (
      'label' => 'Maithili',
      'inactive' => false,
    ),
    'mak' => 
    array (
      'label' => 'Makasar',
      'inactive' => false,
    ),
    'mgh' => 
    array (
      'label' => 'Makhuwa-Meetto',
      'inactive' => false,
    ),
    'kde' => 
    array (
      'label' => 'Makonde',
      'inactive' => false,
    ),
    'mg' => 
    array (
      'label' => 'Malagasy',
      'inactive' => false,
    ),
    'ms' => 
    array (
      'label' => 'Malay',
      'inactive' => false,
    ),
    'ml' => 
    array (
      'label' => 'Malayalam',
      'inactive' => false,
    ),
    'mt' => 
    array (
      'label' => 'Maltese',
      'inactive' => false,
    ),
    'mnc' => 
    array (
      'label' => 'Manchu',
      'inactive' => false,
    ),
    'mdr' => 
    array (
      'label' => 'Mandar',
      'inactive' => false,
    ),
    'man' => 
    array (
      'label' => 'Mandingo',
      'inactive' => false,
    ),
    'mni' => 
    array (
      'label' => 'Manipuri',
      'inactive' => false,
    ),
    'gv' => 
    array (
      'label' => 'Manx',
      'inactive' => false,
    ),
    'mi' => 
    array (
      'label' => 'Maori',
      'inactive' => false,
    ),
    'arn' => 
    array (
      'label' => 'Mapuche',
      'inactive' => false,
    ),
    'mr' => 
    array (
      'label' => 'Marathi',
      'inactive' => false,
    ),
    'chm' => 
    array (
      'label' => 'Mari',
      'inactive' => false,
    ),
    'mh' => 
    array (
      'label' => 'Marshallese',
      'inactive' => false,
    ),
    'mwr' => 
    array (
      'label' => 'Marwari',
      'inactive' => false,
    ),
    'mas' => 
    array (
      'label' => 'Masai',
      'inactive' => false,
    ),
    'mzn' => 
    array (
      'label' => 'Mazanderani',
      'inactive' => false,
    ),
    'byv' => 
    array (
      'label' => 'Medumba',
      'inactive' => false,
    ),
    'men' => 
    array (
      'label' => 'Mende',
      'inactive' => false,
    ),
    'mwv' => 
    array (
      'label' => 'Mentawai',
      'inactive' => false,
    ),
    'mer' => 
    array (
      'label' => 'Meru',
      'inactive' => false,
    ),
    'mgo' => 
    array (
      'label' => 'Metaʼ',
      'inactive' => false,
    ),
    'es_MX' => 
    array (
      'label' => 'Mexican Spanish',
      'inactive' => false,
    ),
    'mic' => 
    array (
      'label' => 'Micmac',
      'inactive' => false,
    ),
    'dum' => 
    array (
      'label' => 'Middle Dutch',
      'inactive' => false,
    ),
    'enm' => 
    array (
      'label' => 'Middle English',
      'inactive' => false,
    ),
    'frm' => 
    array (
      'label' => 'Middle French',
      'inactive' => false,
    ),
    'gmh' => 
    array (
      'label' => 'Middle High German',
      'inactive' => false,
    ),
    'mga' => 
    array (
      'label' => 'Middle Irish',
      'inactive' => false,
    ),
    'nan' => 
    array (
      'label' => 'Min Nan Chinese',
      'inactive' => false,
    ),
    'min' => 
    array (
      'label' => 'Minangkabau',
      'inactive' => false,
    ),
    'xmf' => 
    array (
      'label' => 'Mingrelian',
      'inactive' => false,
    ),
    'mwl' => 
    array (
      'label' => 'Mirandese',
      'inactive' => false,
    ),
    'lus' => 
    array (
      'label' => 'Mizo',
      'inactive' => false,
    ),
    'ar_001' => 
    array (
      'label' => 'Modern Standard Arabic',
      'inactive' => false,
    ),
    'moh' => 
    array (
      'label' => 'Mohawk',
      'inactive' => false,
    ),
    'mdf' => 
    array (
      'label' => 'Moksha',
      'inactive' => false,
    ),
    'ro_MD' => 
    array (
      'label' => 'Moldavian',
      'inactive' => false,
    ),
    'lol' => 
    array (
      'label' => 'Mongo',
      'inactive' => false,
    ),
    'mn' => 
    array (
      'label' => 'Mongolian',
      'inactive' => false,
    ),
    'mfe' => 
    array (
      'label' => 'Morisyen',
      'inactive' => false,
    ),
    'ary' => 
    array (
      'label' => 'Moroccan Arabic',
      'inactive' => false,
    ),
    'mos' => 
    array (
      'label' => 'Mossi',
      'inactive' => false,
    ),
    'mul' => 
    array (
      'label' => 'Multiple Languages',
      'inactive' => false,
    ),
    'mua' => 
    array (
      'label' => 'Mundang',
      'inactive' => false,
    ),
    'ttt' => 
    array (
      'label' => 'Muslim Tat',
      'inactive' => false,
    ),
    'mye' => 
    array (
      'label' => 'Myene',
      'inactive' => false,
    ),
    'naq' => 
    array (
      'label' => 'Nama',
      'inactive' => false,
    ),
    'na' => 
    array (
      'label' => 'Nauru',
      'inactive' => false,
    ),
    'nv' => 
    array (
      'label' => 'Navajo',
      'inactive' => false,
    ),
    'ng' => 
    array (
      'label' => 'Ndonga',
      'inactive' => false,
    ),
    'nap' => 
    array (
      'label' => 'Neapolitan',
      'inactive' => false,
    ),
    'ne' => 
    array (
      'label' => 'Nepali',
      'inactive' => false,
    ),
    'new' => 
    array (
      'label' => 'Newari',
      'inactive' => false,
    ),
    'sba' => 
    array (
      'label' => 'Ngambay',
      'inactive' => false,
    ),
    'nnh' => 
    array (
      'label' => 'Ngiemboon',
      'inactive' => false,
    ),
    'jgo' => 
    array (
      'label' => 'Ngomba',
      'inactive' => false,
    ),
    'yrl' => 
    array (
      'label' => 'Nheengatu',
      'inactive' => false,
    ),
    'nia' => 
    array (
      'label' => 'Nias',
      'inactive' => false,
    ),
    'niu' => 
    array (
      'label' => 'Niuean',
      'inactive' => false,
    ),
    'zxx' => 
    array (
      'label' => 'No linguistic content',
      'inactive' => false,
    ),
    'nog' => 
    array (
      'label' => 'Nogai',
      'inactive' => false,
    ),
    'nd' => 
    array (
      'label' => 'North Ndebele',
      'inactive' => false,
    ),
    'frr' => 
    array (
      'label' => 'Northern Frisian',
      'inactive' => false,
    ),
    'se' => 
    array (
      'label' => 'Northern Sami',
      'inactive' => false,
    ),
    'nso' => 
    array (
      'label' => 'Northern Sotho',
      'inactive' => false,
    ),
    'no' => 
    array (
      'label' => 'Norwegian',
      'inactive' => false,
    ),
    'nb' => 
    array (
      'label' => 'Norwegian Bokmål',
      'inactive' => false,
    ),
    'nn' => 
    array (
      'label' => 'Norwegian Nynorsk',
      'inactive' => false,
    ),
    'nov' => 
    array (
      'label' => 'Novial',
      'inactive' => false,
    ),
    'nus' => 
    array (
      'label' => 'Nuer',
      'inactive' => false,
    ),
    'nym' => 
    array (
      'label' => 'Nyamwezi',
      'inactive' => false,
    ),
    'ny' => 
    array (
      'label' => 'Nyanja',
      'inactive' => false,
    ),
    'nyn' => 
    array (
      'label' => 'Nyankole',
      'inactive' => false,
    ),
    'tog' => 
    array (
      'label' => 'Nyasa Tonga',
      'inactive' => false,
    ),
    'nyo' => 
    array (
      'label' => 'Nyoro',
      'inactive' => false,
    ),
    'nzi' => 
    array (
      'label' => 'Nzima',
      'inactive' => false,
    ),
    'nqo' => 
    array (
      'label' => 'NʼKo',
      'inactive' => false,
    ),
    'oc' => 
    array (
      'label' => 'Occitan',
      'inactive' => false,
    ),
    'oj' => 
    array (
      'label' => 'Ojibwa',
      'inactive' => false,
    ),
    'ang' => 
    array (
      'label' => 'Old English',
      'inactive' => false,
    ),
    'fro' => 
    array (
      'label' => 'Old French',
      'inactive' => false,
    ),
    'goh' => 
    array (
      'label' => 'Old High German',
      'inactive' => false,
    ),
    'sga' => 
    array (
      'label' => 'Old Irish',
      'inactive' => false,
    ),
    'non' => 
    array (
      'label' => 'Old Norse',
      'inactive' => false,
    ),
    'peo' => 
    array (
      'label' => 'Old Persian',
      'inactive' => false,
    ),
    'pro' => 
    array (
      'label' => 'Old Provençal',
      'inactive' => false,
    ),
    'or' => 
    array (
      'label' => 'Oriya',
      'inactive' => false,
    ),
    'om' => 
    array (
      'label' => 'Oromo',
      'inactive' => false,
    ),
    'osa' => 
    array (
      'label' => 'Osage',
      'inactive' => false,
    ),
    'os' => 
    array (
      'label' => 'Ossetic',
      'inactive' => false,
    ),
    'ota' => 
    array (
      'label' => 'Ottoman Turkish',
      'inactive' => false,
    ),
    'pal' => 
    array (
      'label' => 'Pahlavi',
      'inactive' => false,
    ),
    'pfl' => 
    array (
      'label' => 'Palatine German',
      'inactive' => false,
    ),
    'pau' => 
    array (
      'label' => 'Palauan',
      'inactive' => false,
    ),
    'pi' => 
    array (
      'label' => 'Pali',
      'inactive' => false,
    ),
    'pam' => 
    array (
      'label' => 'Pampanga',
      'inactive' => false,
    ),
    'pag' => 
    array (
      'label' => 'Pangasinan',
      'inactive' => false,
    ),
    'pap' => 
    array (
      'label' => 'Papiamento',
      'inactive' => false,
    ),
    'ps' => 
    array (
      'label' => 'Pashto',
      'inactive' => false,
    ),
    'pdc' => 
    array (
      'label' => 'Pennsylvania German',
      'inactive' => false,
    ),
    'fa' => 
    array (
      'label' => 'Persian',
      'inactive' => false,
    ),
    'phn' => 
    array (
      'label' => 'Phoenician',
      'inactive' => false,
    ),
    'pcd' => 
    array (
      'label' => 'Picard',
      'inactive' => false,
    ),
    'pms' => 
    array (
      'label' => 'Piedmontese',
      'inactive' => false,
    ),
    'pdt' => 
    array (
      'label' => 'Plautdietsch',
      'inactive' => false,
    ),
    'pon' => 
    array (
      'label' => 'Pohnpeian',
      'inactive' => false,
    ),
    'pl' => 
    array (
      'label' => 'Polish',
      'inactive' => false,
    ),
    'pnt' => 
    array (
      'label' => 'Pontic',
      'inactive' => false,
    ),
    'pt' => 
    array (
      'label' => 'Portuguese',
      'inactive' => false,
    ),
    'prg' => 
    array (
      'label' => 'Prussian',
      'inactive' => false,
    ),
    'pa' => 
    array (
      'label' => 'Punjabi',
      'inactive' => false,
    ),
    'qu' => 
    array (
      'label' => 'Quechua',
      'inactive' => false,
    ),
    'raj' => 
    array (
      'label' => 'Rajasthani',
      'inactive' => false,
    ),
    'rap' => 
    array (
      'label' => 'Rapanui',
      'inactive' => false,
    ),
    'rar' => 
    array (
      'label' => 'Rarotongan',
      'inactive' => false,
    ),
    'rif' => 
    array (
      'label' => 'Riffian',
      'inactive' => false,
    ),
    'rgn' => 
    array (
      'label' => 'Romagnol',
      'inactive' => false,
    ),
    'ro' => 
    array (
      'label' => 'Romanian',
      'inactive' => false,
    ),
    'rm' => 
    array (
      'label' => 'Romansh',
      'inactive' => false,
    ),
    'rom' => 
    array (
      'label' => 'Romany',
      'inactive' => false,
    ),
    'rof' => 
    array (
      'label' => 'Rombo',
      'inactive' => false,
    ),
    'root' => 
    array (
      'label' => 'Root',
      'inactive' => false,
    ),
    'rtm' => 
    array (
      'label' => 'Rotuman',
      'inactive' => false,
    ),
    'rug' => 
    array (
      'label' => 'Roviana',
      'inactive' => false,
    ),
    'rn' => 
    array (
      'label' => 'Rundi',
      'inactive' => false,
    ),
    'ru' => 
    array (
      'label' => 'Russian',
      'inactive' => false,
    ),
    'rue' => 
    array (
      'label' => 'Rusyn',
      'inactive' => false,
    ),
    'rwk' => 
    array (
      'label' => 'Rwa',
      'inactive' => false,
    ),
    'ssy' => 
    array (
      'label' => 'Saho',
      'inactive' => false,
    ),
    'sah' => 
    array (
      'label' => 'Sakha',
      'inactive' => false,
    ),
    'sam' => 
    array (
      'label' => 'Samaritan Aramaic',
      'inactive' => false,
    ),
    'saq' => 
    array (
      'label' => 'Samburu',
      'inactive' => false,
    ),
    'sm' => 
    array (
      'label' => 'Samoan',
      'inactive' => false,
    ),
    'sgs' => 
    array (
      'label' => 'Samogitian',
      'inactive' => false,
    ),
    'sad' => 
    array (
      'label' => 'Sandawe',
      'inactive' => false,
    ),
    'sg' => 
    array (
      'label' => 'Sango',
      'inactive' => false,
    ),
    'sbp' => 
    array (
      'label' => 'Sangu',
      'inactive' => false,
    ),
    'sa' => 
    array (
      'label' => 'Sanskrit',
      'inactive' => false,
    ),
    'sat' => 
    array (
      'label' => 'Santali',
      'inactive' => false,
    ),
    'sc' => 
    array (
      'label' => 'Sardinian',
      'inactive' => false,
    ),
    'sas' => 
    array (
      'label' => 'Sasak',
      'inactive' => false,
    ),
    'sdc' => 
    array (
      'label' => 'Sassarese Sardinian',
      'inactive' => false,
    ),
    'stq' => 
    array (
      'label' => 'Saterland Frisian',
      'inactive' => false,
    ),
    'saz' => 
    array (
      'label' => 'Saurashtra',
      'inactive' => false,
    ),
    'sco' => 
    array (
      'label' => 'Scots',
      'inactive' => false,
    ),
    'gd' => 
    array (
      'label' => 'Scottish Gaelic',
      'inactive' => false,
    ),
    'sly' => 
    array (
      'label' => 'Selayar',
      'inactive' => false,
    ),
    'sel' => 
    array (
      'label' => 'Selkup',
      'inactive' => false,
    ),
    'seh' => 
    array (
      'label' => 'Sena',
      'inactive' => false,
    ),
    'see' => 
    array (
      'label' => 'Seneca',
      'inactive' => false,
    ),
    'sr' => 
    array (
      'label' => 'Serbian',
      'inactive' => false,
    ),
    'sh' => 
    array (
      'label' => 'Serbo-Croatian',
      'inactive' => false,
    ),
    'srr' => 
    array (
      'label' => 'Serer',
      'inactive' => false,
    ),
    'sei' => 
    array (
      'label' => 'Seri',
      'inactive' => false,
    ),
    'ksb' => 
    array (
      'label' => 'Shambala',
      'inactive' => false,
    ),
    'shn' => 
    array (
      'label' => 'Shan',
      'inactive' => false,
    ),
    'sn' => 
    array (
      'label' => 'Shona',
      'inactive' => false,
    ),
    'ii' => 
    array (
      'label' => 'Sichuan Yi',
      'inactive' => false,
    ),
    'scn' => 
    array (
      'label' => 'Sicilian',
      'inactive' => false,
    ),
    'sid' => 
    array (
      'label' => 'Sidamo',
      'inactive' => false,
    ),
    'bla' => 
    array (
      'label' => 'Siksika',
      'inactive' => false,
    ),
    'szl' => 
    array (
      'label' => 'Silesian',
      'inactive' => false,
    ),
    'zh_Hans' => 
    array (
      'label' => 'Simplified Chinese',
      'inactive' => false,
    ),
    'sd' => 
    array (
      'label' => 'Sindhi',
      'inactive' => false,
    ),
    'si' => 
    array (
      'label' => 'Sinhala',
      'inactive' => false,
    ),
    'sms' => 
    array (
      'label' => 'Skolt Sami',
      'inactive' => false,
    ),
    'den' => 
    array (
      'label' => 'Slave',
      'inactive' => false,
    ),
    'sk' => 
    array (
      'label' => 'Slovak',
      'inactive' => false,
    ),
    'sl' => 
    array (
      'label' => 'Slovenian',
      'inactive' => false,
    ),
    'xog' => 
    array (
      'label' => 'Soga',
      'inactive' => false,
    ),
    'sog' => 
    array (
      'label' => 'Sogdien',
      'inactive' => false,
    ),
    'so' => 
    array (
      'label' => 'Somali',
      'inactive' => false,
    ),
    'snk' => 
    array (
      'label' => 'Soninke',
      'inactive' => false,
    ),
    'ckb' => 
    array (
      'label' => 'Sorani Kurdish',
      'inactive' => false,
    ),
    'azb' => 
    array (
      'label' => 'South Azerbaijani',
      'inactive' => false,
    ),
    'nr' => 
    array (
      'label' => 'South Ndebele',
      'inactive' => false,
    ),
    'alt' => 
    array (
      'label' => 'Southern Altai',
      'inactive' => false,
    ),
    'sma' => 
    array (
      'label' => 'Southern Sami',
      'inactive' => false,
    ),
    'st' => 
    array (
      'label' => 'Southern Sotho',
      'inactive' => false,
    ),
    'es' => 
    array (
      'label' => 'Spanish',
      'inactive' => false,
    ),
    'srn' => 
    array (
      'label' => 'Sranan Tongo',
      'inactive' => false,
    ),
    'zgh' => 
    array (
      'label' => 'Standard Moroccan Tamazight',
      'inactive' => false,
    ),
    'suk' => 
    array (
      'label' => 'Sukuma',
      'inactive' => false,
    ),
    'sux' => 
    array (
      'label' => 'Sumerian',
      'inactive' => false,
    ),
    'su' => 
    array (
      'label' => 'Sundanese',
      'inactive' => false,
    ),
    'sus' => 
    array (
      'label' => 'Susu',
      'inactive' => false,
    ),
    'sw' => 
    array (
      'label' => 'Swahili',
      'inactive' => false,
    ),
    'ss' => 
    array (
      'label' => 'Swati',
      'inactive' => false,
    ),
    'sv' => 
    array (
      'label' => 'Swedish',
      'inactive' => false,
    ),
    'fr_CH' => 
    array (
      'label' => 'Swiss French',
      'inactive' => false,
    ),
    'gsw' => 
    array (
      'label' => 'Swiss German',
      'inactive' => false,
    ),
    'de_CH' => 
    array (
      'label' => 'Swiss High German',
      'inactive' => false,
    ),
    'syr' => 
    array (
      'label' => 'Syriac',
      'inactive' => false,
    ),
    'shi' => 
    array (
      'label' => 'Tachelhit',
      'inactive' => false,
    ),
    'tl' => 
    array (
      'label' => 'Tagalog',
      'inactive' => false,
    ),
    'ty' => 
    array (
      'label' => 'Tahitian',
      'inactive' => false,
    ),
    'dav' => 
    array (
      'label' => 'Taita',
      'inactive' => false,
    ),
    'tg' => 
    array (
      'label' => 'Tajik',
      'inactive' => false,
    ),
    'tly' => 
    array (
      'label' => 'Talysh',
      'inactive' => false,
    ),
    'tmh' => 
    array (
      'label' => 'Tamashek',
      'inactive' => false,
    ),
    'ta' => 
    array (
      'label' => 'Tamil',
      'inactive' => false,
    ),
    'trv' => 
    array (
      'label' => 'Taroko',
      'inactive' => false,
    ),
    'twq' => 
    array (
      'label' => 'Tasawaq',
      'inactive' => false,
    ),
    'tt' => 
    array (
      'label' => 'Tatar',
      'inactive' => false,
    ),
    'te' => 
    array (
      'label' => 'Telugu',
      'inactive' => false,
    ),
    'ter' => 
    array (
      'label' => 'Tereno',
      'inactive' => false,
    ),
    'teo' => 
    array (
      'label' => 'Teso',
      'inactive' => false,
    ),
    'tet' => 
    array (
      'label' => 'Tetum',
      'inactive' => false,
    ),
    'th' => 
    array (
      'label' => 'Thai',
      'inactive' => false,
    ),
    'bo' => 
    array (
      'label' => 'Tibetan',
      'inactive' => false,
    ),
    'tig' => 
    array (
      'label' => 'Tigre',
      'inactive' => false,
    ),
    'ti' => 
    array (
      'label' => 'Tigrinya',
      'inactive' => false,
    ),
    'tem' => 
    array (
      'label' => 'Timne',
      'inactive' => false,
    ),
    'tiv' => 
    array (
      'label' => 'Tiv',
      'inactive' => false,
    ),
    'tli' => 
    array (
      'label' => 'Tlingit',
      'inactive' => false,
    ),
    'tpi' => 
    array (
      'label' => 'Tok Pisin',
      'inactive' => false,
    ),
    'tkl' => 
    array (
      'label' => 'Tokelau',
      'inactive' => false,
    ),
    'to' => 
    array (
      'label' => 'Tongan',
      'inactive' => false,
    ),
    'fit' => 
    array (
      'label' => 'Tornedalen Finnish',
      'inactive' => false,
    ),
    'zh_Hant' => 
    array (
      'label' => 'Traditional Chinese',
      'inactive' => false,
    ),
    'tkr' => 
    array (
      'label' => 'Tsakhur',
      'inactive' => false,
    ),
    'tsd' => 
    array (
      'label' => 'Tsakonian',
      'inactive' => false,
    ),
    'tsi' => 
    array (
      'label' => 'Tsimshian',
      'inactive' => false,
    ),
    'ts' => 
    array (
      'label' => 'Tsonga',
      'inactive' => false,
    ),
    'tn' => 
    array (
      'label' => 'Tswana',
      'inactive' => false,
    ),
    'tcy' => 
    array (
      'label' => 'Tulu',
      'inactive' => false,
    ),
    'tum' => 
    array (
      'label' => 'Tumbuka',
      'inactive' => false,
    ),
    'aeb' => 
    array (
      'label' => 'Tunisian Arabic',
      'inactive' => false,
    ),
    'tr' => 
    array (
      'label' => 'Turkish',
      'inactive' => false,
    ),
    'tk' => 
    array (
      'label' => 'Turkmen',
      'inactive' => false,
    ),
    'tru' => 
    array (
      'label' => 'Turoyo',
      'inactive' => false,
    ),
    'tvl' => 
    array (
      'label' => 'Tuvalu',
      'inactive' => false,
    ),
    'tyv' => 
    array (
      'label' => 'Tuvinian',
      'inactive' => false,
    ),
    'tw' => 
    array (
      'label' => 'Twi',
      'inactive' => false,
    ),
    'kcg' => 
    array (
      'label' => 'Tyap',
      'inactive' => false,
    ),
    'udm' => 
    array (
      'label' => 'Udmurt',
      'inactive' => false,
    ),
    'uga' => 
    array (
      'label' => 'Ugaritic',
      'inactive' => false,
    ),
    'uk' => 
    array (
      'label' => 'Ukrainian',
      'inactive' => false,
    ),
    'umb' => 
    array (
      'label' => 'Umbundu',
      'inactive' => false,
    ),
    'und' => 
    array (
      'label' => 'Unknown Language',
      'inactive' => false,
    ),
    'hsb' => 
    array (
      'label' => 'Upper Sorbian',
      'inactive' => false,
    ),
    'ur' => 
    array (
      'label' => 'Urdu',
      'inactive' => false,
    ),
    'ug' => 
    array (
      'label' => 'Uyghur',
      'inactive' => false,
    ),
    'uz' => 
    array (
      'label' => 'Uzbek',
      'inactive' => false,
    ),
    'vai' => 
    array (
      'label' => 'Vai',
      'inactive' => false,
    ),
    've' => 
    array (
      'label' => 'Venda',
      'inactive' => false,
    ),
    'vec' => 
    array (
      'label' => 'Venetian',
      'inactive' => false,
    ),
    'vep' => 
    array (
      'label' => 'Veps',
      'inactive' => false,
    ),
    'vi' => 
    array (
      'label' => 'Vietnamese',
      'inactive' => false,
    ),
    'vo' => 
    array (
      'label' => 'Volapük',
      'inactive' => false,
    ),
    'vro' => 
    array (
      'label' => 'Võro',
      'inactive' => false,
    ),
    'vot' => 
    array (
      'label' => 'Votic',
      'inactive' => false,
    ),
    'vun' => 
    array (
      'label' => 'Vunjo',
      'inactive' => false,
    ),
    'wa' => 
    array (
      'label' => 'Walloon',
      'inactive' => false,
    ),
    'wae' => 
    array (
      'label' => 'Walser',
      'inactive' => false,
    ),
    'war' => 
    array (
      'label' => 'Waray',
      'inactive' => false,
    ),
    'was' => 
    array (
      'label' => 'Washo',
      'inactive' => false,
    ),
    'guc' => 
    array (
      'label' => 'Wayuu',
      'inactive' => false,
    ),
    'cy' => 
    array (
      'label' => 'Welsh',
      'inactive' => false,
    ),
    'vls' => 
    array (
      'label' => 'West Flemish',
      'inactive' => false,
    ),
    'fy' => 
    array (
      'label' => 'Western Frisian',
      'inactive' => false,
    ),
    'mrj' => 
    array (
      'label' => 'Western Mari',
      'inactive' => false,
    ),
    'wal' => 
    array (
      'label' => 'Wolaytta',
      'inactive' => false,
    ),
    'wo' => 
    array (
      'label' => 'Wolof',
      'inactive' => false,
    ),
    'wuu' => 
    array (
      'label' => 'Wu Chinese',
      'inactive' => false,
    ),
    'xh' => 
    array (
      'label' => 'Xhosa',
      'inactive' => false,
    ),
    'hsn' => 
    array (
      'label' => 'Xiang Chinese',
      'inactive' => false,
    ),
    'yav' => 
    array (
      'label' => 'Yangben',
      'inactive' => false,
    ),
    'yao' => 
    array (
      'label' => 'Yao',
      'inactive' => false,
    ),
    'yap' => 
    array (
      'label' => 'Yapese',
      'inactive' => false,
    ),
    'ybb' => 
    array (
      'label' => 'Yemba',
      'inactive' => false,
    ),
    'yi' => 
    array (
      'label' => 'Yiddish',
      'inactive' => false,
    ),
    'yo' => 
    array (
      'label' => 'Yoruba',
      'inactive' => false,
    ),
    'zap' => 
    array (
      'label' => 'Zapotec',
      'inactive' => false,
    ),
    'dje' => 
    array (
      'label' => 'Zarma',
      'inactive' => false,
    ),
    'zza' => 
    array (
      'label' => 'Zaza',
      'inactive' => false,
    ),
    'zea' => 
    array (
      'label' => 'Zeelandic',
      'inactive' => false,
    ),
    'zen' => 
    array (
      'label' => 'Zenaga',
      'inactive' => false,
    ),
    'za' => 
    array (
      'label' => 'Zhuang',
      'inactive' => false,
    ),
    'gbz' => 
    array (
      'label' => 'Zoroastrian Dari',
      'inactive' => false,
    ),
    'zu' => 
    array (
      'label' => 'Zulu',
      'inactive' => false,
    ),
    'zun' => 
    array (
      'label' => 'Zuni',
      'inactive' => false,
    ),
  ),
  'titles' => 
  array (
    'Mr' => 
    array (
      'label' => 'Mr',
      'inactive' => false,
    ),
    'Mrs' => 
    array (
      'label' => 'Mrs',
      'inactive' => false,
    ),
    'Ms' => 
    array (
      'label' => 'Ms',
      'inactive' => false,
    ),
    'Dr' => 
    array (
      'label' => 'Dr',
      'inactive' => false,
    ),
  ),
);

    protected $fields = array (
);

    protected $menus = array (
  'admin_menu' => 
  array (
    0 => 
    array (
      'label' => 'Access Control',
      'desc' => 'Manage access control',
      'url' => '#',
      'icon' => 'lock',
      'order' => 10,
      'children' => 
      array (
        0 => 
        array (
          'label' => 'Groups',
          'desc' => 'Manage system groups',
          'url' => '/groups/groups/',
          'icon' => 'users',
          'order' => 20,
        ),
        1 => 
        array (
          'label' => 'Roles',
          'desc' => 'Manage system roles',
          'url' => '/roles-capabilities/roles/',
          'icon' => 'unlock',
          'order' => 30,
        ),
      ),
    ),
    1 => 
    array (
      'label' => 'Settings',
      'desc' => 'Manage settings',
      'url' => '#',
      'icon' => 'cog',
      'order' => 20,
      'children' => 
      array (
        0 => 
        array (
          'label' => 'Menus',
          'desc' => 'Manage system menus',
          'url' => '/menu/menus/',
          'icon' => 'bars',
          'order' => 40,
        ),
        1 => 
        array (
          'label' => 'Lists',
          'desc' => 'Manage database lists',
          'url' => '/csv-migrations/dblists/',
          'icon' => 'list',
          'order' => 50,
        ),
        2 => 
        array (
          'label' => 'Languages',
          'desc' => 'Manage languages',
          'url' => '/language-translations/languages',
          'icon' => 'language',
          'order' => 55,
        ),
        3 => 
        array (
          'label' => 'System Searches',
          'desc' => 'Manage system searches',
          'url' => '/system/searches',
          'icon' => 'search',
          'order' => 60,
        ),
      ),
    ),
    2 => 
    array (
      'label' => 'System',
      'desc' => 'Manage system',
      'url' => '#',
      'icon' => 'desktop',
      'order' => 30,
      'children' => 
      array (
        0 => 
        array (
          'label' => 'Swagger',
          'desc' => 'View swagger documentation',
          'url' => '/swagger/',
          'icon' => 'exchange',
          'target' => '_blank',
          'order' => 50,
        ),
        1 => 
        array (
          'label' => 'Logs',
          'desc' => 'View system logs',
          'url' => '/logs/',
          'icon' => 'list-alt',
          'order' => 60,
        ),
        2 => 
        array (
          'label' => 'Information',
          'desc' => 'System information screen',
          'url' => '/system/info/',
          'icon' => 'info-circle',
          'order' => 70,
        ),
      ),
    ),
  ),
);

    protected $views = array (
);
}
