<?php

namespace App\DataFixtures;

use App\Entity\Review;
use App\Enum\Language;
use App\Enum\ReviewSentiment;
use App\Enum\ReviewStar;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ReviewFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $reviews = [
            // HU - Hungarian
            new ReviewDTO(
                Language::Hungarian,
                'Nagyszerű termék, nagyon elégedett alebo s minőséggel.',
                ReviewStar::Five,
                ReviewSentiment::Positive
            ),
            new ReviewDTO(
                Language::Hungarian,
                'A szállítás gyors volt, a termék pedig pontosan olyan, mint a leírásban.',
                ReviewStar::Five,
                ReviewSentiment::Positive
            ),
            new ReviewDTO(
                Language::Hungarian,
                'Rendben van, de a csomagolás kicsit sérült volt.',
                ReviewStar::Three,
                ReviewSentiment::Positive
            ),
            new ReviewDTO(
                Language::Hungarian,
                'Nem rossz, de láttam már jobbat is ebben az árkategóriában.',
                ReviewStar::Three,
                ReviewSentiment::Negative
            ),
            new ReviewDTO(
                Language::Hungarian,
                'Sajnos nem működik úgy, ahogy vártam. Csalódott vagyok.',
                ReviewStar::One,
                ReviewSentiment::Negative
            ),

            // CZ - Czech
            new ReviewDTO(
                Language::Czech,
                'Naprostá spokojenost, produkt dorazil v pořádku a funguje skvěle.',
                ReviewStar::Five,
                ReviewSentiment::Positive
            ),
            new ReviewDTO(
                Language::Czech,
                'Velmi kvalitní zpracování, doporučuji všem.',
                ReviewStar::Five,
                ReviewSentiment::Positive
            ),
            new ReviewDTO(
                Language::Czech,
                'Průměrný produkt, splnil očekávání, ale ničím nepřekvapil.',
                ReviewStar::Three,
                ReviewSentiment::Positive
            ),
            new ReviewDTO(
                Language::Czech,
                'Dodání trvalo déle, než bylo slíbeno.',
                ReviewStar::Two,
                ReviewSentiment::Negative
            ),
            new ReviewDTO(
                Language::Czech,
                'Nefunguje po dvou dnech používání. Budu reklamovat.',
                ReviewStar::One,
                ReviewSentiment::Negative
            ),

            // RO - Romanian
            new ReviewDTO(
                Language::Romanian,
                'Un produs excelent, raport calitate-preț foarte bun.',
                ReviewStar::Five,
                ReviewSentiment::Positive
            ),
            new ReviewDTO(
                Language::Romanian,
                'Sunt foarte mulțumit de achiziție, recomand cu încredere.',
                ReviewStar::Five,
                ReviewSentiment::Positive
            ),
            new ReviewDTO(
                Language::Romanian,
                'Este ok, dar materialul pare puțin fragil.',
                ReviewStar::Three,
                ReviewSentiment::Positive
            ),
            new ReviewDTO(
                Language::Romanian,
                'A ajuns destul de greu, dar produsul este bun.',
                ReviewStar::Four,
                ReviewSentiment::Positive
            ),
            new ReviewDTO(
                Language::Romanian,
                'Nu este ceea ce am comandat. Foarte dezamăgit.',
                ReviewStar::One,
                ReviewSentiment::Negative
            ),

            // HR - Croatian
            new ReviewDTO(
                Language::Croatian,
                'Izvrstan proizvod, nadmašio je moja očekivanja.',
                ReviewStar::Five,
                ReviewSentiment::Positive
            ),
            new ReviewDTO(
                Language::Croatian,
                'Brza dostava i odlična komunikacija s prodavačem.',
                ReviewStar::Five,
                ReviewSentiment::Positive
            ),
            new ReviewDTO(
                Language::Croatian,
                'Solidna kvaliteta za ovu cijenu.',
                ReviewStar::Four,
                ReviewSentiment::Positive
            ),
            new ReviewDTO(
                Language::Croatian,
                'Boja nije ista kao na slici.',
                ReviewStar::Two,
                ReviewSentiment::Negative
            ),
            new ReviewDTO(
                Language::Croatian,
                'Proizvod je stigao oštećen. Ne preporučujem.',
                ReviewStar::One,
                ReviewSentiment::Negative
            ),

            // SI - Slovenian
            new ReviewDTO(
                Language::Slovenian,
                'Zelo zadovoljen z nakupom, vse deluje brezhibno.',
                ReviewStar::Five,
                ReviewSentiment::Positive
            ),
            new ReviewDTO(
                Language::Slovenian,
                'Izdelek je vrhunske kakovosti, zagotovo bom še naročil.',
                ReviewStar::Five,
                ReviewSentiment::Positive
            ),
            new ReviewDTO(
                Language::Slovenian,
                'Povprečna izkušnja, izdelek je v redu.',
                ReviewStar::Three,
                ReviewSentiment::Positive
            ),
            new ReviewDTO(
                Language::Slovenian,
                'Navodila niso bila v mojem jeziku, kar je precej moteče.',
                ReviewStar::Two,
                ReviewSentiment::Negative
            ),
            new ReviewDTO(
                Language::Slovenian,
                'Zelo slaba kakovost plastike, takoj se je zlomilo.',
                ReviewStar::One,
                ReviewSentiment::Negative
            ),

            // SK - Slovak
            new ReviewDTO(
                Language::Slovak,
                'Vynikajúci produkt, som maximálne spokojný s kvalitou aj doručením.',
                ReviewStar::Five,
                ReviewSentiment::Positive
            ),
            new ReviewDTO(
                Language::Slovak,
                'Skvelý pomer ceny a výkonu. Odporúčam každému.',
                ReviewStar::Five,
                ReviewSentiment::Positive
            ),
            new ReviewDTO(
                Language::Slovak,
                'Produkt je fajn, ale doprava trvala o dva dni dlhšie.',
                ReviewStar::Three,
                ReviewSentiment::Positive
            ),
            new ReviewDTO(
                Language::Slovak,
                'Návod nie je veľmi jasný, musel som hľadať informácie na internete.',
                ReviewStar::Two,
                ReviewSentiment::Negative
            ),
            new ReviewDTO(
                Language::Slovak,
                'Veľké sklamanie, výrobok prišiel nefunkčný.',
                ReviewStar::One,
                ReviewSentiment::Negative
            ),
        ];

        foreach ($reviews as $reviewDto) {
            $review = new Review();
            $review->setPrimaryLanguage($reviewDto->language);
            $review->setContent($reviewDto->content);
            $review->setStars($reviewDto->stars);
            $review->setSentiment($reviewDto->sentiment);
            $review->setProductId(101); // Default product ID for fixtures

            $manager->persist($review);
        }

        $manager->flush();
    }
}

/**
 * @internal
 */
readonly class ReviewDTO
{
    public function __construct(
        public Language $language,
        public string $content,
        public ReviewStar $stars,
        public ReviewSentiment $sentiment,
    ) {
    }
}
