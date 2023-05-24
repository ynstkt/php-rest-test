#!/bin/sh

aws s3 rm s3://$AWS_S3_BUCKET_VUE/ --recursive
aws s3 cp dist s3://$AWS_S3_BUCKET_VUE/ --recursive