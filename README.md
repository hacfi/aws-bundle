# AWS SDK for PHP Bundle for Symfony


## Install


``` sh
composer require "hacfi/aws-bundle":"dev-master"
```

## Resolves request parameters with Symfony service parameters

Example:

``` php
$buckets = $s3->listObjects([
    'Bucket' => '%awesome_bucket%',
]);
```

This feature can be disabled by setting the configuration option `resolve_parameters` of the client to `false`.

## Configuration


``` yml

hacfi_aws:
    aws.aws:
        client: aws
    aws.aws2: ~ # defaults to 'aws' client
    aws.autoscaling:
        client: autoscaling
        config: "%kernel.root_dir%/Resources/aws/aws_credentials.json"
    aws.cloudformation:
        client: cloudformation
        config:
            region: "%aws_region%"
            key: "%aws_key%"
            secret: "%aws_secret%"
    aws.cloudfront:
        client: cloudfront
    aws.cloudfront_20120505:
        client: cloudfront_20120505
    aws.cloudhsm:
        client: cloudhsm
        config:
    aws.cloudsearch:
        client: cloudsearch
    aws.cloudsearch_20110201:
        client: cloudsearch_20110201
    aws.cloudsearchdomain:
        client: cloudsearchdomain
    aws.cloudtrail:
        client: cloudtrail
    aws.cloudwatch:
        client: cloudwatch
    aws.cloudwatchlogs:
        client: cloudwatchlogs
    aws.cognito.identity:
        client: cognito-identity
    aws.cognitoidentity:
        client: cognitoidentity
    aws.cognito.sync:
        client: cognito-sync
    aws.cognitosync:
        client: cognitosync
    aws.codedeploy:
        client: codedeploy
    aws.config:
        client: config
    aws.datapipeline:
        client: datapipeline
    aws.directconnect:
        client: directconnect
    aws.dynamodb:
        client: dynamodb
    aws.dynamodb_20111205:
        client: dynamodb_20111205
    aws.ec2:
        client: ec2
    aws.ecs:
        client: ecs
    aws.elasticache:
        client: elasticache
    aws.elasticbeanstalk:
        client: elasticbeanstalk
    aws.elasticloadbalancing:
        client: elasticloadbalancing
    aws.elastictranscoder:
        client: elastictranscoder
    aws.emr:
        client: emr
    aws.glacier:
        client: glacier
    aws.kinesis:
        client: kinesis
    aws.kms:
        client: kms
    aws.lambda:
        client: lambda
    aws.iam:
        client: iam
    aws.importexport:
        client: importexport
    aws.machinelearning:
        client: machinelearning
    aws.opsworks:
    aws.rds:
        client: rds
    aws.redshift:
        client: redshift
    aws.route53:
        client: route53
    aws.route53domains:
        client: route53domains
    aws.s3:
        client: s3
    aws.sdb:
        client: sdb
    aws.ses:
        client: ses
    aws.sns:
        client: sns
    aws.sqs:
        client: sqs
    aws.ssm:
        client: ssm
    aws.storagegateway:
        client: storagegateway
    aws.sts:
        client: sts
    aws.support:
        client: support
    aws.swf:
        client: swf
    aws.workspaces:
        client: workspaces
```