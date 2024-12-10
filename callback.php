<?php
session_start();

$client_id = 'Ov23litCbeMoR6ymsX7S';
$client_secret = '4e6e5429339274d542f76b5d1a4eaba13cfbe5b7';
$code = $_GET['code'];

if (!$code) {
    die('Authorization failed.');
}

// Exchange the code for an access token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://github.com/login/oauth/access_token');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'code' => $code,
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
if (!isset($data['access_token'])) {
    die('Error retrieving access token.');
}

// Fetch the user's details
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/user');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $data['access_token'],
    'User-Agent: Multichat-App',
]);
$response = curl_exec($ch);
curl_close($ch);

$user = json_decode($response, true);
if (!isset($user['login'])) {
    die('Error retrieving user information.');
}

// Set session and redirect to chat interface
$_SESSION['username'] = $user['login'];
header('Location: index.html'); // Redirect to chat interface
