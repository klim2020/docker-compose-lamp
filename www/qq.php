<?php
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://qwqer.hostcream.eu/api/v1/plugins/opencart/clients/auth/trading-points/1/delivery-orders/get-price',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'type=Regular&category=Flowers&real_type=OmnivaParcelTerminal&origin%5Baddress%5D=Vien%C4%ABbas+gat.+109%2C+Zemgales+priek%C5%A1pils%C4%93ta%2C+R%C4%ABga%2C+LV-1058%2C+Latvia&origin%5Bcoordinates%5D%5B0%5D=56.9085346&origin%5Bcoordinates%5D%5B1%5D=24.0836221&origin%5Bname%5D=Your+Store&origin%5Bphone%5D=%2B37167224278&destinations%5B0%5D%5Baddress%5D=%C4%A2ertr%C5%ABdes+iela+109%2C+Latgales+priek%C5%A1pils%C4%93ta%2C+R%C4%ABga%2C+LV-1009%2C+Latvia&destinations%5B0%5D%5Bcoordinates%5D%5B0%5D=56.9494578&destinations%5B0%5D%5Bcoordinates%5D%5B1%5D=24.1374319&destinations%5B0%5D%5Bname%5D=test+test&destinations%5B0%5D%5Bphone%5D=%2B37167224279&parcel_size=L',
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer BKOmDNasO2HFFgpyyTiFZX5kGPUqCkiVcgPz5VUn',
        'Accept: application/json'
    ),
));

$response = curl_exec($curl);
$r = mb_detect_encoding($response);
$response = mb_convert_encoding($response,$r,'utf-8');
curl_close($curl);
$response = json_decode($response,true);