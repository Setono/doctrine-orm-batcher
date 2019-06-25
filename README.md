# Doctrine ORM Batcher library

[![Latest Version][ico-version]][link-packagist]
[![Latest Unstable Version][ico-unstable-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-code-quality]][link-code-quality]

Use this library when you need to process large amounts of entities and maybe in an asynchronous way.

Why do we need this library? Why not just use a paginator library like [Pagerfanta](https://github.com/whiteoctober/Pagerfanta) or normal [batch processing in Doctrine](https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/batch-processing.html)?

Well, because MySQL is [not very good with LIMIT and OFFSET](https://www.eversql.com/faster-pagination-in-mysql-why-order-by-with-limit-and-offset-is-slow/) 
when the tables become too large. As for Doctrine batch processing capabilities the difference is that this 
library is very opinionated. It will work very well in a message based architecture where large processing will 
likely be done in an asynchronous way.

[ico-version]: https://poser.pugx.org/setono/doctrine-orm-batcher/v/stable
[ico-unstable-version]: https://poser.pugx.org/setono/doctrine-orm-batcher/v/unstable
[ico-license]: https://poser.pugx.org/setono/doctrine-orm-batcher/license
[ico-travis]: https://travis-ci.com/Setono/doctrine-orm-batcher.svg?branch=master
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Setono/doctrine-orm-batcher.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/setono/doctrine-orm-batcher
[link-travis]: https://travis-ci.com/Setono/doctrine-orm-batcher
[link-code-quality]: https://scrutinizer-ci.com/g/Setono/doctrine-orm-batcher
