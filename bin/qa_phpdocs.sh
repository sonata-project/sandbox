#!/bin/sh
# This file is part of the Sonata package.
#
# (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
#
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.

if [ -z "$1" ]; then
    echo "Please provide a valid project name"
    exit 1
fi

if [ -z "$2" ]; then
    echo "Please provide a valid version name"
    exit 1
fi

if [ ! -f  "composer.json" ]; then
    echo "The script must be started at the root of a project with a composer.json file"
    exit 1
fi

name=$1
version=$2
tmpfolder=`mktemp -d --suffix=_sami`
root=`pwd`

if [ ! -d "build" ]; then
    mkdir build
fi

rm -rf build/sami

echo "cloning Sami in $tmpfolder"
git clone https://github.com/fabpot/Sami.git $tmpfolder

cd $tmpfolder
composer install

content="
<?php  \n
\n
use Sami\Sami;\n
use Symfony\Component\Finder\Finder;\n
\n
\$iterator = Finder::create()\n
    ->files()\n
    ->name('*.php')\n
    ->exclude('Resources')\n
    ->exclude('Tests')\n
    ->in('${root}/vendor')\n
;\n
\n
return new Sami(\$iterator, array(
    'title'                => 'Sonata Project API',
    'build_dir'            => '${root}/build/sami',
    'cache_dir'            => '${tmpfolder}/cache',
    'default_opened_level' => 0,
));\n
";

echo "generating config.php in $tmpfolder"
echo $content >> $tmpfolder/config.php

php ${tmpfolder}/sami.php update ${tmpfolder}/config.php

echo "cleaning up data"
rm -rf $tmpfolder

cd ${root}/build/sami

echo "creating tarball archive"
tar -czf ../$name-$version-api.tar.gz .

echo "create zip archive"
zip -9 -q -r ../$name-$version-api.zip .

echo "done!"