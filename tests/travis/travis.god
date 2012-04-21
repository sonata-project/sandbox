God.watch do |w|
  w.name = "php-cgi"
  w.start = "php-cgi -b 127.0.0.1:9000"
  w.keepalive
end