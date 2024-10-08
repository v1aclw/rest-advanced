FROM nginx:latest

WORKDIR /var/www

COPY docker/nginx/conf.d/ /etc/nginx/conf.d/

CMD ["nginx", "-g", "daemon off;"]