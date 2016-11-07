<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Agenda;
use AppBundle\Entity\Cliente;
use AppBundle\Entity\Equipo;
use AppBundle\Entity\Servicio;
use AppBundle\Entity\Sesion;
use AppBundle\Entity\Tipo;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the sample data to load in the database when running the unit and
 * functional tests. Execute this command to load the data:
 *
 *   $ php bin/console doctrine:fixtures:load
 *
 * See http://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html
 *
 */
class LoadFixtures implements FixtureInterface, ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadTipo($manager);
        $this->loadEquipo($manager);
        $this->loadCliente($manager);
        $this->loadServicio($manager);
        $this->loadSesion($manager);
        $this->loadAgenda($manager);
    }

    private function loadUsers(ObjectManager $manager)
    {
        $passwordEncoder = $this->container->get('security.password_encoder');
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 15; $i++) {
            $user = new User();
            $user->setUsername($faker->unique()->userName);
            $user->setEmail($faker->unique()->email);
            $user->setNombre($faker->firstName);
            $user->setApellido($faker->lastName);
            $user->setActivo($faker->boolean());
            $user->setRoles($faker->randomElements(['ROLE_ADMIN', 'ROLE_MANAGER', 'ROLE_ESPECIALISTA']));
            $user->setTelefono($faker->unique()->phoneNumber);
            $encodedPassword = $passwordEncoder->encodePassword($user, 'password');
            $user->setPassword($encodedPassword);
            $manager->persist($user);
            $manager->flush();
        }

        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@gmail.com');
        $user->setNombre('Christian');
        $user->setApellido('Guillén');
        $user->setActivo(true);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setTelefono($faker->unique()->phoneNumber);
        $encodedPassword = $passwordEncoder->encodePassword($user, 'password');
        $user->setPassword($encodedPassword);
        $manager->persist($user);
        $manager->flush();
    }

    private function loadTipo(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i <= 100; $i++) {
            $tipo = new Tipo();
            $tipo->setNombre($faker->unique()->word);
            $tipo->setDescripcion($faker->paragraph());
            $manager->persist($tipo);
            $manager->flush();
        }
    }

    private function loadEquipo(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i <= 100; $i++) {
            $equipo = new Equipo();
            $equipo->setNombre($faker->unique()->word);
            $equipo->setDescripcion($faker->paragraph());
            $manager->persist($equipo);
            $manager->flush();
        }
    }

    private function loadServicio(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();
        $repository = $manager->getRepository('AppBundle:Tipo');
        $tipos = $repository->findAll();
        $repository = $manager->getRepository('AppBundle:Equipo');
        $equipo = $repository->findAll();

        for ($i = 0; $i <= 10; $i++) {
            $servicio = new Servicio();
            $servicio->setNombre($faker->unique()->word);
            $servicio->setDescripcion($faker->paragraph());
            $servicio->setPrecio($faker->numberBetween(1000, 140000));
            $servicio->setDuracion($faker->numberBetween(5, 300));
            $servicio->setActivo($faker->boolean());
            $servicio->setTipo($tipos[$faker->numberBetween(0, 99)]);
            $servicio->addEquipo($equipo[$faker->numberBetween(0, 99)]);
            $manager->persist($servicio);
            $manager->flush();
        }
    }

    private function loadCliente(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i <= 50; $i++) {
            $cliente = new Cliente();
            $cliente->setNombre($faker->firstName);
            $cliente->setApellido($faker->lastName);
            $cliente->setCedula($faker->unique()->numberBetween(100000, 32000000));
            $cliente->setCelular($faker->unique()->phoneNumber);
            $cliente->setTelefono($faker->unique()->phoneNumber);
            $cliente->setEmail($faker->unique()->email);
            $manager->persist($cliente);
            $manager->flush();
        }
    }

    private function loadSesion(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();

        $repository = $manager->getRepository('AppBundle:User');
        $rolmanager = $repository->createQueryBuilder('p')
            ->where('p.roles = :rol')
            ->andWhere('p.activo = true')
            ->setParameter('rol', '["ROLE_MANAGER"]')
            ->getQuery()
            ->getResult();
        $repository = $manager->getRepository('AppBundle:Cliente');
        $cliente = $repository->findAll();
        $repository = $manager->getRepository('AppBundle:Servicio');
        $servicio = $repository->findAll();

        for ($i = 0; $i <= 100; $i++) {
            $sesion = new Sesion();
            $sesion->setCliente($cliente[$faker->numberBetween(0, 50)]);
            $sesion->setManager($faker->randomElement($rolmanager));
            $sesion->setServicio($servicio[$faker->numberBetween(0, 10)]);
            $sesion->setCancelada($faker->boolean());
            $sesion->setCheckin(false);
            $sesion->setEjecutada(false);

            $manager->persist($sesion);
            $manager->flush();
        }
    }

    private function loadAgenda(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();

        $repository = $manager->getRepository('AppBundle:User');
        $especialista = $repository->createQueryBuilder('p')
            ->where('p.roles = :rol')
            ->andWhere('p.activo = true')
            ->setParameter('rol', '["ROLE_ESPECIALISTA"]')
            ->getQuery()
            ->getResult();
        $repository = $manager->getRepository('AppBundle:Sesion');
        $sesiones = $repository->findAll();

        for ($i = 1; $i <= 50; $i++) {
            $agenda = new Agenda();
            $agenda->setEspecialista($faker->randomElement($especialista));
            $agenda->setSesion($sesiones[$faker->unique()->numberBetween(0, 99)]);
            $agenda->setDia($faker->dateTimeBetween('now', '3 days'));
            $contador = $i*30;
            $agenda->setHoraInicio($faker->dateTimeBetween(((string)$contador) . "minutes", ((string)$contador) . "minutes"));
            $agenda->setHoraFinal($faker->dateTimeBetween(((string)($contador + 25)) . "minutes", ((string)($contador + 25)) . "minutes"));
            $manager->persist($agenda);
            $manager->flush();
        }
    }


    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
