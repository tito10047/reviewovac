<?php

namespace App\Service;

use App\Entity\Translation;
use Doctrine\ORM\EntityManagerInterface;

class TranslationManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function upsert(TranslatableInterface $object, string $locale, string $field, string $value, bool $flush = true): void
    {
        $type = $object->getTranslatableType();
        $id = $object->getTranslatableId();

        $repo = $this->em->getRepository(Translation::class);
        $translation = $repo->findOneBy([
            'objectType' => $type,
            'objectId' => $id,
            'locale' => $locale,
            'field' => $field,
        ]);

        if (!$translation) {
            $translation = new Translation();
            $translation->objectType = $type;
            $translation->objectId = $id;
            $translation->locale = $locale;
            $translation->field = $field;
        }

        $translation->value = $value;

        $this->em->persist($translation);
        if ($flush) {
            $this->em->flush();
        }
    }
}
