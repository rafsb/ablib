cd $(dirname $0)
mkdir -p ../var/logs
docker-compose up --detach --force-recreate --build --remove-orphans
docker exec -it app zsh
