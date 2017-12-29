FROM node:alpine
MAINTAINER Louis TREZZINI <louis.trezzini@ponts.org>

RUN mkdir /app && chown node:node /app && chmod 700 /app
WORKDIR /app

RUN apk --no-cache add git
RUN npm install -g -s --no-progress yarn http-server

USER node

COPY .yarnrc package.json yarn.lock /app/
RUN yarn --dev

COPY . /app/
RUN yarn build

WORKDIR /app/dist
CMD ["hs", "-p", "3000"]
EXPOSE 3000
