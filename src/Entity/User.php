<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="`user`")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="`name`", type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"quote"})
     */
    private $name;

    /**
     * @var string
     * @Groups({"quote"})
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(name="`lastname`", type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="`phone`", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="`address`", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="`nacionality`", type="string", length=255, nullable=true)
     */
    private $nacionality;

    /**
     * @var string
     *
     * @ORM\Column(name="`age`", type="string", length=255, nullable=true)
     */
    private $age;

    /**
     * @var boolean
     *
     * @ORM\Column(name="`news`", type="boolean")
     */
    private $news;

    /**
     * @var string
     *
     * @ORM\Column(name="`billing_address`", type="string", length=255)
     * @Assert\Length(
     *     min = 0,
     *     max = 100
     * )
     */
    private $billingAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="`city`", type="string", length=255)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="`postal`", type="string", length=255)
     */
    private $postal;

    /**
     * @var string
     *
     * @ORM\Column(name="`state`", type="string", length=255)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="`custom_email`", type="string", length=255)
     *
     */
    private $custom_email;

    /**
     * @var string
     *
     * @ORM\Column(name="additional", type="text", nullable=true)
     */
    private $additional;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getNacionality(): ?string
    {
        return $this->nacionality;
    }

    public function setNacionality(?string $nacionality): self
    {
        $this->nacionality = $nacionality;

        return $this;
    }

    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setAge(?string $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getNews(): ?bool
    {
        return $this->news;
    }

    public function setNews(bool $news): self
    {
        $this->news = $news;

        return $this;
    }

    public function getBillingAddress(): ?string
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(string $billingAddress): self
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPostal(): ?string
    {
        return $this->postal;
    }

    public function setPostal(string $postal): self
    {
        $this->postal = $postal;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getCustomEmail(): ?string
    {
        return $this->custom_email;
    }

    public function setCustomEmail(string $custom_email): self
    {
        $this->custom_email = $custom_email;

        return $this;
    }

    public function getAdditional(): ?string
    {
        return $this->additional;
    }

    public function setAdditional(?string $additional): self
    {
        $this->additional = $additional;

        return $this;
    }

    
}
