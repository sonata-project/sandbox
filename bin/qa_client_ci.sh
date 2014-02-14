#!/bin/sh
# This file is part of the Sonata package.
#
# (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
#
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.

if [ ! -f  "composer.json" ]; then
    echo "The script must be started at the root of a project with a composer.json file"
    exit 1
fi

extended=false

if [ "$1" = "--extended" ]; then
  extended=true
fi

if [ ! -d "build" ]; then
    mkdir build
fi

rm -rf build/junit

if $extended ; then
    rm -rf build/coverage
    rm -rf build/clover
fi

error_code=0

# Execute PHPUnit test on the targetted folder
run_test() {
    if [ -f "${1}/phpunit.xml.dist" ]; then

        extra=""

        if $extended ; then
            extra="--coverage-html build/coverage/`basename ${1}` --coverage-clover build/clover/`basename ${1}`.xml"
        fi

        phpunit -c ${1} ${extra} --log-junit build/junit/`basename ${1}`.xml

        status=$?

        if [ $status -ne 0 ]; then
            echo "Unit Test Suite has failed!"
            error_code=1
        fi
    fi
}

# iterate over folders and run test
run_tests() {
    for folder in $@; do
        if [ -d $folder ]; then
            run_test $folder
        fi
    done
}

run_tests *                        # run application tests
run_tests vendor/sonata-project/*  # run Sonata's unit tests
run_tests src/MyCompanyName/*      # add your own test suite here

if [ $error_code -ne 0 ]; then
    echo "Errors occur when running unit tests"

    exit 1
fi

echo ""
echo "Tests suites executed successfully!!!"

exit 0