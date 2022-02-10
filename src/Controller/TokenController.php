<?php

namespace OAuth2\ServerBundle\Controller;

use OAuth2\GrantType\AuthorizationCode;
use OAuth2\GrantType\ClientCredentials;
use OAuth2\GrantType\RefreshToken;
use OAuth2\GrantType\UserCredentials;
use OAuth2\HttpFoundationBridge\Request;
use OAuth2\HttpFoundationBridge\Response;
use OAuth2\Server;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TokenController extends AbstractController
{
    protected Server $server;
    protected ClientCredentials $clientCredentials;
    protected AuthorizationCode $authorizationCode;
    protected RefreshToken $refreshToken;
    protected UserCredentials $userCredentials;
    protected Request $request;
    protected Response $response;

    /**
     * @param Server            $server
     * @param ClientCredentials $clientCredentials
     * @param AuthorizationCode $authorizationCode
     * @param RefreshToken      $refresh_token
     * @param UserCredentials   $userCredentials
     * @param Request           $request
     * @param Response          $response
     */
    public function __construct(
        Server $server,
        ClientCredentials $clientCredentials,
        AuthorizationCode $authorizationCode,
        RefreshToken $refresh_token,
        UserCredentials $userCredentials,
        Request $request,
        Response $response
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
