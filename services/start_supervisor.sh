#!/usr/bin/env bash

# Start Sopervisor & worker tasks
supervisord -c /etc/supervisor/supervisord.conf
supervisorctl start laravel-worker:*
