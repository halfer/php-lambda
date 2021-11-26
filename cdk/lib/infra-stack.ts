import * as cdk from '@aws-cdk/core';
import * as lambda from "@aws-cdk/aws-lambda";
import * as ecr from "@aws-cdk/aws-ecr";
import * as ec2 from "@aws-cdk/aws-ec2"
//import * as efs from "@aws-cdk/aws-efs"
import {Handler} from "@aws-cdk/aws-lambda";
import * as fs from 'fs';

export class InfraStack extends cdk.Stack {
  constructor(scope: cdk.Construct, id: string, props?: cdk.StackProps) {
    super(scope, id, props);

    // Plan:
    //
    // * A lambda based on a Docker image
    // * Permissions on the lambda
    // * EFS
    // * A VPC to connect between EFS & lambda
    // * A security group to poke a hole for NFS
    // * EventBridge cron
    // * A lifecycle for ECR to stop old image stockpiling

    const account:string = fs.readFileSync('aws-account.txt', 'utf8').trim();
    const repo = ecr.Repository.fromRepositoryArn(this, "DockerRegistry",
        `arn:aws:ecr:eu-west-2:${account}:repository/php-lambda`
    );

    const network = new ec2.Vpc(this, "LambdaVpc", {
    });

    const sg = new ec2.SecurityGroup(this, "SecurityGroup", {
      vpc: network,
      allowAllOutbound: false,
      securityGroupName: "PermitNfsTraffic",
    });
    sg.addIngressRule(ec2.Peer.anyIpv4(), ec2.Port.tcp(2049));
    sg.addEgressRule(ec2.Peer.anyIpv4(), ec2.Port.tcp(2049));

    // const fileSystem = new efs.FileSystem(this, "Efs", {
    //   vpc: network,
    //   encrypted: true,
    // });

    const func = new lambda.Function(this, "DockerFunction", {
      runtime: lambda.Runtime.FROM_IMAGE,
      code: lambda.Code.fromEcrImage(repo),
      handler: Handler.FROM_IMAGE,
      securityGroups: [],
      //vpc: network,
    });
  }
}
