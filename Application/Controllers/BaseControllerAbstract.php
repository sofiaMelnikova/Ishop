<?php

namespace Application\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BaseControllerAbstract
{
    // userProfile.php, adminGoods.php, addJacket.php, addPlaid.php, addShoes.php, login.php, registration.php (csrfToken)

    /**
     * @var string
     */
    public static $csrfToken;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * BaseControllerAbstract constructor.
     * @param Application $app
     * @param $request
     */
    public function __construct(Application $app, $request) {
        $this->app = $app;
        $this->twig = $app['twig'];
        $this->request = $request;
        $this->response = new Response();
        self::$csrfToken = $this->app['RandomString.helper']->get();
    }

    /**
     * @param string $template
     * @param array $params
     * @return Response
     */
    protected function render(string $template,array $params = []) {
        $this->response->setContent($this->twig->render($template, $params));
        return $this->response;
    }

    public function addCsrfToken () {
        $cookie = new \Symfony\Component\HttpFoundation\Cookie('csrfToken', self::$csrfToken, strtotime('now + 65 minutes'));
        $this->response->headers->setCookie($cookie);
    }
}