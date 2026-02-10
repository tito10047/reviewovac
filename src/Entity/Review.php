<?php

namespace App\Entity;

use App\Enum\Language;
use App\Enum\ReviewProblemTarget;
use App\Enum\ReviewSentiment;
use App\Enum\ReviewStar;
use App\Repository\ReviewRepository;
use App\Service\TranslatableInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use SymfonyCasts\ObjectTranslationBundle\Mapping\Translatable;
use SymfonyCasts\ObjectTranslationBundle\Mapping\TranslatableProperty;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[Translatable('product')]
class Review implements TranslatableInterface
{
    public const CONTENT_PROPERTY = 'content';
    public const TRANSLATABLE_TYPE = 'product';

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[TranslatableProperty]
    private ?string $content = null;

    #[ORM\Column(nullable: true, enumType: ReviewStar::class)]
    private ?ReviewStar $stars = null;

    #[ORM\Column]
    private ?int $productId = null;

    #[ORM\Column(length: 25, enumType: ReviewSentiment::class, nullable: true)]
    private ?ReviewSentiment $sentiment = null;

    #[ORM\Column(length: 25, nullable: true, enumType: ReviewProblemTarget::class)]
    private ?ReviewProblemTarget $primaryProblem = null;

    #[ORM\Column(length: 5, nullable: true, enumType: Language::class)]
    private ?Language $primaryLanguage = null;

    #[ORM\Column()]
    private bool $processed = false;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getStars(): ?ReviewStar
    {
        return $this->stars;
    }

    public function setStars(?ReviewStar $stars): static
    {
        $this->stars = $stars;

        return $this;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(?int $productId): static
    {
        $this->productId = $productId;

        return $this;
    }

    public function getSentiment(): ?ReviewSentiment
    {
        return $this->sentiment;
    }

    public function setSentiment(?ReviewSentiment $sentiment): static
    {
        $this->sentiment = $sentiment;

        return $this;
    }

    public function getPrimaryProblem(): ?ReviewProblemTarget
    {
        return $this->primaryProblem;
    }

    public function setPrimaryProblem(?ReviewProblemTarget $primaryProblem): static
    {
        $this->primaryProblem = $primaryProblem;

        return $this;
    }

    public function getPrimaryLanguage(): ?Language
    {
        return $this->primaryLanguage;
    }

    public function setPrimaryLanguage(?Language $primaryLanguage): static
    {
        $this->primaryLanguage = $primaryLanguage;

        return $this;
    }

    public function isProcessed(): bool
    {
        return $this->processed;
    }

    public function setProcessed(bool $processed): static
    {
        $this->processed = $processed;

        return $this;
    }

    public function getTranslatableType(): string
    {
        return self::TRANSLATABLE_TYPE;
    }

    public function getTranslatableId(): string
    {
        return (string) $this->id;
    }
}
