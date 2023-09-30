<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CountrySeeder extends Seeder {

    public function run()
    {
        DB::table('countries')->delete();

        $countries = array(
            array('code' => 'US', 'name' => 'United States', 'timezone' => 'America/New_York', 'continent_id' => 5),
            array('code' => 'CA', 'name' => 'Canada', 'timezone' => 'America/Toronto', 'continent_id' => 5),
            array('code' => 'AF', 'name' => 'Afghanistan', 'timezone' => 'Asia/Kabul', 'continent_id' => 3),
            array('code' => 'AL', 'name' => 'Albania', 'timezone' => 'Europe/Tirane', 'continent_id' => 4),
            array('code' => 'DZ', 'name' => 'Algeria', 'timezone' => 'Africa/Algiers', 'continent_id' => 1),
            array('code' => 'AS', 'name' => 'American Samoa', 'timezone' => 'Pacific/Pago_Pago', 'continent_id' => 6),
            array('code' => 'AD', 'name' => 'Andorra', 'timezone' => 'Europe/Andorra', 'continent_id' => 4),
            array('code' => 'AO', 'name' => 'Angola', 'timezone' => 'Africa/Luanda', 'continent_id' => 1),
            array('code' => 'AI', 'name' => 'Anguilla', 'timezone' => 'America/Anguilla', 'continent_id' => 5),
            array('code' => 'AQ', 'name' => 'Antarctica', 'timezone' => 'Antarctica/Casey', 'continent_id' => 2),
            array('code' => 'AG', 'name' => 'Antigua and/or Barbuda', 'timezone' => 'America/Antigua', 'continent_id' => 5),
            array('code' => 'AR', 'name' => 'Argentina', 'timezone' => 'America/Argentina/Buenos_Aires', 'continent_id' => 7),
            array('code' => 'AM', 'name' => 'Armenia', 'timezone' => 'Asia/Yerevan', 'continent_id' => 3),
            array('code' => 'AW', 'name' => 'Aruba', 'timezone' => 'America/Aruba', 'continent_id' => 5),
            array('code' => 'AU', 'name' => 'Australia', 'timezone' => 'Australia/Sydney', 'continent_id' => 6),
            array('code' => 'AT', 'name' => 'Austria', 'timezone' => 'Europe/Vienna', 'continent_id' => 4),
            array('code' => 'AZ', 'name' => 'Azerbaijan', 'timezone' => 'Asia/Baku', 'continent_id' => 3),
            array('code' => 'BS', 'name' => 'Bahamas', 'timezone' => 'America/Nassau', 'continent_id' => 5),
            array('code' => 'BH', 'name' => 'Bahrain', 'timezone' => 'Asia/Bahrain', 'continent_id' => 3),
            array('code' => 'BD', 'name' => 'Bangladesh', 'timezone' => 'Asia/Dhaka', 'continent_id' => 3),
            array('code' => 'BB', 'name' => 'Barbados', 'timezone' => 'America/Barbados', 'continent_id' => 5),
            array('code' => 'BY', 'name' => 'Belarus', 'timezone' => 'Europe/Minsk', 'continent_id' => 4),
            array('code' => 'BE', 'name' => 'Belgium', 'timezone' => 'Europe/Brussels', 'continent_id' => 4),
            array('code' => 'BZ', 'name' => 'Belize', 'timezone' => 'America/Belize', 'continent_id' => 5),
            array('code' => 'BJ', 'name' => 'Benin', 'timezone' => 'Africa/Porto-Novo', 'continent_id' => 1),
            array('code' => 'BM', 'name' => 'Bermuda', 'timezone' => 'Atlantic/Bermuda', 'continent_id' => 5),
            array('code' => 'BT', 'name' => 'Bhutan', 'timezone' => 'Asia/Thimphu', 'continent_id' => 3),
            array('code' => 'BO', 'name' => 'Bolivia', 'timezone' => 'America/La_Paz', 'continent_id' => 7),
            array('code' => 'BA', 'name' => 'Bosnia and Herzegovina', 'timezone' => 'Europe/Sarajevo', 'continent_id' => 4),
            array('code' => 'BW', 'name' => 'Botswana', 'timezone' => 'Africa/Gaborone', 'continent_id' => 1),
//            array('code' => 'BV', 'name' => 'Bouvet Island', 'timezone' => 'Europe/Oslo', 'continent_id' => 2),
            array('code' => 'BR', 'name' => 'Brazil', 'timezone' => 'America/Sao_Paulo', 'continent_id' => 7),
//            array('code' => 'IO', 'name' => 'British lndian Ocean Territory', 'timezone' => 'Indian/Chagos', 'continent_id' => 2),
            array('code' => 'BN', 'name' => 'Brunei Darussalam', 'timezone' => 'Asia/Brunei', 'continent_id' => 3),
            array('code' => 'BG', 'name' => 'Bulgaria', 'timezone' => 'Europe/Sofia', 'continent_id' => 4),
            array('code' => 'BF', 'name' => 'Burkina Faso', 'timezone' => 'Africa/Ouagadougou', 'continent_id' => 1),
            array('code' => 'BI', 'name' => 'Burundi', 'timezone' => 'Africa/Bujumbura', 'continent_id' => 1),
            array('code' => 'KH', 'name' => 'Cambodia', 'timezone' => 'Asia/Phnom_Penh', 'continent_id' => 3),
            array('code' => 'CM', 'name' => 'Cameroon', 'timezone' => 'Africa/Douala', 'continent_id' => 1),
            array('code' => 'CV', 'name' => 'Cape Verde', 'timezone' => 'Atlantic/Cape_Verde', 'continent_id' => 1),
            array('code' => 'KY', 'name' => 'Cayman Islands', 'timezone' => 'America/Cayman', 'continent_id' => 5),
            array('code' => 'CF', 'name' => 'Central African Republic', 'timezone' => 'Africa/Bangui', 'continent_id' => 1),
            array('code' => 'TD', 'name' => 'Chad', 'timezone' => 'Africa/Ndjamena', 'continent_id' => 1),
            array('code' => 'CL', 'name' => 'Chile', 'timezone' => 'America/Santiago', 'continent_id' => 7),
            array('code' => 'CN', 'name' => 'China', 'timezone' => 'Asia/Shanghai', 'continent_id' => 3),
            array('code' => 'CX', 'name' => 'Christmas Island', 'timezone' => 'Indian/Christmas', 'continent_id' => 6),
            array('code' => 'CC', 'name' => 'Cocos (Keeling) Islands', 'timezone' => 'Indian/Cocos', 'continent_id' => 6),
            array('code' => 'CO', 'name' => 'Colombia', 'timezone' => 'America/Bogota', 'continent_id' => 7),
            array('code' => 'KM', 'name' => 'Comoros', 'timezone' => 'Indian/Comoro', 'continent_id' => 1),
            array('code' => 'CG', 'name' => 'Congo', 'timezone' => 'Africa/Brazzaville', 'continent_id' => 1),
            array('code' => 'CK', 'name' => 'Cook Islands', 'timezone' => 'Pacific/Rarotonga', 'continent_id' => 6),
            array('code' => 'CR', 'name' => 'Costa Rica', 'timezone' => 'America/Costa_Rica', 'continent_id' => 5),
            array('code' => 'HR', 'name' => 'Croatia', 'timezone' => 'Europe/Zagreb', 'continent_id' => 4),
            array('code' => 'CU', 'name' => 'Cuba', 'timezone' => 'America/Havana', 'continent_id' => 5),
            array('code' => 'CY', 'name' => 'Cyprus', 'timezone' => 'Asia/Nicosia', 'continent_id' => 3),
            array('code' => 'CZ', 'name' => 'Czech Republic', 'timezone' => 'Europe/Prague', 'continent_id' => 4),
            array('code' => 'DK', 'name' => 'Denmark', 'timezone' => 'Europe/Copenhagen', 'continent_id' => 4),
            array('code' => 'DJ', 'name' => 'Djibouti', 'timezone' => 'Africa/Djibouti', 'continent_id' => 1),
            array('code' => 'DM', 'name' => 'Dominica', 'timezone' => 'America/Dominica', 'continent_id' => 5),
            array('code' => 'DO', 'name' => 'Dominican Republic', 'timezone' => 'America/Santo_Domingo', 'continent_id' => 5),
//            array('code' => 'TP', 'name' => 'East Timor', 'timezone' => 'Asia/Dili', 'continent_id' => 6),
            array('code' => 'EC', 'name' => 'Ecudaor', 'timezone' => 'America/Guayaquil', 'continent_id' => 7),
            array('code' => 'EG', 'name' => 'Egypt', 'timezone' => 'Africa/Cairo', 'continent_id' => 1),
            array('code' => 'SV', 'name' => 'El Salvador', 'timezone' => 'America/El_Salvador', 'continent_id' => 5),
            array('code' => 'GQ', 'name' => 'Equatorial Guinea', 'timezone' => 'Africa/Malabo', 'continent_id' => 1),
            array('code' => 'ER', 'name' => 'Eritrea', 'timezone' => 'Africa/Asmara', 'continent_id' => 1),
            array('code' => 'EE', 'name' => 'Estonia', 'timezone' => 'Europe/Tallinn', 'continent_id' => 4),
            array('code' => 'ET', 'name' => 'Ethiopia', 'timezone' => 'Africa/Addis_Ababa', 'continent_id' => 1),
            array('code' => 'FK', 'name' => 'Falkland Islands (Malvinas)', 'timezone' => 'Atlantic/Stanley', 'continent_id' => 7),
            array('code' => 'FO', 'name' => 'Faroe Islands', 'timezone' => 'Atlantic/Faroe', 'continent_id' => 4),
            array('code' => 'FJ', 'name' => 'Fiji', 'timezone' => 'Pacific/Fiji', 'continent_id' => 6),
            array('code' => 'FI', 'name' => 'Finland', 'timezone' => 'Europe/Helsinki', 'continent_id' => 4),
            array('code' => 'FR', 'name' => 'France', 'timezone' => 'Europe/Paris', 'continent_id' => 4),
//            array('code' => 'FX', 'name' => 'France, Metropolitan', 'timezone' => 'Europe/Paris', 'continent_id' => 4),
//            array('code' => 'GF', 'name' => 'French Guiana', 'timezone' => 'America/Cayenne', 'continent_id' => 7),
            array('code' => 'PF', 'name' => 'French Polynesia', 'timezone' => 'Pacific/Tahiti', 'continent_id' => 6),
            array('code' => 'TF', 'name' => 'French Southern Territories', 'timezone' => 'Indian/Kerguelen', 'continent_id' => 2),
            array('code' => 'GA', 'name' => 'Gabon', 'timezone' => 'Africa/Libreville', 'continent_id' => 1),
            array('code' => 'GM', 'name' => 'Gambia', 'timezone' => 'Africa/Banjul', 'continent_id' => 1),
            array('code' => 'GE', 'name' => 'Georgia', 'timezone' => 'Asia/Tbilisi', 'continent_id' => 3),
            array('code' => 'DE', 'name' => 'Germany', 'timezone' => 'Europe/Berlin', 'continent_id' => 4),
            array('code' => 'GH', 'name' => 'Ghana', 'timezone' => 'Africa/Accra', 'continent_id' => 1),
            array('code' => 'GI', 'name' => 'Gibraltar', 'timezone' => 'Europe/Gibraltar', 'continent_id' => 4),
            array('code' => 'GR', 'name' => 'Greece', 'timezone' => 'Europe/Athens', 'continent_id' => 4),
            array('code' => 'GL', 'name' => 'Greenland', 'timezone' => 'America/Godthab', 'continent_id' => 4),
            array('code' => 'GD', 'name' => 'Grenada', 'timezone' => 'America/Grenada', 'continent_id' => 5),
//            array('code' => 'GP', 'name' => 'Guadeloupe', 'timezone' => 'America/Guadeloupe', 'continent_id' => 5),
            array('code' => 'GU', 'name' => 'Guam', 'timezone' => 'Pacific/Guam', 'continent_id' => 6),
            array('code' => 'GT', 'name' => 'Guatemala', 'timezone' => 'America/Guatemala', 'continent_id' => 5),
            array('code' => 'GN', 'name' => 'Guinea', 'timezone' => 'Africa/Conakry', 'continent_id' => 1),
            array('code' => 'GW', 'name' => 'Guinea-Bissau', 'timezone' => 'Africa/Bissau', 'continent_id' => 1),
            array('code' => 'GY', 'name' => 'Guyana', 'timezone' => 'America/Guyana', 'continent_id' => 7),
            array('code' => 'HT', 'name' => 'Haiti', 'timezone' => 'America/Port-au-Prince', 'continent_id' => 5),
//            array('code' => 'HM', 'name' => 'Heard and Mc Donald Islands', 'timezone' => 'Indian/Kerguelen', 'continent_id' => 2),
            array('code' => 'HN', 'name' => 'Honduras', 'timezone' => 'America/Tegucigalpa', 'continent_id' => 5),
            array('code' => 'HK', 'name' => 'Hong Kong', 'timezone' => 'Asia/Hong_Kong', 'continent_id' => 3),
            array('code' => 'HU', 'name' => 'Hungary', 'timezone' => 'Europe/Budapest', 'continent_id' => 4),
            array('code' => 'IS', 'name' => 'Iceland', 'timezone' => 'Atlantic/Reykjavik', 'continent_id' => 4),
            array('code' => 'IN', 'name' => 'India', 'timezone' => 'Asia/Kolkata', 'continent_id' => 3),
            array('code' => 'ID', 'name' => 'Indonesia', 'timezone' => 'Asia/Jakarta', 'continent_id' => 3),
            array('code' => 'IR', 'name' => 'Iran', 'timezone' => 'Asia/Tehran', 'continent_id' => 3),
            array('code' => 'IQ', 'name' => 'Iraq', 'timezone' => 'Asia/Baghdad', 'continent_id' => 3),
            array('code' => 'IE', 'name' => 'Ireland', 'timezone' => 'Europe/Dublin', 'continent_id' => 4),
            array('code' => 'IL', 'name' => 'Israel', 'timezone' => 'Asia/Jerusalem', 'continent_id' => 3),
            array('code' => 'IT', 'name' => 'Italy', 'timezone' => 'Europe/Rome', 'continent_id' => 4),
            array('code' => 'CI', 'name' => 'Ivory Coast', 'timezone' => 'Africa/Abidjan', 'continent_id' => 1),
            array('code' => 'JM', 'name' => 'Jamaica', 'timezone' => 'America/Jamaica', 'continent_id' => 5),
            array('code' => 'JP', 'name' => 'Japan', 'timezone' => 'Asia/Tokyo', 'continent_id' => 3),
            array('code' => 'JO', 'name' => 'Jordan', 'timezone' => 'Asia/Amman', 'continent_id' => 3),
            array('code' => 'KZ', 'name' => 'Kazakhstan', 'timezone' => 'Asia/Almaty', 'continent_id' => 3),
            array('code' => 'KE', 'name' => 'Kenya', 'timezone' => 'Africa/Nairobi', 'continent_id' => 1),
            array('code' => 'KI', 'name' => 'Kiribati', 'timezone' => 'Pacific/Tarawa', 'continent_id' => 6),
            array('code' => 'KP', 'name' => 'North Korea', 'timezone' => 'Asia/Pyongyang', 'continent_id' => 3),
            array('code' => 'KR', 'name' => 'South Korea', 'timezone' => 'Asia/Seoul', 'continent_id' => 3),
            array('code' => 'KW', 'name' => 'Kuwait', 'timezone' => 'Asia/Kuwait', 'continent_id' => 3),
            array('code' => 'KG', 'name' => 'Kyrgyzstan', 'timezone' => 'Asia/Bishkek', 'continent_id' => 3),
            array('code' => 'LA', 'name' => 'Lao People\'s Democratic Republic', 'timezone' => 'Asia/Vientiane', 'continent_id' => 3),
            array('code' => 'LV', 'name' => 'Latvia', 'timezone' => 'Europe/Riga', 'continent_id' => 4),
            array('code' => 'LB', 'name' => 'Lebanon', 'timezone' => 'Asia/Beirut', 'continent_id' => 3),
            array('code' => 'LS', 'name' => 'Lesotho', 'timezone' => 'Africa/Maseru', 'continent_id' => 1),
            array('code' => 'LR', 'name' => 'Liberia', 'timezone' => 'Africa/Monrovia', 'continent_id' => 1),
            array('code' => 'LY', 'name' => 'Libyan Arab Jamahiriya', 'timezone' => 'Africa/Tripoli', 'continent_id' => 1),
            array('code' => 'LI', 'name' => 'Liechtenstein', 'timezone' => 'Europe/Vaduz', 'continent_id' => 4),
            array('code' => 'LT', 'name' => 'Lithuania', 'timezone' => 'Europe/Vilnius', 'continent_id' => 4),
            array('code' => 'LU', 'name' => 'Luxembourg', 'timezone' => 'Europe/Luxembourg', 'continent_id' => 4),
            array('code' => 'MO', 'name' => 'Macau', 'timezone' => 'Asia/Macau', 'continent_id' => 3),
            array('code' => 'MK', 'name' => 'Macedonia', 'timezone' => 'Europe/Skopje', 'continent_id' => 4),
            array('code' => 'MG', 'name' => 'Madagascar', 'timezone' => 'Indian/Antananarivo', 'continent_id' => 1),
            array('code' => 'MW', 'name' => 'Malawi', 'timezone' => 'Africa/Blantyre', 'continent_id' => 1),
            array('code' => 'MY', 'name' => 'Malaysia', 'timezone' => 'Asia/Kuala_Lumpur', 'continent_id' => 3),
            array('code' => 'MV', 'name' => 'Maldives', 'timezone' => 'Indian/Maldives', 'continent_id' => 3),
            array('code' => 'ML', 'name' => 'Mali', 'timezone' => 'Africa/Bamako', 'continent_id' => 1),
            array('code' => 'MT', 'name' => 'Malta', 'timezone' => 'Europe/Malta', 'continent_id' => 4),
            array('code' => 'MH', 'name' => 'Marshall Islands', 'timezone' => 'Pacific/Majuro', 'continent_id' => 6),
            array('code' => 'MQ', 'name' => 'Martinique', 'timezone' => 'America/Martinique', 'continent_id' => 5),
            array('code' => 'MR', 'name' => 'Mauritania', 'timezone' => 'Africa/Nouakchott', 'continent_id' => 1),
            array('code' => 'MU', 'name' => 'Mauritius', 'timezone' => 'Indian/Mauritius', 'continent_id' => 1),
            array('code' => 'YT', 'name' => 'Mayotte', 'timezone' => 'Indian/Mayotte', 'continent_id' => 1),
            array('code' => 'MX', 'name' => 'Mexico', 'timezone' => 'America/Mexico_City', 'continent_id' => 5),
            array('code' => 'FM', 'name' => 'Micronesia', 'timezone' => 'Pacific/Chuuk', 'continent_id' => 6),
            array('code' => 'MD', 'name' => 'Moldova', 'timezone' => 'Europe/Chisinau', 'continent_id' => 4),
            array('code' => 'MC', 'name' => 'Monaco', 'timezone' => 'Europe/Monaco', 'continent_id' => 4),
            array('code' => 'MN', 'name' => 'Mongolia', 'timezone' => 'Asia/Ulaanbaatar', 'continent_id' => 3),
            array('code' => 'MS', 'name' => 'Montserrat', 'timezone' => 'America/Montserrat', 'continent_id' => 5),
            array('code' => 'MA', 'name' => 'Morocco', 'timezone' => 'Africa/Casablanca', 'continent_id' => 1),
            array('code' => 'MZ', 'name' => 'Mozambique', 'timezone' => 'Africa/Maputo', 'continent_id' => 1),
            array('code' => 'MM', 'name' => 'Myanmar', 'timezone' => 'Asia/Yangon', 'continent_id' => 3),
            array('code' => 'NA', 'name' => 'Namibia', 'timezone' => 'Africa/Windhoek', 'continent_id' => 1),
            array('code' => 'NR', 'name' => 'Nauru', 'timezone' => 'Pacific/Nauru', 'continent_id' => 6),
            array('code' => 'NP', 'name' => 'Nepal', 'timezone' => 'Asia/Kathmandu', 'continent_id' => 3),
            array('code' => 'NL', 'name' => 'Netherlands', 'timezone' => 'Europe/Amsterdam', 'continent_id' => 4),
            array('code' => 'AN', 'name' => 'Netherlands Antilles', 'timezone' => 'America/Curacao', 'continent_id' => 5),
            array('code' => 'NC', 'name' => 'New Caledonia', 'timezone' => 'Pacific/Noumea', 'continent_id' => 6),
            array('code' => 'NZ', 'name' => 'New Zealand', 'timezone' => 'Pacific/Auckland', 'continent_id' => 6),
            array('code' => 'NI', 'name' => 'Nicaragua', 'timezone' => 'America/Managua', 'continent_id' => 5),
            array('code' => 'NE', 'name' => 'Niger', 'timezone' => 'Africa/Niamey', 'continent_id' => 1),
            array('code' => 'NG', 'name' => 'Nigeria', 'timezone' => 'Africa/Lagos', 'continent_id' => 1),
            array('code' => 'NU', 'name' => 'Niue', 'timezone' => 'Pacific/Niue', 'continent_id' => 6),
            array('code' => 'NF', 'name' => 'Norfork Island', 'timezone' => 'Pacific/Norfolk', 'continent_id' => 6),
            array('code' => 'MP', 'name' => 'Northern Mariana Islands', 'timezone' => 'Pacific/Saipan', 'continent_id' => 6),
            array('code' => 'NO', 'name' => 'Norway', 'timezone' => 'Europe/Oslo', 'continent_id' => 4),
            array('code' => 'OM', 'name' => 'Oman', 'timezone' => 'Asia/Muscat', 'continent_id' => 3),
            array('code' => 'PK', 'name' => 'Pakistan', 'timezone' => 'Asia/Karachi', 'continent_id' => 3),
            array('code' => 'PW', 'name' => 'Palau', 'timezone' => 'Pacific/Palau', 'continent_id' => 6),
            array('code' => 'PA', 'name' => 'Panama', 'timezone' => 'America/Panama', 'continent_id' => 5),
            array('code' => 'PG', 'name' => 'Papua New Guinea', 'timezone' => 'Pacific/Port_Moresby', 'continent_id' => 6),
            array('code' => 'PY', 'name' => 'Paraguay', 'timezone' => 'America/Asuncion', 'continent_id' => 7),
            array('code' => 'PE', 'name' => 'Peru', 'timezone' => 'America/Lima', 'continent_id' => 7),
            array('code' => 'PH', 'name' => 'Philippines', 'timezone' => 'Asia/Manila', 'continent_id' => 3),
            array('code' => 'PN', 'name' => 'Pitcairn', 'timezone' => 'Pacific/Pitcairn', 'continent_id' => 6),
            array('code' => 'PL', 'name' => 'Poland', 'timezone' => 'Europe/Warsaw', 'continent_id' => 4),
            array('code' => 'PT', 'name' => 'Portugal', 'timezone' => 'Europe/Lisbon', 'continent_id' => 4),
            array('code' => 'PR', 'name' => 'Puerto Rico', 'timezone' => 'America/Puerto_Rico', 'continent_id' => 5),
            array('code' => 'QA', 'name' => 'Qatar', 'timezone' => 'Asia/Qatar', 'continent_id' => 3),
            array('code' => 'RE', 'name' => 'Reunion', 'timezone' => 'Indian/Reunion', 'continent_id' => 1),
            array('code' => 'RO', 'name' => 'Romania', 'timezone' => 'Europe/Bucharest', 'continent_id' => 4),
            array('code' => 'RU', 'name' => 'Russian Federation', 'timezone' => 'Europe/Moscow', 'continent_id' => 4),
            array('code' => 'RW', 'name' => 'Rwanda', 'timezone' => 'Africa/Kigali', 'continent_id' => 1),
            array('code' => 'KN', 'name' => 'Saint Kitts and Nevis', 'timezone' => 'America/St_Kitts', 'continent_id' => 5),
            array('code' => 'LC', 'name' => 'Saint Lucia', 'timezone' => 'America/St_Lucia', 'continent_id' => 5),
            array('code' => 'VC', 'name' => 'Saint Vincent and the Grenadines', 'timezone' => 'America/St_Vincent', 'continent_id' => 5),
            array('code' => 'WS', 'name' => 'Samoa', 'timezone' => 'Pacific/Apia', 'continent_id' => 6),
            array('code' => 'SM', 'name' => 'San Marino', 'timezone' => 'Europe/San_Marino', 'continent_id' => 4),
            array('code' => 'ST', 'name' => 'Sao Tome and Principe', 'timezone' => 'Africa/Sao_Tome', 'continent_id' => 1),
            array('code' => 'SA', 'name' => 'Saudi Arabia', 'timezone' => 'Asia/Riyadh', 'continent_id' => 3),
            array('code' => 'SN', 'name' => 'Senegal', 'timezone' => 'Africa/Dakar', 'continent_id' => 1),
            array('code' => 'SC', 'name' => 'Seychelles', 'timezone' => 'Indian/Mahe', 'continent_id' => 1),
            array('code' => 'SL', 'name' => 'Sierra Leone', 'timezone' => 'Africa/Freetown', 'continent_id' => 1),
            array('code' => 'SG', 'name' => 'Singapore', 'timezone' => 'Asia/Singapore', 'continent_id' => 3),
            array('code' => 'SK', 'name' => 'Slovakia', 'timezone' => 'Europe/Bratislava', 'continent_id' => 4),
            array('code' => 'SI', 'name' => 'Slovenia', 'timezone' => 'Europe/Ljubljana', 'continent_id' => 4),
            array('code' => 'SB', 'name' => 'Solomon Islands', 'timezone' => 'Pacific/Guadalcanal', 'continent_id' => 6),
            array('code' => 'SO', 'name' => 'Somalia', 'timezone' => 'Africa/Mogadishu', 'continent_id' => 1),
            array('code' => 'ZA', 'name' => 'South Africa', 'timezone' => 'Africa/Johannesburg', 'continent_id' => 1),
            array('code' => 'GS', 'name' => 'South Georgia South Sandwich Islands', 'timezone' => 'Atlantic/South_Georgia', 'continent_id' => 2),
            array('code' => 'ES', 'name' => 'Spain', 'timezone' => 'Europe/Madrid', 'continent_id' => 4),
            array('code' => 'LK', 'name' => 'Sri Lanka', 'timezone' => 'Asia/Colombo', 'continent_id' => 3),
            array('code' => 'SH', 'name' => 'St. Helena', 'timezone' => 'Atlantic/St_Helena', 'continent_id' => 2),
//            array('code' => 'PM', 'name' => 'St. Pierre and Miquelon', 'timezone' => 'America/Miquelon', 'continent_id' => 5),
            array('code' => 'SD', 'name' => 'Sudan', 'timezone' => 'Africa/Khartoum', 'continent_id' => 1),
            array('code' => 'SR', 'name' => 'Suriname', 'timezone' => 'America/Paramaribo', 'continent_id' => 7),
//            array('code' => 'SJ', 'name' => 'Svalbarn and Jan Mayen Islands', 'timezone' => 'Arctic/Longyearbyen', 'continent_id' => 4),
            array('code' => 'SZ', 'name' => 'Swaziland', 'timezone' => 'Africa/Mbabane', 'continent_id' => 1),
            array('code' => 'SE', 'name' => 'Sweden', 'timezone' => 'Europe/Stockholm', 'continent_id' => 4),
            array('code' => 'CH', 'name' => 'Switzerland', 'timezone' => 'Europe/Zurich', 'continent_id' => 4),
            array('code' => 'SY', 'name' => 'Syrian Arab Republic', 'timezone' => 'Asia/Damascus', 'continent_id' => 3),
            array('code' => 'TW', 'name' => 'Taiwan', 'timezone' => 'Asia/Taipei', 'continent_id' => 3),
            array('code' => 'TJ', 'name' => 'Tajikistan', 'timezone' => 'Asia/Dushanbe', 'continent_id' => 3),
            array('code' => 'TZ', 'name' => 'Tanzania', 'timezone' => 'Africa/Dar_es_Salaam', 'continent_id' => 1),
            array('code' => 'TH', 'name' => 'Thailand', 'timezone' => 'Asia/Bangkok', 'continent_id' => 3),
            array('code' => 'TG', 'name' => 'Togo', 'timezone' => 'Africa/Lome', 'continent_id' => 1),
            array('code' => 'TK', 'name' => 'Tokelau', 'timezone' => 'Pacific/Fakaofo', 'continent_id' => 6),
            array('code' => 'TO', 'name' => 'Tonga', 'timezone' => 'Pacific/Tongatapu', 'continent_id' => 6),
            array('code' => 'TT', 'name' => 'Trinidad and Tobago', 'timezone' => 'America/Port_of_Spain', 'continent_id' => 5),
            array('code' => 'TN', 'name' => 'Tunisia', 'timezone' => 'Africa/Tunis', 'continent_id' => 1),
            array('code' => 'TR', 'name' => 'Turkey', 'timezone' => 'Europe/Istanbul', 'continent_id' => 4),
            array('code' => 'TM', 'name' => 'Turkmenistan', 'timezone' => 'Asia/Ashgabat', 'continent_id' => 3),
            array('code' => 'TC', 'name' => 'Turks and Caicos Islands', 'timezone' => 'America/Grand_Turk', 'continent_id' => 5),
            array('code' => 'TV', 'name' => 'Tuvalu', 'timezone' => 'Pacific/Funafuti', 'continent_id' => 6),
            array('code' => 'UG', 'name' => 'Uganda', 'timezone' => 'Africa/Kampala', 'continent_id' => 1),
            array('code' => 'UA', 'name' => 'Ukraine', 'timezone' => 'Europe/Kiev', 'continent_id' => 4),
            array('code' => 'AE', 'name' => 'United Arab Emirates', 'timezone' => 'Asia/Dubai', 'continent_id' => 3),
            array('code' => 'GB', 'name' => 'United Kingdom', 'timezone' => 'Europe/London', 'continent_id' => 4),
            array('code' => 'US', 'name' => 'United States', 'timezone' => 'America/New_York', 'continent_id' => 5),
//            array('code' => 'UM', 'name' => 'United States minor outlying islands', 'timezone' => 'Pacific/Wake', 'continent_id' => 6),
            array('code' => 'UY', 'name' => 'Uruguay', 'timezone' => 'America/Montevideo', 'continent_id' => 7),
            array('code' => 'UZ', 'name' => 'Uzbekistan', 'timezone' => 'Asia/Samarkand', 'continent_id' => 3),
            array('code' => 'VU', 'name' => 'Vanuatu', 'timezone' => 'Pacific/Efate', 'continent_id' => 6),
            array('code' => 'VA', 'name' => 'Vatican City State', 'timezone' => 'Europe/Vatican', 'continent_id' => 4),
            array('code' => 'VE', 'name' => 'Venezuela', 'timezone' => 'America/Caracas', 'continent_id' => 7),
            array('code' => 'VN', 'name' => 'Vietnam', 'timezone' => 'Asia/Saigon', 'continent_id' => 3),
            array('code' => 'VG', 'name' => 'Virgin Islands (British)', 'timezone' => 'America/Tortola', 'continent_id' => 5),
            array('code' => 'VI', 'name' => 'Virgin Islands (U.S.)', 'timezone' => 'America/St_Thomas', 'continent_id' => 5),
            array('code' => 'WF', 'name' => 'Wallis and Futuna Islands', 'timezone' => 'Pacific/Wallis', 'continent_id' => 6),
            array('code' => 'EH', 'name' => 'Western Sahara', 'timezone' => 'Africa/El_Aaiun', 'continent_id' => 1),
            array('code' => 'YE', 'name' => 'Yemen', 'timezone' => 'Asia/Aden', 'continent_id' => 3),
//            array('code' => 'YU', 'name' => 'Yugoslavia', 'timezone' => 'Europe/Belgrade', 'continent_id' => 4),
            array('code' => 'ZM', 'name' => 'Zambia', 'timezone' => 'Africa/Lusaka', 'continent_id' => 1),
            array('code' => 'ZW', 'name' => 'Zimbabwe', 'timezone' => 'Africa/Harare', 'continent_id' => 1),
            array('code' => 'GF', 'name' => 'French Guiana', 'timezone' => 'America/Cayenne', 'continent_id' => 7),
        );

        DB::table('countries')->insert($countries);
    }
}
