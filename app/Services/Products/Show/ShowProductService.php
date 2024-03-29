<?php

namespace App\Services\Products\Show;

use App\Models\Product;
use App\Repositories\Products\ProductsRepository;

class ShowProductService
{
    private ProductsRepository $productsRepository;

    public function __construct(ProductsRepository $productsRepository)
    {
        $this->productsRepository = $productsRepository;
    }

    public function execute(ShowProductRequest $request): Product
    {
        $productId = $request->getProductId();

        return $this->productsRepository->show($productId);
    }
}