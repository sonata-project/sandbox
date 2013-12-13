#!/usr/bin/env bash
app/console doctrine:database:drop --force
app/console doctrine:database:create
app/console doctrine:schema:create
app/console assets:install web
app/console init:acl
app/console sonata:admin:setup-acl
app/console sonata:admin:generate-object-acl
#app/console sonata:page:create-site --enabled=1 --name=localhost --host=localhost --relativePath=/ --enabledFrom=now --enabledTo="+10 years" --default=1
app/console doctrine:fixtures:load --append
app/console sonata:page:update-core-routes --site=all
app/console sonata:page:create-snapshots --site=all
