<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\TrajetModel;

class TrajetController extends Controller
{
    private TrajetModel $model;

    public function __construct()
    {
        $this->model = new TrajetModel();
    }

    public function index()
    {
        $trajets = $this->model->findAll();
        return $this->render('trajets/index', ['trajets' => $trajets]);
    }
}
