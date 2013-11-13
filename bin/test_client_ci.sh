#!/bin/sh

rm -rf junit

# Execute PHPUnit test on the targetted folder
run_test() {
    if [ -f "${1}/phpunit.xml.dist" ]; then
        phpunit -c ${1} --log-junit junit/`basename ${1}`.xml
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
