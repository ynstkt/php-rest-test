FROM mysql:8.0-debian

RUN apt-get update && apt-get install nkf locales -y && localedef -i ja_JP -c -f UTF-8 -A /usr/share/locale/locale.alias ja_JP.UTF-8
ENV LANG ja_JP.utf8

EXPOSE 3306