import * as cdk from '@aws-cdk/core';
import * as lambda from "@aws-cdk/aws-lambda";
import * as ecr from "@aws-cdk/aws-ecr";
import * as ec2 from "@aws-cdk/aws-ec2"
import * as efs from "@aws-cdk/aws-efs"
import {Handler} from "@aws-cdk/aws-lambda";
import * as fs from 'fs';

export class LambdaStack extends cdk.Stack {
  constructor(scope: cdk.Construct, id: string, props?: cdk.StackProps) {
    super(scope, id, props);

    // Still need to add:
    //
    // * EventBridge cron
    // * A lifecycle for ECR to stop old image stockpiling

    const account:string = fs.readFileSync('aws-account.txt', 'utf8').trim();
    const repo = ecr.Repository.fromRepositoryArn(this, "DockerRegistry",
        `arn:aws:ecr:eu-west-2:${account}:repository/php-lambda`
    );

    // This pokes a port hole for NFS to work
    const network = new ec2.Vpc(this, "LambdaVpc");
    const sg = new ec2.SecurityGroup(this, "SecurityGroup", {
      vpc: network,
      allowAllOutbound: false,
      securityGroupName: "PermitNfsTraffic",
    });
    sg.addIngressRule(ec2.Peer.anyIpv4(), ec2.Port.tcp(2049));
    sg.addEgressRule(ec2.Peer.anyIpv4(), ec2.Port.tcp(2049));

    // Here's the file system to connect to the lambda
    const fileSystem = new efs.FileSystem(this, "Efs", {
      vpc: network,
      encrypted: true,
    });
    const accessPoint = fileSystem.addAccessPoint('AccessPoint', {
      path: '/',
      posixUser: { uid: '0', gid: '0', }
    });

    // And here's the lambda
    const func = new lambda.Function(this, "DockerFunction", {
      runtime: lambda.Runtime.FROM_IMAGE,
      code: lambda.Code.fromEcrImage(repo),
      handler: Handler.FROM_IMAGE,
      securityGroups: [sg, ],
      vpc: network,
      filesystem: lambda.FileSystem.fromEfsAccessPoint(accessPoint, '/mnt/lambda-disk'),
    });
  }
}
