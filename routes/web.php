<?php
use App\Debt;
use App\DebtInterest;
use Carbon\Carbon;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['role:merchant', 'role:customer'])->group(function () {
    Route::get("/users-profile", "UserController@userDetails")->name("users.profile");
    Route::post("/users-update_profile", "UserController@updateProfile")->name("user.update_profile");
});

Route::get("/user-interest", "InterestController@viewLoanInterestStatus")->name("users.interest");
Route::get("/user_interest_data", "InterestController@interestData")->name("users.interest_data");

######## User Routes ########
Route::group(['middleware' => ['role:customer'], 'prefix' => 'customer'], function () {
    ####### Profile #######
    Route::get("/users-profile", "UserController@userDetails")->name("users.profile");
    Route::post("/users-update_profile", "UserController@updateProfile")->name("user.update_profile");


    Route::get('/home', 'UserController@index')->name('users.dashboard');
    Route::get("/shop", "UserController@shop")->name("users.shop");
    Route::get('/user-details', 'UserController@userDetails')->name('users.details');

    ######## Payment Routes ########
    Route::post("/loan-payment", "UserController@loanPayment")->name("users.loan.payment");
    Route::get("/loan-payment-success", "UserController@loanPaymentSuccess")->name("users.loan.payment.success");
    Route::get("/loan-payment-createWebhook", "UserController@loanPaymentCreateWebhook")->name("users.loan.payment.createWebhook");
    Route::get("/loan-payment-succesWebhook", "UserController@loanPaymentSuccessWebhook")->name("users.loan.payment.successWebhook");

    ######## Create Payment Routes ########
    Route::get("/payments", "PaymentController@payments")->name("payment.loan.payments");
    Route::get("/loan-payment-fail", "PaymentController@loanPaymentFail")->name("users.loan.payment.fail");

    // Disabling webhooks
    Route::get("/loan-payment-retreiveWebhook", function () {
        header('Content-Type: application/json');
        $request = file_get_contents('php://input');
        $payload = json_decode($request, true);
        $type = $payload['data']['attributes']['type'];
        //If event type is source.chargeable, call the createPayment API
        if ($type == 'source.chargeable') {
            $amount = $payload['data']['attributes']['data']['attributes']['amount'];
            $id = $payload['data']['attributes']['data']['id'];
            $description = "GCash Payment Description";
            $curl = curl_init();
            $fields = array("data" => array("attributes" => array("amount" => $amount, "source" => array("id" => $id, "type" => "source"), "currency" => "PHP", "description" => $description)));
            $jsonFields = json_encode($fields);

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.paymongo.com/v1/payments",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $jsonFields,
                CURLOPT_HTTPHEADER => [
                    "Accept: application/json",
                    //Input your encoded API keys below for authorization
                    "Authorization:",
                    "Content-Type: application/json"
                ],
            ]);

            $response = curl_exec($curl);
            //Log the response
            $fp = file_put_contents('test.log', $response);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                echo "cURL Error #:" . $err;
                //Log the response
                $fp = file_put_contents('test.log', $err);
            } else {
                echo $response;
            }
        }
    });

    ######## Loan Routes ########
    Route::post("/loan-details", "UserController@loanDetails")->name("users.loan.details");
    Route::post("/loan-product", "UserController@loanProduct")->name("users.loan.product");

    ######## Change password ########
    Route::get("/change_password", "UserController@changePassword")->name("customer.change.password");
    Route::post("/change_password", "UserController@updatePassword")->name("customer.update.password");

    ######## DATA TABLES FOR USERS #######
    Route::get("/recent_transaction_data", "UserController@recentTransactionData")->name("users.recent_transaction_data");
    Route::get("/recent_payment_transaction_data", "UserController@recentPaymentTransactionData")->name("users.recent_payment_transaction_data");

    // Interest Calculation
    Route::get('/run_interest_calculation', 'InterestController@runInterestCalculation');

    // Faker Generator
    Route::get("/faker_generator", function () {
        $faker = app(Faker::class);

        foreach (range(1, 10) as $test) {
            $startYear = date("Y");
            $startMonth = '08'; // August
            $startDay = $faker->numberBetween(1, 31); // Generate a random day within the month

            $augustDate = "$startYear-$startMonth-$startDay";

            $debts = Debt::create([
                "customer_id" => 1,
                "product_id" => rand(1, 2),
                "amount" => rand(500, 200),
                "due_date" => Carbon::now()->addMonths(2),
                "amount_paid" => 0,
                "debt_status_id" => 4,
                "current_amount" => rand(500, 200),
                "product_price_change_date" => $augustDate,
                "updated_at" => $augustDate,
                "created_at" => $augustDate,
            ]);

            $debt_interest = DebtInterest::create([
                "debt_id" => $debts->id,
                "loan_setting_id" => 1,
                "start_date" => $debts->created_at,
                "end_date" => Carbon::now()->addMonths(2),
            ]);


        }
    });
});

