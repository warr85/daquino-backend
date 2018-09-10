<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Uds0403
 *
 * @ORM\Table(name="uds0403", uniqueConstraints={@ORM\UniqueConstraint(name="uds0403_uq", columns={"iduds003", "iduds004"})}, indexes={@ORM\Index(name="IDX_BDF415A9DF55FCF7", columns={"iduds003"}), @ORM\Index(name="IDX_BDF415A941316954", columns={"iduds004"}), @ORM\Index(name="IDX_BDF415A9AF3F0878", columns={"iduds006"})})
 * @ORM\Entity
 */
class Uds0403
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="uds0403_id_seq", allocationSize=1, initialValue=1)
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
     * @var \AppBundle\Entity\Uds004
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Uds004")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="iduds004", referencedColumnName="id")
     * })
     */
    private $iduds004;

    /**
     * @var \AppBundle\Entity\Uds003
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Uds003")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="iduds003", referencedColumnName="id")
     * })
     */
    private $iduds003;



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
     * @return Uds0403
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
     * Set iduds004
     *
     * @param \AppBundle\Entity\Uds004 $iduds004
     *
     * @return Uds0403
     */
    public function setIduds004(\AppBundle\Entity\Uds004 $iduds004 = null)
    {
        $this->iduds004 = $iduds004;

        return $this;
    }

    /**
     * Get iduds004
     *
     * @return \AppBundle\Entity\Uds004
     */
    public function getIduds004()
    {
        return $this->iduds004;
    }

    /**
     * Set iduds003
     *
     * @param \AppBundle\Entity\Uds003 $iduds003
     *
     * @return Uds0403
     */
    public function setIduds003(\AppBundle\Entity\Uds003 $iduds003 = null)
    {
        $this->iduds003 = $iduds003;

        return $this;
    }

    /**
     * Get iduds003
     *
     * @return \AppBundle\Entity\Uds003
     */
    public function getIduds003()
    {
        return $this->iduds003;
    }
}
