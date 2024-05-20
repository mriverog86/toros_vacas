<?php

namespace App\Repository\V1;

use App\Models\Game;
use App\Interfaces\V1\GameRepositoryInterface;

/**
 * Class GameRepository
 *
 * Servicio de acceso a datos para la entidad Juego
 *
 * @package App\Repository\V1
 */
class GameRepository implements GameRepositoryInterface
{
    public function index(){
        return Game::all();
    }

    public function getById($id){
        return Game::find($id);
    }

    public function store(array $data){
        return Game::create($data);
    }

    public function update(array $data,$id){
        return Game::whereId($id)->update($data);
    }

    public function delete($id){
        Game::destroy($id);
    }
}
