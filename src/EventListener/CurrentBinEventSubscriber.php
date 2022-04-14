<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Dto\Bin;
use App\Manager\BinManager;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Uid\Uuid;

class CurrentBinEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private BinManager $binManager, private string $bucketMode)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (! $event->isMainRequest()) {
            return;
        }
        $request = $event->getRequest();
        $pathInfo = explode(".", $request->getHttpHost(), 2);
        $subdomain = current($pathInfo);
        if(Uuid::isValid($subdomain)){
            if (in_array($this->bucketMode, ["both", "subdomain"])){
                $this->setBin($event, $subdomain);
            }else{
                throw new NotFoundHttpException();
            }
        }elseif (str_starts_with($request->getPathInfo(), '/b/') && in_array($this->bucketMode, ["both", "path"])) {
            $binId = current(explode('/', str_replace('/b/', '', $request->getPathInfo())));
            if($binId){
                $this->setBin($event, $binId);
            }
        }
    }

    private function setBin(RequestEvent $event, string $binId){
        $this->binManager->setCurrentBin(new Bin($binId));
        $event->getRequest()->attributes->set('_bin', $this->binManager->getCurrentBin());
        $event->getRequest()->attributes->set('_bin_id', (string) $this->binManager->getCurrentBin());
    }

    /**
     * @return array<string, mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 250]];
    }
}
