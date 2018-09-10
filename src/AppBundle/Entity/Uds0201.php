<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Uds0201
 *
 * @ORM\Table(name="uds0201", uniqueConstraints={@ORM\UniqueConstraint(name="uds0201_uq", columns={"iduds001", "iduds002"})}, indexes={@ORM\Index(name="IDX_57770837315B9DDB", columns={"iduds001"}), @ORM\Index(name="IDX_57770837A852CC61", columns={"iduds002"}), @ORM\Index(name="IDX_57770837AF3F0878", columns={"iduds006"})})
 * @ORM\Entity
 */
class Uds0201
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="uds0201_id_seq", allocationSize=1, initialValue=1)
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
     * @var \AppBundle\Entity\Uds002
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Uds002")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="iduds002", referencedColumnName="id")
     * })
     */
    private $iduds002;

    /**
     * @var \AppBundle\Entity\Uds001
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Uds001")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="iduds001", referencedColumnName="id")
     * })
     */
    private $iduds001;



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
     * @return Uds0201
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
     * @return Uds0201
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

    /**
     * Set iduds001
     *
     * @param \AppBundle\Entity\Uds001 $iduds001
     *
     * @return Uds0201
     */
    public function setIduds001(\AppBundle\Entity\Uds001 $iduds001 = null)
    {
        $this->iduds001 = $iduds001;

        return $this;
    }

    /**
     * Get iduds001
     *
     * @return \AppBundle\Entity\Uds001
     */
    public function getIduds001()
    {
        return $this->iduds001;
    }
}
