# Doctrine ORM Batch library
Use this library when you need to process large amounts of entities and maybe in an async way ;)

Why do we need this library? Why not just use a paginator library like [Pagerfanta](https://github.com/whiteoctober/Pagerfanta) or normal [batch processing in Doctrine](https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/batch-processing.html)?

Well, because MySQL is [not very good with LIMIT and OFFSET](https://www.eversql.com/faster-pagination-in-mysql-why-order-by-with-limit-and-offset-is-slow/) 
when the tables become too large. As for Doctrine batch processing capabilities the difference is that this 
library is very opinionated. It will work very well in a message based architecture where large processing will 
likely be done in an asynchronous way.

## Different batcher ;-)

[![Batcher, Batcher](https://img.youtube.com/vi/NL6CDFn2i3I/0.jpg)](https://www.youtube.com/watch?v=NL6CDFn2i3I)
