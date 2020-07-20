<?php

namespace Aerni\Snipcart\Contracts;

interface ProductRepository
{
    public function find(string $id): self;
}
