<?php

namespace App\Http\Controllers;

use App\Services\TreeService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * @var TreeService
     */
    private $treeService;

    /**
     * Create a new controller instance.
     *
     * @param TreeService $treeService
     */
    public function __construct(TreeService $treeService)
    {
        $this->treeService = $treeService;
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */

    public function index()
    {
        $tree = $this->treeService->getTree();
        $deep = $this->treeService->getDeep();
        $count = count($tree);
        $return = null;
        return view('home', compact('tree', 'return', 'deep', 'count'));
    }

    /**
     * @param Request $request
     * @return Factory|\Illuminate\View\View
     */
    public function execute(Request $request)
    {
        $action = $request->get('action');
        $value = $request->get('value');
        $return = $value ? $this->{$action}($request) : 'You have to fill form';
        $tree = $this->treeService->getTree();
        $deep = $this->treeService->getDeep();
        $count = count($tree);
        return view('home', compact('tree', 'return', 'deep', 'count'));
    }

    /**
     * @param Request $request
     * @return string
     */
    private function add(Request $request)
    {
        $data = $this->treeService->insert($request->get('value'));
        return $data;
    }

    /**
     * @param Request $request
     * @return string
     * @throws \Exception
     */
    private function remove(Request $request)
    {
        $data = $this->treeService->delete($request->get('value'));
        return $data;
    }
}
