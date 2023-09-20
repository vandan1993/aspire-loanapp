
<h1 align="center">Aspire Repayment App Using Laravel </h1>
<h3 align="center">A basic loans and  payment app in laravel using api.</h3>

## Built with
- [Laravel 10](https://github.com/laravel/framework)
- [Laravel Sanctumn](https://github.com/laravel/sanctum)
- [PHP 8.1](https://www.php.net/releases/8.1/en.php)
- [SQLITE3]

## Sqlite3  Configuration
- Install php dependency  
```bash 
  sudo apt-get install php-sqlite3
  ```

- Create sqlite3 database file database.sqlite in expenseapp/database
  file path: - expenseapp/database/database.sqlite

- .env Configuration
```env
     DB_CONNECTION=sqlite  
     #DB_HOST=127.0.0.1  
     #DB_PORT=3306  
     #DB_DATABASE=laravel  
     #DB_USERNAME=root  
     #DB_PASSWORD=  
```

## PostMan Documentation Link  
[![Run in Postman](https://run.pstmn.io/button.svg)](https://documenter.getpostman.com/view/25590512/2s9YCARA5M)


[https://documenter.getpostman.com/view/25590512/2s9YCARA5M](https://documenter.getpostman.com/view/25590512/2s9YCARA5M)

## Api Sequence 

## Admin 

- Register Admin

    {{base_url}}/api/admin/register

- Login Admin

    {{base_url}}/api/admin/login

####   All Endpoint require  {{token}} for  getting admin id toekn can be found from login api

- Get all All User Loan List

    {{base_url}}/api/admin/getAllUserLoanList

- Get Single User Loan

    {{base_url}}/api/admin/getSingleUserLoan

- Approve Single Loan By Admin

    {{base_url}}/api/admin/approveSingleLoanByAdmin

## User

- Register User
    
    {{base_url}}/api/user/register

- Login User
    
    {{base_url}}/api/user/login

####   All Endpoint require  {{token}} for  getting user id toekn can be found from user login api

- Create Loan User

    {{base_url}}/api/user/createLoan

- User Loan  List

    {{base_url}}/api/user/userLoanList

- User Single Loan Info 
    
    {{base_url}}/api/user/userSingleLoan

- User Single Loan Repayment  

    {{base_url}}/api/user/loanRepayment;

## License
The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
