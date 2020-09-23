VERSION=6.0.5
PORT=6379
VOLUMES=$PWD/volumes
PWD=123456
docker run -d --name redis-${VERSION} -p ${PORT}:6379 -v ${VOLUMES}:/data redis:${VERSION} --appendonly yes --requirepass ${PWD}