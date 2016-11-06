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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @IgnoreAnnotation({"cond", "endcond"})
 */
class ServicioController extends Controller
{
    /**
     * Ver todos los servicios en una vista
     * @return Response Devuelve la vista de todos los servicios
     *
     * @cond
     * @Route("/servicio", name="servicio_index")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_MANAGER')")
     * @endcond
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Servicio');
        $servicios = $repository->findAll();

        return $this->render('servicio/index.html.twig', [
            'servicios' => $servicios,
        ]);
    }

    /**
     *  Crea un nuevo servicio
     * @param Request $request Los datos del nuevo servicio
     * @return RedirectResponse Redirecciona a la vista de todos los servicios si el formulario es enviado con éxito
     * @return Response Devuelve la vista de formulario
     *
     * @cond
     * @Route("/servicio/crear", name="servicio_create")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Method({"GET", "POST"})
     * @endcond
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
     * Muestra los datos del servicio seleccionado
     * @param Servicio $servicio El servicio seleccionado
     * @return Response Devuelve la vista de los datos del servicio seleccionado
     *
     * @cond
     * @Route("/servicio/{id}", name="servicio_show")
     * @Security("is_granted('ROLE_MANAGER')")
     * @Method("GET")
     * @ParamConverter("servicio", options={"mapping": {"id" : "id"}})
     * @endcond
     */
    public function showAction(Servicio $servicio)
    {
        return $this->render('servicio/show.html.twig', ['servicio' => $servicio]);
    }

    /**
     * Edita un servicio existente
     * @param Servicio $servicio El servicio seleccionado
     * @param Request $request Los datos editados del servicio seleccionado
     * @return RedirectResponse Redirecciona a la vista de todos los servicios si el formulario es enviado con éxito
     * @return Response Devuelve la vista de formulario de editar un servicio
     *
     * @cond
     * @Route("/servicio/{id}/editar", name="servicio_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     * @ParamConverter("servicio", options={"mapping": {"id" : "id"}})
     * @endcond
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