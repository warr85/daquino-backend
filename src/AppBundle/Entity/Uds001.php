<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Uds001
 *
 * @ORM\Table(name="uds001", uniqueConstraints={@ORM\UniqueConstraint(name="uds001_uq", columns={"description"})}, indexes={@ORM\Index(name="IDX_55E63077AF3F0878", columns={"iduds006"})})
 * @ORM\Entity
 */
class Uds001
{
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=18, nullable=false)
     */
    private $description;


    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=18, nullable=false)
     */
    private $email;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="uds001_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Uds006
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Uds006")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="iduds006", referencedColumnName="id")
     * })
     */
    private $iduds006;



    /**
     * Set description
     *
     * @param string $description
     *
     * @return Uds001
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }


    /**
     * Set email
     *
     * @param string $email
     *
     * @return Uds001
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
     * Set iduds006
     *
     * @param \AppBundle\Entity\Uds006 $iduds006
     *
     * @return Uds001
     */
    public function setIduds006(\AppBundle\Entity\Uds006 $iduds006 = null)
    {
        $this->iduds006 = $iduds006;

        return $this;
    }

    /**
     * Get iduds006
     *
     * @return \AppBundle\Entity\Uds006
     */
    public function getIduds006()
    {
        return $this->iduds006;
    }
}
