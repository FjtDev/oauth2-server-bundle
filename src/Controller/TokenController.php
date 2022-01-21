<?php

namespace OAuth2\ServerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TokenController extends AbstractController
{
    protected $server;
    protected $clientCredentials;
    protected $authorizationCode;
    protected $refreshToken;
    protected $userCredentials;
    protected $request;
    protected $response;

    /**
     * @param $server
     * @param $clientCredentials
     * @param $authorizationCode
     * @param $refresh_token
     * @param $userCredentials
     * @param $request
     * @param $response
     */
    public function __construct(
        $server,
        $clientCredentials,
        $authorizationCode,
        $refresh_token,
        $userCredentials,
        $request,
        $response
    )
    {
        $this->server = $server;
        $this->clientCredentials = $clientCredentials;
        $this->authorizationCode = $authorizationCode;
        $this->refreshToken = $refresh_token;
        $this->userCredentials = $userCredentials;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * This is called by the client app once the client has obtained
     * an authorization code from the Authorize Controller (@see OAuth2\ServerBundle\Controller\AuthorizeController).
     * returns a JSON-encoded Access Token or a JSON object with
     * "error" and "error_description" properties.
     *
     * @Route("/token", name="_token")
     */
    public function tokenAction()
    {
        // Add Grant Types
        $this->server->addGrantType($this->clientCredentials);
        $this->server->addGrantType($this->authorizationCode);
        $this->server->addGrantType($this->refreshToken);
        $this->server->addGrantType($this->userCredentials);

        return $this->server->handleTokenRequest($this->request, $this->response);
    }
}
