<?php

namespace App\Entity;

use App\Repository\CreditRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CreditRepository::class)]
class Credit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\Column]
    private ?int $deadline_months = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_issued = null;

    #[ORM\ManyToOne(inversedBy: 'credits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'Credit', targetEntity: Payment::class, orphanRemoval: true)]
    private Collection $payments;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $finished_at = null;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDeadlineMonths(): ?int
    {
        return $this->deadline_months;
    }

    public function setDeadlineMonths(int $deadline_months): static
    {
        $this->deadline_months = $deadline_months;

        return $this;
    }

    public function getDateIssued(): ?\DateTimeInterface
    {
        return $this->date_issued;
    }

    public function setDateIssued(\DateTimeInterface $date_issued): static
    {
        $this->date_issued = $date_issued;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setCredit($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getCredit() === $this) {
                $payment->setCredit(null);
            }
        }

        return $this;
    }

    public function getAmountLeft(): int {
        return $this->amount - $this->payments->reduce(function(int $accumulator, Payment $payment): int {
            return $accumulator + $payment->getAmount();
        }, 0);

    }

    public function getDeadlineDate(): ?\DateTimeInterface {
        $date = clone $this->date_issued;
        $date->modify("+{$this->deadline_months} month");

        return $date;
    }

    public function getFinishedAt(): ?\DateTimeImmutable
    {
        return $this->finished_at;
    }

    public function setFinishedAt(?\DateTimeImmutable $finished_at): static
    {
        $this->finished_at = $finished_at;

        return $this;
    }

    public function isFinished(): bool {
        return !!$this->finished_at;
    }
}
