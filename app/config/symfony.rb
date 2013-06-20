before 'symfony:composer:install', 'composer:copy_vendors'
before 'symfony:composer:update', 'composer:copy_vendors'

after 'symfony:composer:install', 'composer:dump_autoload'
after 'symfony:composer:update', 'composer:dump_autoload'

namespace :composer do
    task :copy_vendors, :except => { :no_release => true } do
        capifony_pretty_print "--> Copy vendor file from previous release"

        run "vendorDir=#{current_path}/vendor; if [ -d $vendorDir ] || [ -h $vendorDir ]; then cp -a $vendorDir #{latest_release}; fi;"
        capifony_puts_ok
    end

    task :dump_autoload, :except => { :no_release => true } do
        run "cd #{latest_release} && #{php_bin} composer.phar dump-autoload -o"
    end
end

# overrided Capifony tasks
namespace :symfony do
    # overrided to not use sf prod environment because some bundles are not
    # registered with this one
    desc "Runs custom symfony command"
    task :default, :roles => :app, :except => { :no_release => true } do
        prompt_with_default(:task_arguments, "cache:clear")

        stream "cd #{latest_release} && #{php_bin} -d memory_limit=512M #{symfony_console} #{task_arguments}"
    end

    namespace :composer do
        # overrided to download composer from FullSIX
        desc "Gets composer and installs it"
        task :get, :roles => :app, :except => { :no_release => true } do
            capifony_pretty_print "--> Downloading Composer from http://packagist.fullsix.com/composer.phar"

            run "cd #{latest_release} && wget -q http://packagist.fullsix.com/composer.phar"

            capifony_puts_ok
        end
    end

    namespace :cache do
        # overrided to warmup the dev & prod cache with --no-debug option
        desc "Warms up an empty cache"
        task :warmup, :roles => :app, :except => { :no_release => true } do
            capifony_pretty_print "--> Warming up cache"

            try_sudo "sh -c 'cd #{latest_release} && #{php_bin} -d memory_limit=512M #{symfony_console} cache:warmup --env=#{symfony_env_local} --no-debug'"
            try_sudo "sh -c 'cd #{latest_release} && #{php_bin} -d memory_limit=512M #{symfony_console} cache:warmup --env=#{symfony_env_prod} --no-debug'"

            try_sudo "chmod -R g+w #{latest_release}/#{cache_path}"

            capifony_puts_ok
        end
    end
end
