<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass=StockRepository::class)
 */
class Stock
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({
     *      "api.stock.post",
     *      "api.stock.get"
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({
     *      "api.stock.post",
     *      "api.stock.get"
     * })
     */
    private $filename;

    /**
     * @var File|null
     */
    private $file;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({
     *      "api.stock.post",
     *      "api.stock.get"
     * })
     */
    private $date_upload;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({
     *      "api.stock.post",
     *      "api.stock.get"
     * })
     */
    private $averagePrice;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({
     *      "api.stock.post",
     *      "api.stock.get"
     * })
     */
    private $maxPrice;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({
     *      "api.stock.post",
     *      "api.stock.get"
     * })
     */
    private $minPrice;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({
     *      "api.stock.post",
     *      "api.stock.get"
     * })
     */
    private $nbCountry;

    /**
     * @ORM\OneToMany(targetEntity=Gift::class, mappedBy="stock")
     */
    private $gifts;

    public function __construct() {
        $this->date_upload = new \DateTime();
        $this->gifts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        if (null !== $file) {
            $this->date_upload = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getDateUpload(): ?\DateTimeInterface
    {
        return $this->date_upload;
    }

    public function setDateUpload(\DateTimeInterface $date_upload): self
    {
        $this->date_upload = $date_upload;

        return $this;
    }

    public function getAveragePrice(): ?float
    {
        return $this->averagePrice;
    }

    public function setAveragePrice(?float $averagePrice): self
    {
        $this->averagePrice = $averagePrice;

        return $this;
    }

    public function getMaxPrice(): ?float
    {
        return $this->maxPrice;
    }

    public function setMaxPrice(?float $maxPrice): self
    {
        $this->maxPrice = $maxPrice;

        return $this;
    }

    public function getMinPrice(): ?float
    {
        return $this->minPrice;
    }

    public function setMinPrice(?float $minPrice): self
    {
        $this->minPrice = $minPrice;

        return $this;
    }

    public function getNbCountry(): ?int
    {
        return $this->nbCountry;
    }

    public function setNbCountry(?int $nbCountry): self
    {
        $this->nbCountry = $nbCountry;

        return $this;
    }

    /**
     * @return Collection|Gift[]
     */
    public function getGifts(): Collection
    {
        return $this->gifts;
    }

    public function addGift(Gift $gift): self
    {
        if (!$this->gifts->contains($gift)) {
            $this->gifts[] = $gift;
            $gift->setStock($this);
        }

        return $this;
    }

    public function removeGift(Gift $gift): self
    {
        if ($this->gifts->removeElement($gift)) {
            // set the owning side to null (unless already changed)
            if ($gift->getStock() === $this) {
                $gift->setStock(null);
            }
        }

        return $this;
    }
}
