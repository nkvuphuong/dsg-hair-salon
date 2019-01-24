<?php

require_once './algoliasearch/algoliasearch.php';

$client = new \AlgoliaSearch\Client('55D6OYYP5R', '125e15cb6cabe55d4b3c8f8ac7acdfde');
//var_dump($client->listIndexes());
//var_dump($client->getLogs());

$index = $client->initIndex('test');

$pdo = new PDO('mysql:host=localhost;dbname=land_db', 'land_user', '123456@');

$results = $pdo->query('SELECT * from nh_real_estate');
print_r ($results);exit;
if ($results)
{
  $batch = array();
  // iterate over results and send them by batch of 10000 elements
  foreach ($results as $row)
  {
  	// select the identifier of this row
    $row['objectID'] = $row['YourIDColumn'];

    array_push($batch, $row);
    
    if (count($batch) == 10000)
    {
      $index->saveObjects($batch);
      $batch = array();
    }
  }
    
  $index->saveObjects($batch);
}

