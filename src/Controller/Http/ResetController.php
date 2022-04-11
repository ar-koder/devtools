<?php

declare(strict_types=1);

namespace App\Controller\Http;

use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResetController extends AbstractController
{
    #[Route('/.symfony-known/db-reset', name: 'app.reset_db', priority: 0)]
    public function index(KernelInterface $kernel): Response
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'schedule:run'
        ]);

        $output = new BufferedOutput(
            OutputInterface::VERBOSITY_NORMAL,
            true
        );
        $application->run($input, $output);

        $converter = new AnsiToHtmlConverter();
        $content = $output->fetch();

        return new Response("<style>body{background-color: black}</style><pre>".$converter->convert($content)."</pre>");
    }
}
