#!/bin/bash
proxyData="http://login:passwords@address:port"
export http_proxy=$proxyData
export https_proxy=$proxyData
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
exit 0