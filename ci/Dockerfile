ARG PHP_VERSION
FROM php:$PHP_VERSION
WORKDIR /php-ast
ADD *.c *.h *.php config.m4 config.w32 package.xml LICENSE README.md ./

# Assume compilation will be the time consuming step.
# Add tests after compiling so that it's faster to update tests and re-run them locally.
#
# The choice of optimization flags is arbitrary and shouldn't affect test results.
# Higher optimization levels will slow compilation but speed up tests (especially if valgrind (run-tests.php -m) is ever used)
#
# Use the same CFLAGS used to build the docker image but also check for warnings (this will emit warnings but not fail the build)
RUN phpize && EXTRA_CFLAGS='-Wall -Wextra -Wno-unused-parameter' ./configure && make -j2 && make install
RUN docker-php-ext-enable ast
ADD tests ./tests
ADD ci ./ci
