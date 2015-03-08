<?php
ini_set( 'session.SESSION_domain', '.nexus.gusadelic.net' );
session_start();
        var_dump($_SESSION);


    if ( isset($_SESSION['token']) ) {


        echo $_SESSION['token'];




    } 

    else if ( isset($_GET['client_id']) || isset($_SESSION['client_id']) ) {
        if ( isset($_GET['client_id']) ) {
            $_SESSION['client_id'] = $_GET['client_id'];
            $_SESSION['client_secret'] = $_GET['client_secret'];
        }

        session_write_close();

        require('global.php');

        $params = array(
            ':client_id' => $_SESSION['client_id'],
            ':client_secret' => $_SESSION['client_secret']
        );

        $add_client = $db->prepare("
            INSERT INTO client (`client_id`, `client_secret`) VALUES (:client_id, :client_secret)
        ");

        $add_client->execute($params);

        $url = "https://home.nest.com/login/oauth2?client_id=" . $_SESSION['client_id'] . "&state=APP";



        header('Location: ' . $url);
        exit();    


    }


    else if ( isset($_GET['code']) ){

        $url = "https://api.home.nest.com/oauth2/access_token";

        $fields = array( 
                'client_id' => $_SESSION['client_id'],
                'code' => $_GET['code'],
                'client_secret' => $_SESSION['client_secret'],
                'grant_type' => "authorization_code"
            );

        $_SESSION['token'] = file_post_contents_curl($url, $fields);
        var_dump($_SESSION);
        session_write_close();

        require('global.php');
        $params = array(
            ':client_id' => $_SESSION['client_id'],
            ':token' => $_GET['token']
        );
        $update_client = $db->prepare("
            UPDATE client SET `token` = :token WHERE `client_id` = :client_id
        ");

        $update_client->execute($params);

       

        //header('Location: http://nexus.gusadelic.net/nest');

    }


    else {

        echo '<html><head>';
        echo '</head>';
        echo '<body>';
        echo '<h1>Nest - Create Profile</h1>';
        echo '<form method="GET" action="https://nexus.gusadelic.com/nest/app">';
        echo '<label for="client_id">Client ID</label>';
        echo '<input type="text" name="client_id" value="" />';
        echo '<br /><label for="client_secret" value="">Client Secret</label>';
        echo '<input type="text" name="client_secret" value="" />';
        echo '<input type="submit" />';
        echo '</form></body></html>';

    }




function file_get_contents_curl($url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);


        $limit  =   0;
        while((curl_errno($ch) == 28 || $limit == 0) && $limit < 5){
                if ( $limit > 0 ) {
                        echo "Connection Timed out.  Retrying... Attempt " . $limit . "\n";
                        sleep(1);
                }

                $limit++;
                $data = curl_exec($ch);
        }


    curl_close($ch);

    return $data;
}


function file_post_contents_curl($url, $fields)
{

    //url-ify the data for the POST
    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
    rtrim($fields_string, '&');

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch,CURLOPT_POST, count($post));
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);


        $limit  =   0;
        while((curl_errno($ch) == 28 || $limit == 0) && $limit < 5){
                if ( $limit > 0 ) {
                        echo "Connection Timed out.  Retrying... Attempt " . $limit . "\n";
                        sleep(1);
                }

                $limit++;
                $data = curl_exec($ch);
        }


    curl_close($ch);

    return $data;
}

?>
