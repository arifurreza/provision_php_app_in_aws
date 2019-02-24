# provision_php_app_in_aws
Provision a highly available and scalable architecture that hosts a PHP application in AWS via CloudFormation

## Introduction

I'm using Amazon Web Service infrastructure for deployment sample PHP application and automation part i'm using CloudFormation. 

- Why use CloudFormation?

There are too many service available in AWS for deploying app ex. (Elastic Beanstalk, Opswork..) but all are using CloudFormation in underlaying part. CloudFormation covers almost all bits and parts of AWS. CloudFormation manages state within the managed service out-of-the-box which is a small plus compared to other. And it's supporting rolling updates for Auto Scaling Groups.


## CloudFormation Template

1. [Goal](#Goal)
2. [Design](#Design)
2. [Installation](#Installation)
3. [Deployment](#Deployment)



## Goal
- This template `cloudformation_template.yml` deploy sample PHP application with your given necessary requirements. 
- Sending a HTTP request, you should return User registration form.
- This can be run on any region.
- User can change the necessary parameter

## Design

![Architecture Diagram](https://github.com/arifurreza/provision_php_app_in_aws/blob/master/diagram.png)


## Installation

Specs:
- VPC
- 2 private & 2 public subnet (User can change the IP range of any subnet)
- Internet gateway
- Nat gateway
- Instance Type: t2.micro (User can change the instance type as parameter input)
- AMI: Amazon Linux AMI
- Web Server - NGINX
- Application ELB
- Redirect http to https
- SSL Termination
- Launch configuration
- Auto scaling group
- SNS topic
- Scaling policy
- RDS(MySQL)-Instance db.t2.micro

1. Should have necessary privilege on aws
2. Create a common key pair for accessing Application Servers
3. Create AWS SSL certificate or you can import SSL certificate
3. You are deploying CloudFormation Templates via AWS console or aws cli with necessary parameter

## Deployment

Download the zip file which already shared or you can clone the git repo to your local machine

`git clone https://github.com/arifurreza/provision_php_app_in_aws.git`

Login AWS console then go the CloudFormation Service then create stack then upload `cloudformation_template.yml` then specify the details parameter and create the stack.

or you can create the stack via aws cli


`aws --region <> cloudformation create-stack --stack-name <> --parameters ParameterKey=KeyName,ParameterValue=<> ParameterKey=OperatorEMail,ParameterValue=<> ParameterKey=CertificateArn,ParameterValue=<> --template-body file://cloudformation_template.yml`

If all information is successfull then you got the `StackId`

After that you can wait until stack creation complete. If the creation complete then you can check the Output via AWS CLI

`aws cloudformation describe-stacks --query 'Stacks[0].Outputs[?OutputKey==`WebsiteURL`].OutputValue' --output <>`

Things achieved by executing the template:

- Create VPC with 4 subnets in 2 availability jone, 2 are public vpc which connected in Internet gateway which is created by the template and 2 are private vpc which connected in Nat gateway which is created by the template.

- Create a SNS topic which is used for cloud watch notification for below type

	- autoscaling:EC2_INSTANCE_LAUNCH
	- autoscaling:EC2_INSTANCE_LAUNCH_ERROR
	- autoscaling:EC2_INSTANCE_TERMINATE
	- autoscaling:EC2_INSTANCE_TERMINATE_ERROR
	
- Create a Application Loadbalancer which has two subnets and it's listen with http and https protocol but all the trafic redirect to https protocol so it's needs a certificate which mentioned in parameter group user gives the certificate arn on parameter. And it's forward all the traffic to ALBTargetGroup which basically connected to ec2 via 80 port.

- create launch configuration which has install necessary package for run the application such as (php, nginx ....) one thing we need to install cfn for resource signaling to autoscaling group.

- nginx configuration & php configuration(`default.conf & www.conf`) on my public repo which is clone on init script and put it to appropriate place.

- Create WebServerGroup for autoscaling which min size 2 & max size 5 users can select desired size on parameter and which has enable Rolling update also it has a scaling policy on CPU, Scale-up if CPU > 90% for 10 minutes & Scale-down if CPU < 70% for 10 minutes.

- Create 2 cloud watch alarm for scale down and scale up.
- Create a RDS(MySql) Instace & RDS security group
- At the end, you can output the Loadbalancer DNS address.
- Now you can change the database config file on `provision_php_app_in_aws/my_test_app/api/config/database.php`
- Also you create database schema via `mysql -h <db url> -P 3306 -u <db user> -p < provision_php_app_in_aws/my_test_app/photoupload.sql`

## Values

- Try to make it clean, i think everyone evolve easily.

- Only use CloudFormation & bash scripting.

- New version deployment with no downtime, we can use Blue-Green Deployment Strategy. It is one common way to efficiently use separate stacks to deploy an application update to production.

	- The blue environment is the production stack, which hosts the current application.
	- The green environment is the staging stack, which hosts the updated application.
	
When you are ready to deploy the updated app to production, you switch user traffic from the blue stack to the green stack, which becomes the new production stack. You then retire the old blue stack.
- If you localy monitor your infrastructure then you can use Netdata, Which is open source as well as you can configure email and push messeages.
