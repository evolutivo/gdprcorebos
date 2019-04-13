#!/usr/bin/env bash

docker build -t spikelabs/corebos-gdpr:latest -t spikelabs/corebos-gdpr:$SHA -f ./deployment/Dockerfile .

docker push spikelabs/corebos-gdpr:latest

docker push spikelabs/corebos-gdpr:$SHA

