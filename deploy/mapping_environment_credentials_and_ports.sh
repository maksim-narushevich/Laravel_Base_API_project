#!/usr/bin/env bash


pwd

ls -la

if [ $1 == "yes" ]; then
    echo "Start App environment WITH Shared Mysql service";
    ./deploy/mapping_docker_env_without_mysql.sh
else
  echo "Start App environment WITHOUT Shared Mysql service";
  ./deploy/mapping_docker_env_with_mysql.sh
fi
