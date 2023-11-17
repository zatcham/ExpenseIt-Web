# In all environments, the following files are loaded if they exist,
# the latter taking precedence over the former:
#
#  * .env                contains default values for the environment variables needed by the app
#  * .env.local          uncommitted file with local overrides
#  * .env.$APP_ENV       committed environment-specific defaults
#  * .env.$APP_ENV.local uncommitted environment-specific overrides
#
# Real environment variables win over .env files.
#
# DO NOT DEFINE PRODUCTION SECRETS IN THIS FILE NOR IN ANY OTHER COMMITTED FILES.
# https://symfony.com/doc/current/configuration/secrets.html
#
# Run "composer dump-env prod" to compile .env files for production use (requires symfony/flex >=1.2).
# https://symfony.com/doc/current/best_practices.html#use-environment-variables-for-infrastructure-configuration

###> symfony/framework-bundle ###
APP_ENV=prod
APP_SECRET=ff594cbdd004395787d6921fe9669228
###< symfony/framework-bundle ###

###> doctrine/doctrine-bundle ###
# Format described at https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url
# IMPORTANT: You MUST configure your server version, either here or in config/packages/doctrine.yaml
#
# DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
# DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8.0.32&charset=utf8mb4"
 DATABASE_URL="mysql://root:@127.0.0.1:3306/expense_app_test?serverVersion=10.4.27-MariaDB&charset=utf8mb4"
# DATABASE_URL="postgresql://vatghlqzfxgwui:446adc2f32dcf107df8baa0e5634ef4358d21165e96dbce29c8d75820d7de620@ec2-52-215-68-14.eu-west-1.compute.amazonaws.com:5432/df30g822jt7grc?serverVersion=15.5&charset=utf8"
###< doctrine/doctrine-bundle ###

###> symfony/messenger ###
# Choose one of the transports below
# MESSENGER_TRANSPORT_DSN=amqp://guest:guest@localhost:5672/%2f/messages
# MESSENGER_TRANSPORT_DSN=redis://localhost:6379/messages
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
###< symfony/messenger ###

###> symfony/mailer ###
# MAILER_DSN=smtp://calid@zm-it.co.uk:Ox46ed80a@smtp.zm-it.co.uk:465
###< symfony/mailer ###

###> symfony/mailgun-mailer ###
MAILER_DSN=mailgun://68c02eb2d821eb1560b55d4851ae93ae-8c9e82ec-cb1da23c:mg.zmit.co.uk@default?region=eu
# MAILER_DSN=mailgun+smtp://USERNAME:PASSWORD@default?region=us
###< symfony/mailgun-mailer ###

### For JWT API Auth
###> lexik/jwt-authentication-bundle ###
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=12224b234fadfa6d93d6e36456fbab65be8d3e31d62a526a0f824cd2f99e9fd9
###< lexik/jwt-authentication-bundle ###