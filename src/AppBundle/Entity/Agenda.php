<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Agenda
 *
 * @ORM\Table(name="agenda")
 * @ORM\Entity
 */
class Agenda
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dia", type="date", nullable=false)
     */
    private $dia;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hora_inicio", type="time", nullable=false)
     */
    private $horaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hora_final", type="time", nullable=false)
     */
    private $horaFinal;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Sesion
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Sesion",  inversedBy="agenda")
     * @ORM\JoinColumn(name="sesion_id", referencedColumnName="id")
     */
    private $sesion;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="especialista_id", referencedColumnName="id")
     * })
     */
    private $especialista;



    /**
     * Set dia
     *
     * @param \DateTime $dia
     *
     * @return Agenda
     */
    public function setDia($dia)
    {
        $this->dia = $dia;

        return $this;
    }

    /**
     * Get dia
     *
     * @return \DateTime
     */
    public function getDia()
    {
        return $this->dia;
    }

    /**
     * Set horaInicio
     *
     * @param \DateTime $horaInicio
     *
     * @return Agenda
     */
    public function setHoraInicio($horaInicio)
    {
        $this->horaInicio = $horaInicio;

        return $this;
    }

    /**
     * Get horaInicio
     *
     * @return \DateTime
     */
    public function getHoraInicio()
    {
        return $this->horaInicio;
    }

    /**
     * Set horaFinal
     *
     * @param \DateTime $horaFinal
     *
     * @return Agenda
     */
    public function setHoraFinal($horaFinal)
    {
        $this->horaFinal = $horaFinal;

        return $this;
    }

    /**
     * Get horaFinal
     *
     * @return \DateTime
     */
    public function getHoraFinal()
    {
        return $this->horaFinal;
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
     * Set sesion
     *
     * @param \AppBundle\Entity\Sesion $sesion
     *
     * @return Agenda
     */
    public function setSesion(\AppBundle\Entity\Sesion $sesion = null)
    {
        $this->sesion = $sesion;

        return $this;
    }

    /**
     * Get sesion
     *
     * @return \AppBundle\Entity\Sesion
     */
    public function getSesion()
    {
        return $this->sesion;
    }

    /**
     * Set especialista
     *
     * @param \AppBundle\Entity\User $especialista
     *
     * @return Agenda
     */
    public function setEspecialista(\AppBundle\Entity\User $especialista = null)
    {
        $this->especialista = $especialista;

        return $this;
    }

    /**
     * Get especialista
     *
     * @return \AppBundle\Entity\User
     */
    public function getEspecialista()
    {
        return $this->especialista;
    }
}
