<?php
// Load the Google API PHP Client Library.
require_once __DIR__ . '/vendor/autoload.php';

// Start a session to persist credentials.
session_start();

// Create the client object and set the authorization configuration
// from the client_secrets.json you downloaded from the Developers Console.
$client = new Google_Client();
$client->setAuthConfig(__DIR__ . '/client_secret_564146751406-c8acklgelm49inlme85p9kmtiskcrko2.apps.googleusercontent.com.json');
$client->getHttpClient()->setDefaultOption('verify', false);
$client->setRedirectUri('http://localhost/google-api/oauth2callback.php');
$client->addScope(Google_Service_Analytics::ANALYTICS_READONLY);

// Handle authorization flow from the server.
if (! isset($_GET['code'])) {
  $auth_url = $client->createAuthUrl();
  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  $redirect_uri = 'http://localhost/google-api';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

