<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Articulos
 *
 * @ORM\Table(name="articulos", indexes={@ORM\Index(name="fk_articulos_lotes", columns={"lote_id"})})
 * @ORM\Entity
 */
class Articulo implements \JsonSerializable
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
     * @var \Lotes
     *
     * @ORM\ManyToOne(targetEntity="Lote")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lote_id", referencedColumnName="id")
     * })
     */
    private $lote;

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

    public function getLote(): ?Lote
    {
        return $this->lote;
    }

    public function setLote(?Lote $lote): self
    {
        $this->lote = $lote;

        return $this;
    }


    public function jsonSerialize()
    {
        return [
            'id'=>$this->getId(),
            'titulo'=>$this->getTitulo(),
            'descripcion'=>$this->getDescripcion()/*,
            'lote'=>$this->getLote()*/
        ];
    }
}
