<?php

namespace App\Entity;

use App\Repository\FilmRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=FilmRepository::class)
 * @UniqueEntity(fields={"tmdbId"}, message="Cet ID TMDB est déjà utilisé")
 */
class Film
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=15)
	 */
	private $tmdbId;

	/**
	 * @ORM\Column(type="string", length=200)
	 */
	private $name;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $year;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $genre;

	/**
	 * @ORM\Column(type="text")
	 */
	private $synopsis;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $poster;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $note;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="films")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $user;

	/**
	 * @ORM\ManyToMany(targetEntity=Collect::class, mappedBy="filmCollect")
	 */
	private $collects;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $trailer;

	/**
	 * @ORM\Column(type="string", length=255, unique=true)
	 * @Gedmo\Slug(fields={"name"})
	 */
	private $slug;

	public function __construct()
	{
		$this->collects = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getTmdbId(): ?string
	{
		return $this->tmdbId;
	}

	public function setTmdbId(string $tmdbId): self
	{
		$this->tmdbId = $tmdbId;

		return $this;
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

	public function getYear(): ?int
	{
		return $this->year;
	}

	public function setYear(int $year): self
	{
		$this->year = $year;

		return $this;
	}

	public function getGenre(): ?string
	{
		return $this->genre;
	}

	public function setGenre(string $genre): self
	{
		$this->genre = $genre;

		return $this;
	}

	public function getSynopsis(): ?string
	{
		return $this->synopsis;
	}

	public function setSynopsis(string $synopsis): self
	{
		$this->synopsis = $synopsis;

		return $this;
	}

	public function getPoster(): ?string
	{
		return $this->poster;
	}

	public function setPoster(?string $poster): self
	{
		$this->poster = $poster;

		return $this;
	}

	public function getNote(): ?int
	{
		return $this->note;
	}

	public function setNote(?int $note): self
	{
		$this->note = $note;

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
			$collect->addFilmCollect($this);
		}

		return $this;
	}

	public function removeCollect(Collect $collect): self
	{
		if ( $this->collects->removeElement($collect) )
		{
			$collect->removeFilmCollect($this);
		}

		return $this;
	}

	public function getTrailer(): ?string
	{
		return $this->trailer;
	}

	public function setTrailer(?string $trailer): self
	{
		$this->trailer = $trailer;

		return $this;
	}

	public function getSlug(): ?string
	{
		return $this->slug;
	}

	public function setSlug(string $slug): self
	{
		$this->slug = $slug;

		return $this;
	}
}
