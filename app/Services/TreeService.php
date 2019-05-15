<?php

namespace App\Services;

use App\Models\Tree;
use App\Repositories\Eloquent\TreeRepository;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class TreeService
{
    /**
     * @var TreeRepository
     */
    private $treeRepository;

    /**
     * @inheritdoc
     */
    private $root;

    /**
     * @inheritdoc
     */
    private $id;

    /**
     * @inheritdoc
     */
    private $type;

    /**
     * @inheritdoc
     */
    private $old;

    /**
     * @inheritdoc
     */
    private $tree;

    /**
     * TreeService constructor.
     * @param TreeRepository $treeRepository
     */
    public function __construct(TreeRepository $treeRepository)
    {
        $this->treeRepository = $treeRepository;
        $this->init();
    }

    /**
     * @param null $data
     */
    public function init($data = null)
    {
        $this->prepareTree($data);
    }

    /**
     * @param $data
     */
    private function prepareTree($data)
    {
        if ($data !== null) {
            foreach ($data as $value) {
                $this->insert($value);
            }
        }
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function createRecord($value)
    {
        return $this->treeRepository->create([
            'credits_left' => 0,
            'credits_right' => 0,
            'username' => App::runningInConsole() ? 'username' : Auth::user()->name,
            'left_child' => null,
            'right_child' => null,
            'value' => $value,
        ]);
    }

    /**
     * @param $value
     * @return string
     */
    public function insert($value)
    {
        if ($this->isEmpty()) {
            $this->root = $this->createRecord($value);
        } else {
            $count = $this->treeRepository->count();
            if ($count === 1) {
                $this->root = $this->treeRepository->getAll()[0];
            } else {
                $this->root = $this->findRoot();
            }
            $object = $this->treeRepository->findByValue($value);
            if (!$object) {
                $child = $this->createRecord($value);
                $this->insertNode($value, $this->root->id, $child);
            } else {
                return 'This value already exists in DB';
            }
        }
        return 'True';
    }

    /**
     * @param $value
     * @param $root
     * @param $child
     */
    public function insertNode($value, $root, $child)
    {
        $root = $this->treeRepository->find($root);
        if ($root) {
            $this->id = $root->id;
        }
        if (isset($root->id)) {
            if ($value > $root->value) {
                $this->type = 'right';
                $this->insertNode($value, $root->right_child, $child);
            } else if ($value < $root->value) {
                $this->type = 'left';
                $this->insertNode($value, $root->left_child, $child);
            }
        } else {
            if ($child) {
                $root = $this->treeRepository->find($this->id);
                $this->creditsManagement($root, $this->type, '+', $child);
            }
        }
    }

    /**
     * @param $root
     * @param $type
     * @param $char
     * @param null $child
     * @param bool $isNotLeaf
     */
    protected function creditsManagement($root, $type, $char, $child = null, $isNotLeaf = false)
    {
        $val = $char === '+' ? +1 : -1;
        $valueToSet = intval($root->{'credits_' . $type}) + $val;
        if ($child) {
            $this->treeRepository->update($root, [
                $type . '_child' => $char === '+' ? $child->id : $isNotLeaf ? $child->id : null,
                'credits_' . $type => $valueToSet,
            ]);
        } else {
            $this->treeRepository->update($root, [
                'credits_' . $type => $valueToSet,
            ]);
        }
        $parent = $this->treeRepository->findByChildId($root->id);
        if ($parent) {
            if ($parent->left_child !== null && $parent->left_child === $root->id) {
                $this->type = 'left';
            }
            if ($parent->right_child !== null && $parent->right_child === $root->id) {
                $this->type = 'right';
            }
            $isNotLeaf = true;
            if ($root->left_child === null && $root->right_child === null) {
                $isNotLeaf = false;
            }
            $this->creditsManagement($parent, $this->type, $char, $root, $isNotLeaf);
        }
    }

    /**
     * @param $value
     * @return string
     * @throws Exception
     */
    public function delete($value)
    {
        $object = $this->treeRepository->findByValue($value);
        if ($object) {
            $parent = $this->treeRepository->findByChildId($object->id);
            if ($parent) {
                if ($parent->left_child !== null && $parent->left_child === $object->id) {
                    $this->type = 'left';
                }
                if ($parent->right_child !== null && $parent->right_child === $object->id) {
                    $this->type = 'right';
                }
            }
            $anyChild = $this->hasAnyChild($object);
            if ($anyChild) {
                $oneChild = $this->hasOneChild($object);
                if ($oneChild) {
                    $idToSet = 0;
                    if ($object->left_child !== null) {
                        $idToSet = $object->left_child;
                    }
                    if ($object->right_child !== null) {
                        $idToSet = $object->right_child;
                    }
                    $child = $this->treeRepository->find($idToSet);
                    $this->creditsManagement($parent, $this->type, '-', $child, true);
                    $object->delete();
                } else {
                    $minValueNode = $this->findMinValueNodeFromRightSubTree($object->right_child);
                    $parent = $this->treeRepository->findByChildId($minValueNode->id);
                    if ($parent->left_child !== null && $parent->left_child === $minValueNode->id) {
                        $this->type = 'left';
                    }
                    if ($parent->right_child !== null && $parent->right_child === $minValueNode->id) {
                        $this->type = 'right';
                    }
                    $this->creditsManagement($parent, $this->type, '-', $minValueNode);
                    $minValueNode->delete();
                }
            } else {
                $this->creditsManagement($object, $this->type, '-');
                $object->delete();
            }
            return 'True';
        } else {
            return 'This value doesn\'t exists in DB';
        }
    }

    /**
     * @param $right_child
     * @return Model|null
     */
    protected function findMinValueNodeFromRightSubTree($right_child)
    {
        $object = $this->treeRepository->find($right_child);
        $minValueObject = null;
        if ($object) {
            $this->old = $object;
            if ($object->left_child === null) {
                $minValueObject = $object;
            } else {
                $minValueObject = $this->findMinValueNodeFromRightSubTree($object->left_child);
            }
        } else {
            $minValueObject = $this->old;
        }
        return $minValueObject;
    }

    /**
     * @param $object
     * @return bool
     */
    protected function hasAnyChild($object)
    {
        if ($object->left_child !== null || $object->right_child !== null) {
            return true;
        }
        return false;
    }

    /**
     * @param $object
     * @return bool
     */
    protected function hasOneChild($object)
    {
        if (($object->left_child !== null && $object->right_child === null) || ($object->right_child !== null && $object->left_child === null)) {
            return true;
        }
        return false;
    }

    /**
     * @return Tree | null
     */
    protected function findRoot()
    {
        $withChildren = $this->treeRepository->getAllParentsWhichHaveAChildren();
        foreach ($withChildren as $parent) {
            if ($this->treeRepository->hasNotChild($parent->id)) {
                return $parent;
            }
        }
        return null;
    }

    /**
     * @return bool
     */
    protected function isEmpty()
    {
        $count = $this->treeRepository->count();
        return $count === 0 ? true : false;
    }

    /**
     * @return mixed
     */
    public function getTree()
    {
        $this->tree = $this->treeRepository->getAll();
        return $this->tree;
    }

    /**
     * @return mixed
     */
    public function getDeep()
    {
        return $this->treeRepository->getCountAllParentsWhichHaveAChildren();
    }
}
