<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Loans;
use App\Models\Repayment;
use App\Traits\HttpResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;


class AdminAuthController extends Controller
{
     use HttpResponse;

     public function login(Request $request)
     {
        try {

        Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6'
        ])->stopOnFirstFailure()->validate();

        // if(!Auth::guard('admin')->attempt($request->only(['email' , 'password']))){
        //     return $this->error('', 'Credentionals do not match' ,401);
        // }

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Admin::where('email' , $request->email)->first();
        $user->tokens()->delete();

        return $this->success([
           'user' => $user,
           'token' => $user->createToken('Token for user ' . $user->name , ['role:admin'])->plainTextToken
        ] ,  "Successfully Login");
        
        }
        catch(ValidationException $e){
            //  return response()->json(['errors'=>$e->errors()]);
            return   $this->errorMsg('',$e->getMessage() , 422) ;
          }
     }

     public function registerUser(Request $request)
     {
        try {

             Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|unique:admin|email',
                'number' => 'required|unique:admin|digits_between:10,12',
                'password' => 'required|string|min:6'
            ])->stopOnFirstFailure()->validate();

            $data = $request->all();

            $user = Admin::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'number' => $data['number'],
                'password' => Hash::make($data['password'])
            ]);

            return $this->success([
                'user' => $user ,
            ] , "Successfully Register");
        }
        catch(ValidationException $e){
          //  return response()->json(['errors'=>$e->errors()]);
          return   $this->errorMsg('',$e->getMessage() , 422) ;
        }

     }


     public function logout()
     {
        Auth::user()->currentAccessToken()->delete();

        return $this->success([], 'Logout Successfully');
     }


     public function getAllUserLoanList(Request $request)
     {
        try {

            $request->merge(['admin_id' => Auth::guard('admin')->user()->id]);

            Validator::make($request->all(), [
                'admin_id' => 'required|exists:admin,id',
                'user_id' => 'required|numeric|exists:users,id',
                'status' => 'nullable|in:PENDING,APPROVED,PAID'
            ])->stopOnFirstFailure()->validate();

            $data = $request->all();

            $loan =  Loans::where('user_id' , $data['user_id']);

            $loan->when( ($request->has('status') && $request->get('status') != ''), function($loan) use ($data) {
                $loan->where( 'status' , $data['status'] );
            });

            $loanData = $loan->get();
          
            return $this->success([
                'loan_data' => $loanData
            ] ,  "Loan Data List For The User");

        }
        catch(ValidationException $e){
          //  return response()->json(['errors'=>$e->errors()]);
          return   $this->errorMsg('',$e->getMessage() , 422) ;
        }
    }

    public function getSingleUserLoan(Request $request){
        try {

            $request->merge(['admin_id' => Auth::guard('admin')->user()->id]);

            Validator::make($request->all(), [
                'admin_id' => 'required|exists:admin,id',
                'user_id' => 'required|numeric|exists:users,id',
                'loan_id' => 'required_without:loan_ref_number|prohibits:loan_ref_number|numeric|nullable',
                'loan_ref_number' => 'required_without:loan_id|prohibits:loan_id|string|nullable',
                'status' => 'nullable|in:PENDING,APPROVED,PAID'
            ])->stopOnFirstFailure()->validate();

            $data = $request->all();

            $loan =  Loans::where('user_id' , $data['user_id']);

            $loan->when( ($request->has('loan_id') && $request->get('loan_id') != ''), function($loan) use ($data) {
                $loan->where( 'id' , $data['loan_id'] );
            });

            $loan->when( ($request->has('loan_ref_number') && $request->get('loan_ref_number') != ''), function($loan) use ($data) {
                $loan->where( 'loan_ref_number' , $data['loan_ref_number'] );
            });

            $loan->when( ($request->has('status') && $request->get('status') != ''), function($loan) use ($data) {
                $loan->where( 'status' , $data['status'] );
            });

            //dd($loan->dd());

            $loanData = $loan->get();

            $repayment_data  = Repayment::where('loan_id' , $loanData[0]->id)->get();
          
            return $this->success([
                'loan_data' => $loanData,
                'repayment_data' => $repayment_data,
            ] ,  "Single Loan Data User");

        }
        catch(ValidationException $e){
          //  return response()->json(['errors'=>$e->errors()]);
          return   $this->errorMsg('',$e->getMessage() , 422) ;
        }
    }


    public function approveSingleLoan(Request $request){
        try {

            $request->merge(['admin_id' => Auth::guard('admin')->user()->id]);

            Validator::make($request->all(), [
                'admin_id' => 'required|exists:admin,id',
                'user_id' => 'required|numeric|exists:users,id',
                'loan_id' => 'required_without:loan_ref_number|prohibits:loan_ref_number|numeric|nullable',
                'loan_ref_number' => 'required_without:loan_id|prohibits:loan_id|string|nullable',
            ])->stopOnFirstFailure()->validate();

            $data = $request->all();

            $loan =  Loans::where('user_id' , $data['user_id'])
                    ->where('status' , 'PENDING' );

            $loan->when( ($request->has('loan_id') && $request->get('loan_id') != ''), function($loan) use ($data) {
                $loan->where( 'id' , $data['loan_id'] );
            });

            $loan->when( ($request->has('loan_ref_number') && $request->get('loan_ref_number') != ''), function($loan) use ($data) {
                $loan->where( 'loan_ref_number' , $data['loan_ref_number'] );
            });

            $loanDataArr = $loan->get()->toArray();

            $updateLoanData = ['status' => 'APPROVED' , 'approved_by' => $data['admin_id'] , 
            'approved_at' => Carbon::now()->format('Y-m-d H:i:s')];

            if(!empty($loanDataArr)){
                Loans::where('id' , $loanDataArr[0]["id"])->update($updateLoanData);
                return $this->success([
                    'loan_data' => []
                ] ,  "Single Loan Status Approved");
            }else{
                return $this->errorMsg([
                    'loan_data' => [],
                ] ,  "No Loan Data Found");
            }
          
        }
        catch(ValidationException $e){
          //  return response()->json(['errors'=>$e->errors()]);
          return   $this->errorMsg('',$e->getMessage() , 422) ;
        }
    }

     public function adminview(Request $request){

        dd('admin',$request->all());
     }



     
}