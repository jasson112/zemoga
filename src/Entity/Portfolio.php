<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * Portfolio
 *
 * @ORM\Table(name="`portfolio`")
 * @ORM\Entity(repositoryClass="App\Repository\PortfolioRepository")
 * @ApiResource(formats={"json"})
 */
class Portfolio
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="idportfolio")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $idportfolio;

    /**
     * @var string
     *
     * @ORM\Column(name="`description`", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="`image_url`", type="string", length=255, nullable=true)
     */
    private $imageUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="`twitter_user_name`", type="string", length=255, nullable=true)
     */
    private $twitterUserName;

    /**
     * @var string
     *
     * @ORM\Column(name="`title`", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="`imag_url`", type="string", length=255, nullable=true)
     */
    private $imagUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="`name`", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="`lastname`", type="string", length=255, nullable=true)
     */
    private $lastname;

    public function getIdportfolio(): ?int
    {
        return $this->idportfolio;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getTwitterUserName(): ?string
    {
        return $this->twitterUserName;
    }

    public function setTwitterUserName(?string $twitterUserName): self
    {
        $this->twitterUserName = $twitterUserName;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getImagUrl(): ?string
    {
        return $this->imagUrl;
    }

    public function setImagUrl(?string $imagUrl): self
    {
        $this->imagUrl = $imagUrl;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }
}
