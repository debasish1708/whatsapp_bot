#!/bin/bash

# Install zip extension for PHP 8.2 on Amazon Linux 2023
echo "Checking if PHP zip extension is already installed..."

if php -m | grep -q "^zip$"; then
    echo "PHP zip extension is already installed. Skipping installation."
else
    echo "Installing PHP zip extension for PHP 8.2..."

    # Install the php-zip package for PHP 8.2
    yum install -y php8.2-zip

    # Alternative approach if the above doesn't work
    if ! php -m | grep -q "^zip$"; then
        echo "Trying alternative installation method..."
        yum install -y php-zip
    fi

    # Verify installation
    if php -m | grep -q "^zip$"; then
        echo "PHP zip extension installation completed successfully."
    else
        echo "Failed to install PHP zip extension. Trying manual approach..."

        # Install dependencies and compile if needed
        yum install -y libzip-devel gcc php-devel php-pear
        yes '' | pecl install zip

        # Ensure the extension is loaded
        if [ ! -f "/etc/php.d/20-zip.ini" ]; then
            echo "extension=zip.so" > /etc/php.d/20-zip.ini
        fi

        echo "Manual PHP zip extension installation completed."
    fi
fi

# Restart PHP-FPM to ensure changes take effect
systemctl restart php-fpm || service php-fpm restart

echo "PHP zip extension installation process finished."
