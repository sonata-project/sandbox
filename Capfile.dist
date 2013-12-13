load 'deploy' if respond_to?(:namespace) # cap2 differentiator

require 'capifony_symfony2'

set :application, "set your application name here"
set :domain,      "#{application}.com"
set :app_path,    "app"

set :repository,  "#{domain}:/var/repos/#{application}.git"
set :scm,         :git
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `subversion`, `mercurial`, `perforce`, or `none`

set :model_manager, "doctrine"
# Or: `propel`

set  :keep_releases,  3

# Be more verbose by uncommenting the following line
# logger.level = Logger::MAX_LEVEL

## Nice options to add
# set :composer_options,        "--prefer-source"
# ssh_options[:forward_agent] = true

# set :shared_files,            ["app/config/parameters.yml"]
# set :shared_children,         [app_path + "/logs", web_path + "/uploads", "data"]
# set :clear_controllers,       false
# set :assets_symlinks,         true


# http://capifony.org/cookbook/speeding-up-deploy.html
# before 'symfony:composer:install', 'composer:copy_vendors'
# before 'symfony:composer:update', 'composer:copy_vendors'
#
# namespace :composer do
#   task :copy_vendors, :except => { :no_release => true } do
#     capifony_pretty_print "--> Copy vendor file from previous release"
#
#     run "vendorDir=#{current_path}/vendor; if [ -d $vendorDir ] || [ -h $vendorDir ]; then cp -a $vendorDir #{latest_release}/vendor; fi;"
#     capifony_puts_ok
#   end
# end

# # configure production settings
# task :production do
#     set :stage,     "production"
#     set :deploy_to, "/usr/local/web/htdocs/org.sonata-project"
#
#     role :app,      'wwww-data@sonata-project.org', :master => true, :primary => true
#     # role :app,      'wwww-data@sonata-project.org'
#
#     role :web,      'wwww-data@sonata-project.org', :master => true, :primary => true
#     # role :web,      'wwww-data@sonata-project.org'
#
#     role :db,       "wwww-data@db.sonata-project.org", :primary => true, :no_release => true
# end
#
# # configure validation settings
# task :validation do
#     set :stage,     "validation"
#     set :deploy_to, "/usr/local/web/htdocs/org.sonata-project.validation"
#
#     role :app,      'wwww-data@validation.sonata-project.org', :master => true, :primary => true
#     # role :app,      'wwww-data@sonata-project.org'
#
#     role :web,      'wwww-data@validation.sonata-project.org', :master => true, :primary => true
#     # role :web,      'wwww-data@sonata-project.org'
#
#     role :db,       "wwww-data@db.validation.sonata-project.org", :primary => true, :no_release => true
#
#     set :sonata_page_managers, ['page', 'snapshot']
# end

namespace :cache do
    namespace :sonata do
        task :purge, :roles => :cache, :except => { :no_release => true } do
            capifony_pretty_print "--> Purge Sonata pages cache"

            run "cd #{latest_release} && #{php_bin} #{symfony_console} sonata:cache:flush-all"
        end
    end
end
