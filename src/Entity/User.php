<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="Cette adresse email est déjà utilisée")
 * @UniqueEntity(fields={"nickname"}, message="Ce nom d'utilisateur est déjà utilisée")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=180, unique=true)
	 */
	private $email;

	/**
	 * @ORM\Column(type="json")
	 */
	private $roles = [];

	/**
	 * @var string The hashed password
	 * @ORM\Column(type="string")
	 */
	private $password;

	/**
	 * @ORM\Column(type="string", length=50)
	 */
	private $nickname;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $registrationDate;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $avatar;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $active;

	/**
	 * @ORM\OneToMany(targetEntity=Film::class, mappedBy="user")
	 */
	private $films;

	/**
	 * @ORM\OneToMany(targetEntity=Collect::class, mappedBy="author")
	 */
	private $collects;

	/**
	 * @ORM\OneToMany(targetEntity=CommentCollect::class, mappedBy="user")
	 */
	private $commentCollects;

	public function __construct()
	{
		$this->films = new ArrayCollection();
		$this->collects = new ArrayCollection();
		$this->commentCollects = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
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
		return (string)$this->email;
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

	/**
	 * Returning a salt is only needed, if you are not using a modern
	 * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
	 *
	 * @see UserInterface
	 */
	public function getSalt(): ?string
	{
		return null;
	}

	/**
	 * @see UserInterface
	 */
	public function eraseCredentials()
	{
		// If you store any temporary, sensitive data on the user, clear it here
		// $this->plainPassword = null;
	}

	/**
	 * correction du bug Security bundle
	 */
	public function getUsername(): string
	{
		return (string)$this->getUserIdentifier();
	}

	public function getNickname(): ?string
	{
		return $this->nickname;
	}

	public function setNickname(string $nickname): self
	{
		$this->nickname = $nickname;

		return $this;
	}

	public function getRegistrationDate(): ?\DateTimeInterface
	{
		return $this->registrationDate;
	}

	public function setRegistrationDate(\DateTimeInterface $registrationDate): self
	{
		$this->registrationDate = $registrationDate;

		return $this;
	}

	public function getAvatar(): ?string
	{
		return $this->avatar;
	}

	public function setAvatar(?string $avatar): self
	{
		$this->avatar = $avatar;

		return $this;
	}

	public function getActive(): ?bool
	{
		return $this->active;
	}

	public function setActive(bool $active): self
	{
		$this->active = $active;

		return $this;
	}

	/**
	 * @return Collection|Film[]
	 */
	public function getFilms(): Collection
	{
		return $this->films;
	}

	public function addFilm(Film $film): self
	{
		if ( !$this->films->contains($film) )
		{
			$this->films[] = $film;
			$film->setUser($this);
		}

		return $this;
	}

	public function removeFilm(Film $film): self
	{
		if ( $this->films->removeElement($film) )
		{
			// set the owning side to null (unless already changed)
			if ( $film->getUser() === $this )
			{
				$film->setUser(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection|Collect[]
	 */
	public function getCollects(): Collection
	{
		return $this->collects;
	}

	public function addCollect(Collect $collect): self
	{
		if ( !$this->collects->contains($collect) )
		{
			$this->collects[] = $collect;
			$collect->setAuthor($this);
		}

		return $this;
	}

	public function removeCollect(Collect $collect): self
	{
		if ( $this->collects->removeElement($collect) )
		{
			// set the owning side to null (unless already changed)
			if ( $collect->getAuthor() === $this )
			{
				$collect->setAuthor(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection|CommentCollect[]
	 */
	public function getCommentCollects(): Collection
	{
		return $this->commentCollects;
	}

	public function addCommentCollect(CommentCollect $commentCollect): self
	{
		if ( !$this->commentCollects->contains($commentCollect) )
		{
			$this->commentCollects[] = $commentCollect;
			$commentCollect->setUser($this);
		}

		return $this;
	}

	public function removeCommentCollect(CommentCollect $commentCollect): self
	{
		if ( $this->commentCollects->removeElement($commentCollect) )
		{
			// set the owning side to null (unless already changed)
			if ( $commentCollect->getUser() === $this )
			{
				$commentCollect->setUser(null);
			}
		}

		return $this;
	}
}
