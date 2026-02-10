<?php

namespace App\Service;

use App\Entity\Translation;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Manages persistence of entity translations.
 */
class TranslationManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * Updates an existing translation or creates a new one.
     *
     * @param TranslatableInterface $object the translatable entity
     * @param string                $locale the target locale for the translation
     * @param string                $field  the entity field being translated
     * @param string                $value  the translated content
     * @param bool                  $flush  whether to flush changes to the database
     */
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
