<?php

namespace App\Tests\Unit\Service;

use App\Entity\Translation;
use App\Service\TranslatableInterface;
use App\Service\TranslationManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TranslationManagerTest extends TestCase
{
    private EntityManagerInterface&MockObject $entityManagerMock;
    private TranslationManager $translationManager;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->translationManager = new TranslationManager($this->entityManagerMock);
    }

    public function testUpsertCreatesNewTranslation(): void
    {
        $type = 'product';
        $id = 'uuid-123';
        $locale = 'sk';
        $field = 'content';
        $value = 'Preložený text';

        $translatableMock = $this->createMock(TranslatableInterface::class);
        $translatableMock->method('getTranslatableType')->willReturn($type);
        $translatableMock->method('getTranslatableId')->willReturn($id);

        $repositoryMock = $this->createMock(EntityRepository::class);
        $repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with([
                'objectType' => $type,
                'objectId' => $id,
                'locale' => $locale,
                'field' => $field,
            ])
            ->willReturn(null);

        $this->entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->with(Translation::class)
            ->willReturn($repositoryMock);

        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (Translation $translation) use ($type, $id, $locale, $field, $value) {
                return $translation->objectType === $type
                       && $translation->objectId === $id
                       && $translation->locale === $locale
                       && $translation->field === $field
                       && $translation->value === $value;
            }));

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $this->translationManager->upsert($translatableMock, $locale, $field, $value);
    }

    public function testUpsertUpdatesExistingTranslation(): void
    {
        $type = 'product';
        $id = 'uuid-123';
        $locale = 'sk';
        $field = 'content';
        $value = 'Nový text';

        $translatableMock = $this->createMock(TranslatableInterface::class);
        $translatableMock->method('getTranslatableType')->willReturn($type);
        $translatableMock->method('getTranslatableId')->willReturn($id);

        $existingTranslation = new Translation();
        $existingTranslation->objectType = $type;
        $existingTranslation->objectId = $id;
        $existingTranslation->locale = $locale;
        $existingTranslation->field = $field;
        $existingTranslation->value = 'Starý text';

        $repositoryMock = $this->createMock(EntityRepository::class);
        $repositoryMock->method('findOneBy')->willReturn($existingTranslation);

        $this->entityManagerMock->method('getRepository')->willReturn($repositoryMock);

        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($existingTranslation);

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $this->translationManager->upsert($translatableMock, $locale, $field, $value);

        $this->assertEquals($value, $existingTranslation->value);
    }

    public function testUpsertWithoutFlush(): void
    {
        $translatableMock = $this->createMock(TranslatableInterface::class);
        $translatableMock->method('getTranslatableType')->willReturn('type');
        $translatableMock->method('getTranslatableId')->willReturn('id');

        $repositoryMock = $this->createMock(EntityRepository::class);
        $this->entityManagerMock->method('getRepository')->willReturn($repositoryMock);

        $this->entityManagerMock->expects($this->once())->method('persist');
        $this->entityManagerMock->expects($this->never())->method('flush');

        $this->translationManager->upsert($translatableMock, 'sk', 'field', 'val', false);
    }
}
