#!/usr/bin/env bash

find storage/ -type d -exec chmod 0777 {} + && find storage -type f -print0 | xargs -0 chmod og+rwx
