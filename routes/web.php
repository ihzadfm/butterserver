<?php

/** @var \Laravel\Lumen\Routing\Router $router */


$router->group(['prefix' => 'api'], function () use ($router) {
    $router->get('/user', 'MUserController@getData');
    $router->get('/user/{id}', 'MUserController@show');
    $router->post('/user', 'MUserController@store');
    $router->delete('/user/{id}', 'MUserController@destroy');
    $router->put('/user/{id}', 'MUserController@update');

    $router->get('/karyawan', 'MKaryawanController@getKaryawans');
    $router->get('/MMUser', ['uses' => 'MMUserController@paging']);
    $router->post('/MMUser', ['uses' => 'MMUserController@store']);
    $router->delete('/MMUser/{id}', ['uses' => 'MMUserController@destroy']);
    $router->get('/MMUser/{id}', ['uses' => 'MMUserController@show']);
    $router->put('/MMUser/{id}', ['uses' => 'MMUserController@update']);

    // Routing untuk m_item
    $router->get('/m_item', ['uses' => 'MItemController@paging']); // Untuk pagination dan pencarian
    $router->post('/m_item', ['uses' => 'MItemController@store']); // Menyimpan data baru
    $router->get('/m_item/{id}', ['uses' => 'MItemController@show']); // Menampilkan detail data berdasarkan ID
    $router->put('/m_item/{id}', ['uses' => 'MItemController@update']); // Memperbarui data berdasarkan ID
    $router->delete('/m_item/{id}', ['uses' => 'MItemController@destroy']); // Menghapus data berdasarkan ID (soft delete)

    // Routing untuk m_item_inventory
    $router->get('/m_item_inventory', ['uses' => 'MItemInventoryController@paging']);
    $router->post('/m_item_inventory', ['uses' => 'MItemInventoryController@store']); // Menyimpan data baru
    $router->get('/m_item_inventory/{id}', ['uses' => 'MItemInventoryController@show']); // Menampilkan detail data berdasarkan ID
    $router->put('/m_item_inventory/{id}', ['uses' => 'MItemInventoryController@update']); // Memperbarui data berdasarkan ID
    $router->delete('/m_item_inventory/{id}', ['uses' => 'MItemInventoryController@destroy']); // Menghapus data berdasarkan ID (soft delete)


    $router->post('/MMUserBulky', ['uses' => 'MMUserController@storeBulky']);

    //budget monitoring
    $router->get('/BudgetMonitoring', ['uses' => 'BudgetMonitoringController@paging']);
    $router->post('/BudgetMonitoring', ['uses' => 'BudgetMonitoringController@store']); // Menyimpan data baru
    $router->get('/BudgetMonitoring/{id}', ['uses' => 'BudgetMonitoringController@show']); // Menampilkan detail data berdasarkan ID
    $router->put('/BudgetMonitoring/{id}', ['uses' => 'BudgetMonitoringController@update']); // Memperbarui data berdasarkan ID
    $router->delete('/BudgetMonitoring/{id}', ['uses' => 'BudgetMonitoringController@destroy']);
    $router->delete('/BudgetMonitoringdelete', ['uses' => 'BudgetMonitoringController@deleteAll']);
    $router->post('/BudgetMonitoringBulky', ['uses' => 'BudgetMonitoringController@storeBulky']);

    //masterbrand
    $router->get('/masterbrand', ['uses' => 'masterbrandController@paging']);
    $router->post('/masterbrand', ['uses' => 'masterbrandController@store']); // Menyimpan data baru
    $router->get('/masterbrand/{id}', ['uses' => 'masterbrandController@show']); // Menampilkan detail data berdasarkan ID
    $router->put('/masterbrand/{id}', ['uses' => 'masterbrandController@update']); // Memperbarui data berdasarkan ID
    $router->delete('/masterbrand/{id}', ['uses' => 'masterbrandController@destroy']);
    $router->delete('/masterbranddelete', ['uses' => 'masterbrandController@deleteAll']);
    $router->post('/masterbrandBulky', ['uses' => 'masterbrandController@storeBulky']);

    //bridgingbudgetbrand
    $router->get('/bridgingbudgetbrand', ['uses' => 'bridgingbudgetbrandController@paging']);
    $router->post('/bridgingbudgetbrand', ['uses' => 'bridgingbudgetbrandController@store']); // Menyimpan data baru
    $router->get('/bridgingbudgetbrand/{id}', ['uses' => 'bridgingbudgetbrandController@show']); // Menampilkan detail data berdasarkan ID
    $router->put('/bridgingbudgetbrand/{id}', ['uses' => 'bridgingbudgetbrandController@update']); // Memperbarui data berdasarkan ID
    $router->delete('/bridgingbudgetbrand/{id}', ['uses' => 'bridgingbudgetbrandController@destroy']);
    $router->delete('/bridgingbudgetbranddelete', ['uses' => 'bridgingbudgetbrandController@deleteAll']);
    $router->post('/bridgingbudgetbrandBulky', ['uses' => 'bridgingbudgetbrandController@storeBulky']);

    //masterproduct
    $router->get('/masterproduct', ['uses' => 'masterproductController@paging']);
    $router->post('/masterproduct', ['uses' => 'masterproductController@store']); // Menyimpan data baru
    $router->get('/masterproduct/{id}', ['uses' => 'masterproductController@show']); // Menampilkan detail data berdasarkan ID
    $router->put('/masterproduct/{id}', ['uses' => 'masterproductController@update']); // Memperbarui data berdasarkan ID
    $router->delete('/masterproduct/{id}', ['uses' => 'masterproductController@destroy']);
    $router->delete('/masterproductdelete', ['uses' => 'masterproductController@deleteAll']);
    $router->post('/masterproductBulky', ['uses' => 'masterproductController@storeBulky']);

    //masterpenampung
    $router->get('/penampung', ['uses' => 'penampungController@paging']);
    $router->post('/penampung', ['uses' => 'penampungController@store']); // Menyimpan data baru
    $router->get('/penampung/{id}', ['uses' => 'penampungController@show']); // Menampilkan detail data berdasarkan ID
    $router->put('/penampung/{id}', ['uses' => 'penampungController@update']); // Memperbarui data berdasarkan ID
    $router->delete('/penampung/{id}', ['uses' => 'penampungController@destroy']);
    $router->delete('/penampungdelete', ['uses' => 'penampungController@deleteAll']);
    $router->post('/penampungBulky', ['uses' => 'penampungController@storeBulky']);
    $router->post('/insertrealizationterm', ['uses' => 'penampungController@insertrealizationterm']);


    //accrued
    $router->get('/accrued', ['uses' => 'accruedController@paging']);
    $router->post('/accrued', ['uses' => 'accruedController@store']); // Menyimpan data baru
    $router->get('/accrued/{id}', ['uses' => 'accruedController@show']); // Menampilkan detail data berdasarkan ID
    $router->put('/accrued/{id}', ['uses' => 'accruedController@update']); // Memperbarui data berdasarkan ID
    $router->delete('/accrued/{id}', ['uses' => 'accruedController@destroy']);
    $router->delete('/accrueddelete', ['uses' => 'accruedController@deleteAll']);
    $router->post('/accruedBulky', ['uses' => 'accruedController@storeBulky']);

    //targetpenjualan
    $router->get('/targetpenjualan', ['uses' => 'targetpenjualanController@paging']);
    $router->post('/targetpenjualan', ['uses' => 'targetpenjualanController@store']); // Menyimpan data baru
    $router->get('/targetpenjualan/{id}', ['uses' => 'targetpenjualanController@show']); // Menampilkan detail data berdasarkan ID
    //kak mario
    // $router->put('/targetpenjualan/{id}', ['uses' => 'targetpenjualanController@update']); // Memperbarui data berdasarkan ID
    $router->delete('/targetpenjualan/{id}', ['uses' => 'targetpenjualanController@destroy']);
    $router->delete('/targetpenjualandelete', ['uses' => 'targetpenjualanController@deleteAll']);
    $router->post('/targetpenjualanBulky', ['uses' => 'targetpenjualanController@storeBulky']);
    $router->get('/suggestion', ['uses' => 'distcodeController@pagingterm']);
    $router->get('/vstargetsalesuggestion', ['uses' => 'vstargetsalesController@paging']);
    $router->get('/vstargetsales', ['uses' => 'vstargetsalesController@paging']);
    $router->get('/bridgingtargetsales', ['uses' => 'bridgingtargetsalesController@paging']);

    //kak mario
    $router->get('/vstargetsalesmario/{distcode}/{brandcode}/{yop}/{mop}', ['uses' => 'vstargetsalesController@excelmario']);
    $router->get('/excelsuggestion/{distcode}/{brandcode}/{term}', ['uses' => 'vstargetsalesController@excelsuggestion']);
    $router->put('/putbudgetafter/{kodebeban}/{term}', ['uses' => 'targetpenjualanController@update']);
    $router->put('/putbudgetaftera/{kodebeban}/{term}', ['uses' => 'targetpenjualanController@updatea']);
    //adjusment
    $router->get('/adjusmentaccrued', ['uses' => 'targetpenjualanController@adjusmentaccrued']);
    $router->get('/adjusmentrealisasi', ['uses' => 'targetpenjualanController@adjusmentrealisasi']);

    $router->get('/targetpenjualanparam/{distcode}/{brandcode}/{yop}/{mop}', ['uses' => 'targetpenjualanController@showdatabyparameter']);
    $router->put('/updatebudget/{kodebeban}/{term}', ['uses' => 'targetpenjualanController@updatebudget']);
    $router->put('/updatepenampungq1/{kodebeban}/{term}', ['uses' => 'targetpenjualanController@updatepenampungq1']);
    $router->put('/updatepenampungq2/{kodebeban}/{term}', ['uses' => 'targetpenjualanController@updatepenampungq2']);
    $router->put('/updatepenampungq3/{kodebeban}/{term}', ['uses' => 'targetpenjualanController@updatepenampungq3']);
    $router->get('/suggestionparam/{distcode}/{brandcode}/{term}', ['uses' => 'targetpenjualanController@showsuggestionbyparameter']);
    $router->get('/suggestionpenampung/{distcode}/{brandcode}/{term}', ['uses' => 'targetpenjualanController@showsuggestionpenampung']);

    //sales
    $router->get('/sales', ['uses' => 'salesController@paging']);
    $router->post('/sales', ['uses' => 'salesController@store']); // Menyimpan data baru
    $router->get('/sales/{id}', ['uses' => 'salesController@show']); // Menampilkan detail data berdasarkan ID
    $router->put('/sales/{id}', ['uses' => 'salesController@update']); // Memperbarui data berdasarkan ID
    $router->delete('/sales/{id}', ['uses' => 'salesController@destroy']);
    $router->delete('/salesdelete', ['uses' => 'salesController@deleteAll']);
    $router->post('/salesBulky', ['uses' => 'salesController@storeBulky']);

    //history
    $router->get('/historybudget', ['uses' => 'reclassController@gethistorybudget']);
    $router->post('/historybudget', ['uses' => 'HistoryBudgetController@store']); // Menyimpan data baru
    $router->get('/historybudget/{id}', ['uses' => 'HistoryBudgetController@show']); // Menampilkan detail data berdasarkan ID
    $router->put('/historybudget/{id}', ['uses' => 'HistoryBudgetController@update']); // Memperbarui data berdasarkan ID
    $router->delete('/historybudget/{id}', ['uses' => 'HistoryBudgetController@destroy']);
    $router->delete('/historybudgetdelete', ['uses' => 'HistoryBudgetController@deleteAll']);
    $router->post('/historybudgetBulky', ['uses' => 'HistoryBudgetController@storeBulky']);

    $router->get('/bridgingbrand', ['uses' => 'masterbridgingbrandController@paging']);


    $router->get('/budgetterm', ['uses' => 'budgettermController@paging']);
    $router->get('/accruedterm', ['uses' => 'accruedtermController@paging']);
    


    $router->get('/getdistcodeall', ['uses' => 'distcodeController@paging']);
    $router->get('/getdistcodeallbrand', ['uses' => 'distcodeController@pagingbrand']);
    $router->get('/getdistcodeallyear', ['uses' => 'distcodeController@pagingyear']);
    $router->get('/getdistcodeallmonth', ['uses' => 'distcodeController@pagingmonth']);

    //OPTION RECLASS
    $router->get('/reclasskodebeban', ['uses' => 'reclassController@pagingkodebeban']);
    $router->post('/reclassupdate', ['uses' => 'reclassController@getdatakodebebanbybulan']);
    $router->get('/reclassbudgetmonitoring', ['uses' => 'reclassController@getbudgetmonitoring']);
    $router->get('/reclassbulan', ['uses' => 'reclassController@pagingbulan']);
    $router->get('/searchhistorybudget', ['uses' => 'reclassController@searchhistorybudget']);
    $router->get('/excelhistorybudget', ['uses' => 'reclassController@excelhistorybudget']);

    // $router->get('/reclassrealokasi', ['uses' => 'reclassController@pagingrealokasi']);
    // $router->get('/reclassbudget', ['uses' => 'reclassController@pagingbudget']);

});
