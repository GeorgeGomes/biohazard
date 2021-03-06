<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class CountryTableSeeder extends Seeder {

	public function run() {
	   DB::statement("
				INSERT IGNORE INTO `country` (`id`, `name`, `iso`, `code`, `phone_code`) VALUES
				(1, 'Afghanistan', 'AF', 4, 93),
				(2, 'Albania', 'AL', 8, 355),
				(3, 'Algeria', 'DZ', 12, 213),
				(4, 'American Samoa', 'AS', 16, 1684),
				(5, 'Andorra', 'AD', 20, 376),
				(6, 'Angola', 'AO', 24, 244),
				(7, 'Anguilla', 'AI', 660, 1264),
				(8, 'Antarctica', 'AQ', NULL, 0),
				(9, 'Antigua and Barbuda', 'AG', 28, 1268),
				(10, 'Argentina', 'AR', 32, 54),
				(11, 'Armenia', 'AM', 51, 374),
				(12, 'Aruba', 'AW', 533, 297),
				(13, 'Australia', 'AU', 36, 61),
				(14, 'Austria', 'AT', 40, 43),
				(15, 'Azerbaijan', 'AZ', 31, 994),
				(16, 'Bahamas', 'BS', 44, 1242),
				(17, 'Bahrain', 'BH', 48, 973),
				(18, 'Bangladesh', 'BD', 50, 880),
				(19, 'Barbados', 'BB', 52, 1246),
				(20, 'Belarus', 'BY', 112, 375),
				(21, 'Belgium', 'BE', 56, 32),
				(22, 'Belize', 'BZ', 84, 501),
				(23, 'Benin', 'BJ', 204, 229),
				(24, 'Bermuda', 'BM', 60, 1441),
				(25, 'Bhutan', 'BT', 64, 975),
				(26, 'Bolivia', 'BO', 68, 591),
				(27, 'Bosnia and Herzegovina', 'BA', 70, 387),
				(28, 'Botswana', 'BW', 72, 267),
				(29, 'Bouvet Island', 'BV', NULL, 0),
				(30, 'Brazil', 'BR', 76, 55),
				(31, 'British Indian Ocean Territory', 'IO', NULL, 246),
				(32, 'Brunei Darussalam', 'BN', 96, 673),
				(33, 'Bulgaria', 'BG', 100, 359),
				(34, 'Burkina Faso', 'BF', 854, 226),
				(35, 'Burundi', 'BI', 108, 257),
				(36, 'Cambodia', 'KH', 116, 855),
				(37, 'Cameroon', 'CM', 120, 237),
				(38, 'Canada', 'CA', 124, 1),
				(39, 'Cape Verde', 'CV', 132, 238),
				(40, 'Cayman Islands', 'KY', 136, 1345),
				(41, 'Central African Republic', 'CF', 140, 236),
				(42, 'Chad', 'TD', 148, 235),
				(43, 'Chile', 'CL', 152, 56),
				(44, 'China', 'CN', 156, 86),
				(45, 'Christmas Island', 'CX', NULL, 61),
				(46, 'Cocos (eling) Islands', 'CC', NULL, 672),
				(47, 'Colombia', 'CO', 170, 57),
				(48, 'Comoros', 'KM', 174, 269),
				(49, 'Congo', 'CG', 178, 242),
				(50, 'Congo, the Democratic Republic of the', 'CD', 180, 242),
				(51, 'Cook Islands', 'CK', 184, 682),
				(52, 'Costa Rica', 'CR', 188, 506),
				(53, 'Cote D''Ivoire', 'CI', 384, 225),
				(54, 'Croatia', 'HR', 191, 385),
				(55, 'Cuba', 'CU', 192, 53),
				(56, 'Cyprus', 'CY', 196, 357),
				(57, 'Czech Republic', 'CZ', 203, 420),
				(58, 'Denmark', 'DK', 208, 45),
				(59, 'Djibouti', 'DJ', 262, 253),
				(60, 'Dominica', 'DM', 212, 1767),
				(61, 'Dominican Republic', 'DO', 214, 1809),
				(62, 'Ecuador', 'EC', 218, 593),
				(63, 'Egypt', 'EG', 818, 20),
				(64, 'El Salvador', 'SV', 222, 503),
				(65, 'Equatorial Guinea', 'GQ', 226, 240),
				(66, 'Eritrea', 'ER', 232, 291),
				(67, 'Estonia', 'EE', 233, 372),
				(68, 'Ethiopia', 'ET', 231, 251),
				(69, 'Falkland Islands (lvinas)', 'FK', 238, 500),
				(70, 'Faroe Islands', 'FO', 234, 298),
				(71, 'Fiji', 'FJ', 242, 679),
				(72, 'Finland', 'FI', 246, 358),
				(73, 'France', 'FR', 250, 33),
				(74, 'French Guiana', 'GF', 254, 594),
				(75, 'French Polynesia', 'PF', 258, 689),
				(76, 'French Southern Territories', 'TF', NULL, 0),
				(77, 'Gabon', 'GA', 266, 241),
				(78, 'Gambia', 'GM', 270, 220),
				(79, 'Georgia', 'GE', 268, 995),
				(80, 'Germany', 'DE', 276, 49),
				(81, 'Ghana', 'GH', 288, 233),
				(82, 'Gibraltar', 'GI', 292, 350),
				(83, 'Greece', 'GR', 300, 30),
				(84, 'Greenland', 'GL', 304, 299),
				(85, 'Grenada', 'GD', 308, 1473),
				(86, 'Guadeloupe', 'GP', 312, 590),
				(87, 'Guam', 'GU', 316, 1671),
				(88, 'Guatemala', 'GT', 320, 502),
				(89, 'Guinea', 'GN', 324, 224),
				(90, 'Guinea-Bissau', 'GW', 624, 245),
				(91, 'Guyana', 'GY', 328, 592),
				(92, 'Haiti', 'HT', 332, 509),
				(93, 'Heard Island and Mcdonald Islands', 'HM', NULL, 0),
				(94, 'Holy See (tican City State)', 'VA', 336, 39),
				(95, 'Honduras', 'HN', 340, 504),
				(96, 'Hong Kong', 'HK', 344, 852),
				(97, 'Hungary', 'HU', 348, 36),
				(98, 'Iceland', 'IS', 352, 354),
				(99, 'India', 'IN', 356, 91),
				(100, 'Indonesia', 'ID', 360, 62),
				(101, 'Iran, Islamic Republic of', 'IR', 364, 98),
				(102, 'Iraq', 'IQ', 368, 964),
				(103, 'Ireland', 'IE', 372, 353),
				(104, 'Israel', 'IL', 376, 972),
				(105, 'Italy', 'IT', 380, 39),
				(106, 'Jamaica', 'JM', 388, 1876),
				(107, 'Japan', 'JP', 392, 81),
				(108, 'Jordan', 'JO', 400, 962),
				(109, 'Kazakhstan', 'KZ', 398, 7),
				(110, 'Kenya', 'KE', 404, 254),
				(111, 'Kiribati', 'KI', 296, 686),
				(112, 'Korea, Democratic People''s Republic of', 'KP', 408, 850),
				(113, 'Korea, Republic of', 'KR', 410, 82),
				(114, 'Kuwait', 'KW', 414, 965),
				(115, 'Kyrgyzstan', 'KG', 417, 996),
				(116, 'Lao People''s Democratic Republic', 'LA', 418, 856),
				(117, 'Latvia', 'LV', 428, 371),
				(118, 'Lebanon', 'LB', 422, 961),
				(119, 'Lesotho', 'LS', 426, 266),
				(120, 'Liberia', 'LR', 430, 231),
				(121, 'Libyan Arab Jamahiriya', 'LY', 434, 218),
				(122, 'Liechtenstein', 'LI', 438, 423),
				(123, 'Lithuania', 'LT', 440, 370),
				(124, 'Luxembourg', 'LU', 442, 352),
				(125, 'Macao', 'MO', 446, 853),
				(126, 'Macedonia, the Former Yugoslav Republic of', 'MK', 807, 389),
				(127, 'Madagascar', 'MG', 450, 261),
				(128, 'Malawi', 'MW', 454, 265),
				(129, 'Malaysia', 'MY', 458, 60),
				(130, 'Maldives', 'MV', 462, 960),
				(131, 'Mali', 'ML', 466, 223),
				(132, 'Malta', 'MT', 470, 356),
				(133, 'Marshall Islands', 'MH', 584, 692),
				(134, 'Martinique', 'MQ', 474, 596),
				(135, 'Mauritania', 'MR', 478, 222),
				(136, 'Mauritius', 'MU', 480, 230),
				(137, 'Mayotte', 'YT', NULL, 269),
				(138, 'Mexico', 'MX', 484, 52),
				(139, 'Micronesia, Federated States of', 'FM', 583, 691),
				(140, 'Moldova, Republic of', 'MD', 498, 373),
				(141, 'Monaco', 'MC', 492, 377),
				(142, 'Mongolia', 'MN', 496, 976),
				(143, 'Montserrat', 'MS', 500, 1664),
				(144, 'Morocco', 'MA', 504, 212),
				(145, 'Mozambique', 'MZ', 508, 258),
				(146, 'Myanmar', 'MM', 104, 95),
				(147, 'Namibia', 'NA', 516, 264),
				(148, 'Nauru', 'NR', 520, 674),
				(149, 'Nepal', 'NP', 524, 977),
				(150, 'Netherlands', 'NL', 528, 31),
				(151, 'Netherlands Antilles', 'AN', 530, 599),
				(152, 'New Caledonia', 'NC', 540, 687),
				(153, 'New Zealand', 'NZ', 554, 64),
				(154, 'Nicaragua', 'NI', 558, 505),
				(155, 'Niger', 'NE', 562, 227),
				(156, 'Nigeria', 'NG', 566, 234),
				(157, 'Niue', 'NU', 570, 683),
				(158, 'Norfolk Island', 'NF', 574, 672),
				(159, 'Northern Mariana Islands', 'MP', 580, 1670),
				(160, 'Norway', 'NO', 578, 47),
				(161, 'Oman', 'OM', 512, 968),
				(162, 'Pakistan', 'PK', 586, 92),
				(163, 'Palau', 'PW', 585, 680),
				(164, 'Palestinian Territory, Occupied', 'PS', NULL, 970),
				(165, 'Panama', 'PA', 591, 507),
				(166, 'Papua New Guinea', 'PG', 598, 675),
				(167, 'Paraguay', 'PY', 600, 595),
				(168, 'Peru', 'PE', 604, 51),
				(169, 'Philippines', 'PH', 608, 63),
				(170, 'Pitcairn', 'PN', 612, 0),
				(171, 'Poland', 'PL', 616, 48),
				(172, 'Portugal', 'PT', 620, 351),
				(173, 'Puerto Rico', 'PR', 630, 1787),
				(174, 'Qatar', 'QA', 634, 974),
				(175, 'Reunion', 'RE', 638, 262),
				(176, 'Romania', 'RO', 642, 40),
				(177, 'Russian Federation', 'RU', 643, 70),
				(178, 'Rwanda', 'RW', 646, 250),
				(179, 'Saint Helena', 'SH', 654, 290),
				(180, 'Saint Kitts and Nevis', 'KN', 659, 1869),
				(181, 'Saint Lucia', 'LC', 662, 1758),
				(182, 'Saint Pierre and Miquelon', 'PM', 666, 508),
				(183, 'Saint Vincent and the Grenadines', 'VC', 670, 1784),
				(184, 'Samoa', 'WS', 882, 684),
				(185, 'San Marino', 'SM', 674, 378),
				(186, 'Sao Tome and Principe', 'ST', 678, 239),
				(187, 'Saudi Arabia', 'SA', 682, 966),
				(188, 'Senegal', 'SN', 686, 221),
				(189, 'Serbia and Montenegro', 'CS', NULL, 381),
				(190, 'Seychelles', 'SC', 690, 248),
				(191, 'Sierra Leone', 'SL', 694, 232),
				(192, 'Singapore', 'SG', 702, 65),
				(193, 'Slovakia', 'SK', 703, 421),
				(194, 'Slovenia', 'SI', 705, 386),
				(195, 'Solomon Islands', 'SB', 90, 677),
				(196, 'Somalia', 'SO', 706, 252),
				(197, 'South Africa', 'ZA', 710, 27),
				(198, 'South Georgia and the South Sandwich Islands', 'GS', NULL, 0),
				(199, 'Spain', 'ES', 724, 34),
				(200, 'Sri Lanka', 'LK', 144, 94),
				(201, 'Sudan', 'SD', 736, 249),
				(202, 'Suriname', 'SR', 740, 597),
				(203, 'Svalbard and Jan Mayen', 'SJ', 744, 47),
				(204, 'Swaziland', 'SZ', 748, 268),
				(205, 'Sweden', 'SE', 752, 46),
				(206, 'Switzerland', 'CH', 756, 41),
				(207, 'Syrian Arab Republic', 'SY', 760, 963),
				(208, 'Taiwan, Province of China', 'TW', 158, 886),
				(209, 'Tajikistan', 'TJ', 762, 992),
				(210, 'Tanzania, United Republic of', 'TZ', 834, 255),
				(211, 'Thailand', 'TH', 764, 66),
				(212, 'Timor-Leste', 'TL', NULL, 670),
				(213, 'Togo', 'TG', 768, 228),
				(214, 'Tokelau', 'TK', 772, 690),
				(215, 'Tonga', 'TO', 776, 676),
				(216, 'Trinidad and Tobago', 'TT', 780, 1868),
				(217, 'Tunisia', 'TN', 788, 216),
				(218, 'Turkey', 'TR', 792, 90),
				(219, 'Turkmenistan', 'TM', 795, 7370),
				(220, 'Turks and Caicos Islands', 'TC', 796, 1649),
				(221, 'Tuvalu', 'TV', 798, 688),
				(222, 'Uganda', 'UG', 800, 256),
				(223, 'Ukraine', 'UA', 804, 380),
				(224, 'United Arab Emirates', 'AE', 784, 971),
				(225, 'United Kingdom', 'GB', 826, 44),
				(226, 'United States', 'US', 840, 1),
				(227, 'United States Minor Outlying Islands', 'UM', NULL, 1),
				(228, 'Uruguay', 'UY', 858, 598),
				(229, 'Uzbekistan', 'UZ', 860, 998),
				(230, 'Vanuatu', 'VU', 548, 678),
				(231, 'Venezuela', 'VE', 862, 58),
				(232, 'Viet Nam', 'VN', 704, 84),
				(233, 'Virgin Islands, British', 'VG', 92, 1284),
				(234, 'Virgin Islands, U.s.', 'VI', 850, 1340),
				(235, 'Wallis and Futuna', 'WF', 876, 681),
				(236, 'Western Sahara', 'EH', 732, 212),
				(237, 'Yemen', 'YE', 887, 967),
				(238, 'Zambia', 'ZM', 894, 260),
				(239, 'Zimbabwe', 'ZW', 716, 263);");
	}

}
