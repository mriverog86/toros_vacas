<?php

namespace App\Interfaces\V1;

/**
 * Interface GameRepositoryInterface
 *
 * @package App\Interfaces\V1
 */
interface GameRepositoryInterface
{
    public function index();
    public function getById($id);
    public function store(array $data);
    public function update(array $data,$id);
    public function delete($id);
}
