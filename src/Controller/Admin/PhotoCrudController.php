<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Photo;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PhotoCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Photo::class;
    }
}
