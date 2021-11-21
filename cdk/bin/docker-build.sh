#!/bin/sh

# Save pwd and then change dir to the project root
STARTDIR=`pwd`
cd `dirname $0`/..

docker build -t node-cdk .

# Go back to original dir
cd $STARTDIR
