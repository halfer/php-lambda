import * as cdk from '@aws-cdk/core';
import * as ecr from "@aws-cdk/aws-ecr";

export class RegistryStack extends cdk.Stack {
    constructor(scope: cdk.Construct, id: string, props?: cdk.StackProps) {
        super(scope, id, props);

        // Need to add:
        //
        // * ECR
        // * A lifecycle for ECR to stop old image stockpiling
    }
}
