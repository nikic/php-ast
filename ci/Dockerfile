ARG PHP_VERSION
FROM php:$PHP_VERSION
WORKDIR /php-ast
ADD *.c *.h *.php config.m4 config.w32 package.xml LICENSE README.md ./

# Assume compilation will be the time consuming step.
# Add tests after compiling so that it's faster to update tests and re-run them locally.
RUN phpize && CFLAGS='-Wall -O2 -Wextra' ./configure && make -j2 && make install
RUN docker-php-ext-enable ast
ADD tests ./tests
ADD ci ./ci
