<?php

namespace App\Entity;

use App\Repository\CollectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CollectRepository::class)
 */
class Collect
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=150)
	 */
	private $name;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $publicationDate;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="collects")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $author;

	/**
	 * @ORM\OneToMany(targetEntity=CommentCollect::class, mappedBy="collect")
	 */
	private $commentCollects;

	/**
	 * @ORM\ManyToMany(targetEntity=Film::class, inversedBy="collects")
	 */
	private $filmCollect;

	public function __construct()
	{
		$this->commentCollects = new ArrayCollection();
		$this->filmCollect = new ArrayCollection();
	}

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

	public function getPublicationDate(): ?\DateTimeInterface
	{
		return $this->publicationDate;
	}

	public function setPublicationDate(\DateTimeInterface $publicationDate): self
	{
		$this->publicationDate = $publicationDate;

		return $this;
	}

	public function getAuthor(): ?User
	{
		return $this->author;
	}

	public function setAuthor(?User $author): self
	{
		$this->author = $author;

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
			$commentCollect->setCollect($this);
		}

		return $this;
	}

	public function removeCommentCollect(CommentCollect $commentCollect): self
	{
		if ( $this->commentCollects->removeElement($commentCollect) )
		{
			// set the owning side to null (unless already changed)
			if ( $commentCollect->getCollect() === $this )
			{
				$commentCollect->setCollect(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection|Film[]
	 */
	public function getFilmCollect(): Collection
	{
		return $this->filmCollect;
	}

	public function addFilmCollect(Film $filmCollect): self
	{
		if ( !$this->filmCollect->contains($filmCollect) )
		{
			$this->filmCollect[] = $filmCollect;
		}

		return $this;
	}

	public function removeFilmCollect(Film $filmCollect): self
	{
		$this->filmCollect->removeElement($filmCollect);

		return $this;
	}
}
