#!/bin/bash
set -xe

# Install pip if not already installed
if ! command -v pip3 &> /dev/null; then
    sudo dnf install -y python3-pip
fi

# Install supervisor via pip
sudo pip3 install supervisor

# Create Supervisor config directory if it doesn't exist
sudo mkdir -p /etc/supervisord.d

# Create a default supervisord config if not already present
if [ ! -f /etc/supervisord.conf ]; then
    echo_supervisord_conf | sudo tee /etc/supervisord.conf > /dev/null
    echo "[include]" | sudo tee -a /etc/supervisord.conf > /dev/null
    echo "files = /etc/supervisord.d/*.ini" | sudo tee -a /etc/supervisord.conf > /dev/null
fi

# Create systemd service file for supervisord
sudo tee /etc/systemd/system/supervisord.service > /dev/null <<EOF
[Unit]
Description=Supervisor daemon
Documentation=http://supervisord.org
After=network.target

[Service]
ExecStart=/usr/local/bin/supervisord -n -c /etc/supervisord.conf
ExecStop=/usr/local/bin/supervisorctl $OPTIONS shutdown
ExecReload=/usr/local/bin/supervisorctl -c /etc/supervisord.conf $OPTIONS reload
KillMode=process
Restart=on-failure
RestartSec=50s

[Install]
WantedBy=multi-user.target
EOF

# Reload systemd daemon
sudo systemctl daemon-reload

# Enable and start supervisord service
sudo systemctl enable supervisord
sudo systemctl start supervisord

# Copy your Laravel worker configuration into supervisor config directory
sudo cp .platform/files/supervisor.ini /etc/supervisord.d/laravel_worker.ini

# Reload supervisor configuration
sudo supervisorctl reread
sudo supervisorctl update
