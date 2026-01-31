FROM composer:2

RUN apk add --no-cache sqlite

WORKDIR /app