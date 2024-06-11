<?php
/**
 * SigninService
 *  - Simple authentication service
 * @author Aélion <jean-luc.aubert@aelion.fr>
 * @version 1.0.0
 *  - signin method that create a DTO to return complete user and account
 */
namespace Api\User;

use Aelion\Dbal\Exception\IncorrectSqlExpressionException;
use Aelion\Dbal\Exception\NotFoundException;
use Aelion\Http\Request\Request;
use Aelion\Http\Response\Response;
use Aelion\Http\Response\HttpResponseStatus;
use Aelion\Http\Response\JsonResponse;
use Aelion\Registry\Registrable;
use Firebase\JWT\JWT;

class SigninService implements Registrable {

    private $repository = null;
    private Request $request;
    private $secretKey;

    private function __construct(Request $request) {
        $this->request = $request;
        $this->repository = new UserRepository();
        $this->secretKey = $_ENV['JWT_KEY'];

    }

    /**
     * @override
     * @see Registrable interface
     */
    public static function getInstance(Request $request): Registrable {
        return new SigninService($request);
    }

    protected function generateJWT(array $payload, string $secretKey): string{
        return JWT::encode($payload, $secretKey);
    }

    public function signin(): Response {
        try {
            $userEntity = $this->repository->findByLoginAndPassword($this->request->get('username'), $this->request->get('userpassword'));
            $roles = [];
            foreach ($userEntity->getRoles() as $role) {
                $userRole = [
                    'id' => $role->getId(),
                    'role' => $role->getRole()
                ];
                array_push($roles, $userRole);
            }

            $payload = [
                'iss' => "http://localhost:8003/signin", // Emetteur du jeton
                'aud' => "http://localhost:8003/signin", // Receveur
                'iat' => time(), // Heure émission du jeton
                'nbf' => time(), // Heure validation
                'data' => [
                    'id' => $userEntity->getId(),
                    'login' => $userEntity->getLogin(),
                    'password' => $userEntity->getPassword(),
                    'account' => [
                        'id' => $userEntity->getAccount()->getId(),
                        'lastname' => $userEntity->getAccount()->getLastname(),
                        'firstname' => $userEntity->getAccount()->getFirstname(),
                        'gender' => $userEntity->getAccount()->getGender()
                    ],
                    'roles' => $roles,
                ]
            ];

            // Génération du JWT
            $payload['data']['token'] = $this->generateJWT($payload['data'], $this->secretKey);
            $response = new JsonResponse();
            
            $response->setPayload($payload);
            
            return $response;
        } catch (IncorrectSqlExpressionException $e) {
            $response = new JsonResponse();
            $response->setStatus(HttpResponseStatus::InternalServerError);
            $content = [
                'message' => $e->getMessage()
            ];
            $response->setPayload($content);
            return $response;
        } catch (NotFoundException $e) {
            $response = new JsonResponse();
            $response->setStatus(HttpResponseStatus::NotFound);
            $content = [
                'message' => $e->getMessage()
            ];
            $response->setPayload($content);
            return $response;       
        }
       
    }
}