<?php

try {
    if ($db && $Data) {
        $number = trim($db->checkStr($Data["number"]));
        $name = trim($db->checkStr($Data["name"]));
        $email = trim($db->checkStr($Data["email"]));
        $password = trim($db->checkStr($Data["password"]));
        if ($number && preg_match('/^[0-9]{10}+$/', $number)) {
            $rowData = $db->selectRow("SELECT `user_id`, `name`, `number`, `email`, `password`, `photo`, `address`, `username` FROM `user` WHERE `number`=?", array($number));
            
			if($rowData!=null){
			
				$Response["status"] = TRUE;
				$Response["message"] = "mobile number exist";
			}else{
					$Data["password"]=md5($password);
				$data = $db->InsertData($Data, "user");
				$Response["status"] = TRUE;
				$Response["message"] = "successfully register.";
			
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