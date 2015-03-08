<?php

require('functions.php');

$client_id = "bac3ceec-d33f-45cf-9566-e97097eedc08";
$client_secret = "rdRBbCwKNPtMptuVO9PXLXXaf";

ini_set( 'session.SESSION_domain', '.nexus.gusadelic.net' );
session_start();
        var_dump($_SESSION);


    if ( isset($_SESSION['token']) ) {


        echo $_SESSION['token'];




    } 


    else if ( isset($_GET['code']) ){

        $url = "https://api.home.nest.com/oauth2/access_token";

        $fields = array( 
                'client_id' => $client_id,
                'code' => $_GET['code'],
                'client_secret' => $client_secret,
                'grant_type' => "authorization_code"
            );

        $token_obj = json_decode(file_post_contents_curl($url, $fields));

        $_SESSION['token'] = $token_obj->access_token;

        var_dump($_SESSION);
        session_write_close();

        require('global.php');
        $params = array(
            ':client_id' => $client_id,
            ':token' => $_SESSION['token']
        );
        $update_client = $db->prepare("
            UPDATE client SET `token` = :token WHERE `client_id` = :client_id
        ");

        $update_client->execute($params);

       

        header('Location: http://nexus.gusadelic.net/nest');

    }

    else  {

        require('global.php');

        $params = array(
            ':client_id' => $client_id,
            ':client_secret' => $client_secret
        );

        $add_client = $db->prepare("
            INSERT INTO client (`client_id`, `client_secret`) VALUES (:client_id, :client_secret)
        ");

        $add_client->execute($params);

        $url = "https://home.nest.com/login/oauth2?client_id=" . $client_id . "&state=APP";



        header('Location: ' . $url);
        exit();    


    }








?>
