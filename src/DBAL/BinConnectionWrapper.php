<?php

declare(strict_types=1);

namespace App\DBAL;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\SchemaException;
use App\Dto\Bin;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Schema\Comparator;

class BinConnectionWrapper extends Connection
{
    private string $bucketDir;

    public function __construct(
        array $params,
        Driver $driver,
        ?Configuration $config = null,
        ?EventManager $eventManager = null
    ) {
        parent::__construct($params, $driver, $config, $eventManager);
    }

    public function setBucketDir(string $bucketDir): void
    {
        $this->bucketDir = $bucketDir;
    }

    /**
     * @throws Exception
     */
    public function selectBin(Bin $bin): void
    {
        if ($this->isConnected()) {
            $this->close();
        }
        $params = array_merge($this->getParams(), [
            'defaultTableOptions' => [],
            'path' => sprintf('%s/%s.db', $this->bucketDir, (string) $bin),
            'charset' => 'utf8',
        ]);

        parent::__construct($params, $this->_driver, $this->_config, $this->_eventManager);

        $exist = file_exists($params['path']);
        if (! $exist) {
            touch($params['path']);
            touch($params['path'].'.lock');
            $this->generateBinSchema();
        }
    }

    public function dropBin(Bin $bin): void
    {
        $this->selectBin($bin);
        $params = $this->getParams();
        if (file_exists($params['path'])) {
            unlink($params['path']);
            unlink($params['path'].'.lock');
        }
    }

    /**
     * @throws SchemaException
     * @throws Exception
     */
    private function generateBinSchema(): void
    {
        $sm = $this->createSchemaManager();
        $fromSchema = $sm->createSchema();
        $toSchema = clone $fromSchema;
        $requestsTable = $toSchema->createTable('requests');
        $requestsTable->addColumn('id', 'string')->setNotnull(true);
        $requestsTable->addColumn('method', 'string');
        $requestsTable->addColumn('origins', 'json');
        $requestsTable->addColumn('content_type', 'string');
        $requestsTable->addColumn('content_length', 'string');
        $requestsTable->addColumn('host', 'text');
        $requestsTable->addColumn('path', 'text');
        $requestsTable->addColumn('query_args', 'json');
        $requestsTable->addColumn('headers', 'json');
        $requestsTable->addColumn('raw_body', 'text');
        $requestsTable->addColumn('body', 'json');
        $requestsTable->addColumn('date', 'datetime')->setDefault('CURRENT_TIMESTAMP');

        $schemaDiff = (new Comparator())->compareSchemas($fromSchema, $toSchema);
        foreach ($schemaDiff->toSql($this->getDatabasePlatform()) as $stmt) {
            $this->executeStatement($stmt);
        }
    }
}
