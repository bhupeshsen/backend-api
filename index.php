<?php

$version = '1.0';
error_reporting(0);
try {
    include_once './connection.php';

    
    $db = new Connection("OK");
    $IP = getenv('HTTP_CLIENT_IP') ?: getenv('HTTP_X_FORWARDED_FOR') ?: getenv('HTTP_X_FORWARDED') ?: getenv('HTTP_FORWARDED_FOR') ?: getenv('HTTP_FORWARDED') ?: getenv('REMOTE_ADDR');
    if (filter_input(INPUT_GET, "url__id")) {
        try {
            $URL1 = filter_input(INPUT_GET, "url__id");
            $GDT = explode("/", $URL1);
            $MTD = filter_input(INPUT_SERVER, "REQUEST_METHOD");
            $Folder = array_shift($GDT);
           $URL = array_shift($GDT);
            $Data = ($MTD == "POST") ? filter_input_array(INPUT_POST) : $GDT;
            $DID = filter_input(INPUT_SERVER, "HTTP_DID");
            $SK = filter_input(INPUT_SERVER, "HTTP_SK");
            $LOGINTYPE = filter_input(INPUT_SERVER, "HTTP_LOGINTYPE");
            $UA = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');
            $Response = array();
            $S = $db->selectRow("SELECT `api_session`.`session_id`,`user`.`user_id`,IFNULL(`user`.`name`,'') AS name,`user`.`number`,IFNULL(`user`.`email`,0) AS email,IFNULL(`user`.`photo`,'') AS `photo` FROM `api_session` INNER JOIN `user` ON `user`.`user_id`=`api_session`.`user_id` AND `api_session`.`session_id`=md5(?) AND `api_session`.`device_id`=? AND `api_session`.`active`=1", array($SK, $DID));
            if ($S != NULL || $URL == "user-register" || $URL == "login") {
                try {
                    $file = './version-' . $version . "/" . $Folder . "/" . $URL . '.php';
                    if (file_exists($file)) {
                        include $file;
                    } else {
                        $Response["status"] = false;
                        $Response["message"] = "API not exists.";
                    }
                } catch (PDOException $e) {
                    $err = $db->error();
                    $Response["status"] = false;
                    $Response["message"] = $err == NULL ? $e->getMessage() : $err;
                } catch (Exception $e) {
                    $Response["status"] = false;
                    $Response["message"] = $e->getMessage();
                }
            } else {
                $Response["status"] = FALSE;
                $Response["message"] = "session expired";
            }
        } catch (PDOException $e) {
            $err = $db->error();
            $Response["status"] = false;
            $Response["message"] = $err == NULL ? $e->getMessage() : $err;
        } catch (Exception $e) {
            $Response["status"] = false;
            $Response["message"] = $e->getMessage();
        }
    } else {
        $Response["status"] = FALSE;
        $Response["message"] = "welcome to Flywingwolf API version v";
    }
} catch (PDOException $e) {
    $err = $db->error();
    $Response["status"] = false;
    $Response["message"] = $err == NULL ? $e->getMessage() : $err;
} catch (Exception $e) {
    $Response["status"] = FALSE;
    $Response["message"] = $e->getMessage();
}
echo json_encode($Response);
