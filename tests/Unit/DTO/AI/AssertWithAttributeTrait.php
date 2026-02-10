<?php

namespace App\Tests\Unit\DTO\AI;

use ReflectionClass;
use Symfony\AI\Platform\Contract\JsonSchema\Attribute\With;

trait AssertWithAttributeTrait
{
    /**
     * @param class-string $dtoClass
     * @param class-string<\BackedEnum> $enumClass
     */
    protected function assertWithAttributeContainsAllEnumValues(string $dtoClass, string $propertyName, string $enumClass): void
    {
        $reflection = new ReflectionClass($dtoClass);
        $constructor = $reflection->getConstructor();
        if (!$constructor){
            $this->fail(sprintf('No constructor found in %s', $dtoClass));
        }
        $parameters = $constructor->getParameters();

        $targetParam = null;
        foreach ($parameters as $parameter) {
            if ($parameter->getName() === $propertyName) {
                $targetParam = $parameter;
                break;
            }
        }

        $this->assertNotNull($targetParam, sprintf('Parameter "%s" not found in %s constructor', $propertyName, $dtoClass));

        $attributes = $targetParam->getAttributes(With::class);
        $this->assertCount(1, $attributes, sprintf('Attribute #[With] not found on "%s" parameter in %s', $propertyName, $dtoClass));

        /** @var With $withAttribute */
        $withAttribute = $attributes[0]->newInstance();
        if (!$withAttribute->enum){
            $this->fail(sprintf('No enum values found in #[With] attribute on "%s" parameter in %s', $propertyName, $dtoClass));
        }

        $allowedValues = $withAttribute->enum;
        $expectedValues = array_map(fn($case) => $case->value, $enumClass::cases());

        sort($allowedValues);
        sort($expectedValues);

        $this->assertEquals(
            $expectedValues,
            $allowedValues,
            sprintf('%s::$%s attribute does not contain all %s values. Please update the #[With] attribute.', $dtoClass, $propertyName, $enumClass)
        );
    }
}
