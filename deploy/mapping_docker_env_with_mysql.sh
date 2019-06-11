#!/usr/bin/env bash

sed -e "s/\${APP_HTTP_PORT}/${DEV_HTTP_PORT}/g;
        s/\${APP_HTTPS_PORT}/${DEV_HTTPS_PORT}/g;
        s/\${APP_REDIS_PORT}/${DEV_REDIS_PORT}/g;
        s/\${APP_MYSQL_PORT}/${DEV_MYSQL_PORT}/g;
        s/\${APP_MYSQL_PASSWORD}/${DEV_MYSQL_PASSWORD}/g;
        s/\${APP_MYSQL_DATABASE}/${DEV_MYSQL_DATABASE}/g;
        s/\${APP_MAIL_PORT}/${DEV_MAIL_PORT}/g;"  ./deploy/docker-compose.dev.tpl.yml > docker-compose.yml
# Using character escaping
sed -e "s/\${APP_MYSQL_PASSWORD}/${DEV_MYSQL_PASSWORD}/g;
     s/\${APP_MYSQL_DATABASE}/${DEV_MYSQL_DATABASE}/g;
     s/\${APP_MYSQL_PORT}/${DEV_MYSQL_PORT}/g;
     s/\${APP_AWS_ACCESS_KEY_ID}/${DEV_AWS_ACCESS_KEY_ID}/g;
     s|${APP_AWS_SECRET_ACCESS_KEY}|${DEV_AWS_SECRET_ACCESS_KEY}|g;
     s/\${APP_AWS_DEFAULT_REGION}/${DEV_AWS_DEFAULT_REGION}/g;
     s/\${APP_AWS_BUCKET}/${DEV_AWS_BUCKET}/g;
     s/\${APP_MAILGUN_DOMAIN}/${DEV_MAILGUN_DOMAIN}/g;
     s/\${APP_MAILGUN_SECRET}/${DEV_MAILGUN_SECRET}/g;"  ./deploy/.env.dist.deploy > ./deploy/.env.dist
