<?php

namespace App\Entity;

use App\Repository\FilmRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FilmRepository::class)
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
	private $tmdb_id;

	/**
	 * @ORM\Column(type="string", length=200)
	 */
	private $name;

	/**
	 * @ORM\Column(type="datetime")
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
	 * @ORM\OneToMany(targetEntity=CommentFilm::class, mappedBy="film")
	 */
	private $commentFilms;

	/**
	 * @ORM\ManyToMany(targetEntity=Collect::class, mappedBy="film_collect")
	 */
	private $collects;

	public function __construct()
	{
		$this->commentFilms = new ArrayCollection();
		$this->collects = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getTmdbId(): ?string
	{
		return $this->tmdb_id;
	}

	public function setTmdbId(string $tmdb_id): self
	{
		$this->tmdb_id = $tmdb_id;

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

	public function getYear(): ?\DateTimeInterface
	{
		return $this->year;
	}

	public function setYear(\DateTimeInterface $year): self
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
	 * @return Collection|CommentFilm[]
	 */
	public function getCommentFilms(): Collection
	{
		return $this->commentFilms;
	}

	public function addCommentFilm(CommentFilm $commentFilm): self
	{
		if ( !$this->commentFilms->contains($commentFilm) )
		{
			$this->commentFilms[] = $commentFilm;
			$commentFilm->setFilm($this);
		}

		return $this;
	}

	public function removeCommentFilm(CommentFilm $commentFilm): self
	{
		if ( $this->commentFilms->removeElement($commentFilm) )
		{
			// set the owning side to null (unless already changed)
			if ( $commentFilm->getFilm() === $this )
			{
				$commentFilm->setFilm(null);
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
}
