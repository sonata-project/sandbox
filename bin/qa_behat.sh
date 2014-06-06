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

parameters=" -f progress,junit --out ,build/junit"

echo "Running Behat default test suite"
bin/behat -f progress $parameters
statusDefault=$?

echo "Running Behat API test suite"
bin/behat --profile api $parameters
statusApi=$?

if [ $statusDefault -ne 0 ] || [ $statusApi -ne 0 ]; then
   echo "Some Behat test suites have failed!"
   exit 1
fi

echo ""
echo "Behat test suites executed successfully!"

exit 0