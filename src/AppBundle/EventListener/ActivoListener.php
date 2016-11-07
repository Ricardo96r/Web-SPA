<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Filtra si el usuario tiene inicio de sesión y no esta activo, para que redireccione a la vista de error de usuario inactivo
 */
class ActivoListener
{
    private $router;
    private $container;

    public function __construct($router, $container)
    {
        $this->router = $router;
        $this->container = $container;
    }

    /**
     * Filtra que si el usuario esta con inicio de sesión y no esta activo redireccione a la vista de error activo
     * @return RedirectResponse Redirecciona a la vista de error activo
     *
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $container = $this->container;
        if( $container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ){
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $activoRouteName = "activo_error";
            $verCuentaRouteName = "usuario_show";
            $logoutRouteName = "security_logout";
            $routeName = $event->getRequest()->get('_route');
            if (!$user->getActivo()) {
                if ($routeName != $activoRouteName && $routeName != $verCuentaRouteName && $routeName != $logoutRouteName) {
                    $url = $this->router->generate($activoRouteName);
                    $event->setResponse(new RedirectResponse($url));
                }
            }
        }
    }
}