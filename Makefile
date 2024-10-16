build:
	export DOCKER_BUILDKIT=1 && docker-compose -p rest-advanced -f docker/docker-compose.yml up -d --build --force-recreate

down:
	export DOCKER_BUILDKIT=1 && docker-compose -p rest-advanced -f docker/docker-compose.yml stop

build-stage:
	export DOCKER_BUILDKIT=1 && docker-compose -p rest-advanced -f docker/docker-compose-stage.yml up -d --build --force-recreate

down-stage:
	export DOCKER_BUILDKIT=1 && docker-compose -p rest-advanced -f docker/docker-compose-stage.yml stop
