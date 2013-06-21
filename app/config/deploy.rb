load File.dirname(__FILE__) + '/cache'
load File.dirname(__FILE__) + '/symfony'

# configure global settings
set :application,             "sonata-sandbox-ecommerce"
set :domain,                  "sonata-ecommerce-demo.dev.fullsix.com"

set :app_path,                "app"

set :repository,              "git@gitlab.fullsix.com:php-labs/sf2-sonata-sandbox.git"
set :scm,                     :git
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `subversion`, `mercurial`, `perforce`, or `none`

set :keep_releases,           3
set :use_sudo,                false

# Be more or less verbose by commenting/uncommenting the following lines
#logger.level = Logger::IMPORTANT
#logger.level = Logger::INFO
#logger.level = Logger::DEBUG
logger.level = Logger::TRACE
#logger.level = Logger::MAX_LEVEL

set :shared_files,            ["app/config/parameters.yml"]
set :shared_children,         [app_path + "/logs", web_path + "/static", "data"]
set :clear_controllers,       false

# do not install assets with Capifony, Composer already does this job
set :assets_install,         false

set :dump_assetic_assets,    true

set :use_composer,            true

set :composer_options,        "--prefer-source"


ssh_options[:forward_agent] = true

before "deploy", "check_releases"
before "deploy:update_code", "check_releases"

task :check_releases, :roles => :app do
    local_releases = capture("ls -xt #{releases_path}").split.reverse
    releases_count = local_releases.length

    if releases_count > keep_releases
        logger.important "Please run \"cap #{stage} deploy:cleanup\" before deploying (#{releases_count} releases already deployed)"

        exit
    end
end

# configure dev settings
desc "Deploy to dev instance (http://sonata-ecommerce-demo.dev.fullsix.com/)"
task :dev do
    set :stage,                "dev"
    set :branch,               "ecommerce"

    set :deploy_to,            "/usr/local/web/htdocs/ekino/sonata/ecommerce-demo"

    role :app,                 "webadmin@orion", :primary => true
    role :web,                 "webadmin@orion"
    role :db,                  "oracle@phoenix", :primary => true, :no_release => true
    role :cache,               "root@ursa",      :no_release => true

    set :sonata_page_managers, ['page', 'snapshot']

    after "deploy:create_symlink" do
        cache.sonata.purge
    end
end

namespace :sonata do
    namespace :page do
        task :create_snapshots, :roles => :app, :except => { :no_release => true }, :only => { :primary => true } do
            run "cd #{latest_release} && #{php_bin} #{symfony_console} sonata:page:create-snapshots --site=all"
        end
    end
end

after "deploy:setup" do
    run "if [ ! -d #{deploy_to}/shared/app/config ]; then mkdir -p #{deploy_to}/shared/app/config; fi"

    upload(
        '%s/parameters_%s.yml' % [File.dirname(__FILE__), fetch(:stage)],
        '%s/shared/app/config/parameters.yml' % fetch(:deploy_to)
    )
end
