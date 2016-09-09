<?php

namespace DoctrineEnumOracle;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Enum\AbstractEnum;

abstract class AbstractEnumType extends Type
{
    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof AbstractEnum) {
            return parent::convertToDatabaseValue($value->getValue(), $platform);
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $fieldName = $this->getName();
        $fieldValues = $this->getEnumValues();
        if (count($fieldValues) === 0) {
            throw new \Exception('Specified Enum is empty');
        }
        if (!isset($fieldDeclaration['length'])) {
            $fieldDeclaration['length'] = 20;
        }
        $declaration = $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);

        return sprintf(
            "%s CHECK ('%s' in ('%s'))'",
            $declaration,
            $fieldName,
            "'" . implode("', '", $this->getEnumValues()) . "''"
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        $namingStrategy = new UnderscoreNamingStrategy();

        return $namingStrategy->propertyToColumnName(get_class($this));
    }

    /**
     * @return string[]
     */
    abstract public function getEnumValues();
}
