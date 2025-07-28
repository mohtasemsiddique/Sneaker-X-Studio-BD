<?php
$location = 'Melbourne';
$apiKey = 'YOUR_API_KEY';
$url = "https://api.weatherapi.com/v1/current.json?key=$apiKey&q=$location";

$response = @file_get_contents($url);
$data = json_decode($response, true);

if ($data && isset($data['current'])) {
    echo "Weather in {$location}: " . $data['current']['temp_c'] . "°C, " . $data['current']['condition']['text'];
} else {
    echo "Unable to fetch weather data at the moment.";
}
