# Verify Uploads to an Amazon S3 Bucket with SlimPHP using Twilio Verify

## About

This project is a small codebase that supports a tutorial which I wrote for the Twilio blog. The tutorial shows how to, albeit simplistically, verify image uploads to an Amazon S3 bucket using SlimPHP and Twilio Verify. It is not intended to be an example of best practices, rather to exemplify one specific approach. Check out the article to get the full story.

## Getting Started

To get started using the project, first clone the project locally, then change into the newly created project directory and install the project's dependencies. You can do all of this by running the commands below.

```bash
git clone git@github.com:settermjd/verify-aws-s3.git
cd verify-aws-s3
composer install
npm install
```

After that, launch the application by running the command below.


```bash
php -S 0.0.0.0:8080 -t public
```

The application will now be available on localhost, on port 8080. Feel free to change the port number, if you wish. For more information about how to use the application, check out the accompanying article on the Twilio blog.
