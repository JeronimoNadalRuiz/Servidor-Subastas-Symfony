<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lotes
 *
 * @ORM\Table(name="lotes", indexes={@ORM\Index(name="fk_lotes_subastas", columns={"subasta_id"})})
 * @ORM\Entity
 */
class Lote implements \JsonSerializable
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
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=50, nullable=false)
     */
    private $titulo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descripcion", type="text", length=65535, nullable=true)
     */
    private $descripcion;

    /**
     * @var \Subastas
     *
     * @ORM\ManyToOne(targetEntity="Subasta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="subasta_id", referencedColumnName="id")
     * })
     */
    private $subasta;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

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


    public function jsonSerialize()
    {
        return [
            'id'=>$this->getId(),
            'titulo'=>$this->getTitulo(),
            'descripcion'=>$this->getDescripcion()/*,
            'subasta'=>$this->getSubasta()*/
        ];
    }
}
