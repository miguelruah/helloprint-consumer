<?php
class consumerrequests{
    // define pdo object
    var $pdo;

    // accept a request in the form of a command and an array of params
    // then call the correspondent method
    public function process($command, $params){
        try {
            $this->pdo = new PDO( 'mysql:host='.$GLOBALS['host'].';dbname='.$GLOBALS['dbname'], $GLOBALS['dbuser'], $GLOBALS['dbpass'] );
        }
        catch(PDOException $e) {
            die("Error connecting to DB\n");
        }
        
        switch($command) {
            case "login":
                if (isset($params[0])) {$username = $params[0];}
                if (isset($params[1])) {$password = $params[1];}

                $result = $this->login($username, $password);

                break;
            case "forgot":
                if (isset($params[0])) {$username = $params[0];}
                
                $result = $this->forgot($username);
                
                break;
        }
        return $result;
    }
    private function login($username, $password){
        // check if user and password exist in database
        $stmt = $this->pdo->prepare("SELECT email FROM users WHERE username=? AND password=?");
        $stmt->execute([$username, $password]);
        $dbrow = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ( isset($dbrow['email']) ) { // user and password are correct
            
            $result = ["result" => 1];
            
        } else { // incorrect or missing user/password
            
            $result = ["result" => 0];
            
        }
        return $result;
    }
    private function forgot($username){

        $stmt = $this->pdo->prepare("SELECT email, username FROM users WHERE username=?");
        $stmt->execute([$username]);
        $dbrow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ( isset($dbrow['email']) ) { // user exists => send email with new password

            $email = $dbrow['email'];
            $username = $dbrow['username'];
            
            // create and set new password
            $password = randomPassword();
            $stmt = $this->pdo->prepare("UPDATE users set password=? WHERE username=?");
            $stmt->execute([$password, $username]);

            // call mailer API
            // it can be installed in any of the 3 servers: webserver, producer, consuler
            //      => just configure $GLOBALS['mailer'] appropriately in the consumer's config.php file

            $subject = 'Your new password from Hello Print';
            $body = "This is your new password: ".$password."</br></br>Please try to login now, using your username and this new password at <a href=\"http://".$GLOBALS['webserver']."\">http://".$GLOBALS['webserver']."</a>";
            $params = [
                "recipient" => $email,
                "recipientname" => $username,
                "subject" => $subject,
                "body" => $body
            ];
            $this->callMailer($params);
            $result = ['result' => 1];
            
        } else { // user not found
            
            $result = ['result' => 0];

        }
        
        return $result;
    }
    private function callMailer($arrayParams) {
        // create resource
        $ch = curl_init();

        // encode $arrayParams as a json string
        $jsonParams = json_encode($arrayParams);

        // set destination and params
        curl_setopt($ch, CURLOPT_URL, $GLOBALS['mailer']);  // set destination *** in real life, this should be https://mailer.local ***
        curl_setopt($ch, CURLOPT_POST, 1);                  // set POST (not GET)
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonParams);  // set params with json string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);     // don't output the result => set a var with it
    
        // run
        $result = curl_exec($ch);

        // close resource
        curl_close($ch);
        
        return $result;
    }
}
?>