## github-scout

![Imgur](https://i.imgur.com/Ngvk5xd.png)

This service monitor multi github repository promoting data and send report to slack channel periodly.

## Usage
First, create a file name `config.json` put in config directory, you can find a sample config file in `config/config_sample.json`.

Second, build docker image.
```
docker build -t github-scout .
```

Finally, execute it.
```
docker run -d github-scout
```
