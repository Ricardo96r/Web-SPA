<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cliente;
use AppBundle\Entity\User;
use AppBundle\Form\ClienteType;
use AppBundle\Form\ClienteBuscarType;
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
class ClienteController extends Controller
{
    /**
     * Muestra todos los clientes y una busqueda personalizada
     * @param Request $request Formulario de busqueda por cliente
     * @return Response Devuelve la vista de clientes
     *
     * @cond
     * @Route("/cliente", name="cliente_index")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_MANAGER')")
     * @endcond
     */
    public function indexAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Cliente');
        $clientes = $query = $repository->createQueryBuilder('p')->orderBy('p.cedula', 'ASC')->getQuery()->getResult();

        $form = $this->createForm(ClienteBuscarType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $request->request->all();
            $query = $repository->createQueryBuilder('p')
                ->where('p.nombre LIKE :nombre')
                ->andWhere('p.apellido LIKE :apellido')
                ->andWhere('p.cedula LIKE :cedula')
                ->andWhere('p.telefono LIKE :telefono')
                ->andWhere('p.celular LIKE :celular')
                ->andWhere('p.email LIKE :email')
                ->setParameter('nombre', '%'.$data['cliente_buscar']['nombre'].'%')
                ->setParameter('apellido', '%'.$data['cliente_buscar']['apellido'].'%')
                ->setParameter('cedula', '%'.$data['cliente_buscar']['cedula'].'%')
                ->setParameter('telefono', '%'.$data['cliente_buscar']['telefono'].'%')
                ->setParameter('celular', '%'.$data['cliente_buscar']['celular'].'%')
                ->setParameter('email', '%'.$data['cliente_buscar']['email'].'%')
                ->getQuery();
            $clientes = $query->getResult();
        }

        return $this->render('cliente/index.html.twig', [
            'clientes' => $clientes,
            'form_buscar' => $form->createView(),
        ]);
    }

    /**
     *  Crea un nuevo cliente
     * @param Request $request Datos del nuevo cliente
     * @return RedirectResponse Redirecciona a la vista de todos los clientes si el formulario es enviado
     * @return Response Muestra la vista de crear un nuevo cliente
     *
     * @cond
     * @Route("/cliente/crear", name="cliente_create")
     * @Security("is_granted('ROLE_MANAGER')")
     * @Method({"GET", "POST"})
     * @endcond
     *
     */
    public function createAction(Request $request)
    {
        $cliente = new Cliente();
        $form = $this->createForm(ClienteType::class, $cliente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cliente);
            $entityManager->flush();

            $this->addFlash('success', 'El cliente '. $cliente->getNombre() . ' '. $cliente->getApellido().' se ha creado');

            return $this->redirectToRoute('cliente_index');
        }

        return $this->render('cliente/create.html.twig', [
            'cliente' => $cliente,
            'form' => $form->createView(),
        ]);
    }


    /**
     * Muestra la vista de los datos de un cliente seleccionado
     * @param Cliente $cliente El cliente seleccionada
     * @return Response Devuelve la vista de los datos del cliente
     *
     * @cond
     * @Route("/cliente/{id}", name="cliente_show")
     * @Security("is_granted('ROLE_MANAGER')")
     * @Method("GET")
     * @ParamConverter("cliente", options={"mapping": {"id" : "id"}})
     * @endcond
     */
    public function showAction(Cliente $cliente)
    {
        return $this->render('cliente/show.html.twig', ['cliente' => $cliente]);
    }

    /**
     * Edita un cliente existente
     * @param Cliente $cliente El cliente seleccionado
     * @param Request $request Los datos a editar del cliente
     * @return RedirectResponse Redirecciona a la vista de los datos del cliente si se ha enviado el formulario
     * @return Response Devuelve la vista del formulario a editar del cliente
     *
     * @cond
     * @Route("/cliente/{id}/editar", name="cliente_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     * @ParamConverter("cliente", options={"mapping": {"id" : "id"}})
     * @endcond
     */
    public function editAction(Cliente $cliente, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(ClienteType::class, $cliente);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Se ha editado correctamente al cliente');

            return $this->redirectToRoute('cliente_show', ['id' => $cliente->getId()]);
        }

        return $this->render('cliente/edit.html.twig', [
            'cliente'        => $cliente,
            'edit_form'   => $editForm->createView(),
        ]);
    }

}
