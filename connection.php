<?php
class Connection {

    private $link;
    private $stmt;

    function __construct($link) {
        if ($link == "OK") {
            date_default_timezone_set('Asia/Calcutta'); //Calculate Time
            try {
                $this->link = new PDO("mysql:host=localhost;dbname=bluestackdb;charset=UTF8", "root", "");
                $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw $e;
            }
        }
    }

    function __destruct() {
        try {
            $this->link = NULL;
        } catch (Exception $ex) {
            
        }
    }

    function lastID() {
        return $this->link->lastInsertId();
    }

    function close() {
        try {
            $this->link = NULL;
        } catch (Exception $ex) {
            
        }
    }

    function startTransaction() {
        $this->link->beginTransaction();
    }

    function commit() {
        $this->link->commit();
    }

    function rollback() {
        $this->link->rollBack();
    }
	 

    function insert($query, $input_parameters) {
        if (strpos($query, " ") !== false) {
            $this->stmt = $this->link->prepare($query);
            if ($input_parameters == null) {
                return $this->stmt->execute();
            } else {
                return $this->stmt->execute($input_parameters);
            }
        } else {
            $this->stmt = $this->link->prepare($this->procedure($query, $input_parameters));
            if ($input_parameters == null) {
                $v = $this->stmt->execute();
            } else {
                $v = $this->stmt->execute($input_parameters);
            }
//            $this->stmt->closeCursor();
            return $v;
        }
    }

function InsertData($data, $table)
	{
		$names = "";
		$values = "";
		foreach ($data as $name=>$value)
		{
			$names .= "`$name`, ";
			if(strtoupper($value) != "NULL")
				$values .= "'".addslashes($value)."', ";
			else
				$values .= addslashes($value).", ";
		}
		$names = substr($names, 0, strlen($names)-2);
		$values = substr($values, 0, strlen($values)-2);
	    $query = "INSERT INTO `$table` ($names) VALUES ($values)";
		//print $query."<br>";
		//die($query);
		$this->stmt = $this->link->prepare($query);
		return $this->stmt->execute();
 
	}
	
	
    function update($query, $input_parameters) {
        return $this->insert($query, $input_parameters);
    }

    function delete($query, $input_parameters) {
        return $this->insert($query, $input_parameters);
    }

	function UpdateData($data, $table, $conditions=false)
	{
		$query = "UPDATE `$table` SET ";
		foreach ($data as $name=>$value)
		{
			if(strtoupper($value) != "NULL")
				$query .= "`$name` = '".addslashes($value)."', ";
			else
				$query .= "`$name` = ".addslashes($value).", ";

		}
		$query = substr($query, 0, strlen($query)-2);
		if ($conditions) $query .= " WHERE $conditions";
		// print $query."<br>";die;
		$this->stmt = $this->link->prepare($query);
		return $this->stmt->execute();
	
	}


    function select($query, $input_parameters) {
        if (strpos($query, " ") !== false) {
            $this->stmt = $this->link->prepare($query);
        } else {
            $this->stmt = $this->link->prepare($this->procedure($query, $input_parameters));
        }
        if ($input_parameters == null) {
            $this->stmt->execute();
        } else {
            $this->stmt->execute($input_parameters);
        }
        if ($this->stmt->rowCount() > 0) {
            $data = [];
            while ($row = $this->stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            $this->stmt->closeCursor();
            return $data;
        } else {
            return NULL;
        }
    }

    private function procedure($name, $input_parameters) {
        $v = "CALL " . $name . "(";
        $l = $input_parameters == NULL ? 0 : count($input_parameters);
        for ($i = 0; $i < $l; $i++) {
            $v .= "?";
            if ($i < ($l - 1)) {
                $v .= ",";
            }
        }
        $v .= ")";
        return $v;
    }

    function selectRow($query, $input_parameters) {
        if (strpos($query, " ") !== false) {
            $this->stmt = $this->link->prepare($query);
        } else {
            $this->stmt = $this->link->prepare($this->procedure($query, $input_parameters));
        }
        if ($input_parameters == null) {
            $this->stmt->execute();
        } else {
            $this->stmt->execute($input_parameters);
        }
        if ($this->stmt->rowCount() > 0) {
            $acc = $this->stmt->fetch(PDO::FETCH_ASSOC);
            $this->stmt->closeCursor();
            return $acc;
        } else {
            return NULL;
        }
    }

    function checkStr($input) {
        try {
            return trim(htmlspecialchars($input));
        } catch (Exception $e) {
            throw new Exception("Input Error." . $e->getMessage());
        }
    }

    function input($Request, $input) {
        try {
            if (array_key_exists($input, $Request)) {
                return $this->checkStr($Request[$input]);
            } else {
                throw new Exception("Input Error.");
            }
        } catch (Exception $e) {
            throw new Exception("Input Error." . $e->getMessage());
        }
    }

    function is_sha1($str) {
        return (bool) preg_match('/^[0-9a-f]{40}$/i', $str);
    }
    function show($query){
        	$this->stmt = $this->link->prepare($query);
        	 if ($this->stmt->rowCount() > 0) {
            $acc = $this->stmt->fetch(PDO::FETCH_ASSOC);
            $this->stmt->closeCursor();
            return $acc;
        } 
	 
    }

    function sha1Input($Request, $input) {
        try {
            if (!array_key_exists($input, $Request)) {
                throw new Exception("Input Error.");
            }
            $str = $this->checkStr($Request[$input]);
            if (!$this->is_sha1($str)) {
                throw new Exception("Input Error.");
            }
            return $str;
        } catch (Exception $e) {
            throw new Exception("Input Error." . $e->getMessage());
        }
    }

    function sha1InputNonR($Request, $input) {
        try {
            if (!array_key_exists($input, $Request)) {
                return "";
            }
            $str = $this->checkStr($Request[$input]);
            if (!$this->is_sha1($str)) {
                throw new Exception("Input Error.");
            }
            return $str;
        } catch (Exception $e) {
            throw new Exception("Input Error." . $e->getMessage());
        }
    }
	// Delete from the database
	function DeleteData($table, $conditions=false)
	{
		$query = "DELETE FROM `$table`";
		if ($conditions) $query .= " WHERE $conditions";
		//print $query;die;
		$this->stmt = $this->link->prepare($query);
		return $this->stmt->execute();
	 
	}
    function inputNonR($Request, $input) {
        try {
            if (array_key_exists($input, $Request)) {
                return $this->checkStr($Request[$input]);
            } else {
                return "";
            }
        } catch (Exception $e) {
            throw new Exception("Input Error." . $e->getMessage());
        }
    }

    function error() {
        try {
            return $this->stmt->errorInfo()[2];
        } catch (Exception $e) {
            return NULL;
        }
    }

}
