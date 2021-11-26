PHP Lambda
===

Introduction
---

This repo is a bit of a self-training project, and a bit of an exercise of curiosity - I wanted to see how
to run AWS lambdas in PHP, now that they can be backed by a Docker container. I have a use-case where I
want to call some APIs on a schedule, and running a lambda based on an EventBridge seems like an interesting,
serverless, and low-cost way to do that.

The project isn't all PHP - I use TypeScript to deploy the project using the AWS CDK library.

Another interesting side-project here shall be the bridging of EFS to Lambda in order to make use of SQLite.
Yes, you heard that right - I want to try out SQLite for a real project, and to demonstrate that there are
cheaper serverless alternatives to RDS for low-volume use-cases. Have a read of the SQLite docs for
[many interesting real-world uses](https://sqlite.org/whentouse.html).

This project can be thought of a PHP equivalent to my [StackWatcher](https://github.com/halfer/stackwatcher)
project, which is an unfinished JavaScript version that runs in the Mongo Stitch environment. I'd like to get
back to that, but that environment forces the involvement of Mongo, and I have mixed feelings about NoSQL - it
seems to often be used because it is cheap, rather than because it is _appropriate_.

Ideas
---

From a toolkit perspective it would be great to set up these things:

* A config file to specify what AWS features are required (EFS, pdo_sqlite, Cron, etc.)
* A simple way to kick off CDK on a dev machine with only Docker as a dependency
* A command to deploy a lambda via an AWS call and run a "ping" command

Status
---

At the time of writing:

* the Docker image builds
* the image can be pushed to AWS ECR
* I have built some AWS infra (Lambda, VPC, ECR, EFS)
* the PHP lambda is callable from the AWS dashboard
* the lambda mediator code is unit-tested
* the lambda can write to EFS
* the CDK deployment creates a working PHP lambda with EFS write access

Here is what does not work:

* The lambda doesn't do anything useful
* SQLite isn't yet in use
* The CDK needs a cron

However, I think there is enough code here for it to be interesting to readers.
