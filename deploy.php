<?php

declare(strict_types=1);

namespace Deployer;

require 'recipe/symfony.php';

// Config
set('repository', 'git@github.com:arnaud-ritti/symfony-demo.git');

set('git_tty', false);
set('ssh_multiplexing', false);

set('bin/php', '/opt/php8.0/bin/php -d memory_limit=-1');
set('bin/composer', '/opt/php8.0/bin/composer2.phar');
set('composer_options', '--no-progress --no-interaction --optimize-autoloader');
// Hosts

host('production')
    ->set('port', '22')
    ->set('hostname', 'af58e.ftp.infomaniak.com')
    ->set('remote_user', 'af58e_aritti')
    ->set('http_user', 'uid196930')
    ->set('deploy_path', '~/sites/json-placeholder.arnaud-ritti.fr')
;

host('staging')
    ->set('port', '22')
    ->set('hostname', 'af58e.ftp.infomaniak.com')
    ->set('remote_user', 'af58e_aritti')
    ->set('http_user', 'uid196930')
    ->set('deploy_path', '~/sites/json-placeholder.staging.arnaud-ritti.fr')
;

// Tasks
task('npm:build', static function (): void {
    runLocally('npm run build');
    upload('public/build/', '{{release_path}}/public/build/');
});

before('deploy:symlink', 'npm:build');
before('deploy:symlink', 'database:migrate');

after('deploy:failed', 'deploy:unlock');
