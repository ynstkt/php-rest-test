#!/usr/bin/env node
import 'source-map-support/register';
import * as cdk from 'aws-cdk-lib';
import { AlbEcsAuroraStack } from '../lib/alb-ecs-aurora-stack';

const app = new cdk.App();

const env = {
  account: '<アカウントID>',
  region: 'ap-northeast-1',
  // account: process.env.CDK_DEFAULT_ACCOUNT,
  // region: process.env.CDK_DEFAULT_REGION
};

new AlbEcsAuroraStack(app, 'AlbEcsAuroraStack', {
  vpcId: process.env.VPC_ID!,
  phpRepositoryName: 'php-test',
  webRepositoryName: 'web-test',
  frontendURL: process.env.FRONTEND_URL!,
  myIp: process.env.MY_IP,
  env,
});