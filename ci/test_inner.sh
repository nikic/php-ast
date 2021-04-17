#!/usr/bin/env bash
# -x Exit immediately if any command fails
# -e Echo all commands being executed.
# -u fail for undefined variables
set -xeu
echo "Run tests in docker"
php ./run-tests.php -P -q --show-diff
echo "Test that package.xml is valid"
REPORT_EXIT_STATUS=1 pecl package
