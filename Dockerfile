FROM php:7.2

# install cron
RUN apt-get update && apt-get install -y cron

# copy scout and setup crontab
RUN mkdir /github-scout
COPY . /github-scout
RUN cp /github-scout/run-scout-cron /etc/cron.d/run-scout-cron ; \
    crontab /etc/cron.d/run-scout-cron

# run cron
CMD ["cron", "-f"]
