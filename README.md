# Boryung BP App AdminSite

docker container 구동
* docker run -d -it --name brserv -p 2200:22 -p 80:80 -v /root/docker/brserv/share:/var/share centos:centos6.10

docker-compose 설치
* curl -L "https://github.com/docker/compose/releases/download/1.24.1/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
* chmod +x /usr/local/bin/docker-compose

yum 설치
* yum install epel-release
* rpm -Uvh http://rpms.famillecollet.com/enterprise/remi-release-6.rpm
* yum --enablerepo=remi-php72 install php
* yum --enablerepo=remi-php72 install php-xml php-soap php-xmlrpc php-mbstring php-json php-gd php-mcrypt php-zip 
* yum install unzip git

php.ini 설정
* Short_open_tag = On
* upload_max_filesize = 300M

php composer 설치
* curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/
* ln -s /usr/local/bin/composer.phar /usr/local/bin/composer
