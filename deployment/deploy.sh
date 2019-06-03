#!/usr/bin/env bash

docker build -t spikelabs/corebos-gdpr:latest -t spikelabs/corebos-gdpr:$SHA -f ./deployment/Dockerfile .

docker push spikelabs/corebos-gdpr:latest

docker push spikelabs/corebos-gdpr:$SHA

curl --header "Content-Type: application/json" \
 --request POST \
 --data '{"token": "'"$COREBOS_ADMIN_TOKEN"'","image_tag":"spikelabs/corebos-gdpr:'"$SHA"'"}' \
 https://k8admin.evolutivo.it/api/update_client_image