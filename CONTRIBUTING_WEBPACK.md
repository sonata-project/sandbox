Contributing webpack encore
===========================

### webpack configuration

You can find configuration for each build under `builds_configs/` directory. This structure allows to use different 
configurations for each layout.

```sh
cd ./builds_configs/sonata_admin_adminlte2
yarn install # to install node_modules
yarn encore dev # to generate files under `public/build/sonata_admin/`
``` 

### templates and css frameworks

Symfony allows to override templates ([see more][link_symfony_templates]). Use it for testing your changes.
Before creating a new PR, move this file under the chosen framework directory (for example, `templates/bootstrap3/`). You can also add other CSS frameworks
and templates for them.

### Links:

- [webpack encore dev-kit issue][link_issue]
- [symfony webpack guide][link_symfony_webpack]
 
[link_issue]: https://github.com/sonata-project/dev-kit/issues/779 "webpack encore dev-kit issue"
[link_symfony_webpack]: https://symfony.com/doc/current/frontend.html#webpack-encore "symfony webpack encore"
[link_symfony_templates]: https://symfony.com/doc/current/bundles/override.html "symfony override templates"
