<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Entity\Comment;
use App\Entity\Photo;
use App\Entity\Post;
use App\Entity\Todo;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\CrudMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\DashboardMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\SectionMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Iterator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('DemoApp');
    }

    /**
     * @return Iterator<(CrudMenuItem | DashboardMenuItem | SectionMenuItem)>
     */
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section();
        yield MenuItem::linkToCrud('Posts', 'fas fa-newspaper', Post::class);
        yield MenuItem::linkToCrud('Comments', 'fas fa-reply', Comment::class);
        yield MenuItem::section();
        yield MenuItem::linkToCrud('Todos', 'fas fa-tasks', Todo::class);
        yield MenuItem::section();
        yield MenuItem::linkToCrud('Albums', 'fas fa-images', Album::class);
        yield MenuItem::linkToCrud('Photos', 'fas fa-image', Photo::class);
        yield MenuItem::section();
        yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class);
    }
}
