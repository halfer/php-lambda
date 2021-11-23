import * as cdk from '@aws-cdk/core';
import * as lambda from "@aws-cdk/aws-lambda";
import * as ecr from "@aws-cdk/aws-ecr";
import * as ec2 from "@aws-cdk/aws-ec2"
import {Handler} from "@aws-cdk/aws-lambda";

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

    const repo = ecr.Repository.fromRepositoryArn(this, "DockerRegistry",
        "arn:aws:ecr:region:111111111111:php-lambda/latest"
    );

    const vpc = new ec2.Vpc(this, "LambdaVpc", {

    });

    const func = new lambda.Function(this, "DockerFunction", {
      runtime: lambda.Runtime.FROM_IMAGE,
      code: lambda.Code.fromEcrImage(repo),
      handler: Handler.FROM_IMAGE,
      securityGroups: [],
      //vpc: vpc
    });

    // example resource
    // const queue = new sqs.Queue(this, 'Src2Queue', {
    //   visibilityTimeout: cdk.Duration.seconds(300)
    // });
  }
}
