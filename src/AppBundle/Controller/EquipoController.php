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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @IgnoreAnnotation({"cond", "endcond"})
 */
class EquipoController extends Controller
{
    /**
     * Muestra todos los equipos en una vista
     * @return Response Devuelve la vista de todos los equipos
     *
     * @cond
     * @Route("/equipo", name="equipo_index")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_MANAGER')")
     * @endcond
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Equipo');
        $equipos = $repository->findAll();

        return $this->render('equipo/index.html.twig', [
            'equipos' => $equipos,
        ]);
    }

    /**
     *  Crea un nuevo equipo
     * @param Request $request Datos del nuevo equipo
     * @return RedirectResponse Redirecciona a la vista de los todos los equipos si el formulario fue enviado
     * @return Response Devuelve la vista de formulario para crear el nuevo equipo
     *
     * @cond
     * @Route("/equipo/crear", name="equipo_create")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Method({"GET", "POST"})
     * @endcond
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
     * Muestra los datos del equipo seleccionado
     * @param Equipo $equipo El equipo seleccionado
     * @return Response Devuelve la vista de los datos del equipo seleccionado
     *
     * @cond
     * @Route("/equipo/{id}", name="equipo_show")
     * @Security("is_granted('ROLE_MANAGER')")
     * @Method("GET")
     * @ParamConverter("equipo", options={"mapping": {"id" : "id"}})
     * @endcond
     */
    public function showAction(Equipo $equipo)
    {
        return $this->render('equipo/show.html.twig', ['equipo' => $equipo]);
    }

    /**
     * Edita un equipo existente
     * @param Equipo $equipo El equipo seleccionado
     * @param Request $request Los datos editados del equipo
     * @return RedirectResponse Redirecciona a la vista de todos los equipos si el formulario fue enviado
     * @return Response Devuelve la vista de formulario de edición de un equipo
     *
     * @cond
     * @Route("/equipo/{id}/editar", name="equipo_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     * @ParamConverter("equipo", options={"mapping": {"id" : "id"}})
     * @endcond
     */
    public function editAction(Equipo $equipo, Request $request)
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