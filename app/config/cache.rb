namespace :cache do
    namespace :sonata do
        task :purge, :roles => :app, :except => { :no_release => true } do
            capifony_pretty_print "--> Purge Sonata pages cache"

            run "cd #{latest_release} && #{php_bin} #{symfony_console} sonata:cache:flush-all"
        end
    end
end
