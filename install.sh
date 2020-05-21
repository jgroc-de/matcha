sudo apt install -y php-zmq golang-go composer
go get github.com/mailhog/MailHog
go get github.com/mailhog/mhsendmail
composer install
cd ratchet && composer install && cd ..
echo 'sendmail_path = /home/user42/go/bin/mhsendmail' | sudo tee -a /etc/php/7.2/cli/php.ini
sudo mysql < conf_user.sql
echo 'launch mail server with command ~/go/bin/MailHog'
echo 'launch websocket server with command php ./ratchet/index.php'
echo 'launch webserver with command php -S localhost:8080 -t public'
php -S localhost:8080 -t public