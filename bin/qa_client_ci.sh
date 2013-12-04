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

if [ ! -d "build" ]; then
    mkdir build
fi

rm -rf build/junit

error_code=0

# Execute PHPUnit test on the targetted folder
run_test() {
    if [ -f "${1}/phpunit.xml.dist" ]; then
        phpunit -c ${1} --log-junit build/junit/`basename ${1}`.xml

        status=$?

        if [ $status -ne 0 ]; then
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

run_tests *                                                    # run application tests
run_tests vendor/sonata-project/*/*/* vendor/sonata-project/*  # run Sonata's unit tests
run_tests src/MyCompanyName/*                                  # add your own test suite here

if [ $error_code -ne 0 ]; then
    echo "Errors occur when running unit tests"
fi

exit $error_code
