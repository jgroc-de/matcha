sudo apt install -y php-zmq sendmail golang-go
go get github.com/mailhog/MailHog
go get github.com/mailhog/mhsendmail
composer install
sudo echo 'sendmail_path = /home/user42/go/bin/mhsendmail' >> /etc/php/7.2/cli/php.ini
sudo mysql < conf_user.sql
echo 'launch mail server with command ~/go/bin/mailhog\n'
echo 'launch webserver with command php -S localhost:8080 -t public'
php -S localhost:8080 -t public