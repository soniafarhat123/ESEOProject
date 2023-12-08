<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity('email', message: "Cet email est déja utilisé")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Cette chaine est trop courte.Elle doit avoir au minimum  {{ limit }} caractères',
        maxMessage: 'Cette chaine est trop longue.Ele ne doit pas dépasser {{ limit }} caractères'
    )]
    #[Assert\NotBlank(message: "Veuillez renseigner ce champs")]
    private ?string $fullName = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Cette chaine est trop courte.Elle doit avoir au minimum  {{ limit }} caractères',
        maxMessage: 'Cette chaine est trop longue.Ele ne doit pas dépasser {{ limit }} caractères'
    )]
    private ?string $pseudo = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Image(mimeTypes : ['image/jpeg' , 'image/png' , 'image/tiff' , 'image/svg+xml'] )]
    private ?string $image = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Length(
        min: 2,
        max: 180,
        minMessage: 'Cette adresse mail est trop courte.Elle doit avoir au minimum  {{ limit }} caractères',
        maxMessage: 'Cette adresse mail est trop longue.Ele ne doit pas dépasser {{ limit }} caractères'
    )]
    #[Assert\Email(message: "euillez saisir une adresse email valid .'{{ value }}' n'est pas valide")]
    #[Assert\NotBlank(message: "Veuillez renseigner ce champs")]
    private ?string $email = null;

    #[ORM\Column]
    #[Assert\NotNull()]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: 'Veuillez renseigner ce champs')]
    private ?string $password = null;

    #[ORM\Column]
    #[Assert\NotNull()]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }


    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
