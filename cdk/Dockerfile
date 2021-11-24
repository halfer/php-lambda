# At the time of writing, CDK has not been tested with Node 17.x
FROM node:16.3-alpine

# Update operating system
RUN apk update && apk upgrade

# Update NPM (image is a major version out of date)
RUN npm install -g npm@8.1.4

WORKDIR /project

CMD ["/bin/sh"]
