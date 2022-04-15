<?php

declare(strict_types=1);

namespace App\Dto;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;

class RequestBin
{
    public ?string $id = null;
    public string $method;
    public array $origins;
    public ?string $contentType = null;
    public ?string $contentLength = null;
    public string $host;
    public string $path;
    public array $queryArgs;
    public array $headers;
    public string $rawBody = '';
    public array $body = [];
    public ?DateTimeInterface $date = null;

    public static function createFromRequest(Request $request): RequestBin
    {
        $dto = new self();
        $dto->method = $request->getMethod();
        $dto->origins = $request->getClientIps();
        $dto->contentType = $request->headers->get('content-type');
        $dto->contentLength = $request->headers->get('content-length');
        $dto->host = $request->getSchemeAndHttpHost();
        $dto->path = $request->getPathInfo();
        $dto->queryArgs = $request->query->all();
        $dto->headers = array_map(static fn ($entry) => implode(', ', $entry), $request->headers->all());
        $dto->rawBody = $request->getContent();
        $dto->body = $request->request->all();

        return $dto;
    }

    /**
     * @throws Exception
     */
    public static function createFromArray(array $data): RequestBin
    {
        $dto = new self();
        $dto->id = $data['id'] ?? null;
        $dto->method = $data['method'] ?? null;
        $dto->origins = $data['origins'] ? unserialize($data['origins'], ['allowed_classes' => false]) : null;
        $dto->contentType = $data['content_type'] ?? null;
        $dto->contentLength = $data['content_length'] ?? null;
        $dto->host = $data['host'] ?? null;
        $dto->path = $data['path'] ?? null;
        $dto->queryArgs = $data['query_args'] ? unserialize($data['query_args'], ['allowed_classes' => false]) : null;
        $dto->headers = $data['headers'] ? unserialize($data['headers'], ['allowed_classes' => false]) : null;
        $dto->rawBody = $data['raw_body'] ?? null;
        $dto->body = $data['body'] ? unserialize($data['body'], ['allowed_classes' => false]) : null;
        $dto->date = $data['date'] ? new DateTimeImmutable($data['date']) : null;

        return $dto;
    }
}
