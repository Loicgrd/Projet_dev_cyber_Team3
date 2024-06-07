<?php
/**
 * Kernel
 *  Base class running first
 * @author Aélion <jean-luc.aubert@aelion.fr>
 * @version 1.0.0
 *  - Instanciate router
 *  - Handle request
 *  - Return Response object
 */
namespace Aelion;

use Aelion\Router\Router;
use Aelion\Http\Request\Request;
use Aelion\Http\Response\Response;
use Aelion\Http\Response\JsonResponse;
use Aelion\Http\Response\HttpResponseStatus;
use Aelion\Router\Exception\NoRouteMatchingException;
use Aelion\Router\Exception\NoSuchFileException;

use Dotenv\Dotenv;

class Kernel {
    /**
     * Instance of the Kernel
     */
    private static ?Kernel $instance = null;

    /**
     * Internal router
     */
    private Router $router;

    private Request $request;

    //Constructeur
    private function __construct() {
        $this->setRouter();
        // Set environment vars
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
    }

    /**
     * Create a Kernel instance if not already exists
     * @return Aelion\Kernel
     */
    public static function create(): Kernel {
        if (self::$instance === null) {
            self::$instance = new Kernel();
        }
        return self::$instance;
    }

    public function processRequest(): ?Response {
        $this->request = new Request($this);
        try {
            $response = $this->request->process();
        } catch (NoRouteMatchingException $e) {
            $payload = [
                'message' => $e->getMessage()
            ];
            $response = new JsonResponse();
            $response->setStatus(HttpResponseStatus::NotFound);
            $response->setPayLoad($payload);
        } catch(NoSuchFileException $e) {
            $payload = [
                'message' => $e->getMessage()
            ];
            $response = new JsonResponse();
            $response->setStatus(HttpResponseStatus::NotFound);
            $response->setPayLoad($payload);            
        }
        return $response;
    }

    public function getRouter(): Router {
        return $this->router;
    }

    private function setRouter() {
        $this->router = new Router();
    }
}