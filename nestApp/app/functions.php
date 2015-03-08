<?php 

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