<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Dto\Bin;
use App\Manager\BinManager;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class CurrentBinEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private BinManager $binManager, private string $baseHost)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (! $event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $currentHost = $request->getHttpHost();
        if ($this->baseHost === $currentHost && ! str_starts_with($request->getPathInfo(), '/b/')) {
            return;
        }

        $binId = str_replace('.'.$this->baseHost, '', $currentHost);
        if ($binId === $this->baseHost) {
            $binId = current(explode('/', str_replace('/b/', '', $request->getPathInfo())));
        }

        if (empty($binId)) {
            return;
        }

        $this->binManager->setCurrentBin(new Bin($binId));
        $request->attributes->set('_bin', $this->binManager->getCurrentBin());
        $request->attributes->set('_bin_id', (string) $this->binManager->getCurrentBin());
    }
    /**
     * @return array<string, mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 250]];
    }
}
