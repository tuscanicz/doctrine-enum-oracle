<?php

namespace DoctrineEnumOracle;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Enum\AbstractEnum;
use ReflectionClass;

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
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }
        $enumClassName = $this->getEnumClassName();

        return new $enumClassName($value);
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
        $fieldName = $fieldDeclaration['name'];
        $fieldValues = $this->getEnumValues();
        if (count($fieldValues) === 0) {
            throw new \Exception('Specified Enum is empty');
        }
        if (!isset($fieldDeclaration['length'])) {
            $fieldDeclaration['length'] = 20;
        }
        $declaration = $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);

        return sprintf(
            "%s CHECK (%s IN (%s))",
            $declaration,
            $fieldName,
            "'" . implode("', '", $this->getEnumValues()) . "'"
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        $namingStrategy = new UnderscoreNamingStrategy();

        return $namingStrategy->propertyToColumnName((new ReflectionClass($this))->getShortName());
    }

    public function getEnumValues()
    {
        $enumClassName = $this->getEnumClassName();

        return $enumClassName::getValues();
    }

    /**
     * @return AbstractEnum
     */
    abstract public function getEnumClassName();
}
