<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Equipo;
use AppBundle\Form\EquipoType;
use AppBundle\Form\ServicioType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EquipoController extends Controller
{
    /**
     * @Route("/equipo", name="equipo_index")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function indexAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Equipo');
        $equipos = $repository->findAll();

        return $this->render('equipo/index.html.twig', [
            'equipos' => $equipos,
        ]);
    }

    /**
     *  Crea un nuevo Usuario
     *
     * @Route("/equipo/crear", name="equipo_create")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Method({"GET", "POST"})
     *
     */
    public function createAction(Request $request)
    {
        $equipo = new equipo();
        $form = $this->createForm(EquipoType::class, $equipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($equipo);
            $entityManager->flush();

            $this->addFlash('success', 'El equipo '. $equipo->getNombre() .' se ha creado');

            return $this->redirectToRoute('equipo_index');
        }

        return $this->render('equipo/create.html.twig', [
            'equipo' => $equipo,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/equipo/{id}", name="equipo_show")
     * @Security("is_granted('ROLE_MANAGER')")
     * @Method("GET")
     * @ParamConverter("equipo", options={"mapping": {"id" : "id"}})
     */
    public function showAction(equipo $equipo)
    {
        return $this->render('equipo/show.html.twig', ['equipo' => $equipo]);
    }

    /**
     * Edita un equipo existente
     *
     * @Route("/equipo/{id}/editar", name="equipo_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     * @ParamConverter("equipo", options={"mapping": {"id" : "id"}})
     */
    public function editAction(equipo $equipo, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(EquipoType::class, $equipo);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Se ha editado correctamente el equipo');

            return $this->redirectToRoute('equipo_edit', ['id' => $equipo->getId()]);
        }

        return $this->render('equipo/edit.html.twig', [
            'equipo'        => $equipo,
            'edit_form'   => $editForm->createView(),
        ]);
    }

}