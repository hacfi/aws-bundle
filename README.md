# AWS SDK for PHP Bundle for Symfony


## Install


``` sh
composer require "hacfi/aws-bundle":"dev-master"
```

## Default parameters file

This bundle allows you to specify a **default parameters file** so only have to specify dynamic parameters in your services.

Example:

app/config/config.yml

``` yml
hacfi_aws:
  services:
        aws.s3:
            client: s3
            default_parameters_file: "%kernel.root_dir%/Resources/aws/defaults.yml"
```

app/Resources/aws/defaults.yml
``` yml
s3.ListObjects:
    Bucket: ''lovely_bucket''
```

``` php
$buckets = $this->container->get('aws.s3')->listObjects();
```

Will automatically add the required `Bucket` parameter with the value `lovely_bucket` before sending the request.


## Resolves request parameters with Symfony service parameters

Example:

``` php
$buckets = $s3->listObjects([
    'Bucket' => '%awesome_bucket%',
]);
```

will resolve `%awesome_bucket%` to whatever value is in your service container.

This feature can be disabled by setting the configuration option `resolve_parameters` of the client to `false`. Parameters from a default parameters file will also be resolved.


## Configuration


``` yml

hacfi_aws:
    config: "%kernel.root_dir%/Resources/aws/aws_credentials.json" # global config - used if service doesn’t overwrite config
    default_parameters_file: "%kernel.root_dir%/Resources/aws/defaults.yml" # global default parameters file - used if service doesn’t overwrite default_parameters_file
    services:
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
            client: opsworks
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