#!/usr/bin/env bash

if [ "$(docker search $2  | grep $2)" == "" ]; then
 apk --update --no-cache add sshpass openssh rsync tree
 docker build -t $1 -f ./docker/sonar_scanner/Dockerfile .
 docker push $1
else
  echo "Image $2 already exist in repository";
fi