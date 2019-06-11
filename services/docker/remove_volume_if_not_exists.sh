#!/usr/bin/env bash

if [ "$(docker volume ls | grep $1)" != "" ]; then
 docker volume rm $1
 echo "Volume $1 was removed.";
else
  echo "Volume $1 does not exist.";
fi
