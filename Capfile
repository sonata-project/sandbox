load 'deploy' if respond_to?(:namespace) # cap2 differentiator

# load the default plugin
# uncomment lines to load new capistrano task
plugins = [
  '/vendor/sonata-capistrano/capifony.rb',
  '/vendor/sonata-capistrano/capifony-symfony2.rb',
  '/vendor/sonata-capistrano/sonata-symfony2.rb',
  # '/vendor/sonata-capistrano/sonata-page-bundle.rb',
]

plugins.each{|plugin| load File.dirname(__FILE__) + plugin}

# configure global settings
set :application,       'Sonata Ecommerce demo'
set :scm,               :git
set :repository,        "git@git.fullsix.com:/php/sonata/sandbox.git"
set :use_sudo,          false
set :keep_releases,     3
set :current_dir,       "/current"  # this must be the web server document root directory
set :shared_children,   [app_path + "/logs", web_path + "/uploads"]
set :shared_files,      ["app/config/parameters.yml"]
set :asset_children,    [web_path + "/css", web_path + "/js"]
set :branch,            'ecommerce'

set :update_vendors,     false
set :configuration_init, false


# Please note, the git_submodules_recursive settings only works if
# the lib/capistrano/recipes/deploy/scm/base.rb capistrano file is
# patched dues to a bug : https://github.com/capistrano/capistrano/pull/103
set :deploy_via,                :remote_cache
set :git_shallow_clone,         1
set :git_enable_submodules,     true
set :git_submodules_recursive,  false

ssh_options[:forward_agent]    = true

# configure validation settings
task :dev do
    set :stage,     "dev"
    set :deploy_to, "/usr/local/web/htdocs/ekino/sonata/ecommerce-demo"
    set :gateway,   "rebond.fullsix.com"
    set :domain,    "sonata-ecommerce-demo.dev.fullsix.com"

    role :app,      'webadmin@orion'
    role :web,      'webadmin@orion'
    role :db,       "webadmin@orion", :primary => true, :no_release => true

    set :sonata_page_managers, ['page', 'snapshot']
end

before "deploy:finalize_update" do
  run "cd %s && php bin/build_bootstrap.php" % [ fetch(:latest_release)]
end

# uncomment these lines if you have specific staging parameters.ini file
#   ie, production_parameters.yml => parameters.yml on the production server
#   ie, validation_parameters.yml => parameters.yml on the validation server
#
after "deploy:setup" do
  run "if [ ! -d %s/shared/app/config ]; then mkdir -p %s/shared/app/config; fi" % [ fetch(:deploy_to),  fetch(:deploy_to)]
  upload(
    '%s/app/config/%s_parameters.yml' % [File.dirname(__FILE__), fetch(:stage)],
    '%s/shared/app/config/parameters.yml' % fetch(:deploy_to)
  )
end