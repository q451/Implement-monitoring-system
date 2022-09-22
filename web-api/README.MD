# Start

## Install with composer

没有安装 **composer** 的通过 [composer 中国镜像](http://www.phpcomposer.com/) 安装

``` bash
cd app
composer install
```

## Configure

```bash
bash ./setup.sh
```

## Nginx server config

```
server {
    listen 8888;
    server_name localhost;

    error_page   500 502 /500.html;

    # change to your project directory
    root /Users/lch/work/mine/juzi/projects/august-api/api;

    if (!-e $request_filename) {
        rewrite ^(.*)$ /index.php/$1 last;
    }

    location ~ {
        set $path_info "";
        set $real_script_name $fastcgi_script_name;
        if ($fastcgi_script_name ~ "^(.+?\.php)(/.+)$") {
            set $real_script_name $1;
            set $path_info $2;
        }

        fastcgi_buffers 8 128k;

        # use ip:port for php-fpm
        # fastcgi_pass 127.0.0.1:9000;
        fastcgi_pass unix:/usr/local/var/run/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$real_script_name;
        fastcgi_param SCRIPT_NAME $real_script_name;
        fastcgi_param PATH_INFO $path_info;
        include fastcgi_params;
    }
}
```
