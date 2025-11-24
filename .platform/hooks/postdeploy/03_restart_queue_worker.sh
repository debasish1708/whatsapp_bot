#!/bin/bash

# Restarts all supervisor workers

sudo php /var/www/html/artisan queue:restart
