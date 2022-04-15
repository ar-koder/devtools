<?php

declare(strict_types=1);

namespace App\Components\Bin;

use App\Dto\Bin;
use App\Manager\BinManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('bin_requests')]
class Requests
{
    use DefaultActionTrait;

    #[LiveProp]
    public Bin $bin;

    public function __construct(private BinManager $binManager)
    {
    }

    public function __invoke(): void
    {
        $this->binManager->setCurrentBin($this->bin);
    }

    /**
     * @throws Exception
     */
    public function getRequests(): ArrayCollection
    {
        return $this->binManager->getRequests();
    }
}
