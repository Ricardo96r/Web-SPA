<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Usuario
 *
 * @ORM\Table(name="usuario")
 * @ORM\Entity
 */
class Usuario
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $usuario;

    /**
     * @ORM\Column(type="string")
     */
    private $contrasena;

    /**
     * @ORM\Column(type="json_array")
     */
    private $roles = [];

    /*
     * Obtener id de usuario
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Obtener usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /*
     * Cambiar usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    /**
     * Obtener contraseña
     */
    public function getContrasena()
    {
        return $this->contrasena;
    }

    /**
     * Cambiar contraseña
     */
    public function setContrasena($contrasena)
    {
        $this->contrasena = $contrasena;
    }

    /**
     * Obtener los roles de un usuario.
     * Si no posee por lo menos será Especialista
     */
    public function getRoles()
    {
        $roles = $this->roles;

        // guarantees that a user always has at least one role for security
        if (empty($roles)) {
            $roles[] = 'ROLE_ESPECIALISTA';
        }

        return array_unique($roles);
    }

    /*
     * Agregar rol
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }
    /**
     * @var string
     */
    private $rol;


    /**
     * Set rol
     *
     * @param string $rol
     *
     * @return Usuario
     */
    public function setRol($rol)
    {
        $this->rol = $rol;

        return $this;
    }

    /**
     * Get rol
     *
     * @return string
     */
    public function getRol()
    {
        return $this->rol;
    }
}
