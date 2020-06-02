<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pujas
 *
 * @ORM\Table(name="pujas", indexes={@ORM\Index(name="fk_pujas_user", columns={"user_id"}), @ORM\Index(name="fk_pujas_subasta", columns={"subasta_id"})})
 * @ORM\Entity
 */
class Puja implements \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="puja", type="integer", nullable=false)
     */
    private $puja;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdAt = 'CURRENT_TIMESTAMP';

    /**
     * @var \Subastas
     *
     * @ORM\ManyToOne(targetEntity="Subasta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="subasta_id", referencedColumnName="id")
     * })
     */
    private $subasta;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPuja(): ?int
    {
        return $this->puja;
    }

    public function setPuja(int $puja): self
    {
        $this->puja = $puja;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSubasta(): ?Subasta
    {
        return $this->subasta;
    }

    public function setSubasta(?Subasta $subasta): self
    {
        $this->subasta = $subasta;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }


    public function jsonSerialize()
    {
        return [
            'id'=>$this->getId(),
            'puja'=>$this->getPuja(),
            'createdAt'=>$this->getCreatedAt()/*,
            'subasta'=>$this->getSubasta()*/
        ];
    }
}
