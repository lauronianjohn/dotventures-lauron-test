<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FriendRequest;
use App\Models\Friend;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class FriendCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class FriendCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Friend::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/friend');
        CRUD::setEntityNameStrings('friend', 'friends');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // CRUD::setFromDb(); // set columns from db columns.

        $this->crud->query = Friend::where('user_id', backpack_user()->id);

        CRUD::column('friend');
        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(FriendRequest::class);
        // CRUD::setFromDb(); // set fields from db columns.

        $this->crud->addField([
            'name'      => 'friends',
            'label'     => 'Friends',
            'type'      => 'select',
            'entity'    => 'friends',
            'attribute' => 'name',
            'model'     => User::class,  
            'options' => function ($query) {
                $currentUserId = backpack_user()->id; // get the ID of the current user

                return $query->where('id', '!=', $currentUserId)->get();
            },
        ]);

        Friend::creating(function($entry) {
            $entry->user_id = backpack_user()->id;
        });
        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function destroy($id)
    {
        CRUD::hasAccessOrFail('delete');

        return CRUD::delete($id);
    }
}
