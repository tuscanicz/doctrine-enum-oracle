<?php

namespace DoctrineEnumOracle;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
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
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        if (!isset($fieldDeclaration['length'])) {
            $fieldDeclaration['length'] = 20;
        }

        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }
}
