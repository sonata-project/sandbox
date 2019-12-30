#!/bin/sh
# This file is part of the Sonata package.
#
# (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
#
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.

if [ ! -d "build" ]; then
    mkdir build
fi

#parameters=" -f pretty --out build/junit"
parameters=" "

echo "Running Behat frontend test suite"
vendor/bin/behat --profile frontend -f progress $parameters
statusFrontend=$?

echo "Running Behat backend test suite"
vendor/bin/behat --profile backend -f progress $parameters
statusBackend=$?

echo "Running Behat API test suite"
vendor/bin/behat --profile api -f progress $parameters
statusApi=$?

if [ $statusFrontend -ne 0 ] || [ $statusBackend -ne 0 ] || [ $statusApi -ne 0 ]; then
   echo "Some Behat test suites have failed!"
   exit 1
fi

echo ""
echo "Behat test suites executed successfully!"

exit 0