<?php

require __DIR__.'/vendor/autoload.php';

use Respect\Validation\Validator as RVal;

class ExampleTest extends PHPUnit_Framework_TestCase
{
    /**
     * This is the data were gonna work with in each test.
     * 
     * @var array
     */
    public $data = [
        'int'    => 1,
        'float'  => 1.0,
        'bool'   => true,
        'string' => 'foo',
        'array'  => [],
        'arrayOfInts' => [1, 2],
        'arrayOfArraysOfBools' => [[true]],
        'arrayOfAssociativeArrays' => [['foo' => 'a', 'bar' => 1]],
        'deeplyNestedString' => ['one' => ['two' => 'foo']]
    ];

    /**
     * Respect has tons on very fine grain validation. By default
     * this validator will fail is a key is missing, this can be
     * turned off by passing `false` to the `key` function as
     * the third parameter.
     */
    public function test_validates_with_respect()
    {
        RVal::create()
            ->key('int', RVal::intType())
            ->key('float', RVal::floatType())
            ->key('bool', RVal::boolType())
            ->key('string', RVal::stringType())
            ->key('array', RVal::arrayType())
            ->key('arrayOfInts', RVal::each(RVal::intType()))
            ->key('arrayOfArraysOfBools', RVal::each(RVal::each(RVal::boolType())))
            ->key('arrayOfAssociativeArrays', RVal::each(RVal::create()
                ->key('foo', RVal::stringType())
                ->key('bar', RVal::intType())
            ))
            ->keyNested('deeplyNestedString.one.two', RVal::stringType())
            ->assert($this->data);
    }

    /**
     * Illuminate doesn't verify a key is present unless you use
     * the `presence` rule.
     *
     * Checkout database 'exists' validation and error message translations
     * at https://github.com/mattstauffer/Torch/blob/master/components/validation/index.php
     * 
     * @return [type] [description]
     */
    public function test_validates_with_illuminate()
    {
        $this->factory = new Illuminate\Validation\Factory(
            new Symfony\Component\Translation\Translator('es')
        );

        $this->factory->validate($this->data, [
            'int'    => 'int',
            'float'  => 'int|numeric', // there is no float validator by default
            'bool'   => 'bool',
            'string' => 'string',
            'array'  => 'array',
            'arrayOfInts.*'  => 'int',
            'arrayOfArraysOfBools.*.*' => 'bool',
            'arrayOfAssociativeArrays.*.foo' => 'string',
            'arrayOfAssociativeArrays.*.bar' => 'int',
            'deeplyNestedString.one.two' => 'string'
        ]);
    }
}
