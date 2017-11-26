FROM node:alpine
MAINTAINER Louis TREZZINI <louis.trezzini@ponts.org>

RUN mkdir /app && chown node:node /app && chmod 700 /app
WORKDIR /app

RUN apk --no-cache add git
RUN npm install -g -s --no-progress yarn bower gulp http-server

USER node

COPY package.json package.json
COPY yarn.lock yarn.lock
COPY bower.json bower.json
COPY .bowerrc .bowerrc
RUN yarn

COPY . /app
RUN yarn run build && \
    yarn run prune && \
    yarn cache clean

WORKDIR /app/www
CMD ["hs", "-p", "3000"]
EXPOSE 3000
