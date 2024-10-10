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
    $router->post('/BudgetMonitoringBulky', ['uses' => 'BudgetMonitoringController@storeBulky']);

    //masterbrand
    $router->get('/masterbrand', ['uses' => 'masterbrandController@paging']); 
    $router->post('/masterbrand', ['uses' => 'masterbrandController@store']); // Menyimpan data baru
    $router->get('/masterbrand/{id}', ['uses' => 'masterbrandController@show']); // Menampilkan detail data berdasarkan ID
    $router->put('/masterbrand/{id}', ['uses' => 'masterbrandController@update']); // Memperbarui data berdasarkan ID
    $router->delete('/masterbrand/{id}', ['uses' => 'masterbrandController@destroy']);
    $router->post('/masterbrandBulky', ['uses' => 'masterbrandController@storeBulky']);

    //bridgingbudgetbrand
    $router->get('/bridgingbudgetbrand', ['uses' => 'bridgingbudgetbrandController@paging']); 
    $router->post('/bridgingbudgetbrand', ['uses' => 'bridgingbudgetbrandController@store']); // Menyimpan data baru
    $router->get('/bridgingbudgetbrand/{id}', ['uses' => 'bridgingbudgetbrandController@show']); // Menampilkan detail data berdasarkan ID
    $router->put('/bridgingbudgetbrand/{id}', ['uses' => 'bridgingbudgetbrandController@update']); // Memperbarui data berdasarkan ID
    $router->delete('/bridgingbudgetbrand/{id}', ['uses' => 'bridgingbudgetbrandController@destroy']);
    $router->post('/bridgingbudgetbrandBulky', ['uses' => 'bridgingbudgetbrandController@storeBulky']);

     //masterproduct
     $router->get('/masterproduct', ['uses' => 'masterproductController@paging']); 
     $router->post('/masterproduct', ['uses' => 'masterproductController@store']); // Menyimpan data baru
     $router->get('/masterproduct/{id}', ['uses' => 'masterproductController@show']); // Menampilkan detail data berdasarkan ID
     $router->put('/masterproduct/{id}', ['uses' => 'masterproductController@update']); // Memperbarui data berdasarkan ID
     $router->delete('/masterproduct/{id}', ['uses' => 'masterproductController@destroy']);
     $router->post('/masterproductBulky', ['uses' => 'masterproductController@storeBulky']);

     //targetpenjualan
     $router->get('/targetpenjualan', ['uses' => 'targetpenjualanController@paging']); 
     $router->post('/targetpenjualan', ['uses' => 'targetpenjualanController@store']); // Menyimpan data baru
     $router->get('/targetpenjualan/{id}', ['uses' => 'targetpenjualanController@show']); // Menampilkan detail data berdasarkan ID
     $router->put('/targetpenjualan/{id}', ['uses' => 'targetpenjualanController@update']); // Memperbarui data berdasarkan ID
     $router->delete('/targetpenjualan/{id}', ['uses' => 'targetpenjualanController@destroy']);
     $router->post('/targetpenjualanBulky', ['uses' => 'targetpenjualanController@storeBulky']);
     $router->get('/vstargetsales', ['uses' => 'vstargetsalesController@paging']);

     //sales
     $router->get('/sales', ['uses' => 'salesController@paging']); 
     $router->post('/sales', ['uses' => 'salesController@store']); // Menyimpan data baru
     $router->get('/sales/{id}', ['uses' => 'salesController@show']); // Menampilkan detail data berdasarkan ID
     $router->put('/sales/{id}', ['uses' => 'salesController@update']); // Memperbarui data berdasarkan ID
     $router->delete('/sales/{id}', ['uses' => 'salesController@destroy']);
     $router->post('/salesBulky', ['uses' => 'salesController@storeBulky']);
});
