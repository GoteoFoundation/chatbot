#!/bin/bash

DIR=$(dirname $(readlink -f $0))
version=$1
if [ -z "$version" ]; then
    echo "Please specify version number"
    exit 1
fi

cd "$(dirname $(dirname $DIR))"
branch=$(git rev-parse --abbrev-ref HEAD)

rm -r docker/src
mkdir docker/src
git archive $branch | tar -x -C docker/src

sudo docker build . -t goteo/chatbot:$version -f docker/prod/Dockerfile
rm -r docker/src
