<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Http\BinController;
use Doctrine\DBAL\Exception;
use App\Dto\Bin;
use App\Manager\BinManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class InspectController extends AbstractDashboardController
{
    public function __construct(
        private BinManager $binManager
    ) {
    }

    #[Route(
        '/inspect',
        condition: '!request.attributes.has("_bin")'
    )]
    public function index(): Response
    {
        throw new NotFoundHttpException();
    }

    /**
     * @throws Exception
     */
    /**
     * @throws Exception
     */
    #[Route(
        '/inspect/{bin}',
        name: 'inspect',
        condition: '!request.attributes.has("_bin")'
    )]
    public function inspect(string $bin): Response
    {
        $this->binManager->setCurrentBin(new Bin($bin));

        return $this->render('inspect/list.html.twig', [
            'bin' => $this->binManager->getCurrentBin(),
            'manager' => $this->binManager,
        ]);
    }

    /**
     * @throws Exception
     */
    /**
     * @throws Exception
     */
    #[Route(
        '/inspect/{bin}/usage',
        name: 'inspect.usage',
        condition: '!request.attributes.has("_bin")'
    )]
    public function usage(string $bin): Response
    {
        $this->binManager->setCurrentBin(new Bin($bin));

        return $this->render('inspect/usage.html.twig', [
            'bin' => $this->binManager->getCurrentBin(),
            'manager' => $this->binManager,
        ]);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->displayUserAvatar(false)
            ->displayUserName(false)
            ;
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Buckets')
            ;
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addWebpackEncoreEntry('inspect')
            ->addJsFile('//cdn.jsdelivr.net/gh/pgrabovets/json-view@master/dist/jsonview.js')
            ->addJsFile('//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.5.1/highlight.min.js')
            ->addCssFile('//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.5.1/styles/github-dark-dimmed.min.css')
            ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl(label: 'Requests', icon: 'fa fa-list', url: $this->generateUrl('inspect', ['bin' => (string) $this->binManager->getCurrentBin()]));
        yield MenuItem::linkToUrl(label: 'How to use', icon: 'fa fa-life-ring', url: $this->generateUrl('inspect.usage', ['bin' => (string) $this->binManager->getCurrentBin()]));
        yield MenuItem::section();

        $request = Request::createFromGlobals();
        yield MenuItem::linkToUrl(label: 'Back to website', icon: 'fa fa-reply', url: sprintf('%s://%s', $request->getScheme(), BinController::getBaseHost($request)));
        yield MenuItem::linkToUrl(label: 'GitHub', icon: 'fab fa-github', url: 'https://github.com/arnaud-ritti/devtools');
    }
}
