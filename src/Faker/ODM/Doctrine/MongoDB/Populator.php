<?php

namespace Faker\ODM\Doctrine\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Service class for populating a database using the Doctrine ODM.
 * A Populator can populate several tables using ActiveRecord classes.
 */
class Populator
{
    protected $generator;
    protected $manager;
    protected $documents = array();
    protected $quantities = array();

    public function __construct(\Faker\Generator $generator, ObjectManager $manager = null)
    {
        $this->generator = $generator;
        $this->manager = $manager;
    }

    /**
     * Add an order for the generation of $number records for $document.
     *
     * @param mixed $document A Doctrine classname, or a \Faker\ODM\Doctrine\DocumentPopulator instance
     * @param int $number The number of documents to populate
     */
    public function addDocument($document, $number, $customColumnFormatters = array())
    {
        if (!$document instanceof \Faker\ODM\Doctrine\MongoDB\DocumentPopulator) {
            $document = new \Faker\ODM\Doctrine\MongoDB\DocumentPopulator($this->manager->getClassMetadata($document));
        }
        $document->setColumnFormatters($document->guessColumnFormatters($this->generator));
        if ($customColumnFormatters) {
            $document->mergeColumnFormattersWith($customColumnFormatters);
        }
        $class = $document->getClass();
        $this->documents[$class] = $document;
        $this->quantities[$class] = $number;
    }

    /**
     * Populate the database using all the Document classes previously added.
     *
     * @param DocumentManager $documentManager
     *
     * @return array A list of the inserted documents
     */
    public function execute($documentManager = null)
    {
        if (null === $documentManager) {
            $documentManager = $this->manager;
        }
        if (null === $documentManager) {
            throw new \InvalidArgumentException("No document manager passed to Doctrine Populator.");
        }

        $insertedDocuments = array();
        foreach ($this->quantities as $class => $number) {
            for ($i = 0; $i < $number; $i++) {
                $insertedDocuments[$class][]= $this->documents[$class]->execute($documentManager, $insertedDocuments);
            }
        }
        $documentManager->flush();

        return $insertedDocuments;
    }

}
