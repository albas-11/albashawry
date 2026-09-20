<?php
namespace App\Repositories\Contracts;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function filter(array $filters);
    public function getFeatured($limit = 8);
    public function getLatest($limit = 12);
    public function search($query);
    public function getBySku($sku);
    public function getRelated($productId, $limit = 4);
}

?>
