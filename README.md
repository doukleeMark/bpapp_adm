# Boryung BP App AdminSite

docker container 구동
* docker run -dit --name brserv -p 80:80 -v /root/docker/brserv/share:/var/share centos:centos6.10
* docker run -dit --name mysql56 -p 3306:3306 -v /root/docker/mysql56:/var/lib/mysql -e MYSQL_ROOT_PASSWORD=lee7578 mysql:5.6

docker-compose 설치
* curl -L "https://github.com/docker/compose/releases/download/1.24.1/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
* chmod +x /usr/local/bin/docker-compose

KST Date 날짜 설정
* ln -sf /usr/share/zoneinfo/Asia/Seoul /etc/localtime

yum 설치
* yum install epel-release
* rpm -Uvh http://rpms.famillecollet.com/enterprise/remi-release-6.rpm
* yum --enablerepo=remi-php72 install php php-devel
* yum --enablerepo=remi-php72 install php-xml php-soap php-xmlrpc php-mbstring php-json php-gd php-mcrypt php-zip php-mysql
* yum install unzip git

php.ini 설정
* Short_open_tag = On
* upload_max_filesize = 1000M
* post_max_size = 1000M
* memory_limit = 512M

php composer 설치
* curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/
* ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

HTML Assest Unzip
* unzip plugins.zip

Upload 폴더생성
* mkdir upload, chmod 707 upload

Upload 다운로드
* wget --ftp-user=bpdata --ftp-password=bpdata4321 ftp://13.209.249.72/upload

ffmpeg 설치
* rpm --import http://li.nux.ro/download/nux/RPM-GPG-KEY-nux.ro
* rpm -Uvh http://li.nux.ro/download/nux/dextop/el6/x86_64/nux-dextop-release-0-2.el6.nux.noarch.rpm
* yum install ffmpeg ffmpeg-devel | yum update
* (생략) cp -r /usr/include/ffmpeg/* /usr/include/

Database S3 Path 변경
* update s3_data set s3_url = replace(s3_url, 'https://insn-brtv', 'https://boryung-brain-upload');
