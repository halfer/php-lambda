#!/bin/sh

# Save pwd and then change dir to the project root
STARTDIR=`pwd`
cd `dirname $0`/..

docker run --entrypoint sh -v `pwd`:/project -it php-lambda

# Go back to original dir
cd $STARTDIR
