# MariaBundle 
[![Build Status](https://travis-ci.com/ibrahimgunduz34/maria-bundle.svg?token=vyj9YGL7pBUY54PzdkJC&branch=master)](https://travis-ci.com/ibrahimgunduz34/maria-bundle)
[![GitHub release](https://img.shields.io/github/release/ibrahimgunduz34/maria-bundle.svg)](https://gitHub.com/ibrahimgunduz34/maria-bundle/releases/)
[![GitHub license](https://img.shields.io/github/license/ibrahimgunduz34/maria-bundle.svg)](https://github.com/ibrahimgunduz34/maria-bundle/blob/master/Resources/docs/LICENSE)

## What is MariaBundle

Maria is a simple and flexible business rule engine that you can integrate easily into 
your Symfony applications through Bundle mechanism. It allows taking 
an action based on the rules when the input data matched. You can 
trigger Maria by a trigger event you defined. It checks the input 
argument which comes through the trigger event by the rules and 
invokes the action handler when matching occurred. Action handlers 
might be a class or a reference that points to a service definition 
in the dependency injection system. So you can communicate with other Symfony 
components easily through action handlers on Maria scenarios.

## Installation

You can install Maria through composer

```bash
$ composer require ibrahimgunduz34/maria-bundle
```

Add the bundle class to the bundle list by your Symfony version:

### For Symfony 3.x users:

**AppKernel.php**
 
```php
<?php
///...
public function registerBundles()
{
    $bundles = array(
        //...
        new SweetCode\MariaBundle\MariaBundle(),
        //...
    );
}
//...
``` 
 
### For Symfony >= 4 users:

**config/bundles.php**

```php
<?php
return [
  //...
   SweetCode\MariaBundle\MariaBundle::class => ['all' => true],
  //...  
];
```

## Configuration Reference
```yaml
# config/packages/maria.yaml
maria:
    scenarios_name:
      trigger: <The event name you will trigger>
      handler: <Class name or service reference>
#      handler:
#        reference: <Class or service reference from DI>
#        method: <handler method name, default: onAction>
#        serialize: <true | false, default: false>
      rules:
        # You can define matchers as any, all, none, first or last matcher 
        # in the first line. If you won't an iterable object, you can simply
        # use default or ignore the first line. 
        default: # [any | all | none | first | last | default ]
          # You can define rules by numeric or associative arrays.
          
          # Associative arrays indicate that you will apply AND logic between the 
          # elements in the array
          
          # Numeric arrays indicate that you will apply OR logic between the 
          # elements in the array.
          
          # amount > 100 AND category_id IN [1, 2, 3]
          amount: {gt: 100}
          category_id: {in: [1,2,3]}   
          
          # (amount > AND category_id=1) OR (amount < 500 AND category_id IN [5,6])
          - amount: {gt: 100}
            category_id: {eql: 1}
          - amount: [lt: 500]
            category_id: [in: [5,6]]
            
          # (category_id IN [1,2] AND amount BETWEEN 100-200) OR (category_id = 3 AND amount >= 200
          - amount: [btw: [100, 200]]
            category_id: [in: [1, 2]]
          - amount: [lte: 200]
            category_id: [eql: 3]                 
```

## Example Usage

Define the following configuration into `config/packages/maria.yaml`

```yaml
maria:
    free_shipment:
        trigger: cart.updated
        handler: App\Handler\FreeShipmentHandler
        rules:
          amount: {gt: 100}
          category_id: {eql: 1}
```

Create a handler class in order to take an action to give free shipment

```php
<?php
namespace App\Handler;

use SweetCode\MariaBundle\MariaEventArg;

class FreeShipmentHandler
{
    public function onAction(MariaEventArg $eventArg){
        //TODO: Write your magic code to give free shipment.
        // You can reach your original input data by:
        // $eventArg->getData();
    }
}
```

Trigger Maria where you updated cart in the project.
```php
<?php
//...
/** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface $eventDispatcher */
$eventDispatcher = $container->get('event_dispatcher');
// You can simply send an object or associative array to Maria through event context.
$context = [
    'amount'        => $order->getAmount(),
    'category_id'   => $order->getCategoryId(),
]
$eventDispatcher->dispatch('cart.updated', new \SweetCode\MariaBundle\MariaEventArg($context));
//...
```

## Working In Asynchronous Way
Maria does not provide a way to invoke action handlers as asynchronous. However, you can make 
action handlers asynchronous easily by using other third-parties like `RabbitMqBundle` or 
built-in Symfony components like `Messenger` You can make your action handler asynchronous by 
following the steps below:

**Important Notice:** We strongly recommend to follow the installation steps from `rabbitmq-bundle` repository:
[https://github.com/php-amqplib/RabbitMqBundle](https://github.com/php-amqplib/RabbitMqBundle)

Install RabbitMqBundle
```bash
$ composer require php-amqplib/rabbitmq-bundle
```

Add the bundle class to bundle the bundle list in the project:

### For Symfony 3.x users:

**AppKernel.php**
 
```php
<?php
///...
public function registerBundles()
{
    $bundles = array(
        //...
        new OldSound\RabbitMqBundle\OldSoundRabbitMqBundle(),
        //...
    );
}
//...
``` 
 
### For Symfony >= 4 users:

**config/bundles.php**

```php
<?php
return [
  //...
   OldSound\RabbitMqBundle\OldSoundRabbitMqBundle::class => ['all' => true],
  //...  
];
```

Configure the RabbitMq bundle.
```yaml
# config/packages/rabbitmq.yaml

old_sound_rabbit_mq:
    connections: 
        host: my.rabbitmq.host
        user: rabbitmq
        password: rabbitmq
        vhost: '/'
        lazy: false
        connection_timeout: 3
        read_write_timeout: 3
        keepalive: false
        heartbeat: 0
        use_socket: true
    producers:
        email_producer:
            connection: default
            exchange_options: {name: 'emails', type: direct}
            service_alias: email_producer # otherwise it gives very long service name
    consumers:
      email_consumer:
        exchange_options: {name: 'emails', type: direct}
        queue_options: {name: 'emails'} 
        # That's the service you need to implement as a consumer.
        # Check the documentation from the repository to see how to implement a consumer:
        # https://github.com/php-amqplib/RabbitMqBundle#consumers
        callback: email_sender_service 
```


And.. say maria, use `email_producer` as an action handler.

```yaml
maria:
    gift-email:
        trigger: some.event
        handler: 
          reference: '@email_producer'
          method: 'publish'
          erialize: true
        rules:
          ##...

```

Enjoy!

## TODO:
* Moving the scenario rules into different type of storage providers such as `in_memory` or `doctrine`
* Validation improvement for configuration.

## License:
You can access the license documentation [here](/Resources/docs/LICENSE).

## Credits:
Bundles structure, extension tests and the documentation partially inspired `RabbitMqBundle`.
