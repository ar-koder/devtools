<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Http\BinController;
use App\Entity\Album;
use App\Entity\Comment;
use App\Entity\Photo;
use App\Entity\Post;
use App\Entity\Todo;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\CrudMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\DashboardMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\SectionMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Iterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private EntityManagerInterface $manager)
    {
    }

    #[Route('/admin', name: 'admin', condition: '!request.attributes.has("_bin")')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'counts' => [
                'users' => $this->manager->getRepository(User::class)->count([]),
                'posts' => $this->manager->getRepository(Post::class)->count([]),
                'comments' => $this->manager->getRepository(Comment::class)->count([]),
                'albums' => $this->manager->getRepository(Album::class)->count([]),
                'photos' => $this->manager->getRepository(Photo::class)->count([]),
                'todos' => $this->manager->getRepository(Todo::class)->count([]),
            ],
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('JSON Manager')
            ;
    }

    /**
     * @return Iterator<(CrudMenuItem | DashboardMenuItem | SectionMenuItem)>
     */
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud(label: 'Posts', icon: 'fas fa-newspaper', entityFqcn: Post::class);
        yield MenuItem::linkToCrud(label: 'Comments', icon: 'fas fa-reply', entityFqcn: Comment::class);
        yield MenuItem::section();
        yield MenuItem::linkToCrud(label: 'Todos', icon: 'fas fa-tasks', entityFqcn: Todo::class);
        yield MenuItem::section();
        yield MenuItem::linkToCrud(label: 'Albums', icon: 'fas fa-images', entityFqcn: Album::class);
        yield MenuItem::linkToCrud(label: 'Photos', icon: 'fas fa-image', entityFqcn: Photo::class);
        yield MenuItem::section();
        yield MenuItem::linkToCrud(label: 'Users', icon: 'fas fa-users', entityFqcn: User::class);

        yield MenuItem::subMenu(label: 'Documentations', icon: 'fas fa-book')
            ->setSubItems([

            ]);
        yield MenuItem::linkToUrl(label: 'OpenAPI', icon: 'fas fa-arrow-up-right-from-square', url: $this->generateUrl('api_doc'));
        yield MenuItem::linkToUrl(label: 'ReDoc', icon: 'fas fa-arrow-up-right-from-square', url: $this->generateUrl('api_doc', ['ui' => 're_doc']));
        yield MenuItem::linkToUrl(label: 'GraphiQL', icon: 'fas fa-arrow-up-right-from-square', url: $this->generateUrl('api_graphql_graphiql'));
        yield MenuItem::linkToUrl(label: 'GraphQL Playground', icon: 'fas fa-arrow-up-right-from-square', url: $this->generateUrl('api_graphql_graphql_playground'));

        yield MenuItem::section();
        $request = Request::createFromGlobals();
        yield MenuItem::linkToUrl(label: 'Back to website', icon: 'fa fa-reply', url: sprintf('%s://%s', $request->getScheme(), BinController::getBaseHost($request)));
        yield MenuItem::linkToUrl(label: 'GitHub', icon: 'fab fa-github', url: 'https://github.com/arnaud-ritti/devtools');
    }
}
