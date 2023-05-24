FROM nginx:1.23-alpine as base

CMD envsubst \
    '$$APP_HOST $$ORIGIN' \
    < /etc/nginx/conf.d/default.conf.template > \
    /etc/nginx/conf.d/default.conf \
    && nginx -g 'daemon off;'


FROM base as prod

COPY settings/default.conf.template /etc/nginx/conf.d/default.conf.template
