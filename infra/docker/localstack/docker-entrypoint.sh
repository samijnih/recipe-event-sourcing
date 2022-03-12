#!/bin/bash

aws configure set aws_access_key_id $AWS_ACCESS_KEY_ID
aws configure set aws_secret_access_key $AWS_SECRET_ACCESS_KEY
aws configure set default.region $AWS_REGION
aws configure set region $AWS_REGION

echo 'Configuration in progress'
#awslocal sqs --no-verify-ssl --output json --region $AWS_REGION --endpoint-url=http://localhost:4566 --color on
