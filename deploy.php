<?php

declare(strict_types=1);

namespace Deployer;

require 'recipe/symfony.php';

// Config
set('repository', 'git@github.com:arnaud-ritti/symfony-demo.git');

set('git_tty', false);
set('ssh_multiplexing', false);

// Hosts
import('inventory.yaml');


task('dotenv:set-env', static function (): void {
    run('rm {{release_path}}/.env.local');
    run('touch {{release_path}}/.env.local');
    run('echo "APP_ENV={{symfony_env}}" >> {{release_path}}/.env.local');
    run('echo "SERVICE_ACCOUNT_PASSWORD={{service_account_password}}" >> {{release_path}}/.env.local');
});

// Tasks
task('npm:build', static function (): void {
    runLocally('npm run build');
    upload('public/build/', '{{release_path}}/public/build/');
});

task('database:fixture', static function (): void {
    runLocally('npm run build');
    run('cd {{release_or_current_path}} && {{bin/console}} doctrine:fixtures:load --purge-with-truncate {{console_options}}');
});


before('deploy:symlink', 'npm:build');
before('deploy:symlink', 'database:migrate');
after('database:migrate', 'database:fixture');
after('database:fixture', 'dotenv:set-env');


after('deploy:failed', 'deploy:unlock');
