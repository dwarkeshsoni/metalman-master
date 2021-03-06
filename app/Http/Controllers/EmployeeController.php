<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Site;
use App\Models\SiteEmployee;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $designation = array('Head' => 'Head', 'Supervisor' => 'Supervisor', 'Operator' => 'Operator');   
        $location    = array('Mumbai' => 'Mumbai', 'Bangalore' => 'Bangalore', 'Chennai' => 'Chennai', 'Lucknow' => 'Lucknow', 'Indore' => 'Indore');
        $site        = Site::get()->pluck('name', 'id')->toArray();

        return view('employee.index', compact('designation', 'location', 'site'));
    }


    public function listAjax(Request $request)
    {

        if (\Request::ajax())
        {
            $employee = Employee::orderBy('id','DESC')->get();
            
            return DataTables::of($employee)
                ->addColumn('addSiteAdmin', function ($employee) {
                    $site_id = 0;
                    $siteEmployeeData = SiteEmployee::where('employee_id', $employee->id)->first();
                    if($siteEmployeeData)
                    {
                        $site_id = $siteEmployeeData->site_id;
                    }
                    return $site_id;
                })
                ->rawColumns(['addSiteAdmin'])
                ->make(true);
        }
        else
        {
            abort(403);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $designation = array('Head' => 'Head', 'Supervisor' => 'Supervisor', 'Operator' => 'Operator');
        $location    = array('Mumbai' => 'Mumbai', 'Bangalore' => 'Bangalore', 'Chennai' => 'Chennai', 'Lucknow' => 'Lucknow', 'Indore' => 'Indore');

        return view('employee.create', compact('designation', 'location'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'employee_name' => 'required',
            'phone_number'  => 'required',
            'email'         => 'required',
            'designation'   => 'required',
            'location'      => 'required',
        ]);

        $employee = new Employee();
        $employee->employee_name = $request->employee_name;
        $employee->phone_number  = $request->phone_number;
        $employee->email         = $request->email;
        $employee->designation   = $request->designation;
        $employee->location      = $request->location;
        $employee->created_at    = Carbon::now();
        $employee->updated_at    = Carbon::now();
        $employee->save();

         return redirect()->route('employee.index')->with('flash_message','Employee created successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $employee = Employee::find($id);

        return view('employee.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employee = Employee::find($id);
        $designation = array('Head' => 'Head', 'Supervisor' => 'Supervisor', 'Operator' => 'Operator');
        $location = array('Mumbai' => 'Mumbai', 'Bangalore' => 'Bangalore', 'Chennai' => 'Chennai', 'Lucknow' => 'Lucknow', 'Indore' => 'Indore');

        return view('employee.edit', compact('designation', 'location', 'employee'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'employee_name' => 'required',
            'phone_number'=>'required',
            'email'=>'required',
            'designation'=>'required',
            'location'=>'required',
        ]);

        // Update all data
        Employee::find($id)->update($request->all());

        return redirect('employee')->with('flash_message','Employee Updated Successfully.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Employee::find($id)->delete();
        return redirect()->route('employee.index')->with('flash_message','Employee Deleted Successfully.');
    }

    /**
     * Employee Export In Excel.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getExport(){
        Excel::create('Export data', function($excel) {
            $excel->sheet('Sheet 1', function($sheet) {
                $employees = Employee::select("employee_name", "phone_number", "email", 'designation', 'location', 'created_at')->get();
                    foreach($employees as $employee) {
                     $data[] = array(
                        $employee->employee_name,
                        $employee->phone_number,
                        $employee->email,
                        $employee->designation,
                        $employee->location,
                        $employee->created_at,
                    );
                }
                $sheet->fromArray($data, null, 'A1', false, false);
                $headings = array('Employee Name', 'Phone Number', 'Email ID', 'Designation', 'Location', 'Created At');
                $sheet->prependRow(1, $headings);
            });
        })->export('xls');
    }

    /*
    Import Employee
    */
    public function importEmployeeSave(Request $request)
    {
        if($request->hasFile('employee_import_file'))
        {
            $extension = $request->file('employee_import_file')->getClientOriginalExtension();
            if ($extension == "xls" || $extension == "xlsx") {
                $path = $request->file('employee_import_file')->getRealPath();
                $data = Excel::load($path, function($reader) {})->get();
                if(!empty($data) && $data->count())
                {
                    foreach($data->toArray() as $key=>$value)
                    {
                        if(!empty($value))
                        {
                            Employee::create($value);
                        }
                    }
                }
            }
            else {
                return redirect()->route('employee.index')->with('flash_message','Please Insert Excel File.');
            }
        }

        return redirect()->route('employee.index')->with('flash_message','Employee imported successfully.');
    }

    public function addSiteEmployee(Request $request)
    {
        if ($request->employee_id && $request->site_id) {
            $employeeName = Employee::find($request->employee_id)->employee_name;
            $emplyeeExsist = SiteEmployee::where('site_id', $request->site_id)->where('employee_id', $request->employee_id)->first();

            if ($emplyeeExsist)
            {
                $response = array(
                    'msg' => '<b style="color:red;">'.$employeeName . ' is already assigned as a venue admin for this site.</b>',
                );
            }
            else
            {
                $siteEmployee = new SiteEmployee();
                $siteEmployee->site_id      = $request->site_id;
                $siteEmployee->employee_id  = $request->employee_id;
                $siteEmployee->save();

                $response = array(
                    'success' => '1',
                    'msg'     => '<b>'.$employeeName . ' is assigned successfully as a venue admin.</b>',
                );
            }
        }else{
            $response = array(
                'msg' => 'ERROR',
            );
        }

        return json_encode($response);
    }

    /**
     * Remove site admin
     */
    public function removeSiteEmployee(Request $request)
    {
        if ($request->employee_id) {
            $siteEmployeeData = SiteEmployee::where('employee_id', $request->employee_id)->first();
            $employeeName     = Employee::find($request->employee_id)->employee_name;

            SiteEmployee::where('employee_id', $request->employee_id)->where('site_id', $siteEmployeeData->site_id)->delete();

            $response = array(
                'success'  => '1',
                'msg'      => '<b>'.$employeeName . ' is removed successfully as a venue admin.</b>',
            );

        }else{
            $response = array(
                'msg' => 'ERROR',
            );
        }

        return json_encode($response);
    }

    /**
     * Get site name
     */
    public function getSiteName(Request $request)
    {
        if ($request->employee_id) {

            $siteEmployeeData = SiteEmployee::where('employee_id', $request->employee_id)->first();
            $siteName         = Site::find($siteEmployeeData->site_id)->name;
            
            $response = array(
                'success'  => '1',
                'msg'      => '<b>'.$siteName.'</b>',
            );

        }else{
            $response = array(
                'msg' => 'ERROR',
            );
        }

        return json_encode($response);
    }
}
