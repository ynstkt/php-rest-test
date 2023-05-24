FROM node:16

RUN mkdir -p /usr/src/app
WORKDIR /usr/src/app

RUN npm -g i npm
RUN npm -g config set user root
RUN npm install -g typescript aws-cdk

# install aws cli v2
RUN curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip" \
    && unzip awscliv2.zip \
    && ./aws/install
