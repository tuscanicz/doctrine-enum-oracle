<?php

namespace DoctrineEnumOracle;

use Doctrine\DBAL\Platforms\OraclePlatform;
use DoctrineEnumOracle\Fixtures\Enum\TestEnum;
use PHPUnit_Framework_TestCase;

class AbstractEnumTypeTest extends PHPUnit_Framework_TestCase
{
    /** @var AbstractEnumType $stub */
    private $stub;

    public function setUp()
    {
        $stub = $this->getMockBuilder(AbstractEnumType::class)
            ->disableOriginalConstructor()
            ->setMockClassName('TestType')
            ->getMockForAbstractClass();

        $stub->expects($this->any())
            ->method('getEnumClassName')
            ->will(
                $this->returnValue(TestEnum::class)
            );

        $this->stub = $stub;
    }

    public function testEnumValues()
    {
        $this->assertCount(3, $this->stub->getEnumValues());
        $this->assertContains(TestEnum::TEST_ENUM_FIRST, $this->stub->getEnumValues());
        $this->assertContains(TestEnum::TEST_ENUM_SECOND, $this->stub->getEnumValues());
        $this->assertContains(TestEnum::TEST_ENUM_THIRD, $this->stub->getEnumValues());
        $this->assertNotContains('else', $this->stub->getEnumValues());
    }

    public function testOracleSqlDeclaration()
    {
        $oraclePlatform = new OraclePlatform();
        $fieldDeclaration = ['name' => 'TestColumnName'];

        $this->assertSame(
            "VARCHAR2(20) CHECK (TestColumnName IN ('first', 'second', 'third'))",
            $this->stub->getSQLDeclaration($fieldDeclaration, $oraclePlatform)
        );
        $this->assertEquals('test_type', $this->stub->getName());
    }

    public function testOracleSqlDeclarationWithLengthAndFixed()
    {
        $oraclePlatform = new OraclePlatform();
        $fieldDeclaration = ['name' => 'TestColumnName', 'length' => 100, 'fixed' => true];

        $this->assertSame(
            "CHAR(100) CHECK (TestColumnName IN ('first', 'second', 'third'))",
            $this->stub->getSQLDeclaration($fieldDeclaration, $oraclePlatform)
        );
        $this->assertEquals('test_type', $this->stub->getName());
    }

    public function testConvertToDatabase()
    {
        $oraclePlatform = new OraclePlatform();

        $this->assertEquals(
            'second',
            $this->stub->convertToDatabaseValue(
                new TestEnum(TestEnum::TEST_ENUM_SECOND),
                $oraclePlatform
            ),
            'Type must accept and not modify Enum values'
        );
        $this->setExpectedException('InvalidArgumentException');
        $this->stub->convertToDatabaseValue(
            new TestEnum('error'),
            $oraclePlatform
        );
    }

    public function testConvertToPhp()
    {
        $oraclePlatform = new OraclePlatform();

        $this->assertEquals(
            new TestEnum(TestEnum::TEST_ENUM_FIRST),
            $this->stub->convertToPHPValue(
                'first',
                $oraclePlatform
            )
        );
        $this->setExpectedException('InvalidArgumentException');
        $this->stub->convertToPHPValue(
            'error',
            $oraclePlatform
        );
    }
}
