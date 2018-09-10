<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Uds05040302
 *
 * @ORM\Table(name="uds05040302", uniqueConstraints={@ORM\UniqueConstraint(name="uds05040302_uq", columns={"iduds002", "iduds050403"})}, indexes={@ORM\Index(name="IDX_1B0A103FA852CC61", columns={"iduds002"}), @ORM\Index(name="IDX_1B0A103FAF3F0878", columns={"iduds006"}), @ORM\Index(name="IDX_1B0A103F75638D65", columns={"iduds050403"})})
 * @ORM\Entity
 */
class Uds05040302
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="uds05040302_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Uds050403
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Uds050403")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="iduds050403", referencedColumnName="id")
     * })
     */
    private $iduds050403;

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
     * @var \AppBundle\Entity\Uds002
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Uds002")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="iduds002", referencedColumnName="id")
     * })
     */
    private $iduds002;



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
     * Set iduds050403
     *
     * @param \AppBundle\Entity\Uds050403 $iduds050403
     *
     * @return Uds05040302
     */
    public function setIduds050403(\AppBundle\Entity\Uds050403 $iduds050403 = null)
    {
        $this->iduds050403 = $iduds050403;

        return $this;
    }

    /**
     * Get iduds050403
     *
     * @return \AppBundle\Entity\Uds050403
     */
    public function getIduds050403()
    {
        return $this->iduds050403;
    }

    /**
     * Set iduds006
     *
     * @param \AppBundle\Entity\Uds006 $iduds006
     *
     * @return Uds05040302
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
     * Set iduds002
     *
     * @param \AppBundle\Entity\Uds002 $iduds002
     *
     * @return Uds05040302
     */
    public function setIduds002(\AppBundle\Entity\Uds002 $iduds002 = null)
    {
        $this->iduds002 = $iduds002;

        return $this;
    }

    /**
     * Get iduds002
     *
     * @return \AppBundle\Entity\Uds002
     */
    public function getIduds002()
    {
        return $this->iduds002;
    }
}
