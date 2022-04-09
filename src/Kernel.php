<?php

declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function boot()
    {
        if (
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'on') &&
            (empty($_SERVER['IS_DOCKERIZED']) || ! $_SERVER['IS_DOCKERIZED']) &&
            in_array($this->getEnvironment(), ['prod', 'staging'])
        ) {
            header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true, 301);
            exit;
        }

        if (PHP_SAPI === 'cli' && ! in_array($this->getEnvironment(), ['prod', 'dev', 'test'])) {
            $valid_passwords = [
                'aritti' => '$2y$10$meOmm44vaBwbp/LJvH/ATeB3vpmmEsU/05k2bkyYUwVNmdyrNUNyS',
            ];

            # Auth basic
            $user = empty($_SERVER['PHP_AUTH_USER']) ? null : $_SERVER['PHP_AUTH_USER'];
            $pass = empty($_SERVER['PHP_AUTH_PW']) ? null : $_SERVER['PHP_AUTH_PW'];
            $validated = array_key_exists($user, $valid_passwords) && (password_verify($pass, $valid_passwords[$user]));

            if (! $validated) {
                header('WWW-Authenticate: Basic realm="Staging env"');
                header('HTTP/1.0 401 Unauthorized');
                echo '<h1>Unauthorized</h1>';
                echo '<p>This server could not verify that you are authorized to access the document requested.  Either you supplied the wrong credentials (e.g., bad password), or your browser doesn\'t understand how to supply the credentials required.</p>';
                exit;
            }
        }

        return parent::boot();
    }
}
