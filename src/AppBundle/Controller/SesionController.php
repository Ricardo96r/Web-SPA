<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Sesion;
use AppBundle\Form\SesionBuscarType;
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

class SesionController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_ESPECIALISTA')")
     */
    public function indexAction(Request $request)
    {
        if ($this->isGranted('ROLE_MANAGER')) {
            $repository = $this->getDoctrine()->getRepository('AppBundle:Sesion');
            $sesiones = $repository->findAll();
        } else {
            $repository = $this->getDoctrine()->getRepository('AppBundle:Sesion');
            $query = $repository->createQueryBuilder('p')
                ->innerJoin('p.agenda', 'c')
                ->where('c.especialista = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->addOrderBy('c.dia', 'ASC')
                ->addOrderBy('c.horaInicio', 'DESC')
                ->getQuery();
            $sesiones = $query->getResult();
        }

        $buscarForm = $this->createForm(SesionBuscarType::class, ['filtro' => 'todo']);
        $buscarForm->handleRequest($request);

        if ($buscarForm->isSubmitted() && $buscarForm->isValid()) {
            $sesiones = $this->filtrarSesiones($buscarForm->getData());
        }

        return $this->render('sesion/index.html.twig', [
            'sesiones' => $sesiones,
            'buscar_form' => $buscarForm->createView(),
        ]);
    }

    /**
     *  Crea un nuevo Usuario
     *
     * @Route("/sesion/crear", name="sesion_create")
     * @Security("is_granted('ROLE_MANAGER')")
     * @Method({"GET", "POST"})
     *
     */
    public function createAction(Request $request)
    {
        $sesion = new Sesion();
        $form = $this->createForm(SesionType::class, $sesion, ['role' => $this->getUser()->getRoles(), 'tipo' => 'crear']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sesion);
            $entityManager->flush();

            $this->addFlash('success', 'La sesion se ha creado con éxito');

            return $this->redirectToRoute('sesion_show', ['id' => $sesion->getId()]);
        }

        return $this->render('sesion/create.html.twig', [
            'sesion' => $sesion,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/sesion/{id}", name="sesion_show")
     * @Security("is_granted('ROLE_ESPECIALISTA')")
     * @Method("GET")
     * @ParamConverter("sesion", options={"mapping": {"id" : "id"}})
     */
    public function showAction(Sesion $sesion)
    {
        return $this->render('sesion/show.html.twig', ['sesion' => $sesion]);
    }

    /**
     * Edita un sesion existente
     *
     * @Route("/sesion/{id}/editar", name="sesion_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_ESPECIALISTA')")
     * @ParamConverter("sesion", options={"mapping": {"id" : "id"}})
     */
    public function editAction(Sesion $sesion, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(SesionType::class, $sesion, ['role' => $this->getUser()->getRoles(), 'agenda' => $sesion->getAgenda() != null]);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid() && $this->validacionEjecutadaSinAgendaSinCheckIn($sesion)) {
            $entityManager->flush();
            $this->addFlash('success', 'Se ha editado correctamente la sesion');
            return $this->redirectToRoute('sesion_show', ['id' => $sesion->getId()]);
        }

        return $this->render('sesion/edit.html.twig', [
            'sesion' => $sesion,
            'edit_form' => $editForm->createView(),
        ]);
    }

    /**
     * Edita un sesion existente
     *
     * @Route("/sesion/{id}/borrar", name="sesion_delete")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN')")
     * @ParamConverter("sesion", options={"mapping": {"id" : "id"}})
     */
    public function deleteAction(Sesion $sesion, Request $request)
    {
        if (!$sesion->getAgenda() && !$sesion->getCancelada()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($sesion);
            $entityManager->flush();
            $this->addFlash('success', 'Se ha borrado correctamente la sesion');
            return $this->redirectToRoute('homepage', ['id' => $sesion->getId()]);
        } else {
            $this->addFlash('info', 'No puedes borrar una sesion con agenda asignada');
            return $this->redirectToRoute('sesion_show', ['id' => $sesion->getId()]);
        }
    }

    private function validacionEjecutadaSinAgendaSinCheckIn(Sesion $sesion)
    {
        // validar si existe checkin y no tiene agenda, tambien si se hace checkin en un horario futuro
        if ($sesion->getCheckin() && $sesion->getAgenda() != null) {
            if ($sesion->getCheckin() && $sesion->getAgenda()->getDia() >= new DateTime(date('Y-m-d')) && $sesion->getAgenda()->getHoraInicio()->setDate(date('Y'), date('m'), date('d')) > new DateTime(date('H:i:s'))) {
                $this->addFlash('info', 'No se puede darle a la sesión el estatus de checkin, ya que posee una agenda con un horario futuro');
                return false;
            }
        } else if ($sesion->getCheckin() && $sesion->getAgenda() == null) {
            $this->addFlash('info', 'No se puede darle a la sesión el estatus de checkin si todavia no tiene agenda');
            return false;
        }

        if ($sesion->getEjecutada() && !$sesion->getCheckin()) {
            $this->addFlash('info', 'No se puede darle a la sesión el estatus de ejecutada si todavia no se ha hecho checkin');
            return false;
        }
        if ($sesion->getEjecutada() && $sesion->getAgenda() == null) {
            $this->addFlash('info', 'No se puede darle a la sesión el estatus de ejecutada si no tiene todavia una agenda asignada. (Este error jamas debio ocurrir)!! :(');
            return false;
        }
        return true;
    }

    private function filtrarSesiones(Array $data)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Sesion');

        $filtro = $data['filtro'];

        if ($filtro == "todo") {
            return $repository->findAll();

        } elseif ($filtro == 'ejecutadas') {
            return $repository->createQueryBuilder('p')
                ->where('p.ejecutada = :ejecutada')
                ->setParameter('ejecutada', true)
                ->andWhere('p.cancelada = :cancelada')
                ->setParameter('cancelada', false)
                ->getQuery()
                ->getResult();

        } elseif ($filtro == 'retrasadaa') {
            return $repository->createQueryBuilder('c')
                ->innerJoin('c.agenda', 'a')
                ->where('c.ejecutada = :ejecutada')
                ->setParameter('ejecutada', false)
                ->andWhere('a.dia <= :dia')
                ->setParameter('dia', date('Y-m-d'))
                ->andWhere('a.horaFinal <= :horaFinal')
                ->setParameter('horaFinal', date('H:i:s'))
                ->getQuery()
                ->getResult();

        } elseif ($filtro == 'sinAgendar') {
            return $repository->createQueryBuilder('p')
                ->leftJoin('p.agenda', 'c')
                ->where('c.id is NULL')
                ->getQuery()
                ->getResult();

        } elseif ($filtro == 'ejecutadasPagadas') {
            return $repository->createQueryBuilder('c')
                ->where('c.ejecutada = :ejecutada')
                ->setParameter('ejecutada', true)
                ->andWhere('c.cancelada = :cancelada')
                ->setParameter('cancelada', true)
                ->getQuery()
                ->getResult();
        }
    }

}