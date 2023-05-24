import * as cdk from 'aws-cdk-lib';
import { Construct } from 'constructs';

interface AlbEcsAuroraStackProps extends cdk.StackProps {
  vpcId: string;
  phpRepositoryName: string;
  webRepositoryName: string;
  frontendURL: string;
  myIp?: string;
}

export class AlbEcsAuroraStack extends cdk.Stack {
  constructor(scope: Construct, id: string, props: AlbEcsAuroraStackProps) {
    super(scope, id, props);

    // Network
    const vpc = cdk.aws_ec2.Vpc.fromLookup(this, 'Vpc', {
      region: 'ap-northeast-1',
      vpcId: props.vpcId
    });

    const privateSubnets = vpc.selectSubnets({
      subnetType: cdk.aws_ec2.SubnetType.PRIVATE_ISOLATED,
    }).subnets;

    const publicSubnets = vpc.selectSubnets({
      subnetType: cdk.aws_ec2.SubnetType.PUBLIC,
    }).subnets;

    const albSecurityGroup = new cdk.aws_ec2.SecurityGroup(this, 'AlbSecurityGroup', {
      vpc,
    });

    const ecsSecurityGroup = new cdk.aws_ec2.SecurityGroup(this, 'EcsSecurityGroup', {
      vpc,
    });

    const dbSecurityGroup = new cdk.aws_ec2.SecurityGroup(this, 'DbSecurityGroup', {
      vpc,
    });
    if(props.myIp) {
      albSecurityGroup.addIngressRule(
        cdk.aws_ec2.Peer.ipv4(`${props.myIp}/32`),
        cdk.aws_ec2.Port.tcp(80),
        'allow 80 from my IP'
      );
    }
    ecsSecurityGroup.addIngressRule(
      albSecurityGroup,
      cdk.aws_ec2.Port.tcp(80),
      'allow 80 from ALB'
    );
    dbSecurityGroup.addIngressRule(
      ecsSecurityGroup,
      cdk.aws_ec2.Port.tcp(3306),
      'allow 3306 from ECS'
    );

    // Aurora
    const subnetGroup = new cdk.aws_rds.SubnetGroup(this, 'SubnetGroup', {
      vpc,
      description: 'aurora serverless subnet group',
      vpcSubnets: {
        subnets: privateSubnets,
      }
    });
 
    const aurora = new cdk.aws_rds.ServerlessCluster(this, "Aurora", {
      engine: cdk.aws_rds.DatabaseClusterEngine.AURORA_MYSQL,
      vpc,
      subnetGroup,
      securityGroups: [dbSecurityGroup],
      scaling: {
        minCapacity: 1,
        maxCapacity: 2,
        autoPause: cdk.Duration.minutes(5)
      },
      defaultDatabaseName: 'foodb',
    });

    const secretsManager = aurora.secret!;


    // ECS
    const cluster = new cdk.aws_ecs.Cluster(this, 'EcsCluster', {
      vpc,
      enableFargateCapacityProviders: true,
    });

    const fargateTaskDefinition = new cdk.aws_ecs.FargateTaskDefinition(this, 'TaskDef', {
      memoryLimitMiB: 512,
      cpu: 256,
    });

    const phpRepo = cdk.aws_ecr.Repository.fromRepositoryName(this, 'PhpRepository', props.phpRepositoryName);    
    const phpContainerDefinition = fargateTaskDefinition.addContainer('PHPContainer', {
      containerName: 'php',
      image: cdk.aws_ecs.ContainerImage.fromEcrRepository(phpRepo),
      logging: cdk.aws_ecs.LogDrivers.awsLogs({ streamPrefix: 'ecs' }),
      secrets: {
        'DB_HOST': cdk.aws_ecs.Secret.fromSecretsManager(secretsManager, 'host'),
        'DB_PORT': cdk.aws_ecs.Secret.fromSecretsManager(secretsManager, 'port'),
        'DB_USER': cdk.aws_ecs.Secret.fromSecretsManager(secretsManager, 'username'),
        'DB_PASS': cdk.aws_ecs.Secret.fromSecretsManager(secretsManager, 'password'),
        'DB_NAME': cdk.aws_ecs.Secret.fromSecretsManager(secretsManager, 'dbname'),
      },
    });

    const webRepo = cdk.aws_ecr.Repository.fromRepositoryName(this, 'WebRepository', props.webRepositoryName);
    const webContainerDefinition = fargateTaskDefinition.addContainer('WebContainer', {
      containerName: 'web',
      image: cdk.aws_ecs.ContainerImage.fromEcrRepository(webRepo),
      logging: cdk.aws_ecs.LogDrivers.awsLogs({ streamPrefix: 'ecs' }),
      portMappings: [
        {
          containerPort: 80,
          hostPort: 80,
        },
      ],
      environment: {
        ['APP_HOST']: '127.0.0.1',
        ['ORIGIN']: props.frontendURL,
      },
    });

    fargateTaskDefinition.defaultContainer = webContainerDefinition;


    const alb = new cdk.aws_elasticloadbalancingv2.ApplicationLoadBalancer(this, 'ALB', {
      vpc,
      internetFacing: true,
      securityGroup: albSecurityGroup,
    });

    const service = new cdk.aws_ecs_patterns.ApplicationLoadBalancedFargateService(
      this,
      'Service',
      {
        cluster,
        taskDefinition: fargateTaskDefinition,
        taskSubnets: {
          subnets: publicSubnets,
        },
        desiredCount: 1,
        listenerPort: 80,
        loadBalancer: alb,
        openListener: false,
        securityGroups: [
          ecsSecurityGroup,
        ],
        assignPublicIp: true,
      }
    );
  }
}
