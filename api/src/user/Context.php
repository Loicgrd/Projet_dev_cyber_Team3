<?php
namespace Api\User;
class Context {
    private $strategy;
    public function __construct(RequestStrategy $requestStrategy) {
        $this->strategy = $requestStrategy;
    }
    public function setStrategy($requestStrategy){
        $this->strategy = $requestStrategy;
    }
    public function useStrategy(\PDO $conn, $query, $params = []){
        return $this->strategy->applyStrategy($conn, $query, $params);
    }
}