<?php

namespace AppBundle\Controller;

use AppBundle\Form\CalendarioFiltroType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @IgnoreAnnotation({"cond", "endcond"})
 */
class CalendarioController extends Controller
{
    /**
     * Muestra la vista de calendario con su filtro por especialista
     * @param Request $request Formulario de filtro
     * @return Response Devuelve la vista de calendario
     *
     * @cond
     * @Route("/calendario", name="calendario_index")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_ESPECIALISTA')")
     * @endcond
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(CalendarioFiltroType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository = $this->getDoctrine()->getRepository('AppBundle:Agenda');
            $agendas = $repository->createQueryBuilder('p')
                ->where('p.especialista = :especialista')
                ->setParameter('especialista', $form->getData()['especialista']->getId())
                ->getQuery()
                ->getResult();
        } else {
            if ($this->isGranted('ROLE_MANAGER')) {
                $repository = $this->getDoctrine()->getRepository('AppBundle:User');
                $especialista = $repository->createQueryBuilder('u')
                    ->where('u.roles = :roleManager')
                    ->andWhere('u.activo = true')
                    ->setParameter('roleManager', '["ROLE_ESPECIALISTA"]')
                    ->setMaxResults(1)->getQuery()->getOneOrNullResult();
                $repository = $this->getDoctrine()->getRepository('AppBundle:Agenda');
                $agendas = $repository->createQueryBuilder('p')
                    ->where('p.especialista = :especialista')
                    ->setParameter('especialista', $especialista->getId())
                    ->getQuery()
                    ->getResult();
            } else {
                $repository = $this->getDoctrine()->getRepository('AppBundle:Agenda');
                $agendas = $repository->createQueryBuilder('p')
                    ->where('p.especialista = :especialista')
                    ->setParameter('especialista', $this->getUser()->getId())
                    ->getQuery()
                    ->getResult();
            }
        }

        return $this->render('calendario.html.twig', [
            'agendas' => $agendas,
            'form' => $form->createView()
        ]);
    }
}