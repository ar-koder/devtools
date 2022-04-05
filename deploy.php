<?php
namespace Deployer;

require 'recipe/symfony.php';

// Config
set('repository', 'git@github.com:arnaud-ritti/symfony-demo.git');

set('git_tty', false);
set('ssh_multiplexing', false);

set('composer_options', ' --prefer-dist --no-progress --no-interaction --no-dev --optimize-autoloader');
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
task('npm:build', function () {
    runLocally('npm run build');
    upload("public/build/", '{{release_path}}/public/build/');
});

before('deploy:symlink', 'npm:build');
before('deploy:symlink', 'database:migrate');

after('deploy:failed', 'deploy:unlock');
