<?php

namespace App\Http\Controllers\Api\V1\Permission;

use App\helpers;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use function PHPUnit\Framework\isNull;
use function PHPUnit\Framework\isEmpty;

use Spatie\Permission\Models\Permission;
use App\Http\Resources\GeneralCollection;
use App\Http\Resources\PermissionRsource;

class RoleController extends Controller
{
    use helpers;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
       if($roles->first()){
        return $this->apiResponse(['data' => $roles], 200);
       }else{
        return $this->apiResponse(['message' => __('not found')], 404);
       }
    }
    public function getRoleByID($id)
    {
        $role = Role::where('id',$id)->with('permissions')->get();
        if($role->first()){
            return $this->apiResponse(['data' => $role], 200);
        }else{
            return $this->apiResponse(['message' => __('not found')], 404);
        }
       
    }

   /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:roles|max:254',
        ]);
       
        $roles = Role::create(['name' => $request->name]);

        return $this->apiResponse(['data' => $roles], 200);
    }
  
   /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateRole(Request $request,$id)
    {
        $validated = $request->validate([
            'name' => 'required|unique:roles|max:254',
        ]);

      
        $roles = Role::where('id', $request->id)->get();
        if($roles->first()){
            $roleUpdated = Role::where('id',$request->id)->update([
                'name' => $request->name,
            ]);
            //$roles = Role::where('id', $request->id)->get();
            return $this->apiResponse(['data' => $roles], 200);
        }

       

       
    }

    public function assignPermissionForRole(Request $request,$id)
    {
        $role = Role::where('id',$id)->first();
        $permission = Permission::where('id',$request->permissionID)->first();
        if($permission){
           // $role->givePermissionTo($request->permissionID);
            $role->syncPermissions($request->permissionID);
            $rolePermission = Role::where('id',$id)->with('permissions')->get();
            return $this->apiResponse(['data' => $rolePermission], 200);
        }else{
            return $this->apiResponse(['message' => __('not found')], 404);
        }

       

    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $roles = Role::where('id', $id)->delete();
        if($roles){
            return $this->apiResponse(['message' => 'The Role Has Been Deleted'], 200);
        }else{
            return $this->apiResponse(['message' => 'not found'], 404);
        }
       
    }
}
