<?php

namespace Faker\ODM\Doctrine\MongoDB;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;

class ColumnTypeGuesser
{
    protected $generator;

    public function __construct(\Faker\Generator $generator)
    {
        $this->generator = $generator;
    }

    public function guessFormat($fieldName, ClassMetadata $class)
    {
        $generator = $this->generator;
        $type = $class->getTypeOfField($fieldName);
        switch ($type) {
            case 'bin_data_custom':
                return function() {
                    return new \MongoBinData($generator->text, \MongoBinData::CUSTOM);
                };
            case 'bin_data_func':
                return function() {
                    return new \MongoBinData($generator->text, \MongoBinData::FUNC);
                };
            case 'bin_data_md5':
                return function() {
                    return new \MongoBinData($generator->text, \MongoBinData::MD5);
                };
            case 'bin_data':
                return function() {
                    return new \MongoBinData($generator->text, \MongoBinData::BYTE_ARRAY);
                };
            case 'bin_data_uuid':
                return function() {
                    return new \MongoBinData($generator->text, \MongoBinData::UUID);
                };
            case 'boolean':
                return function() use ($generator) {
                    return $generator->boolean;
                };
            case 'date':
                return function() use ($generator) {
                    return new \MongoDate($generator->unixTime);
                };
            case 'file':
                return function() {
                    return new \MongoGridfsFile();
                } ;
            case 'float':
                return function() {
                    return mt_rand(0, intval('4294967295')) / mt_rand(1, intval('4294967295'));
                };
            case 'hash':
                return function() {
                    $len = mt_rand(0, intval('64'));
                    $array = array();

                    for ($i = 0; $i < $len; ++$i) {
                        $value = mt_rand(0, intval('4294967295'));
                        $array[] = ($value % 2 === 0) ? hash('md5', $value) : $value;
                    }

                    return $array;
                };
            case 'id':
                return function() {
                    $id = mt_rand(1, intval('4294967295'));
                    return new \MongoId(hash('md5', $id));
                };
            case 'int':
                return function() {
                    return mt_rand(0, intval('4294967295'));
                };
            case 'key':
                //TODO implementation
                return null;
            case 'string':
                return function() use ($generator) {
                    return $generator->text;
                };
            case 'timestamp':
                return function() use ($generator) {
                    return new \MongoTimestamp($generator->unixTime);
                };
            case 'increment':
                // TODO implementation
                return null;
            default:
                // no smart way to guess what the user expects here
                return null;
        }
    }
}