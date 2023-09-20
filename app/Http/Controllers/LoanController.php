<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Loans;
use App\Models\Repayment;
use App\Traits\HttpResponse;
use App\Traits\Utils;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LoanController extends Controller
{
     use HttpResponse;
     use Utils;

     public function createLoan(Request $request){

        try {

            Validator::make($request->all(), [
                'amount' => 'required|numeric|gt:0',
                'term' => 'required|numeric|gt:0'
            ])->stopOnFirstFailure()->validate();

            $data = $request->all();
            
            $loan_ref_number = $this->generateRef('LOAN');
            $loan_creation_date = Carbon::now()->format('Y-m-d');
            //dd($loan_creation_date);
            
            $loan = Loans::create([
                'loan_ref_number' => $loan_ref_number , 
                'user_id' => Auth::guard('users')->user()->id,
                'loan_amount' => $data['amount'],
                'term' => $data['term'],
                'status' => 'PENDING',
                'loan_creation_date' => $loan_creation_date
            ]);

            if(!empty($loan)){

                $outstanding_amount = $loan_amount = $data['amount'];
                $total_repayment_amount = 0;
                $repayment_list_array = [];
                
                for($i = 1 ; $i <= $data['term'] ; $i++){
    
                    $repayment_ref_number = $this->generateRef('RPAY');
    
                    if($i == $data['term']){
                        $final_repayment = $loan_amount - $total_repayment_amount;
                        $repayment_amount_ist = $final_repayment;
                    }else{
                        $repayment_amount_ist =  (float) $loan_amount /  $data['term'];
                        $repayment_amount_ist = (int) ($repayment_amount_ist * 10000);
                        $repayment_amount_ist = (float) $repayment_amount_ist / 10000 ;
                        $total_repayment_amount +=  $repayment_amount_ist;
                    }
    
                    $repayment_array = [
                        'repayment_ref_number' => $repayment_ref_number,
                        'loan_id' => $loan->id, 
                        'loan_ref_number' => $loan_ref_number,
                        'user_id' => Auth::guard('users')->user()->id,
                        'repayment_amount' => $repayment_amount_ist,
                        'repayment_date' =>   Carbon::now()->addWeeks($i)->format('Y-m-d'),
                        'status' => 'PENDING',
                    ];

                   $repayment_data =  Repayment::create($repayment_array);  
                   $repayment_list_array[] = $repayment_data; 
                }

            }
           
    
          
            return $this->success([
                'loan_data' => $loan,
                'repayment_list' => $repayment_list_array
            ] ,  "Successfully Loan Created");
            
            }
            catch(ValidationException $e){
                //  return response()->json(['errors'=>$e->errors()]);
                return   $this->errorMsg('',$e->getMessage() , 422) ;
            }
     }

     public function userSingleLoan(Request $request){

        try {

            $request->merge(['user_id' => Auth::guard('users')->user()->id]);

            Validator::make($request->all(), [
                'user_id' => 'required|numeric|exists:users,id',
                'loan_id' => 'required_without:loan_ref_number|prohibits:loan_ref_number|numeric|nullable',
                'loan_ref_number' => 'required_without:loan_id|prohibits:loan_id|string|nullable',
            ])->stopOnFirstFailure()->validate();

            $data = $request->all();
            
            $loan = Loans::where('user_id' , $data['user_id']);

            $loan->when( ($request->has('loan_id') && $request->get('loan_id') != ''), function($loan) use ($data) {
                $loan->where( 'id' , $data['loan_id'] );
            });
            $loan->when($request->has('loan_ref_number') && $request->get('loan_ref_number') != '', function($loan) use ($data) {
                $loan->where('loan_ref_number' , $data['loan_ref_number']);
            });

            $loanData = $loan->get();
            $repayment_data = [];
            if(!empty($loanData[0])){
                $repayment_data  = Repayment::where('loan_id' , $loanData[0]->id)->get();
            }else{
                return   $this->errorMsg('','No loan Found', 422) ;
            }

            return $this->success([
                
                'loan_data' => $loanData,
               'repayment_data' => $repayment_data,
            ] ,  "Loan Details");
            
        }
            catch(ValidationException $e){
                //  return response()->json(['errors'=>$e->errors()]);
                return   $this->errorMsg('',$e->getMessage() , 422) ;
            }
     }

     public function userLoanList(Request $request){

        try {

            $request->merge(['user_id' => Auth::guard('users')->user()->id]);

            Validator::make($request->all(), [
                'user_id' => 'required|numeric|exists:users,id',
                'loan_id' => 'nullable|string',
                'loan_ref_number' => 'nullable|string',
            ])->stopOnFirstFailure()->validate();

            $data = $request->all();
            
            $loan = Loans::where('user_id' , $data['user_id']);

            $loan->when( ($request->has('loan_id') && $request->get('loan_id') != ''), function($loan) use ($data) {
                $loan->where( 'id' , $data['loan_id'] );
            });
            $loan->when($request->has('loan_ref_number') && $request->get('loan_ref_number') != '', function($loan) use ($data) {
                $loan->where('loan_ref_number' , $data['loan_ref_number']);
            });

            $loanData = $loan->get();
  
            return $this->success([
                
                'loan_data' => $loanData,
            ] ,  "Loan Details");
            
        }
            catch(ValidationException $e){
                //  return response()->json(['errors'=>$e->errors()]);
                return   $this->errorMsg('',$e->getMessage() , 422) ;
            }

     }


     public function loanRepayment(Request $request){

        try {

            $request->merge(['user_id' => Auth::guard('users')->user()->id]);

            Validator::make($request->all(), [
                'user_id' => 'required|numeric|exists:users,id',
                'repayment_amount' => 'required|numeric|gt:0',
                'loan_id' => 'nullable|string',
                'loan_ref_number' => 'nullable|string',
            ])->stopOnFirstFailure()->validate();

            $data = $request->all();
                
            $loan = Loans::where('user_id' , $data['user_id'])->where('status' , 'APPROVED');

            $loan->when( ($request->has('loan_id') && $request->get('loan_id') != ''), function($loan) use ($data) {
                $loan->where( 'id' , $data['loan_id'] );
            });
            $loan->when($request->has('loan_ref_number') && $request->get('loan_ref_number') != '', function($loan) use ($data) {
                $loan->where('loan_ref_number' , $data['loan_ref_number']);
            });

            $loanDataArr = $loan->get()->toArray();
            //check for loan data 
            if(!empty($loanDataArr)){

                $repaymentDataArr = Repayment::where('loan_id' , $loanDataArr[0]["id"])->where( "status" , "PENDING")
                ->orderby('repayment_date' , 'asc')->get()->toArray();

                //cehck fro repayment Data
                if(!empty($repaymentDataArr)){

                    //check  for amount 
                    if($data['repayment_amount'] >=  $repaymentDataArr[0]["repayment_amount"] ){
                        
                        $repaymentUpdateData =["status" => "PAID" , 
                        "received_date" =>  Carbon::now()->format('Y-m-d H:i:s') , 
                        "received_amount" => $data['repayment_amount']
                        ];
                        Repayment::where('loan_id' , $loanDataArr[0]["id"])->where("id" , $repaymentDataArr[0]["id"])
                        ->update($repaymentUpdateData);

                        $paidRepaymentCount = Repayment::where('loan_id' , $loanDataArr[0]["id"])->where("status" ,  "PAID")->count();

                        //check for  loan term
                        if($paidRepaymentCount == $loanDataArr[0]["term"]){

                            $loanUpdateData =  ["status" => "PAID" , "loan_closure_date" => Carbon::now()->format('Y-m-d')];
                            Loans::where('id' , $loanDataArr[0]["id"])->update($loanUpdateData);
                            return $this->success([
                                'data' => []
                            ] ,  "Successfully Repayment Done and Loan Marked PAID");
                        }

                        return $this->success([
                            'data' => []
                        ] ,  "Successfully Repayment Done ");

                    }else{
                        return $this->errorMsg([
                            'data' => [],
                        ] ,  "Repayment Amount Should be equal to or greater than Schedule Amount");

                    }
                   
                }else{
                    return $this->errorMsg([
                        'data' => [],
                    ] ,  "No Repayment Data Found");
                }
                
               
            }else{
                return $this->errorMsg([
                    'data' => [],
                ] ,  "No Loan Data Found");
            }


        }
        catch(ValidationException $e){
            //  return response()->json(['errors'=>$e->errors()]);
            return   $this->errorMsg('',$e->getMessage() , 422) ;
        }
    }
     
}