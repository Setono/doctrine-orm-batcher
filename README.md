# Doctrine ORM Batcher library

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![Quality Score][ico-code-quality]][link-code-quality]

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
use Setono\DoctrineORMBatcher\Batcher\Collection\ObjectCollectionBatcher;
use Setono\DoctrineORMBatcher\Batcher\Collection\IdCollectionBatcher;
use Setono\DoctrineORMBatcher\Batcher\Range\NaiveIdRangeBatcher;
use Setono\DoctrineORMBatcher\Batcher\Range\IdRangeBatcher;
use Setono\DoctrineORMBatcher\Factory\BatcherFactory;

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

$factory = new BatcherFactory(
    ObjectCollectionBatcher::class,
    IdCollectionBatcher::class,
    NaiveIdRangeBatcher::class,
    IdRangeBatcher::class
);
$idRangeBatcher = $factory->createIdRangeBatcher($qb);

/** @var RangeBatch[] $batches */
$batches = $idRangeBatcher->getBatches(50);
foreach ($batches as $batch) {
    $commandBus->dispatch(new ProcessProductBatchMessage($batch));
}
```

Then sometime somewhere a consumer will receive that message and process the products:

```php
<?php
use Setono\DoctrineORMBatcher\Query\QueryRebuilderInterface;

class ProcessProductBatchMessageHandler
{
    public function __invoke(ProcessProductBatchMessage $message)
    {
        /** @var QueryRebuilderInterface $queryRebuilder */
        $q = $queryRebuilder->rebuild($message->getBatch());
        $products = $q->getResult();
        
        foreach ($products as $product) {
            // process $product
        }
    }
}
```

This approach is *extremely* fast, but if you have complex queries it may be easier to use the collection batchers.

### Collection of ids

Should be used for async handling of sets that selected with complex queries.

**Example**

You want to process only enabled `Product` entities.

```php
<?php

use Doctrine\ORM\EntityManagerInterface;
use Setono\DoctrineORMBatcher\Factory\BatcherFactory;
use Setono\DoctrineORMBatcher\Batch\CollectionBatch;
use Setono\DoctrineORMBatcher\Batcher\Collection\ObjectCollectionBatcher;
use Setono\DoctrineORMBatcher\Batcher\Collection\IdCollectionBatcher;
use Setono\DoctrineORMBatcher\Batcher\Range\NaiveIdRangeBatcher;
use Setono\DoctrineORMBatcher\Batcher\Range\IdRangeBatcher;

class ProcessEnabledProductBatchMessage
{
    /** @var CollectionBatch */
    private $batch;
    
    public function __construct(CollectionBatch $batch)
    {
        $this->batch = $batch;        
    }
    
    public function getBatch(): CollectionBatch
    {
        return $this->batch;
    }
}

/** @var EntityManagerInterface $em */
$qb = $em->createQueryBuilder();
$qb->select('o')
    ->from(Product::class, 'o')
    ->where('o.enabled = 1')
;
$factory = new BatcherFactory(
    ObjectCollectionBatcher::class,
    IdCollectionBatcher::class,
    NaiveIdRangeBatcher::class,
    IdRangeBatcher::class
);
$idCollectionBatcher = $factory->createIdCollectionBatcher($qb);

/** @var CollectionBatch[] $batches */
$batches = $idCollectionBatcher->getBatches(50);
foreach ($batches as $batch) {
    $commandBus->dispatch(new ProcessEnabledProductBatchMessage($batch));
}
```

Then sometime somewhere a consumer will receive that message and process the products:

```php
<?php
use Setono\DoctrineORMBatcher\Query\QueryRebuilderInterface;

class ProcessProductBatchMessageHandler
{
    public function __invoke(ProcessEnabledProductBatchMessage $message)
    {
        /** @var QueryRebuilderInterface $queryRebuilder */
        $q = $queryRebuilder->rebuild($message->getBatch());
        $products = $q->getResult();
        
        foreach ($products as $product) {
            // process $product
        }
    }
}
```

### Collection of objects

Should be used for immediate handing objects that selected with complex queries.

**Example**

You want to immediately process only enabled `Product` entities.

```php
<?php

use Doctrine\ORM\EntityManagerInterface;
use Setono\DoctrineORMBatcher\Factory\BatcherFactory;
use Setono\DoctrineORMBatcher\Batch\CollectionBatch;
use Setono\DoctrineORMBatcher\Batcher\Collection\ObjectCollectionBatcher;
use Setono\DoctrineORMBatcher\Batcher\Collection\IdCollectionBatcher;
use Setono\DoctrineORMBatcher\Batcher\Range\NaiveIdRangeBatcher;
use Setono\DoctrineORMBatcher\Batcher\Range\IdRangeBatcher;

/** @var EntityManagerInterface $em */
$qb = $em->createQueryBuilder();
$qb->select('o')
    ->from(Product::class, 'o')
    ->where('o.enabled = 1')
;
$factory = new BatcherFactory(
    ObjectCollectionBatcher::class,
    IdCollectionBatcher::class,
    NaiveIdRangeBatcher::class,
    IdRangeBatcher::class
);
$collectionBatcher = $factory->createObjectCollectionBatcher($qb);

/** @var CollectionBatch[] $batches */
$batches = $collectionBatcher->getBatches(50);
foreach ($batches as $batch) {
    /** @var Product $product */
    foreach ($batch->getCollection() as $product) {
        // process $product
    }
}
```

## Framework integration
- [Symfony bundle](https://github.com/Setono/DoctrineORMBatcherBundle)

[ico-version]: https://img.shields.io/packagist/v/setono/doctrine-orm-batcher.svg
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg
[ico-github-actions]: https://github.com/Setono/doctrine-orm-batcher/workflows/Build/badge.svg
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Setono/doctrine-orm-batcher.svg

[link-packagist]: https://packagist.org/packages/setono/doctrine-orm-batcher
[link-github-actions]: https://github.com/Setono/doctrine-orm-batcher/actions
[link-code-quality]: https://scrutinizer-ci.com/g/Setono/doctrine-orm-batcher
