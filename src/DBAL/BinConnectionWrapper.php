<?php

declare(strict_types=1);

namespace App\DBAL;

use App\Dto\Bin;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\SchemaException;

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
        if (!$exist) {
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
        $requestsTable->addColumn('method', 'string')->setNotnull(true);
        $requestsTable->addColumn('origins', 'json')->setNotnull(false);
        $requestsTable->addColumn('content_type', 'string')->setNotnull(false);
        $requestsTable->addColumn('content_length', 'string')->setNotnull(false);
        $requestsTable->addColumn('host', 'text')->setNotnull(false);
        $requestsTable->addColumn('path', 'text')->setNotnull(false);
        $requestsTable->addColumn('query_args', 'json')->setNotnull(false);
        $requestsTable->addColumn('headers', 'json')->setNotnull(false);
        $requestsTable->addColumn('raw_body', 'text')->setNotnull(false);
        $requestsTable->addColumn('body', 'json')->setNotnull(false);
        $requestsTable->addColumn('date', 'datetime')->setDefault('CURRENT_TIMESTAMP');

        $schemaDiff = (new Comparator())->compareSchemas($fromSchema, $toSchema);
        foreach ($schemaDiff->toSql($this->getDatabasePlatform()) as $stmt) {
            $this->executeStatement($stmt);
        }
    }
}
