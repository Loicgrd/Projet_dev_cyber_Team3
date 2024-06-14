<?php
/**
 * UserRepository
 *  Simple repository to manage User entity
 * @author Aélion <jean-luc.aubert@aelion.fr>
 * @version 1.0.0
 *  - findByLogin implementation
 */
namespace Api\User;

use Aelion\Dbal\DBAL;
use Aelion\Dbal\Exception\NotFoundException;
use Aelion\Dbal\Exception\IncorrectSqlExpressionException;
use Api\Account\AccountEntity;

class UserRepository {
    private \PDO $dbInstance;

    public function __construct() {
        $this->dbInstance = DBAL::getConnection();
    }

    public function findByLogin(string $username): ?UserEntity {
        $sqlQuery = "SELECT 
            u.id userid, u.login login, u.password password, r.id roleid, r.role role, 
            a.id accountid, a.lastname lastname, a.firstname firstname, a.gender gender 
            FROM 
            user u 
            JOIN user_has_role uhr ON u.id = uhr.user_id 
            JOIN role r ON uhr.role_id = r.id
            JOIN account a ON u.id = a.user_id 
            WHERE u.login = :username";

        // Préparation de la requête
        $pdoStatement = $this->dbInstance->prepare($sqlQuery);

        // Exécution de la requête avec le paramètre lié
        $pdoStatement->execute([':username' => $username]);

        // Récupération du résultat de la requête
        $result = $pdoStatement->fetch(\PDO::FETCH_OBJ);
        if ($result) {
            $user = new UserEntity();
            $user->setId($result->userid);
            $user->setLogin($result->login);
            $user->setPassword($result->password);

            $role = new RoleEntity();
            $role->setId($result->roleid);
            $role->setRole($result->role);
            $user->addRole($role);

            $account = new AccountEntity();
            $account->setId($result->accountid);
            $account->setLastname($result->lastname);
            $account->setFirstname($result->firstname);
            $account->setGender($result->gender);
            $user->setAccount($account);

            return $user;
        } else {
            throw new NotFoundException('No user were found with this credentials');
        }
    }      
}
