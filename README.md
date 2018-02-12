## github-scout

## Usage
First, create a file name `config.json` put in config, you can find a sample config file in `config/config_sample.json`.

Second, build docker image.
```
docker build -t github-scout .
```

Finally, execute it.
```
docker run -d github-scout
```
