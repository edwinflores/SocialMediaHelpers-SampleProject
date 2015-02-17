<?php
require_once(__DIR__ . '/autoload.php');

class GoogleHelper
{
    private $client_id;
    private $client_secret;
    private $redirect_url;
    private $simple_api_key;
    private $app_name;
    private $scopes;
    private $objOAuthService;

    public function setAppDetails($name, $client_id, $client_secret, $simple_api)
    {
        $this->app_name = $name;
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->simple_api_key = $simple_api;
    }

    public function setRedirectURL($url)
    {
        $this->redirect_url = $url;
    }

    public function setScopes($google_scopes = array())
    {
        $this->scopes = $google_scopes;
    }

    public function getUserData()
    {
        //Client request for Google API access
        $client = new Google_Client();
        $client->setApplicationName($this->app_name);
        $client->setClientId($this->client_id);
        $client->setClientSecret($this->client_secret);
        $client->setRedirectUri($this->redirect_url);
        $client->setDeveloperKey($this->simple_api_key);
        $client->addScope($this->scopes);

        //Client Request Send
        $this->objOAuthService = new Google_Service_Oauth2($client);

        //Logout Request
        if (isset($_REQUEST['logout'])) {
            unset($_SESSION['gACCESSTOKEN']);
            $client->revokeToken();
            header('Location: ' . $this->redirect_url);
        }

        /**
         * Google OAuth Flow Authenticate code
         * Set Access Token to Session
         */
        if (isset($_GET['code'])) {
            $client->authenticate(Param::get('code'));
            $_SESSION['gACCESSTOKEN'] = $client->getAccessToken();
            header('Location: ' . $this->redirect_url);
        }

        // Set access token to make request
        if (!empty($_SESSION['gACCESSTOKEN'])) {
            $client->setAccessToken($_SESSION['gACCESSTOKEN']);
        }

        //Fetch user data from Google
        if ($client->getAccessToken()) {
            $userData = $this->objOAuthService->userinfo->get();
            return $userData;
        } else {
            $authUrl = $client->createAuthUrl();
            redirect($authUrl);
        }

        return null;
    }
}