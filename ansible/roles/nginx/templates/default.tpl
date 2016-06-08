server {
    listen  80;

    root {{ nginx.docroot }}/front/www;
    index index.html;

    server_name {{ nginx.servername }};

    location / {
        try_files $uri /index.html;
    }

    # Backend avec symfony

    location = /api {
        rewrite ^ /api permanent;
    }

    location /api {
        alias {{ nginx.docroot }}/back/web;
        try_files $uri $uri/ @symfo;
    }

    location @symfo {
        fastcgi_pass unix:/var/run/php5-fpm.sock;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME {{ nginx.docroot }}/back/web/app_dev.php;
        fastcgi_param SCRIPT_NAME /api/app_dev.php;
        fastcgi_param HTTPS on;
    }

    location /phpmyadmin {
        root /usr/share/;
        index index.php index.html index.htm;
        location ~ ^/phpmyadmin/(.+\.php)$ {
            try_files $uri =404;
            root /usr/share/;
            fastcgi_pass unix:/var/run/php5-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param HTTPS off;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include fastcgi_params;
        }
        location ~* ^/phpmyadmin/(.+\.(jpg|jpeg|gif|css|png|js|ico|html|xml|txt))$ {
            root /usr/share/;
        }
    }

    location /favicon.ico {
        log_not_found off;
        access_log off;
    }

    # Make sure files with the following extensions do not get loaded by nginx because nginx would display the source code, and these files can contain PASSWORDS!
    location ~* \.(engine|inc|info|install|make|module|profile|test|po|sh|.*sql|theme|tpl(\.php)?|xtmpl)$|^(\..*|Entries.*|Repository|Root|Tag|Template)$|\.php_ {
        deny all;
    }

    # Pour les ressources statiques
    location ~ ^/(index.html$|img|fonts|libs|views) {
        gzip_static on;
        expires 1y;
        add_header Cache-Control public;
        add_header ETag "";
        break;
    }

    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;
}
