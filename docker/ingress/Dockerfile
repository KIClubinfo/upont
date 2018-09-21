FROM nginx:alpine

RUN apk --no-cache add openssl
# RUN openssl dhparam -out /etc/ssl/dhparam.pem 2048

COPY nginx.conf /etc/nginx/nginx.conf
RUN rm -rf /etc/nginx/conf.d/
COPY conf.d/ /etc/nginx/
