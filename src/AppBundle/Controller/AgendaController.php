<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Agenda;
use AppBundle\Entity\Sesion;
use AppBundle\Form\AgendaType;
use AppBundle\Form\SesionType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AgendaController extends Controller
{
    /**
     * @Route("/agenda/{id}/crear", name="agenda_create")
     * @Security("is_granted('ROLE_MANAGER')")
     * @Method({"GET", "POST"})
     * @ParamConverter("sesion", options={"mapping": {"id" : "id"}})
     */
    public function agendarAction(Sesion $sesion, Request $request)
    {
        $agenda = new Agenda();
        $form = $this->createForm(AgendaType::class, $agenda);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $agenda->setSesion($sesion);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($agenda);
            $entityManager->flush();

            $this->addFlash('success', 'La sesion se ha agendado correctamente');

            return $this->redirectToRoute('sesion_show', ["id" => $sesion->getId()]);
        }

        return $this->render('agenda/create.html.twig', [
            'sesion' => $sesion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edita un agenda existente
     *
     * @Route("/agenda/{id}/editar", name="agenda_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_MANAGER')")
     * @ParamConverter("agenda", options={"mapping": {"id" : "id"}})
     */
    public function editAction(Agenda $agenda, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(AgendaType::class, $agenda);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Se ha editado correctamente la agenda');

            return $this->redirectToRoute('sesion_show', ['id' => $agenda->getId()]);
        }

        return $this->render('agenda/edit.html.twig', [
            'agenda'        => $agenda,
            'edit_form'   => $editForm->createView(),
        ]);
    }

}