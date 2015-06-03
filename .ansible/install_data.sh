#!/usr/bin/env bash

cd /var/www/sonata-sandbox && php bin/load_data.php

curl -s -H "Host: sonata.local" http://127.0.0.1/app.php | grep 3e9fda56df2cdd3b039f189693ab7844fbb2d4f6

echo ""
echo ""
echo "Congratulation!!!"
echo "You have installed a virtual machine with Sonata Project"
echo ""
echo "You need to :"
echo " - add sonata.local to point 192.168.33.99 in your hosts file"
echo " - start hacking around ;)"
echo ""
