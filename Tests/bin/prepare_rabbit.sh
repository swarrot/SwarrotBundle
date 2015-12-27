#!/bin/sh

set -e

echo "# Preparing vhost"
rabbitmqctl delete_vhost swarrot || true
rabbitmqctl add_vhost swarrot
rabbitmqctl set_permissions -p swarrot guest ".*" ".*" ".*"

echo "# Enable rabbitmq_management plugin"
rabbitmq-plugins enable rabbitmq_management

if ! type "rabbitmqadmin" > /dev/null
then
    echo "# Installing rabbitmqadmin"
    curl -XGET http://127.0.0.1:15672/cli/rabbitmqadmin > /usr/local/bin/rabbitmqadmin
    chmod +x /usr/local/bin/rabbitmqadmin
fi

echo "# Declaring mapping"
rabbitmqadmin declare exchange name=exchange type=direct auto_delete=false durable=true --vhost=swarrot
