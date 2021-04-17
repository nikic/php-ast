#!/usr/bin/env bash
if [ $# != 1 ]; then
    echo "Usage: $0 PHP_VERSION" 1>&2
    echo "e.g. $0 8.0 or $0 8.0-alpine" 1>&2
    echo "The PHP_VERSION is the version of the php docker image to use" 1>&2
    exit 1
fi
# -x Exit immediately if any command fails
# -e Echo all commands being executed.
# -u fail for undefined variables
set -xeu
PHP_VERSION=$1
DOCKER_IMAGE=php-ast-$PHP_VERSION-test-runner
docker build --build-arg="PHP_VERSION=$PHP_VERSION" --tag="$DOCKER_IMAGE" -f ci/Dockerfile .
docker run $DOCKER_IMAGE ci/test_inner.sh
