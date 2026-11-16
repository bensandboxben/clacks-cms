<?php

$wsdl = 'https://osg.scot/services/NGSearchService?wsdl';
$ns = 'http://www.scottishcitizen.gov.uk/improvementservice/nationalinfrastructure/addresssearch/v0-1';

$client = new SoapClient($wsdl, [
    'trace' => true,
    'exceptions' => true,
]);

$auth = [
    'username' => 'ben.short',
    'password' => 'Sp1derman88_',
];

$header = new SoapHeader($ns, 'HeaderLogin', $auth);
$client->__setSoapHeaders($header);

try {
    //$response = $client->__soapCall('sendNGListDataSetsMessage', []);


//var_dump($client->__getFunctions());

$params = [
    'query' => [
        'dataset' => 'STD_ADDRESS_SEARCH',
        'attribute' => [
            'name' => 'UPRN',
            'matchtype' => 'between',
            'value' => ['350000',100000000000000]
        ],
        'sortField' => '',
        'sortOrder' => 'asc',
    ],
];

    $response = $client->__soapCall('sendNGSearchMessage', $params);

    var_dump($response);

	$file = 'test.txt';
	$current = file_get_contents($file);
	$current .= $response;
	file_put_contents($file, $current);


} catch (\Exception $e) {
    echo $e->getMessage();
}

?>

