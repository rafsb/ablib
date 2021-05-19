cd $(dirname $0)
mkdir -p ../var/logs
docker-compose up --detach --force-recreate --build
docker exec -it app zsh
