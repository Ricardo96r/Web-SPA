<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Tipo;
use AppBundle\Form\TipoType;
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
class TipoController extends Controller
{
    /**
     * Muestra la vista de todos los tipos de servicio
     * @return Response Devuelve la vista de todos los tipos de servicio
     *
     * @cond
     * @Route("/tipo", name="tipo_index")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_MANAGER')")
     * @cond
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Tipo');
        $tipos = $repository->findAll();

        return $this->render('tipo/index.html.twig', [
            'tipos' => $tipos,
        ]);
    }

    /**
     *  Crea un nuevo tipo de servicio
     * @param Request $request Los datos del nuevo tipo de servicio
     * @return RedirectResponse Redirecciona a la vista de todos los tipos de servicio si el formulario es enviado con éxito
     * @return Response Devuelve el formulario de creacion de un tipo de servicio
     *
     * @cond
     * @Route("/tipo/crear", name="tipo_create")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Method({"GET", "POST"})
     * @endcond
     */
    public function createAction(Request $request)
    {
        $tipo = new Tipo();
        $form = $this->createForm(TipoType::class, $tipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tipo);
            $entityManager->flush();

            $this->addFlash('success', 'El tipo '. $tipo->getNombre() .' se ha creado');

            return $this->redirectToRoute('tipo_index');
        }

        return $this->render('tipo/create.html.twig', [
            'tipo' => $tipo,
            'form' => $form->createView(),
        ]);
    }


    /**
     * Muestra los datos del tipo de servicio seleccionado
     * @param Tipo $tipo El tipo de servicio seleccionado
     * @return Response Devuelve la vista de los datos del tipo de servicio seleccionado
     *
     * @cond
     * @Route("/tipo/{id}", name="tipo_show")
     * @Security("is_granted('ROLE_MANAGER')")
     * @Method("GET")
     * @ParamConverter("tipo", options={"mapping": {"id" : "id"}})
     * @endcond
     */
    public function showAction(Tipo $tipo)
    {
        return $this->render('tipo/show.html.twig', ['tipo' => $tipo]);
    }

    /**
     * Edita un tipo de servicio
     * @param Tipo $tipo El tipo de servicio seleccionado
     * @param Request $request Los datos editados del tipo de servicio seleccionado
     * @return RedirectResponse Redirecciona a la vista de todos los servicios si el formulario es enviado con éxito
     * @return Response Devuelve el formulario de editar un tipo de servicio
     *
     * @cond
     * @Route("/tipo/{id}/editar", name="tipo_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     * @ParamConverter("tipo", options={"mapping": {"id" : "id"}})
     * @endcond
     */
    public function editAction(Tipo $tipo, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(TipoType::class, $tipo);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Se ha editado correctamente el tipo');

            return $this->redirectToRoute('tipo_edit', ['id' => $tipo->getId()]);
        }

        return $this->render('tipo/edit.html.twig', [
            'tipo'        => $tipo,
            'edit_form'   => $editForm->createView(),
        ]);
    }

}