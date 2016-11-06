<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Agenda;
use AppBundle\Entity\Sesion;
use AppBundle\Form\AgendaBuscarType;
use AppBundle\Form\AgendaType;
use AppBundle\Form\SesionType;
use DateTime;
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
 *
 * @IgnoreAnnotation({"cond", "endcond"})
 */
class AgendaController extends Controller
{
    /**
     *
     *  Motrar todas las agendas
     *  @param Request $request Tipo de filtro a aplicar
     *  @return Response Muestra la pantalla de agenda
     *
     * @cond
     * @Route("/agenda", name="agenda_index")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_MANAGER')")
     * @endcond
     */
    public function indexAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Agenda');
        $sesiones = $repository->findAll();

        $buscarForm = $this->createForm(AgendaBuscarType::class, ['filtro' => 'todo']);
        $buscarForm->handleRequest($request);

        if ($buscarForm->isSubmitted() && $buscarForm->isValid()) {
            $sesiones = $this->filtrarAgendas($buscarForm->getData());
        }

        return $this->render('agenda/index.html.twig', [
            'agendas' => $sesiones,
            'buscar_form' => $buscarForm->createView(),
        ]);
    }

    /**
     * Agendar una sesión
     *
     * @param Sesion $sesion Sesión para agendar
     * @param Request $request Datos de la agenda
     * @return RedirectResponse Redirecciona a la pantalla de la sesión seleccionada si fue enviando con éxito el formulario
     * @return Response Muestra el formulario de la pantalla de agendar sesión en caso de fracaso
     *
     * @cond
     * @Route("/agenda/{id}/crear", name="agenda_create")
     * @Security("is_granted('ROLE_MANAGER')")
     * @Method({"GET", "POST"})
     * @ParamConverter("sesion", options={"mapping": {"id" : "id"}})
     * @endcond
     */
    public function agendarAction(Sesion $sesion, Request $request)
    {
        $agenda = new Agenda();
        $form = $this->createForm(AgendaType::class, $agenda);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->validarDiaHoraInicioHoraFinalEspecialista($agenda)) {
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
     * Editar la agenda de una sesión
     *
     * @param Agenda $agenda Agenda que se va a editar
     * @param Request $request Datos que se van a editar de la agenda
     * @return RedirectResponse Redirecciona a la pantalla de la sesión seleccionada
     * @return Response Muestra el formulario de editar agenda
     *
     * @cond
     * @Route("/agenda/{id}/editar", name="agenda_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_MANAGER')")
     * @ParamConverter("agenda", class="AppBundle:Agenda", options={"mapping": {"id" : "sesion"}})
     * @endcond
     */
    public function editAction(Agenda $agenda, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(AgendaType::class, $agenda);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid() && $this->validarDiaHoraInicioHoraFinalEspecialista($agenda)) {
            $entityManager->flush();

            $this->addFlash('success', 'Se ha editado correctamente la agenda');

            return $this->redirectToRoute('sesion_show', ['id' => $agenda->getSesion()->getId()]);
        }

        return $this->render('agenda/edit.html.twig', [
            'agenda' => $agenda,
            'edit_form' => $editForm->createView(),
        ]);
    }

    /**
     * Borrar la agenda de una sesión
     *
     * @param Agenda $agenda Agenda que se va a borrar
     * @param Request $request Respuesta de borrado.
     * @return RedirectResponse Redirecciona a la pantalla de la sesión seleccionada
     *
     * @cond
     * @Route("/agenda/{id}/borrar", name="agenda_delete")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN')")
     * @ParamConverter("agenda", class="AppBundle:Agenda", options={"mapping": {"id" : "sesion"}})
     * @endcond
     */
    public function deleteAction(Agenda $agenda, Request $request)
    {
        $id = $agenda->getId();
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($agenda);
        $entityManager->flush();
        $this->addFlash('success', 'Se ha borrado correctamente la agenda de la sesión');
        return $this->redirectToRoute('sesion_show', ['id' => $id]);
    }

    /**
     * Validar la hora de inicio con la hora final
     *
     * @param Agenda $agenda Agenda que se va a validar
     *
     * @return boolean Validado
     */
    private function validarHoraInicioFinal(Agenda $agenda)
    {
        if ($agenda->getHoraInicio() >= $agenda->getHoraFinal()) {
            $this->addFlash('info', 'La hora de inicio no puede ser despues de la hora final de sesion o igual');
            return false;
        }

        return true;
    }


    /**
     * Validar que un especialista no tenga dos sesiones al mismo tiempo
     *
     * @param Agenda $agenda Agenda que se va a validar
     * @return bool Validado
     */
    private function validarDiaHoraInicioHoraFinalEspecialista(Agenda $agenda)
    {

        if ($this->validarHoraInicioFinal($agenda)) {
            $repository = $this->getDoctrine()->getRepository('AppBundle:Agenda');

            $sesiones = $repository->createQueryBuilder('p')
                ->where('p.id != :idAgenda')
                ->setParameter('idAgenda', $agenda->getId())
                ->andWhere('p.especialista = :especialista')
                ->setParameter('especialista', $agenda->getEspecialista())
                ->andWhere('p.dia = :dia AND (p.horaInicio >= :horaInicio AND p.horaInicio <= :horaInicio OR p.horaFinal >= :horaFinal AND p.horaFinal <= :horaFinal)')
                ->setParameter('dia', $agenda->getDia()->format('Y-m-d'))
                ->setParameter('horaInicio', $agenda->getHoraInicio()->format('h-i-s'))
                ->setParameter('horaFinal', $agenda->getHoraFinal()->format('h-i-s'))
                ->getQuery()
                ->getResult();

            if ($sesiones != null) {
                $this->addFlash('info', 'Existe una sesión con el mismo especialista, donde el dia, la hora inicial y hora final chocan y se encuentran con otra sesion existente');
                return false;
            } else {
                $sesiones = $repository->createQueryBuilder('p')
                    ->where('p.id != :idAgenda')
                    ->setParameter('idAgenda', $agenda->getId())
                    ->andWhere('p.especialista = :especialista')
                    ->setParameter('especialista', $agenda->getEspecialista())
                    ->andWhere('p.dia = :dia AND (p.horaInicio <= :horaInicio OR p.horaFinal >= :horaFinal)')
                    ->setParameter('dia', $agenda->getDia()->format('Y-m-d'))
                    ->setParameter('horaInicio', $agenda->getHoraInicio()->format('h-i-s'))
                    ->setParameter('horaFinal', $agenda->getHoraFinal()->format('h-i-s'))
                    ->getQuery()
                    ->getResult();
                foreach ($sesiones as $sesion) {
                    if ($sesion->getHoraInicio() <= $agenda->getHoraInicio()) {
                        if ($sesion->getHoraFinal() >= $agenda->getHoraInicio()) {
                            $this->addFlash('info', 'Existe una sesión con el mismo especialista, donde el dia, la hora inicial y hora final chocan y se encuentran con otra sesion existente');
                            return false;
                        }
                    }
                    if ($sesion->getHoraInicio() >= $agenda->getHoraInicio()) {
                        if ($sesion->getHoraInicio() <= $agenda->getHoraFinal()) {
                            $this->addFlash('info', 'Existe una sesión con el mismo especialista, donde el dia, la hora inicial y hora final chocan y se encuentran con otra sesion existente');
                            return false;
                        }
                    }

                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * Filtra las agendas
     *
     * @param array $data Tipo de filtro
     * @return array Devuelve las agendas filtradas
     */
    private function filtrarAgendas(Array $data)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Agenda');

        $filtro = $data['filtro'];

        if ($filtro == "todo") {
            return $repository->findAll();

        } elseif ($filtro == 'agendasNoEjecutadas') {
            return $repository->createQueryBuilder('p')
                ->innerJoin('p.sesion', 's')
                ->where('s.ejecutada = :ejecutada')
                ->setParameter('ejecutada', false)
                ->getQuery()
                ->getResult();
        }
    }

}