<?php
require_once __DIR__ . '/autoload.php';
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\Entities\AccessToken;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookHttpable;

class FacebookHelper
{
    /** @var $redirect_helper :: Used as a helper for Facebook redirection*/
    private $redirect_helper;

    /** @var  $scopes :: Scopes for data to be requested
     *  Refer to: https://developers.facebook.com/docs/facebook-login/permissions/v2.2 */
    private $scopes;

    /** @var  $app_id_key & $app_secret_key :: The app's keys given by facebook */
    private $app_id_key;
    private $app_secret_key;

    /** @var  $redirect_url :: The redirect url Facebook goes to in redirection */
    private $redirect_url;

    public function setAppKeys($facebook_id, $facebook_secret)
    {
        $this->app_id_key = $facebook_id;
        $this->app_secret_key = $facebook_secret;
    }

    public function setScopes($scopes = array())
    {
        $this->scopes = $scopes;
    }

    public function setRedirectUrl($url)
    {
        $this->redirect_url = $url;
    }

    /**
     * Initialize and/or get a Facebook Session
     */
    public function init()
    {
        // Initialize app with the app id and the secret id
        FacebookSession::setDefaultApplication($this->app_id_key, $this->app_secret_key);
        $this->redirect_helper = new FacebookRedirectLoginHelper($this->redirect_url);
        try {
            $_SESSION['fb_session_redirect'] = $this->redirect_helper->getSessionFromRedirect();
        } catch( FacebookRequestException $ex ) {
        } catch( Exception $ex ) {
        }
    }

    /**
     * Checks if an ongoing Facebook Session is present
     */
    public function IsFacebookSessionPresent()
    {
        return isset($_SESSION['fb_session_redirect']);
    }

    /**
     * If the app isn't confirmed by the user, goes through this Url first
     */
    public function GetConfirmationUrl()
    {
        $scopes_string = implode(",", $this->scopes);
        $loginUrl = $this->redirect_helper->getLoginUrl(array('scope' => $scopes_string));
        header("Location: " . $loginUrl);
    }

    /**
     * @param array $data :: The list of data needed
     * @return array $user_data :: Returns an associative array with the user's data
     */
    public function GetUserData(array $data = null)
    {
        //Request the user data
        $request = new FacebookRequest($_SESSION['fb_session_redirect'], 'GET', '/me');
        $response = $request->execute();

        //Fetch the response
        $graphObject = $response->getGraphObject();
        if ($data) {
            $user_data = array();
            $data_count = count($data);
            for ($i = 0; $i < $data_count; $i++) {
                $info = $graphObject->getProperty($data[$i]);
                if ($info) {
                    $user_data[$data[$i]] = $info;
                }
            }
            return $user_data;
        }
        $user_data = $this->GetAllUserData($graphObject);

        return $user_data;
    }

    private function GetAllUserData($graphObject)
    {
        $keys = $graphObject->getPropertyNames();
        $key_count = count($keys);
        $data = [];
        for ($i = 0; $i < $key_count; $i++) {
            $info = $graphObject->getProperty($keys[$i]);
            if ($info) {
                $data[$keys[$i]] = $info;
            }
        }
        return $data;
    }

    /*
     * For manual redirection
     */
    public function getRedirectUrl()
    {
        return $this->redirect_url;
    }
}