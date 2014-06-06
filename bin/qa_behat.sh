#!/bin/sh
# This file is part of the Sonata package.
#
# (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
#
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.

echo "Running Behat default test suite"
bin/behat -f progress
statusDefault=$?

echo "Running Behat API test suite"
bin/behat --profile api -f progress
statusApi=$?

if [ $statusDefault -ne 0 ] || [ $statusApi -ne 0 ]; then
   echo "Some Behat test suites have failed!"
   exit 1
fi

echo ""
echo "Behat test suites executed successfully!"

exit 0