<?php

class TestController extends AppController
{
    private $facebook_data;
    private $twitter_data;
    private $google_data;

    public function page()
    {
        $message = 'Beware, for it is a vegetable and it is eager!';
        $this->set(get_defined_vars());
    }

    public function logout()
    {
        $_SESSION = array();
        session_destroy();
        header('Location: ' . APP_URL . '/test/page');
    }

    public function fb_login()
    {
        if (Param::get('error')) {
            redirect('test/page');
            exit;
        }

        $fb = new FacebookHelper();
        $fb->setAppKeys(APP_FB_ID, APP_FB_SECRET);
        $fb->setRedirectUrl(APP_URL . '/test/fb_login');
        $fb->setScopes(array('email'));
        $fb->init();

        if ($fb->IsFacebookSessionPresent()) {
            $this->facebook_data = $fb->GetUserData();
            $_SESSION['open_session_id'] = $this->facebook_data['id'];
            header("Location: " . $fb->getRedirectUrl());
        } else {
            $fb->GetConfirmationUrl();
        }

        if ($_SESSION['open_session_id']) {
            $_SESSION['usr_dt'] = $this->facebook_data;
            header("Location: " . APP_URL . '/test/page');
        }
    }

    public function twitter_login()
    {
        $tweet = new TwitterHelper();
        $tweet->setConsumerKeys(APP_CONSUMER_KEY, APP_CONSUMER_SECRET);
        $tweet->setTokenRequestUrl('http://www.eagervegetable.com/test/twitter_login');

        if (Param::get('oauth_token')) {
            $this->twitter_data = $tweet->getUserData();
            $_SESSION['open_session_id'] = $this->twitter_data->id;
            header('Location: ' . APP_URL . '/test/twitter_login');
        } else {
            $tweet->InitializeTwitterOauth();
        }

        if ($_SESSION['open_session_id']) {
            $_SESSION['usr_dt'] = (array) $this->twitter_data;
            header('Location: ' . APP_URL . '/test/page');
        }
    }

    public function google_login()
    {
        $google = new GoogleHelper();
        $google->setAppDetails('Eager Vegetable', APP_GOOGLE_CLIENTID, APP_GOOGLE_CLIENTSECRET, APP_GOOGLE_SIMPLEAPIKEY);
        $google->setRedirectURL('http://www.eagervegetable.com/test/google_login');
        $google->setScopes(array('profile', 'email'));
        $this->google_data = $google->getUserData();

        if (!$_SESSION['open_session_id']) {
            $_SESSION['open_session_id'] = $this->google_data->id;
        } else {
            $_SESSION['usr_dt'] = $this->google_data;
            header('Location: ' . APP_URL . '/test/page');
        }
    }
}

