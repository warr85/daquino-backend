<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Uds050403
 *
 * @ORM\Table(name="uds050403", uniqueConstraints={@ORM\UniqueConstraint(name="uds050403_uq", columns={"iduds0403", "iduds005"})}, indexes={@ORM\Index(name="IDX_B78249062F047C7", columns={"iduds0403"}), @ORM\Index(name="IDX_B782490363659C2", columns={"iduds005"}), @ORM\Index(name="IDX_B782490AF3F0878", columns={"iduds006"})})
 * @ORM\Entity
 */
class Uds050403
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="uds050403_id_seq", allocationSize=1, initialValue=1)
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
     * @var \AppBundle\Entity\Uds005
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Uds005")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="iduds005", referencedColumnName="id")
     * })
     */
    private $iduds005;

    /**
     * @var \AppBundle\Entity\Uds0403
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Uds0403")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="iduds0403", referencedColumnName="id")
     * })
     */
    private $iduds0403;



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
     * @return Uds050403
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

    /**
     * Set iduds005
     *
     * @param \AppBundle\Entity\Uds005 $iduds005
     *
     * @return Uds050403
     */
    public function setIduds005(\AppBundle\Entity\Uds005 $iduds005 = null)
    {
        $this->iduds005 = $iduds005;

        return $this;
    }

    /**
     * Get iduds005
     *
     * @return \AppBundle\Entity\Uds005
     */
    public function getIduds005()
    {
        return $this->iduds005;
    }

    /**
     * Set iduds0403
     *
     * @param \AppBundle\Entity\Uds0403 $iduds0403
     *
     * @return Uds050403
     */
    public function setIduds0403(\AppBundle\Entity\Uds0403 $iduds0403 = null)
    {
        $this->iduds0403 = $iduds0403;

        return $this;
    }

    /**
     * Get iduds0403
     *
     * @return \AppBundle\Entity\Uds0403
     */
    public function getIduds0403()
    {
        return $this->iduds0403;
    }
}
