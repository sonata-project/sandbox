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

rm -rf app/logs/* app/cache/*

if [ ! -d "build" ]; then
    mkdir build
fi

rm -rf build/$name-$version.*

echo "creating tarball archive"
tar -czf build/$name-$version.tar.gz \
    --exclude=app/config/parameters.yml \
    --exclude-vcs \
    --exclude=build \
    --exclude=puppet \
    --exclude=web/uploads/media \
    --exclude='web/sitemap*' \
    .

echo "create zip archive"
zip -9 -q -r \
    --exclude=app/config/parameters.yml \
    --exclude=*.svn* \
    --exclude=*.git*  \
    --exclude=build* \
    --exclude=puppet* \
    --exclude=web/uploads/media/* \
    --exclude=web/sitemap* \
    build/$name-$version.zip .

echo "done!"