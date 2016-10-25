<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Sesion;
use AppBundle\Form\SesionType;
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

        return $this->render('sesion/index.html.twig', [
            'sesiones' => $sesiones,
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
        dump($sesion);
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
        $editForm = $this->createForm(SesionType::class, $sesion, ['role' => $this->getUser()->getRoles()]);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Se ha editado correctamente la sesion');

            return $this->redirectToRoute('sesion_show', ['id' => $sesion->getId()]);
        }

        return $this->render('sesion/edit.html.twig', [
            'sesion'        => $sesion,
            'edit_form'   => $editForm->createView(),
        ]);
    }

}