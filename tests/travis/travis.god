God.watch do |w|
  w.name = "php-cgi"
  w.start = "/usr/bin/spawn-fcgi -n -a 127.0.0.1 -p 9000 -u root -f /usr/bin/php5-cgi"
  w.keepalive
end