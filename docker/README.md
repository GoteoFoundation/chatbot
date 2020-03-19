# Using Docker with Goteo-Chatbot

We have two docker versions, one for development and another for production.

In order to use the development version, please refer to the main [README](../README.md) file.

# Using chatbot docker in production

Use the docker image `goteo/chatbot:latest`, this image includes all the compiled code. It does not include any web server (such as nginx), only the CGI `php-fpm`. In order to run it in production you will need to add a database and a web server for proxy. 

Use the provided `docker/prod/docker-compose.yml` as an example.

**INITIALIZATION:**

The first time the container runs it will create the required database and the first admin user. By default, the user is `admin@example.org` with the password `chatbot123456`. You can change this by overriding the environment variables:

```
ADMIN_USER=admin@example.org
ADMIN_PASSWORD=chatbot123456
```

**IMPORTANT:** 

- Change any environment variable required (specially secrets)
- Generate your own Laravel `APP_KEY` environment variable. In order to do that, you can execute `docker-compose up -d` and then this command:

```
docker-compose exec chatbot artisan key:generate --show
```

Check the rest of customizable vars in the file [.env.prod](.env.prod) (this is just an example).

# Building Docker in production

This application is available as a Docker image in:
https://cloud.docker.com/u/goteo/repository/docker/goteofoundation/chatbot

To build a new image we use the `docker/prod/Dockerfile` file. First be sure to be on the root of the repository, then run:

Ensure you are working on a clean environment:

```
rm -r docker/src/*
git archive master | tar -x -C docker/src
```

Buld docker:

```
sudo docker build . -t goteo/chatbot:VERSION -f docker/prod/Dockerfile
```

To upload it to the Docker hub (permissions needed):

```
sudo docker login
sudo docker push goteo/chatbot:VERSION
sudo docker tag goteo/chatbot:VERSION goteo/chatbot:latest
sudo docker push goteo/chatbot:latest
```
