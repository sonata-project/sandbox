server {
    listen 80;
    
    root {{ sonata_root }};
    server_name {{ servername }};

    location / {
        # try to serve file directly, fallback to rewrite
        try_files $uri @rewriteapp;
    }

    location @rewriteapp {
        # rewrite all to app.php
        rewrite ^(.*)$ /app.php/$1 last;
    }

    location ~ ^/(api|api_dev|app|app_dev|admin|admin_dev)\.php(/|$) {
        include fastcgi_params;

        fastcgi_pass unix:/var/run/php5-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;

        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param HTTPS off;
        fastcgi_param SERVER_PORT 80;
    }

    client_max_body_size 1024m;
}
