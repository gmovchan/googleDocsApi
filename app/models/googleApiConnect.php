<?php



class googleApiConnect
{

    const APPLICATION_NAME = 'Google Sheets API PHP Quickstart';

    // If modifying these scopes, delete your previously saved credentials
    // at ~/.credentials/sheets.googleapis.com-php-quickstart.json
    private $clientSecretPath;
    private $scopes;
    private $client;
    private $service;

    function __construct($credentialsName, $spreadsheetId, $clientSecretPath, $credentialsPath)
    {
        
        $this->scopes = implode(' ', array(
            Google_Service_Sheets::SPREADSHEETS_READONLY)
        );
        
        $this->clientSecretPath = $clientSecretPath;
                
        $this->credentialsPath = $credentialsPath;
        
        $this->client = $this->getClient();
        
        $this->service = new Google_Service_Sheets($this->client);
        
        $this->spreadsheetId = $spreadsheetId;
    }

    public function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName(self::APPLICATION_NAME);
        $client->setScopes($this->scopes);
        $client->setAuthConfig($this->clientSecretPath);
        $client->setAccessType('offline');

        // Load previously authorized credentials from a file.
        $credentialsPath = $this->expandHomeDirectory($this->credentialsPath);
        if (file_exists($credentialsPath)) {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

            // Store the credentials to disk.
            if (!file_exists(dirname($credentialsPath))) {
                mkdir(dirname($credentialsPath), 0700, true);
            }
            file_put_contents($credentialsPath, json_encode($accessToken));
            printf("Credentials saved to %s\n", $credentialsPath);
        }
        $client->setAccessToken($accessToken);

        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
        }
        return $client;
    }

    private function expandHomeDirectory($path)
    {
        $homeDirectory = getenv('HOME');
        if (empty($homeDirectory)) {
            $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
        }
        return str_replace('~', realpath($homeDirectory), $path);
    }
    
    public function getRows($range)
    {
        $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
        $values = $response->getValues();
        return $values;
    }

}