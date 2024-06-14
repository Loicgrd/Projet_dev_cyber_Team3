<?php
namespace Api\User;
interface RequestStrategy {
    public function applyStrategy(\PDO $conn, string $query, array $params = []);
}