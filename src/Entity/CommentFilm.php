<?php

namespace App\Entity;

use App\Repository\CommentFilmRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentFilmRepository::class)
 */
class CommentFilm
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commentFilms")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $user;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $filmNote;

	/**
	 * @ORM\Column(type="text")
	 */
	private $content;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $publicationDate;

	/**
	 * @ORM\ManyToOne(targetEntity=Film::class, inversedBy="commentFilms")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $film;

	public function getId(): ?int
	{
		return $this->id;
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

	public function getFilmNote(): ?int
	{
		return $this->filmNote;
	}

	public function setFilmNote(int $filmNote): self
	{
		$this->filmNote = $filmNote;

		return $this;
	}

	public function getContent(): ?string
	{
		return $this->content;
	}

	public function setContent(string $content): self
	{
		$this->content = $content;

		return $this;
	}

	public function getPublicationDate(): ?\DateTimeInterface
	{
		return $this->publicationDate;
	}

	public function setPublicationDate(\DateTimeInterface $publicationDate): self
	{
		$this->publicationDate = $publicationDate;

		return $this;
	}

	public function getFilm(): ?Film
	{
		return $this->film;
	}

	public function setFilm(?Film $film): self
	{
		$this->film = $film;

		return $this;
	}
}
