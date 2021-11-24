#!/bin/sh

# Save pwd and then change dir to the project root
STARTDIR=`pwd`
cd `dirname $0`/..

# This command shares the on-host AWS config/auth with the container
docker run --entrypoint sh \
  -v `pwd`:/project \
  -v ~/.aws:/root/.aws \
  -it node-cdk

# Go back to original dir
cd $STARTDIR
