<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Servicio;
use AppBundle\Form\ServicioType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ServicioController extends Controller
{
    /**
     * @Route("/servicio", name="servicio_index")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function indexAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Servicio');
        $servicios = $repository->findAll();

        return $this->render('servicio/index.html.twig', [
            'servicios' => $servicios,
        ]);
    }

    /**
     *  Crea un nuevo Usuario
     *
     * @Route("/servicio/crear", name="servicio_create")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Method({"GET", "POST"})
     *
     */
    public function createAction(Request $request)
    {
        $servicio = new Servicio();
        $form = $this->createForm(ServicioType::class, $servicio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($servicio);
            $entityManager->flush();

            $this->addFlash('success', 'El servicio '. $servicio->getNombre() .' se ha creado');

            return $this->redirectToRoute('servicio_index');
        }

        return $this->render('servicio/create.html.twig', [
            'servicio' => $servicio,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/servicio/{id}", name="servicio_show")
     * @Security("is_granted('ROLE_MANAGER')")
     * @Method("GET")
     * @ParamConverter("servicio", options={"mapping": {"id" : "id"}})
     */
    public function showAction(Servicio $servicio)
    {
        return $this->render('servicio/show.html.twig', ['servicio' => $servicio]);
    }

    /**
     * Edita un servicio existente
     *
     * @Route("/servicio/{id}/editar", name="servicio_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     * @ParamConverter("servicio", options={"mapping": {"id" : "id"}})
     */
    public function editAction(Servicio $servicio, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(ServicioType::class, $servicio);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Se ha editado correctamente el servicio');

            return $this->redirectToRoute('servicio_edit', ['id' => $servicio->getId()]);
        }

        return $this->render('servicio/edit.html.twig', [
            'servicio'        => $servicio,
            'edit_form'   => $editForm->createView(),
        ]);
    }

}