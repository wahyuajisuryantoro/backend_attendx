<?php

namespace App\Http\Controllers\Web;

use Carbon\Carbon;
use App\Models\User;
use App\Models\user_profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{


    public function index(Request $request)
    {
        $totalEmployees = User::where('is_admin', 0)->count();

        $activeEmployees = User::where('is_admin', 0)->where('is_active', 1)->count();

        $inactiveEmployees = User::where('is_admin', 0)->where('is_active', 0)->count();

        $departmentCounts = user_profile::select('department', \DB::raw('count(*) as total'))
            ->groupBy('department')
            ->get()
            ->pluck('total', 'department')
            ->toArray();

        $topDepartment = '';
        $topCount = 0;
        foreach ($departmentCounts as $dept => $count) {
            if ($count > $topCount) {
                $topDepartment = $dept;
                $topCount = $count;
            }
        }
        return view('employee.employee-list', compact(
            'totalEmployees',
            'activeEmployees',
            'inactiveEmployees',
            'departmentCounts',
            'topDepartment',
            'topCount'
        ));
    }

    public function getData(Request $request)
    {
        $users = User::with('profile')->where('is_admin', 0)->select('users.*');

        return DataTables::of($users)
            ->addColumn('employee_id', function ($user) {
                return $user->profile->employee_id;
            })
            ->addColumn('profile_photo', function ($user) {
                return $user->profile && $user->profile->profile_photo
                    ? '<img src="' . asset('storage/' . $user->profile->profile_photo) . '" width="45" class="rounded-circle" />'
                    : '<div class="rounded-circle" style="width: 45px; height: 45px; background-color: #ddd; display: flex; align-items: center; justify-content: center;">' . strtoupper(substr($user->profile->name ?? 'No Data', 0, 1)) . '</div>';
            })
            ->addColumn('join_date', function ($user) {
                return $user->profile && $user->profile->join_date
                    ? Carbon::parse($user->profile->join_date)->format('Y/m/d')
                    : 'No Data';
            })
            ->addColumn('profile_name', function ($user) {
                return $user->profile->name ?? 'No Data';
            })
            ->addColumn('position', function ($user) {
                return $user->profile->position ?? 'No Data';
            })
            ->addColumn('department', function ($user) {
                return $user->profile->department ?? 'No Data';
            })
            ->addColumn('action', function ($user) {
                return '
                    <div class="dropdown">
                        <a class="fs-6 text-muted" href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-placement="top" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="' . route('employee.edit', $user->id) . '">
                                <i class="ti ti-edit fs-6 mx-2"></i> Edit</a>
                            </li>
                            <li><a class="dropdown-item" href="' . route('employee.show', $user->id) . '">
                                <i class="ti ti-eye fs-6 mx-2"></i> Show</a>
                            </li>
                            <li><a class="dropdown-item delete-btn" href="javascript:void(0)" data-form="delete-form-' . $user->id . '">
                                <i class="ti ti-trash fs-6 mx-2"></i> Delete</a>
                            </li>
                            <form id="delete-form-' . $user->id . '" action="' . route('employee.destroy', $user->id) . '" method="POST" style="display: none;">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                            </form>
                        </ul>
                    </div>
                ';
            })
            ->rawColumns(['profile_photo', 'action'])
            ->make(true);
    }

    public function create()
    {
        return view('employee.employee-create');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
            'name' => 'required|string',
            'department' => 'required|string',
            'position' => 'required|string',
            'email' => 'required|email|unique:users_profile',
            'phone' => 'required|string',
            'address' => 'required|string',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {

            $user = User::create([
                'username' => $validated['username'],
                'password' => bcrypt($validated['password']),
                'is_admin' => false,
                'is_active' => true,
            ]);


            $latestProfile = user_profile::orderByDesc('employee_id')->first();
            $employeeId = $latestProfile ? $latestProfile->employee_id + 1 : 100;
            $joinDate = now()->format('Y-m-d');

            $profilePhotoPath = null;
            if ($request->hasFile('profile_photo')) {
                $profilePhotoPath = $request->file('profile_photo')
                    ->store('profile-photos', 'public');
            }


            $user->profile()->create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'employee_id' => $employeeId,
                'department' => $validated['department'],
                'position' => $validated['position'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'join_date' => $joinDate,
                'profile_photo' => $profilePhotoPath,
            ]);

            DB::commit();

            return redirect()->route('employee.list')
                ->with('success', 'Employee created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create employee: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $employee = User::with('profile')->findOrFail($id);
        return view('employee.employee-show', compact('employee'));
    }


    public function edit($id)
    {
        $user = User::with('profile')->findOrFail($id);
        return view('employee.employee-edit', compact('user'));
    }


    public function update(Request $request, $id)
    {
        $user = User::with('profile')->findOrFail($id);
        $validated = $request->validate([
            'username' => 'required|string|unique:users,username,' . $id,
            'password' => 'nullable|string|min:6',
            'name' => 'required|string',
            'department' => 'required|string',
            'position' => 'required|string',
            'email' => 'required|email|unique:users_profile,email,' . $user->profile->id . ',id',
            'phone' => 'required|string',
            'address' => 'required|string',
            'profile_photo' => 'nullable|image|max:10240',
            'is_active' => 'sometimes|boolean',
        ]);

        DB::beginTransaction();
        try {
            $user->update([
                'username' => $validated['username'],
                'is_active' => $validated['is_active'] ?? $user->is_active,
                'password' => !empty($validated['password'])
                    ? bcrypt($validated['password'])
                    : $user->password
            ]);

            $profileUpdateData = [
                'name' => $validated['name'],
                'department' => $validated['department'],
                'position' => $validated['position'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
            ];

            if ($request->hasFile('profile_photo')) {
                if ($user->profile->profile_photo) {
                    Storage::disk('public')->delete($user->profile->profile_photo);
                }
                $profileUpdateData['profile_photo'] = $request->file('profile_photo')
                    ->store('profile-photos', 'public');
            }

            $user->profile()->update($profileUpdateData);

            DB::commit();

            return redirect()->route('employee.list')
                ->with('success', 'Employee updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update employee: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        try {

            if ($user->profile && $user->profile->profile_photo) {
                \Storage::disk('public')->delete($user->profile->profile_photo);
            }


            $user->profile()->delete();
            $user->delete();

            return redirect()->route('employee.list')
                ->with('success', 'Employee deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete employee: ' . $e->getMessage());
        }
    }
}