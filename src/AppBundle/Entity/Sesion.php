<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Events;

/**
 * Sesion
 *
 * @ORM\Table(name="sesion")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Sesion
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creado", type="datetime", nullable=false)
     */
    private $creado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cambiado", type="datetime", nullable=false)
     */
    private $cambiado;

    /**
     * @var boolean
     *
     * @ORM\Column(name="checkin", type="boolean", nullable=false)
     */
    private $checkin = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="ejecutada", type="boolean", nullable=false)
     */
    private $ejecutada = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cancelada", type="boolean", nullable=false)
     */
    private $cancelada;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="manager_id", referencedColumnName="id")
     * })
     */
    private $manager;

    /**
     * @var \AppBundle\Entity\Servicio
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Servicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="servicio_id", referencedColumnName="id")
     * })
     */
    private $servicio;

    /**
     * @var \AppBundle\Entity\Cliente
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cliente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cliente_id", referencedColumnName="id")
     * })
     */
    private $cliente;

    /**
     * @var \AppBundle\Entity\Agenda
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Agenda", mappedBy="sesion")
     *
     */
    private $agenda;

    /** @ORM\PrePersist */
    public function timestampPrePersist()
    {
        $this->setCambiado(new \DateTime(date('Y-m-d H:i:s')));
        $this->setCreado(new \DateTime(date('Y-m-d H:i:s')));
    }

    /** @ORM\PreUpdate */
    public function timestampPreUpdate()
    {
        $this->setCambiado(new \DateTime(date('Y-m-d H:i:s')));
    }

    /**
     * Set creado
     *
     * @param \DateTime $creado
     *
     * @return Sesion
     */
    public function setCreado($creado)
    {
        $this->creado = $creado;

        return $this;
    }

    /**
     * Get creado
     *
     * @return \DateTime
     */
    public function getCreado()
    {
        return $this->creado;
    }

    /**
     * Set cambiado
     *
     * @param \DateTime $cambiado
     *
     * @return Sesion
     */
    public function setCambiado($cambiado)
    {
        $this->cambiado = $cambiado;

        return $this;
    }

    /**
     * Get cambiado
     *
     * @return \DateTime
     */
    public function getCambiado()
    {
        return $this->cambiado;
    }

    /**
     * Set checkin
     *
     * @param boolean $checkin
     *
     * @return Sesion
     */
    public function setCheckin($checkin)
    {
        $this->checkin = $checkin;

        return $this;
    }

    /**
     * Get checkin
     *
     * @return boolean
     */
    public function getCheckin()
    {
        return $this->checkin;
    }

    /**
     * Set ejecutada
     *
     * @param boolean $ejecutada
     *
     * @return Sesion
     */
    public function setEjecutada($ejecutada)
    {
        $this->ejecutada = $ejecutada;

        return $this;
    }

    /**
     * Get ejecutada
     *
     * @return boolean
     */
    public function getEjecutada()
    {
        return $this->ejecutada;
    }

    /**
     * Set cancelada
     *
     * @param boolean $cancelada
     *
     * @return Sesion
     */
    public function setCancelada($cancelada)
    {
        $this->cancelada = $cancelada;

        return $this;
    }

    /**
     * Get cancelada
     *
     * @return boolean
     */
    public function getCancelada()
    {
        return $this->cancelada;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set manager
     *
     * @param \AppBundle\Entity\User $manager
     *
     * @return Sesion
     */
    public function setManager(\AppBundle\Entity\User $manager = null)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get manager
     *
     * @return \AppBundle\Entity\User
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Set agenda
     *
     * @param \AppBundle\Entity\Agenda $agenda
     *
     * @return Sesion
     */
    public function setAgenda(\AppBundle\Entity\Agenda $agenda = null)
    {
        $this->agenda = $agenda;

        return $this;
    }

    /**
     * Get agenda
     *
     * @return \AppBundle\Entity\Agenda
     */
    public function getAgenda()
    {
        return $this->agenda;
    }

    /**
     * Set servicio
     *
     * @param \AppBundle\Entity\Servicio $servicio
     *
     * @return Sesion
     */
    public function setServicio(\AppBundle\Entity\Servicio $servicio = null)
    {
        $this->servicio = $servicio;

        return $this;
    }

    /**
     * Get servicio
     *
     * @return \AppBundle\Entity\Servicio
     */
    public function getServicio()
    {
        return $this->servicio;
    }

    /**
     * Set cliente
     *
     * @param \AppBundle\Entity\Cliente $cliente
     *
     * @return Sesion
     */
    public function setCliente(\AppBundle\Entity\Cliente $cliente = null)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return \AppBundle\Entity\Cliente
     */
    public function getCliente()
    {
        return $this->cliente;
    }
}
