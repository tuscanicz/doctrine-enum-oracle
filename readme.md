# Doctrine Enum Oracle

An abstract class defining a new Doctrine type for Enum data type in Oracle.

Has dependency on ``kstefan/enum`` and Doctrine 2 `doctrine/orm`.

## How to use

Prepare a new Enum using `kstefan/enum`:

```php
<?php

namespace MyApp\Enum;

use Enum\AbstractEnum;

class MyExampleEnum extends AbstractEnum
{
    const MY_EXAMPLE_ENUM_FIRST = 'first';
    const MY_EXAMPLE_ENUM_SECOND = 'second';
    const MY_EXAMPLE_ENUM_THIRD = 'third';
}

```

Create a new Type `MyExampleType` into `MyApp\Component\Doctrine\Type` namespace and extending the `AbstractEnumType`:

```php
<?php

namespace MyApp\Component\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use DoctrineEnumOracle\AbstractEnumType;
use MyApp\Enum\MyExampleEnum;

class MyExampleType extends AbstractEnumType
{
    public function getEnumClassName()
    {
        return MyExampleEnum::class;
    }
}
```

### How to use it with Symfony

Configure a new type (config.yml):

    doctrine:
        dbal:
            types:
                MyExampleType: 'MyApp\Component\Doctrine\Type\MyExampleType'


### How to use it with Zend Framework

Configure a new type (config.php):

    'doctrine' => [
        'configuration' => [
            'orm_default' => [
                'types' => [
                    'my_example_type' => 'MyApp\Component\Doctrine\Type\MyExampleType',
                ]
                ...
            ]
            ...
        ]
        ...
    ]

### That's all!
Now you can try to generate a new migration or schema diff.