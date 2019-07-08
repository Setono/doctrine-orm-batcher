# Doctrine ORM Batcher library

![Packagist Version](https://img.shields.io/packagist/v/setono/doctrine-orm-batcher.svg?color=brightgreen&label=latest%20release)
![Packagist Pre Release Version](https://img.shields.io/packagist/vpre/setono/doctrine-orm-batcher.svg?label=unstable)
![Packagist](https://img.shields.io/packagist/l/setono/doctrine-orm-batcher.svg?color=blue)
![Travis (.com)](https://img.shields.io/travis/com/setono/doctrine-orm-batcher.svg)
![Scrutinizer code quality (GitHub/Bitbucket)](https://img.shields.io/scrutinizer/quality/g/setono/doctrine-orm-batcher.svg)

Use this library when you need to process large amounts of entities and maybe in an asynchronous way.

Why do we need this library? Why not just use a paginator library like [Pagerfanta](https://github.com/whiteoctober/Pagerfanta) or normal [batch processing in Doctrine](https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/batch-processing.html)?

Well, because MySQL is [not very good with LIMIT and OFFSET](https://www.eversql.com/faster-pagination-in-mysql-why-order-by-with-limit-and-offset-is-slow/) 
when the tables become too large. As for Doctrine batch processing capabilities the difference is that this 
library is very opinionated. It will work very well in a message based architecture where large processing will 
likely be done in an asynchronous way.

How does it work then? It uses the [seek method](https://www.google.com/search?q=mysql+seek+method) to paginate results instead.

## Installation

```bash
$ composer require setono/doctrine-orm-batcher
```

## Usage

There are two ways to get results: Getting a range of ids or getting a collection (either of ids or entities).

### Range of ids
A range is a lower and upper bound of ids. This is typically intended to be used in an asynchronous environment
where you will dispatch a message with the lower and upper bounds so that the consumer of that message
will be able to easily fetch the respective entities based on these bounds.

**Example**

You want to process all your `Product` entities. A query builder for that would look like:

```php
<?php
use Doctrine\ORM\EntityManagerInterface;

/** @var EntityManagerInterface $em */

$qb = $em->createQueryBuilder();
$qb->select('o')->from(Product::class, 'o');
```

Now inject that query builder into the id range batcher and dispatch a message:

```php
<?php
use Setono\DoctrineORMBatcher\Batch\RangeBatch;
use Setono\DoctrineORMBatcher\Factory\BestIdRangeBatcherFactory;

class ProcessProductBatchMessage
{
    private $batch;
    
    public function __construct(RangeBatch $batch)
    {
        $this->batch = $batch;        
    }
    
    public function getBatch(): RangeBatch
    {
        return $this->batch;
    }
}

$factory = new BestIdRangeBatcherFactory();
$bestChoiceIdBatcher = $factory->create($qb);

$batches = $bestChoiceIdBatcher->getBatches(50);

foreach ($batches as $batch) {
    $commandBus->dispatch(new ProcessProductBatchMessage($batch));
}
```

Then sometime somewhere a consumer will receive that message and process the products:

```php
<?php
class ProcessProductBatchMessageHandler
{
    public function __invoke(ProcessProductBatchMessage $message)
    {
        $query = $em->createQuery($message->getBatch()->getDql());
        $query->setParameters($message->getBatch()->getParameters());
        $products = $query->getResult();
        
        foreach ($products as $product) {
            // process $product
        }
    }
}
```

This approach is *extremely* fast, but if you have complex queries it may be easier to use the collection batchers.

[ico-version]: https://poser.pugx.org/setono/doctrine-orm-batcher/v/stable
[ico-unstable-version]: https://poser.pugx.org/setono/doctrine-orm-batcher/v/unstable
[ico-license]: https://poser.pugx.org/setono/doctrine-orm-batcher/license
[ico-travis]: https://travis-ci.com/Setono/doctrine-orm-batcher.svg?branch=master
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Setono/doctrine-orm-batcher.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/setono/doctrine-orm-batcher
[link-travis]: https://travis-ci.com/Setono/doctrine-orm-batcher
[link-code-quality]: https://scrutinizer-ci.com/g/Setono/doctrine-orm-batcher
