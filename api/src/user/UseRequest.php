<?php
namespace Api\User;
use Api\User\RequestStrategy;
class UseRequest implements RequestStrategy {
    function applyStrategy($conn, $query, $params = [])
    {
        foreach($params as $key => $value){
            $query = str_replace($key, $conn->quote($value), $query);
        }
        $pdoStatement = $conn -> query($query);
        return $pdoStatement;
    }
}