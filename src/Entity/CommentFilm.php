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
	private $film_note;

	/**
	 * @ORM\Column(type="text")
	 */
	private $content;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $publication_date;

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
		return $this->film_note;
	}

	public function setFilmNote(int $film_note): self
	{
		$this->film_note = $film_note;

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
		return $this->publication_date;
	}

	public function setPublicationDate(\DateTimeInterface $publication_date): self
	{
		$this->publication_date = $publication_date;

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
