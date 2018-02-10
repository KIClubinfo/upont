FROM node:alpine
MAINTAINER Louis TREZZINI <louis.trezzini@ponts.org>

RUN mkdir /app && chown node:node /app && chmod 700 /app
WORKDIR /app

RUN apk --no-cache add git
RUN npm install -g -s --no-progress yarn superstatic

USER node

COPY .yarnrc package.json yarn.lock /app/
RUN yarn --dev

COPY . /app/
RUN yarn build

WORKDIR /app
CMD ["superstatic", "-c", "superstatic.json", "--port", "3000", "--host", "0.0.0.0"]
EXPOSE 3000
