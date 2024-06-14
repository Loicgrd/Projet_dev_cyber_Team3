<?php
namespace Api\User;
use Api\User\RequestStrategy;
class UsePreparedRequest implements RequestStrategy {
    function applyStrategy(\PDO $conn, $query, $params = [])
    {
        $pdoStatement = $conn->prepare($query);
        if ($pdoStatement === false) {
            echo $this->$conn->errorCode().': '.$this->$conn->errorInfo();
       }
        foreach($params as $key => $value){
            $pdoStatement->bindValue($key, $value, \PDO::PARAM_STR);
        }
    
        $pdoStatement->execute();
        return $pdoStatement;

    }
}