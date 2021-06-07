<?php

namespace App\Entity;

use App\Repository\CommentCollectRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentCollectRepository::class)
 */
class CommentCollect
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commentCollects")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $user;

	/**
	 * @ORM\Column(type="text")
	 */
	private $content;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $publication_date;

	/**
	 * @ORM\ManyToOne(targetEntity=Collect::class, inversedBy="commentCollects")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $collect;

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

	public function getCollect(): ?Collect
	{
		return $this->collect;
	}

	public function setCollect(?Collect $collect): self
	{
		$this->collect = $collect;

		return $this;
	}
}
