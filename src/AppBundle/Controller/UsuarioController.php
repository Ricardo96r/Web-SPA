<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UsuarioType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UsuarioController extends Controller
{
    /**
     * @Route("/usuario", name="usuario_index")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function indexAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');

        if ($this->isGranted('ROLE_MANAGER') && ! $this->isGranted('ROLE_ADMIN')) {
            $query = $repository->createQueryBuilder('p')
                ->where('p.roles = :rol')
                ->setParameter('rol', '["ROLE_ESPECIALISTA"]')
                ->getQuery();
            $usuarios = $query->getResult();
        } else {
            $query = $repository->createQueryBuilder('p')
                ->orderBy('p.roles')
                ->getQuery();
            $usuarios = $query->getResult();
        }

        return $this->render('usuario/index.html.twig', [
            'usuarios' => $usuarios,
        ]);
    }

    /**
     *  Crea un nuevo Usuario
     *
     * @Route("/usuario/crear", name="usuario_create")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Method({"GET", "POST"})
     *
     */
    public function createAction(Request $request)
    {
        $usuario = new User();
        $form = $this->createForm(UsuarioType::class, $usuario, ['role' => $this->getUser()->getRoles()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            if ($this->isGranted('ROLE_MANAGER')) {
                $usuario->setRoles(['ROLE_ESPECIALISTA']);
            }

            $encoder = $this->get('security.password_encoder');
            $encodedPassword = $encoder->encodePassword($usuario, $usuario->getPassword());
            $usuario->setPassword($encodedPassword);

            $entityManager->persist($usuario);
            $entityManager->flush();

            $this->addFlash('success', 'El usuario '. $usuario->getNombre() . ' '. $usuario->getApellido().' se ha creado');

            return $this->redirectToRoute('usuario_index');
        }

        return $this->render('usuario/create.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/usuario/{id}", name="usuario_show")
     * @Security("is_granted('ROLE_ESPECIALISTA')")
     * @Method("GET")
     * @ParamConverter("usuario", options={"mapping": {"id" : "id"}})
     */
    public function showAction(User $usuario)
    {
        return $this->render('usuario/show.html.twig', ['usuario' => $usuario]);
    }

    /**
     * Edita un usuario existente
     *
     * @Route("/usuario/{id}/editar", name="usuario_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     * @ParamConverter("usuario", options={"mapping": {"id" : "id"}})
     */
    public function editAction(User $usuario, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(UsuarioType::class, $usuario, ['role' => $this->getUser()->getRoles(), 'editar' => true]);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $encoder = $this->get('security.password_encoder');
            $encodedPassword = $encoder->encodePassword($usuario, $usuario->getPassword());
            $usuario->setPassword($encodedPassword);
            
            $entityManager->flush();

            $this->addFlash('success', 'Se ha editado correctamente al usuario');

            return $this->redirectToRoute('usuario_show', ['id' => $usuario->getId()]);
        }

        return $this->render('usuario/edit.html.twig', [
            'usuario'        => $usuario,
            'edit_form'   => $editForm->createView(),
        ]);
    }

}
