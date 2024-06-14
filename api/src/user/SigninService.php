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

class SigninService implements Registrable {

    private $repository = null;
    private Request $request;

    private function __construct(Request $request) {
        $this->request = $request;
        $this->repository = new UserRepository();
    }

    /**
     * @override
     * @see Registrable interface
     */
    public static function getInstance(Request $request): Registrable {
        return new SigninService($request);
    }

    public function signin(): Response {
        try {
            $username = $this->request->get('username');
            $userpassword = $this->request->get('userpassword');

            $userEntity = $this->repository->findByLogin($username);

            if ($userEntity && password_verify($userpassword, $userEntity->getPassword())) {
                $roles = [];
                foreach ($userEntity->getRoles() as $role) {
                    $userRole = [
                        'id' => $role->getId(),
                        'role' => $role->getRole()
                    ];
                    array_push($roles, $userRole);
                }

                $payload = [
                    'id' => $userEntity->getId(),
                    'login' => $userEntity->getLogin(),
                    'account' => [
                        'id' => $userEntity->getAccount()->getId(),
                        'lastname' => $userEntity->getAccount()->getLastname(),
                        'firstname' => $userEntity->getAccount()->getFirstname(),
                        'gender' => $userEntity->getAccount()->getGender()
                    ],
                    'roles' => $roles
                ];
                $response = new JsonResponse();
                $response->setPayload($payload);
                return $response;
            } else {
                // Invalid credentials
                $response = new JsonResponse();
                $response->setStatus(HttpResponseStatus::Unauthorized);
                $content = ['message' => 'Invalid credentials'];
                $response->setPayload($content);
                return $response;
            }
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
