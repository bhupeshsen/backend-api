<?php

try {
    if ($db && $Data) {
        $user_id = trim($db->input($Data, "user_id"));
 
            $data = $db->selectRow("SELECT  `user_id`, `name`, `number`, `email`, `photo`, `address`, `username` FROM `user`  WHERE `user`.`user_id`=? ", array($user_id));
            if ($data != NULL) {
                $Response["status"] = true;
                $Response["data"] = $data;
            } else {
                $Response["status"] = false;
                $Response["message"] = "not exits";
            }
        
    } else {
        $Response["status"] = false;
        $Response["message"] = "something went wrong.\nplease try again later.";
    }
} catch (Exception $e) {
    $Response["status"] = false;
    $Response["message"] = $e->getMessage();
}