######## Auth Routes ########
Auth::routes();

######## Admin Routes ########
Route::group(['middleware' => ['role:admin'], 'prefix' => 'admin'], function () {
    Route::get('/dashboard', 'AdminController@index')->name('admin.dashboard');
    Route::get('/create-merchants', 'AdminController@createMerchant')->name('admin.create_merchants');
    Route::post('/store-merchants', 'AdminController@storeMerchant')->name('admin.store_merchants');
    Route::get("/store-merchants-data", "AdminController@storeMerchantData")->name("admin.store_merchant_data");
    Route::post("/store-merchant-update-status", "AdminController@storeMerchantUpdateStatus")->name("admin.store_merchant_update_status");
    Route::get("/store-merchants-info", "AdminController@merchantsInfo")->name("admin.store_merchants_info");
    Route::post("/store-merchants-update-info", "AdminController@merchantsUpdateInfo")->name("admin.store_merchants_update");

    Route::get("/customers", "AdminController@customers")->name("admin.customers");
    Route::get("/customers-data", "AdminController@customersData")->name("admin.customers.data");
    Route::get("/customers-info", "AdminController@customersInfo")->name("admin.customer.info");
    Route::post("/customers-update", "AdminController@customersUpdate")->name("admin.customers.update");
    Route::post("/customers-deactivate", "AdminController@customerDeactivate")->name("admin.customers.deactivate");

    Route::get("/history", "HistoryController@data")->name("admin.history");
});

######## Merchant Routes ########
Route::group(['middleware' => ['role:merchant'], 'prefix' => 'merchant'], function () {
    ####### Profile #######
    Route::get("/users-profile", "UserController@userDetails")->name("users.profile");
    Route::post("/users-update_profile", "UserController@updateProfile")->name("user.update_profile");


    ####### Dashboard #######
    Route::get('/dashboard', 'MerchantController@dashboard')->name('merchant.dashboard');
    Route::get('/customer', 'MerchantController@customer')->name('merchant.customer.index');
    Route::get("/customer_detail", "MerchantController@customerDetail")->name("merchant.customer.detail");
    Route::post("/customer_update_detail", "MerchantController@customerUpdateDetail")->name("merchant.customer.update_detail");

    Route::get("/customer_loan_status", "MerchantController@customerLoanStatus")->name("merchant.customer_loan_status");
    Route::get("/customer_loan_status/data", "MerchantController@customerLoanStatusData")->name("merchant.customer_loan_status_data");
    Route::get("/customer_loan_interest_status", "InterestController@viewLoanInterestStatus")->name("merchant.customer_loan_interest_status");

    Route::get("/change_password", "MerchantController@changePassword")->name("merchant.change.password");
    Route::post("/change_password", "MerchantController@updatePassword")->name("merchant.update.password");

    Route::get('/product', 'MerchantController@product')->name('merchant.product.index');
    Route::post('/save-product', 'MerchantController@saveProduct')->name('merchant.product.save');
    Route::get('/get-product', 'MerchantController@getProduct')->name('merchant.product.get');
    Route::post('/update-product', 'MerchantController@updateProduct')->name('merchant.product.update');
    Route::post('/delete-product', 'MerchantController@deleteProduct')->name('merchant.product.delete');

    Route::get('/store', 'MerchantController@store')->name('merchant.store.index');
    Route::post('/save-store', 'MerchantController@saveStore')->name('merchant.store.save');
    Route::post('/update-store', 'MerchantController@updateStore')->name('merchant.store.update');
    Route::post('/delete-store', 'MerchantController@deleteStore')->name('merchant.store.delete');
    Route::get("/get-store", "MerchantController@getStore")->name("merchant.store.get");

    // Loan Settings
    Route::get('/loan-setup', 'LoanSettingController@index')->name('loan.setup');
    Route::post("/loan-create_interest_setup", "LoanSettingController@create")->name("loan.create_interest_setup");
    Route::get("/loan-interest_setup_data", "LoanSettingController@show")->name("loan.show_data");
    Route::get("/loan-interest_setup_edit_data", "LoanSettingController@edit")->name("loan.edit_data");
    Route::post("/loan-interest_setup_update_data", "LoanSettingController@update")->name("loan.update_data");
    Route::post("/loan-interest_setup_delete_data", "LoanSettingController@destroy")->name("loan.delete_data");

    Route::get('/customer-data', 'MerchantController@customerData')->name('customer-data.index');
    Route::post("/save-customer-details", "MerchantController@saveCustomerDetail")->name("save.customer.details");
    Route::post("/deactivate-customer", "MerchantController@deactivateCustomer")->name("deactivate.customer");

    Route::get('/edit-profile/{id}', 'MerchantController@editProfile')->name('edit-profile');
    Route::get('/view-profile/{id}', 'MerchantController@viewProfile')->name('view-profile');
});











