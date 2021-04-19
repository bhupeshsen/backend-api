<?php

try {
    if ($db && $Data) {
        $number = trim($db->input($Data, "number"));
        $password = trim($db->input($Data, "password"));
        if ($number && preg_match('/^[0-9]{10}+$/', $number) && $password) {
            $CK = $db->selectRow("SELECT  `user_id`, `name`, `number`, `email`, `password`, `photo`, `address`, `username` FROM `user`  WHERE `user`.`number`=? AND `user`.`password`=md5(?) ", array($number, $password));
            if ($CK != NULL) {
                $SK = sha1(time() . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9));
                $db->insert("INSERT INTO `api_session`(`session_id`, `user_id`, `device_id`, `ip`, `user_agent`, `active`) VALUES (md5(?),?,?,?,?,?);", array($SK, $CK["user_id"], $DID, $IP, $UA,"1"));
                $Response["sk"] = $SK;
				$Response["user_id"] = $CK["user_id"];
                $Response["name"] = $CK["name"];
                $Response["number"] = $CK["number"];
                $Response["email"] = $CK["email"];
                $Response["photo"] = $CK["photo"];
                $Response["city"] = "";
                $Response["status"] = TRUE;
                $Response["message"] = "successfully login.";
            } else {
                $Response["status"] = false;
                $Response["message"] = "username or password may be wrong.\nOnly Delivery boy can login in this app.";
            }
        } else {
            $Response["status"] = false;
            $Response["message"] = "please check mobile number.";
        }
    } else {
        $Response["status"] = false;
        $Response["message"] = "something went wrong.\nplease try again later.";
    }
} catch (Exception $e) {
    $Response["status"] = false;
    $Response["message"] = $e->getMessage();
}