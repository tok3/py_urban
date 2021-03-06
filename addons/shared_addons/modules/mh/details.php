<?PHP defined('BASEPATH') or exit('No direct script access allowed');
class Module_mh extends Module {
   public $version = '0.0.6';
   public $namespace = "mh";
   public function info()
   {
	  return array(
				   'name' => array(
								   'en' => 'Material Handling '
								   ),
				   'description' => array(
										  'en' => 'Transportkostenermittlung Urban / Linde MH'
										  ),
				   'frontend' => TRUE,
				   'backend' => true,
				   'roles' => array(
									'mh_admin'
									)

				   );
   }
   public function admin_menu(&$menu)
   {
	  /*
	   $menu['Rampensteuerung'] = array(
	   'Standorte' => 'admin/mh/',
	   'Rampen' => 'admin/mh/ramp/'
	   );
	   // beispiel sortierung
	   add_admin_menu_place('Rampensteuerung', 2);
	  */

	  return $menu;
   }
   public function install()
   {
	  $this->dbforge->drop_table('mh_distances');
	  $this->dbforge->drop_table('mh_weight_range');
	  $this->dbforge->drop_table('mh_portage_reference');

	  $this->db->delete('settings', array('module' => 'mh'));

	  /*tabelle mit kilomenterbereichen*/
	  $mh_distances = array(
							'id' => array(
										  'type' => 'INT',
										  'constraint' => '11',
										  'auto_increment' => TRUE
										  ),
							'km' => array(
										  'type' => 'INT',
										  'constraint' => '5'
										  )
							);
	  $this->dbforge->add_field($mh_distances);
	  $this->dbforge->add_key('id', TRUE);
	  $this->dbforge->create_table('mh_distances') ;


	  // --------------------------------------------------------------------
	  /*tabelle mit gewichtsbereichen*/

	  $mh_weight_range = array(
							   'id' => array(
											 'type' => 'INT',
											 'constraint' => '11',
											 'auto_increment' => TRUE
											 ),
							   'kg' => array(
											 'type' => 'INT',
											 'constraint' => '5'
											 )
							   );
	  $this->dbforge->add_field($mh_weight_range);
	  $this->dbforge->add_key('id', TRUE);
	  $this->dbforge->create_table('mh_weight_range') ;

	  // --------------------------------------------------------------------
	  /*tabelle preise, entfernung und gewicht, referenz*/
	  $this->dbforge->drop_table('mh_portage_reference');

	  $mh_portage_reference = array(
									'id' => array(
												  'type' => 'INT',
												  'constraint' => '11',
												  'auto_increment' => TRUE
												  ),
									'distance_id' => array(
														   'type' => 'INT',
														   'constraint' => '11'
														   ),
									'weight_range_id' => array(
															   'type' => 'INT',
															   'constraint' => '11'
															   ),
									'portage_eur' => array(
														   'type' => 'Varchar',
														   'constraint' => '8'
														   )

									);
	  $this->dbforge->add_field($mh_portage_reference);
	  $this->dbforge->add_key('id', TRUE);
	  $this->dbforge->create_table('mh_portage_reference') ;


	  // --------------------------------------------------------------------
	  
	  $this->dbforge->drop_table('mh_countries');

	  $mh_countries = array(
							'id' => array(
										  'type' => 'INT',
										  'constraint' => '11',
										  'auto_increment' => TRUE
										  ),
							'iso' => array(
										   'type' => 'CHAR',
										   'constraint' => '2'
										   ),
							'iso3' => array(
											'type' => 'CHAR',
											'constraint' => '3'
											)
							,
							'de' => array(
										  'type' => 'VARCHAR',
										  'constraint' => '100'
										  )
							,
							'en' => array(
										  'type' => 'VARCHAR',
										  'constraint' => '100'
										  )

							);

	  $this->dbforge->add_field($mh_countries);
	  $this->dbforge->add_key('id', TRUE);
	  $this->dbforge->create_table('mh_countries') ;

	  $query = "INSERT INTO `default_mh_countries` (`id`, `iso`, `iso3`, `de`, `en`) VALUES
				(1, 'AD', 'AND', 'Andorra', 'Andorra'),
				(2, 'AE', 'ARE', 'Vereinigte Arabische Emirate', 'United Arab Emirates'),
				(3, 'AF', 'AFG', 'Afghanistan', 'Afghanistan'),
				(4, 'AG', 'ATG', 'Antigua und Barbuda', 'Antigua And Barbuda'),
				(5, 'AI', 'AIA', 'Anguilla', 'Anguilla'),
				(6, 'AL', 'ALB', 'Albanien', 'Albania'),
				(7, 'AM', 'ARM', 'Armenien', 'Armenia'),
				(8, 'AN', 'ANT', 'Niederl�ndische Antillen', 'Netherlands Antilles'),
				(9, 'AO', 'AGO', 'Angola', 'Angola'),
				(10, 'AQ', 'ATA', 'Antarktis', 'Antarctica'),
				(11, 'AR', 'ARG', 'Argentinien', 'Argentina'),
				(12, 'AS', 'ASM', 'Amerikanisch Samoa', 'American Samoa'),
				(13, 'AT', 'AUT', '�sterreich', 'Austria'),
				(14, 'AU', 'AUS', 'Australien', 'Australia'),
				(15, 'AW', 'ABW', 'Aruba', 'Aruba'),
				(16, 'AZ', 'AZE', 'Aserbaidschan', 'Azerbaijan'),
				(17, 'BA', 'BIH', 'Bosnien und Herzegowina', 'Bosnia And Herzegovina'),
				(18, 'BB', 'BRB', 'Barbados', 'Barbados'),
				(19, 'BD', 'BGD', 'Bangladesch', 'Bangladesh'),
				(20, 'BE', 'BEL', 'Belgien', 'Belgium'),
				(21, 'BF', 'BFA', 'Burkina Faso', 'Burkina Faso'),
				(22, 'BG', 'BGR', 'Bulgarien', 'Bulgaria'),
				(23, 'BH', 'BHR', 'Bahrain', 'Bahrain'),
				(24, 'BI', 'BDI', 'Burundi', 'Burundi'),
				(25, 'BJ', 'BEN', 'Benin', 'Benin'),
				(26, 'BM', 'BMU', 'Bermuda', 'Bermuda'),
				(27, 'BN', 'BRN', 'Brunei Darussalam', 'Brunei Darussalam'),
				(28, 'BO', 'BOL', 'Bolivien', 'Bolivia'),
				(29, 'BR', 'BRA', 'Brasilien', 'Brazil'),
				(30, 'BS', 'BHS', 'Bahamas', 'Bahamas'),
				(31, 'BT', 'BTN', 'Bhutan', 'Bhutan'),
				(32, 'BV', 'BVT', 'Bouvetinsel', 'Bouvet Island'),
				(33, 'BW', 'BWA', 'Botsuana', 'Botswana'),
				(34, 'BY', 'BLR', 'Wei�russland', 'Belarus'),
				(35, 'BZ', 'BLZ', 'Belize', 'Belize'),
				(36, 'CA', 'CAN', 'Kanada', 'Canada'),
				(37, 'CC', 'CCK', 'Kokosinseln', 'Cocos (Keeling) Islands'),
				(38, 'CF', 'CAF', 'Zentralafrikanische Republik', 'Central African Republic'),
				(39, 'CG', 'COG', 'Kongo', 'Congo'),
				(40, 'CH', 'CHE', 'Schweiz', 'Switzerland'),
				(41, 'CI', 'CIV', 'C�te d�Ivoire', 'Cote d''Ivoire'),
				(42, 'CK', 'COK', 'Cookinseln', 'Cook Islands'),
				(43, 'CL', 'CHL', 'Chile', 'Chile'),
				(44, 'CM', 'CMR', 'Kamerun', 'Cameroon'),
				(45, 'CN', 'CHN', 'China', 'China'),
				(46, 'CO', 'COL', 'Kolumbien', 'Colombia'),
				(47, 'CR', 'CRI', 'Costa Rica', 'Costa Rica'),
				(48, 'CU', 'CUB', 'Kuba', 'Cuba'),
				(49, 'CV', 'CPV', 'Kap Verde', 'Cape Verde'),
				(50, 'CX', 'CXR', 'Weihnachtsinsel', 'Christmas Island'),
				(51, 'CY', 'CYP', 'Zypern', 'Cyprus'),
				(52, 'CZ', 'CZE', 'Tschechische Republik', 'Czech Republic'),
				(53, 'DE', 'DEU', 'Deutschland', 'Germany'),
				(54, 'DJ', 'DJI', 'Republik Dschibuti', 'Djibouti'),
				(55, 'DK', 'DNK', 'D�nemark', 'Denmark'),
				(56, 'DM', 'DMA', 'Dominica', 'Dominica'),
				(57, 'DO', 'DOM', 'Dominikanische Republik', 'Dominican Republic'),
				(58, 'DZ', 'DZA', 'Algerien', 'Algeria'),
				(59, 'EC', 'ECU', 'Ecuador', 'Ecuador'),
				(60, 'EE', 'EST', 'Estland', 'Estonia'),
				(61, 'EG', 'EGY', '�gypten', 'Egypt'),
				(62, 'EH', 'ESH', 'Westsahara', 'Western Sahara'),
				(63, 'ER', 'ERI', 'Eritrea', 'Eritrea'),
				(64, 'ES', 'ESP', 'Spanien', 'Spain'),
				(65, 'ET', 'ETH', '�thiopien', 'Ethiopia'),
				(66, 'FI', 'FIN', 'Finnland', 'Finland'),
				(67, 'FJ', 'FJI', 'Fidschi', 'Fiji'),
				(68, 'FK', 'FLK', 'Falklandinseln', 'Falkland Islands (Malvinas)'),
				(69, 'FM', 'FSM', 'Mikronesien, F�derierte Staa', 'Micronesia, Federated States Of'),
				(70, 'FO', 'FRO', 'F�r�er', 'Faeroe Islands'),
				(71, 'FR', 'FRA', 'Frankreich', 'France'),
				(72, 'GA', 'GAB', 'Gabun', 'Gabon'),
				(73, 'UK', 'GBR', 'Vereinigtes K�nigreich', 'United Kingdom'),
				(74, 'GD', 'GRD', 'Grenada', 'Grenada'),
				(75, 'GE', 'GEO', 'Georgien', 'Georgia'),
				(76, 'GF', 'GUF', 'Franz�sisch Guiana', 'French Guiana'),
				(77, 'GG', 'GGY', 'Guernsey', 'Guernsey'),
				(78, 'GH', 'GHA', 'Ghana', 'Ghana'),
				(79, 'GI', 'GIB', 'Gibraltar', 'Gibraltar'),
				(80, 'GL', 'GRL', 'Gr�nland', 'Greenland'),
				(81, 'GM', 'GMB', 'Gambia', 'Gambia'),
				(82, 'GN', 'GIN', 'Guinea', 'Guinea'),
				(83, 'GP', 'GLP', 'Guadeloupe', 'Guadeloupe'),
				(84, 'GQ', 'GNQ', '�quatorialguinea', 'Equatorial Guinea'),
				(85, 'GR', 'GRC', 'Griechenland', 'Greece'),
				(86, 'GS', 'SGS', 'S�dgeorgien und die S�dliche', 'South Georgia And The South Sandwich Islands'),
				(87, 'GT', 'GTM', 'Guatemala', 'Guatemala'),
				(88, 'GU', 'GUM', 'Guam', 'Guam'),
				(89, 'GW', 'GNB', 'Guinea-Bissau', 'Guinea-Bissau'),
				(90, 'GY', 'GUY', 'Guyana', 'Guyana'),
				(91, 'HK', 'HKG', 'Hong Kong', 'Hong Kong'),
				(92, 'HM', 'HMD', 'Heard Insel und McDonald Ins', 'Heard And Mc Donald Islands'),
				(93, 'HN', 'HND', 'Honduras', 'Honduras'),
				(94, 'HR', 'HRV', 'Kroatien', 'Croatia (local name: Hrvatska)'),
				(95, 'HT', 'HTI', 'Haiti', 'Haiti'),
				(96, 'HU', 'HUN', 'Ungarn', 'Hungary'),
				(97, 'ID', 'IDN', 'Indonesien', 'Indonesia'),
				(98, 'IE', 'IRL', 'Irland', 'Ireland'),
				(99, 'II', 'ISR', 'Israel', 'International (SSGFI only)'),
				(100, 'IL', 'IMN', 'Isle of Man', 'Israel'),
				(101, 'IN', 'IND', 'Indien', 'India'),
				(102, 'IO', 'IOT', 'Britische Territorien im Ind', 'British Indian Ocean Territory'),
				(103, 'IQ', 'IRQ', 'Irak', 'Iraq'),
				(104, 'IR', 'IRN', 'Iran, Islam. Rep.', 'Iran (Islamic Republic Of)'),
				(105, 'IS', 'ISL', 'Island', 'Iceland'),
				(106, 'IT', 'ITA', 'Italien', 'Italy'),
				(107, 'JE', 'JEY', 'Jersey', 'Jersey'),
				(108, 'JM', 'JAM', 'Jamaika', 'Jamaica'),
				(109, 'JO', 'JOR', 'Jordanien', 'Jordan'),
				(110, 'JP', 'JPN', 'Japan', 'Japan'),
				(111, 'KE', 'KEN', 'Kenia', 'Kenya'),
				(112, 'KG', 'KGZ', 'Kirgisistan', 'Kyrgyzstan'),
				(113, 'KH', 'KHM', 'Kambodscha', 'Cambodia'),
				(114, 'KI', 'KIR', 'Kiribati', 'Kiribati'),
				(115, 'KM', 'COM', 'Komoren', 'Comoros'),
				(116, 'KN', 'KNA', 'St. Kitts und Nevis', 'Saint Kitts And Nevis'),
				(117, 'KP', 'PRK', 'Korea, Dem. Volksrep.', 'Korea, Democratic People''s Republic Of'),
				(118, 'KR', 'KOR', 'Korea, Rep.', 'Korea, Republic Of'),
				(119, 'KW', 'KWT', 'Kuwait', 'Kuwait'),
				(120, 'KY', 'CYM', 'Kaimaninseln', 'Cayman Islands'),
				(121, 'KZ', 'KAZ', 'Kasachstan', 'Kazakhstan'),
				(122, 'LA', 'LAO', 'Laos, Dem. Volksrep.', 'Lao People''s Democratic Republic'),
				(123, 'LB', 'LBN', 'Libanon', 'Lebanon'),
				(124, 'LC', 'LCA', 'St. Lucia', 'Saint Lucia'),
				(125, 'LI', 'LIE', 'Liechtenstein', 'Liechtenstein'),
				(126, 'LK', 'LKA', 'Sri Lanka', 'Sri Lanka'),
				(127, 'LR', 'LBR', 'Liberia', 'Liberia'),
				(128, 'LS', 'LSO', 'Lesotho', 'Lesotho'),
				(129, 'LT', 'LTU', 'Litauen', 'Lithuania'),
				(130, 'LU', 'LUX', 'Luxemburg', 'Luxembourg'),
				(131, 'LV', 'LVA', 'Lettland', 'Latvia'),
				(132, 'LY', 'LBY', 'Libysch-Arabische Dschamahir', 'Libyan Arab Jamahiriya'),
				(133, 'MA', 'MAR', 'Marokko', 'Morocco'),
				(134, 'MC', 'MCO', 'Monaco', 'Monaco'),
				(135, 'MD', 'MDA', 'Moldau, Rep.', 'Moldova, Republic Of'),
				(136, 'ME', 'MNE', 'Montenegro', 'Montenegro'),
				(137, 'MG', 'MDG', 'Madagaskar', 'Madagascar'),
				(138, 'MH', 'MHL', 'Marshallinseln', 'Marshall Islands'),
				(139, 'MK', 'MKD', 'Mazedonien, ehemalige jugosl', 'Macedonia, The Former Yugoslav Republic Of'),
				(140, 'ML', 'MLI', 'Mali', 'Mali'),
				(141, 'MM', 'MMR', 'Myanmar', 'Myanmar'),
				(142, 'MN', 'MNG', 'Mongolei', 'Mongolia'),
				(143, 'MO', 'MAC', 'Macao', 'Macau'),
				(144, 'MP', 'MNP', 'N�rdliche Marianen', 'Northern Mariana Islands'),
				(145, 'MQ', 'MTQ', 'Martinique', 'Martinique'),
				(146, 'MR', 'MRT', 'Mauretanien', 'Mauritania'),
				(147, 'MS', 'MSR', 'Montserrat', 'Montserrat'),
				(148, 'MT', 'MLT', 'Malta', 'Malta'),
				(149, 'MU', 'MUS', 'Mauritius', 'Mauritius'),
				(150, 'MV', 'MDV', 'Malediven', 'Maldives'),
				(151, 'MW', 'MWI', 'Malawi', 'Malawi'),
				(152, 'MX', 'MEX', 'Mexiko', 'Mexico'),
				(153, 'MY', 'MYS', 'Malaysia', 'Malaysia'),
				(154, 'MZ', 'MOZ', 'Mosambik', 'Mozambique'),
				(155, 'NA', 'NAM', 'Namibia', 'Namibia'),
				(156, 'NC', 'NCL', 'Neukaledonien', 'New Caledonia'),
				(157, 'NE', 'NER', 'Niger', 'Niger'),
				(158, 'NF', 'NFK', 'Norfolk Insel', 'Norfolk Island'),
				(159, 'NG', 'NGA', 'Nigeria', 'Nigeria'),
				(160, 'NI', 'NIC', 'Nicaragua', 'Nicaragua'),
				(161, 'NL', 'NLD', 'Niederlande', 'Netherlands'),
				(162, 'NO', 'NOR', 'Norwegen', 'Norway'),
				(163, 'NP', 'NPL', 'Nepal', 'Nepal'),
				(164, 'NR', 'NRU', 'Nauru', 'Nauru'),
				(165, 'NU', 'NIU', 'Niue', 'Niue'),
				(166, 'NZ', 'NZL', 'Neuseeland', 'New Zealand'),
				(167, 'OM', 'OMN', 'Oman', 'Oman'),
				(168, 'PA', 'PAN', 'Panama', 'Panama'),
				(169, 'PE', 'PER', 'Peru', 'Peru'),
				(170, 'PF', 'PYF', 'Franz�sisch Polynesien', 'French Polynesia'),
				(171, 'PG', 'PNG', 'Papua-Neuguinea', 'Papua New Guinea'),
				(172, 'PH', 'PHL', 'Philippinen', 'Philippines'),
				(173, 'PK', 'PAK', 'Pakistan', 'Pakistan'),
				(174, 'PL', 'POL', 'Polen', 'Poland'),
				(175, 'PM', 'SPM', 'Saint Pierre und Miquelon', 'St. Pierre And Miquelon'),
				(176, 'PN', 'PCN', 'Pitcairn', 'Pitcairn'),
				(177, 'PR', 'PRI', 'Puerto Rico', 'Puerto Rico'),
				(178, 'PT', 'PRT', 'Portugal', 'Portugal'),
				(179, 'PW', 'PLW', 'Palau', 'Palau'),
				(180, 'PY', 'PRY', 'Paraguay', 'Paraguay'),
				(181, 'QA', 'QAT', 'Katar', 'Qatar'),
				(182, 'RE', 'REU', 'R�union', 'Reunion'),
				(183, 'RO', 'ROU', 'Rum�nien', 'Romania'),
				(184, 'RS', 'SRB', 'Serbien', 'Serbia'),
				(185, 'RU', 'RUS', 'Russische F�deration', 'Russian Federation'),
				(186, 'RW', 'RWA', 'Ruanda', 'Rwanda'),
				(187, 'SA', 'SAU', 'Saudi-Arabien', 'Saudi Arabia'),
				(188, 'SB', 'SLB', 'Salomonen', 'Solomon Islands'),
				(189, 'SC', 'SYC', 'Seychellen', 'Seychelles'),
				(190, 'SD', 'SDN', 'Sudan', 'Sudan'),
				(191, 'SE', 'SWE', 'Schweden', 'Sweden'),
				(192, 'SG', 'SGP', 'Singapur', 'Singapore'),
				(193, 'SH', 'SHN', 'Saint Helena', 'St. Helena'),
				(194, 'SI', 'SVN', 'Slowenien', 'Slovenia'),
				(195, 'SJ', 'SJM', 'Svalbard und Jan Mayen', 'Svalbard And Jan Mayen Islands'),
				(196, 'SK', 'SVK', 'Slowakei', 'Slovakia (Slovak Republic)'),
				(197, 'SL', 'SLE', 'Sierra Leone', 'Sierra Leone'),
				(198, 'SM', 'SMR', 'San Marino', 'San Marino'),
				(199, 'SN', 'SEN', 'Senegal', 'Senegal'),
				(200, 'SO', 'SOM', 'Somalia', 'Somalia'),
				(201, 'SR', 'SUR', 'Suriname', 'Suriname'),
				(202, 'ST', 'STP', 'S�o Tom� und Pr�ncipe', 'Sao Tome And Principe'),
				(203, 'SV', 'SLV', 'El Salvador', 'El Salvador'),
				(204, 'SY', 'SYR', 'Syrien, Arab. Rep.', 'Syrian Arab Republic'),
				(205, 'SZ', 'SWZ', 'Swasiland', 'Swaziland'),
				(206, 'TC', 'TCA', 'Turks- und Caicosinseln', 'Turks And Caicos Islands'),
				(207, 'TD', 'TCD', 'Tschad', 'Chad'),
				(208, 'TF', 'ATF', 'Franz�sische S�dgebiete', 'French Southern Territories'),
				(209, 'TG', 'TGO', 'Togo', 'Togo'),
				(210, 'TH', 'THA', 'Thailand', 'Thailand'),
				(211, 'TJ', 'TJK', 'Tadschikistan', 'Tajikistan'),
				(212, 'TK', 'TKL', 'Tokelau', 'Tokelau'),
				(213, 'TM', 'TKM', 'Turkmenistan', 'Turkmenistan'),
				(214, 'TN', 'TUN', 'Tunesien', 'Tunisia'),
				(215, 'TO', 'TON', 'Tonga', 'Tonga'),
				(216, 'TR', 'TUR', 'T�rkei', 'Turkey'),
				(217, 'TT', 'TTO', 'Trinidad und Tobago', 'Trinidad And Tobago'),
				(218, 'TV', 'TUV', 'Tuvalu', 'Tuvalu'),
				(219, 'TW', 'TWN', 'Taiwan', 'Taiwan, Province Of China'),
				(220, 'TZ', 'TZA', 'Tansania, Vereinigte Rep.', 'Tanzania, United Republic Of'),
				(221, 'UA', 'UKR', 'Ukraine', 'Ukraine'),
				(222, 'UG', 'UGA', 'Uganda', 'Uganda'),
				(223, 'UM', 'UMI', 'United States Minor Outlying', 'United States Minor Outlying Islands'),
				(224, 'US', 'USA', 'Vereinigte Staaten von Ameri', 'United States'),
				(225, 'UY', 'URY', 'Uruguay', 'Uruguay'),
				(226, 'UZ', 'UZB', 'Usbekistan', 'Uzbekistan'),
				(227, 'VA', 'VAT', 'Heiliger Stuhl', 'Vatican City State (Holy See)'),
				(228, 'VC', 'VCT', 'St. Vincent und die Grenadin', 'Saint Vincent And The Grenadines'),
				(229, 'VE', 'VEN', 'Venezuela', 'Venezuela'),
				(230, 'VG', 'VGB', 'Britische Jungferninseln', 'Virgin Islands (British)'),
				(231, 'VI', 'VIR', 'Amerikanische Jungferninseln', 'Virgin Islands (U.S.)'),
				(232, 'VN', 'VNM', 'Vietnam', 'Viet Nam'),
				(233, 'VU', 'VUT', 'Vanuatu', 'Vanuatu'),
				(234, 'WF', 'WLF', 'Wallis und Futuna', 'Wallis And Futuna Islands'),
				(235, 'WS', 'WSM', 'Samoa', 'Samoa'),
				(236, 'YE', 'YEM', 'Jemen', 'Yemen'),
				(237, 'YT', 'MYT', 'Mayotte', 'Mayotte'),
				(238, 'ZA', 'ZAF', 'S�dafrika', 'South Africa'),
				(239, 'ZM', 'ZMB', 'Sambia', 'Zambia'),
				(240, 'ZR', 'ZAR', 'Zaire', 'Zaire'),
				(241, 'ZW', 'ZWE', 'Simbabwe', 'Zimbabwe');";

	  $this->db->query(utf8_encode($query));

	  // --------------------------------------------------------------------
	  /*tabelle preise, entfernung und gewicht, referenz*/
	  $this->dbforge->drop_table('mh_calc_factors');

	  $mh_calc_factors = array(
							   'id' => array(
											 'type' => 'INT',
											 'constraint' => '11',
											 'auto_increment' => TRUE
											 ),
							   'country_id' => array(
													 'type' => 'INT',
													 'constraint' => '11'
													 ),
							   'factor' => array(
												 'type' => 'DECIMAL',
												 'constraint' => array(4,2),
												 'unsigned' => FALSE,
												 )

							   );
	  $this->dbforge->add_field($mh_calc_factors);
	  $this->dbforge->add_key('id', TRUE);
	  $this->dbforge->create_table('mh_calc_factors') ;



	  // --------------------------------------------------------------------
	  /*tabelle module settings*/
	  $mh_setting = array(
						  'slug' => 'mh_setting',
						  'title' => 'Material Handling',
						  'description' => 'Einestellungen Material Handling',
						  '`default`' => '1',
						  '`value`' => '1',
						  'type' => 'select',
						  '`options`' => '1=Yes|0=No',
						  'is_required' => 1,
						  'is_gui' => 1,
						  'module' => 'mh'
						  );
	  $this->dbforge->add_field($mh_setting);
	  $this->dbforge->add_key('id', TRUE);
	  if($this->db->insert('settings', $mh_setting) AND
		 is_dir($this->upload_path.'mh') OR @mkdir($this->upload_path.'mh',0777,TRUE))
		 {
			return TRUE;
		 }
   }
   public function uninstall()
   {

	  $this->dbforge->drop_table('mh_countries');
	  $this->dbforge->drop_table('mh_distances');
	  $this->dbforge->drop_table('mh_weight_range');
	  $this->dbforge->drop_table('mh_portage_reference');
	  $this->dbforge->drop_table('mh_calc_factors');

	  $this->dbforge->drop_table('mh_settings');
	  $this->db->delete('settings', array('module' => 'mh'));
	  return TRUE;
   }
   public function upgrade($old_version)
   {

	  return TRUE;
		
   }
   public function help()
   {
	  // Return a string containing help info
	  // You could include a file and return it here.
	  return "No documentation has been added for this module.<br />Contact the module developer for assistance.";
   }
}
/* End of file details.php */