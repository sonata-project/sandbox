#!/usr/bin/env bash

# Update Repositories
sudo apt-get update

# Determine Ubuntu Version
. /etc/lsb-release

# Decide on package to install for `add-apt-repository` command
#
# USE_COMMON=1 when using a distribution over 12.04
# USE_COMMON=0 when using a distribution at 12.04 or older
USE_COMMON=$(echo "$DISTRIB_RELEASE > 12.04" | bc)

if [ "$USE_COMMON" -eq "1" ];
then
    sudo apt-get install -y software-properties-common
else
    sudo apt-get install -y python-software-properties
fi

echo "Updating packages ..."
# Add Ansible Repository & Install Ansible
sudo add-apt-repository -y ppa:ansible/ansible
sudo apt-get update
sudo apt-get install -y ansible

# Setup Ansible for Local Use and Run
cp /vagrant/.ansible/inventories/dev /etc/ansible/hosts -f
chmod 666 /etc/ansible/hosts
cat /vagrant/.ansible/files/authorized_keys >> /home/vagrant/.ssh/authorized_keys

echo "Starting Ansible playbook ..."
sudo ansible-playbook /vagrant/.ansible/playbook.yml -e hostname=$1 --connection=local

if [ ! -d /var/www ]; then
    mkdir /var/www
fi

if [ ! -d /var/www/sonata-sandbox ]; then
    ln -s /vagrant /var/www/sonata-sandbox
fi

if [ ! -d /var/www/sonata-sandbox/vendor ]; then
    composer install
fi

if [ ! -f /var/www/sonata-sandbox/app/config/parameters.yml ]; then
    cp /var/www/sonata-sandbox/app/config/parameters.yml.dist /var/www/sonata-sandbox/app/config/parameters.yml
fi
