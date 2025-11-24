#!/bin/bash
set -xe

# Ensure supervisord service is running
sudo systemctl start supervisord

# Copy your Laravel worker configuration
sudo cp .platform/files/supervisor.ini /etc/supervisord.d/laravel_worker.ini

# Reload supervisor configuration
sudo supervisorctl reread
sudo supervisorctl update

# Restart all supervisor programs
sudo supervisorctl restart all
