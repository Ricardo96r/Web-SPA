<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @IgnoreAnnotation({"cond", "endcond"})
 */
class SecurityController extends Controller
{
    /**
     * Inicio de sesión de usuario
     * @return Response Devuelve la vista de inicio de sesión de usuario
     *
     * @cond
     * @Route("/login", name="security_login")
     * @endcond
     */
    public function loginAction()
    {
        $helper = $this->get('security.authentication_utils');

        return $this->render('security/login.html.twig', [
            // last username entered by the user (if any)
            'last_username' => $helper->getLastUsername(),
            // last authentication error (if any)
            'error' => $helper->getLastAuthenticationError(),
        ]);
    }

    /**
     * Cerrar sesión de usuario
     * @return void
     *
     * @cond
     * @Route("/logout", name="security_logout")
     * @endcond
     */
    public function logoutAction()
    {
        throw new \Exception('This should never be reached!');
    }
}
