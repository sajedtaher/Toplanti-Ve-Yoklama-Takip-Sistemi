<?php
namespace Config;
// aşağıdaki iki satır test için
//use App\Controllers\Meetings;

//test için kullandık
//$routes->get('meetings/add/(:num)', 'Meetings::add/$1'); //GET ile çalışmasın istiyorsan
//$routes->match(['GET','POST'],'meetings/add/(:num)', 'Meetings::add/$1'); //  hem POST hem GET ile çalışmasını istiyorsan

// test için hem GET hem POST kabul eden route
//$routes->match(['get','post'], 'meetings/(:num)/agenda', 'Meetings::add/$1');
//$routes->get('meetings', 'Meetings::index');

$routes->get('meetings', 'Meetings::index');

$routes = Services::routes();
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

$routes->setDefaultNamespace('App\\Controllers');
$routes->setDefaultController('AuthController');
$routes->setDefaultMethod('login');
$routes->setTranslateURIDashes(false);
$routes->set404Override();

// test için aşapıdaki iki satrın yerine koyduk
//$routes->get('/login', 'AuthController::login');  // bu linkle çalışır: http://localhost:8080/login
//$routes->post('/login', 'AuthController::attempt');

//veya üsteki iki satrın yerine 
//$routes->match(['get','post'], 'login', 'AuthController::login');


//$routes->get('/', 'AuthController::login'); // bu linkle çalışır:http://localhost:8080/
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::attempt');
$routes->get('/logout', 'AuthController::logout', ['filter' => 'auth']);

// Ana sayfa açılınca direkt demo başlasın
$routes->get('/', 'DemoController::login');

// Demo giriş linki
$routes->get('demo', 'DemoController::login');

// Demo rol değiştirme
$routes->post('demo/change-role', 'DemoController::changeRole', ['filter' => 'auth']);

//infinityFree için
//$routes->get('/', 'Home::index');


// madde düzelmesi için 
$routes->post('agenda/(:num)/update', 'Agenda::update/$1', ['filter' => 'manager']); 
$routes->post('agenda/(:num)/decision', 'Agenda::saveDecision/$1', ['filter' => 'manager']);

$routes->get('hash.php', 'Hash::index'); // hash.php yazılması bunu ne yazsak  bu linkin snunua ekleriz:http://localhost:8080/ yani bu şekil olacak: http://localhost:8080/hash.php 

// Kişiler listesi için 
$routes->get('/users', 'UsersController::index');
$routes->get('/users/delete/(:num)', 'UsersController::delete/$1');
$routes->get('/users/edit/(:num)', 'UsersController::edit/$1');
$routes->post('/users/update/(:num)', 'UsersController::update/$1');

// + kişi ekle için
$routes->get('/users', 'UsersController::index');
$routes->get('/users/edit/(:num)', 'UsersController::edit/$1');
$routes->post('/users/update/(:num)', 'UsersController::update/$1');
$routes->post('/users/delete/(:num)', 'UsersController::delete/$1');
$routes->post('/users/store', 'UsersController::store');

// Birimler listesi için 
// auth filtresiyle koruduk
$routes->group('units', ['filter' => 'superadmin'], function($routes) {
    $routes->get('',              'UnitsController::index');
    $routes->post('store',        'UnitsController::store');
    $routes->get('edit/(:num)',   'UnitsController::edit/$1');
    $routes->post('update/(:num)','UnitsController::update/$1');
    $routes->get('delete/(:num)', 'UnitsController::delete/$1');
    $routes->post('units/select', 'UnitsController::select', ['filter' => 'superadmin']);
});
// 🟢 Superadmin birim seçimi (dropdown için)
$routes->post('units/select', 'UnitsController::select');

//katılım raporu sayfası için 
$routes->get('reports/attendance', 'ReportsController::attendance', ['filter' => 'auth']);
$routes->get('reports/selectUnit/(:num)', 'ReportsController::selectUnit/$1');


// Toplantılar listesi için
$routes->group('meetings', ['filter' => 'auth'], function($routes) {
    $routes->get('', 'Meetings::index'); 
    $routes->get('create', 'Meetings::create', ['filter' => 'manager']);
    $routes->post('store', 'Meetings::store', ['filter' => 'manager']);
    $routes->get('(:num)', 'Meetings::show/$1', ['filter' => 'unitAccess']);
    $routes->get('edit/(:num)', 'Meetings::edit/$1', ['filter' => 'manager']);
    $routes->post('update/(:num)', 'Meetings::update/$1', ['filter' => 'manager']);
    $routes->get('delete/(:num)', 'Meetings::delete/$1', ['filter' => 'manager']);
    $routes->post('end/(:num)', 'Meetings::end/$1', ['filter' => 'manager']);

    $routes->post('(:num)/participants', 'Meetings::saveParticipants/$1', ['filter' => 'manager']);
    $routes->post('(:num)/agenda', 'Agenda::add/$1', ['filter' => 'manager']);

    // Katılım durumunu güncelleme (auth veya manager yetkili)
    $routes->post('updateParticipantStatus', 'Meetings::updateParticipantStatus', ['filter' => 'auth']);

    $routes->get('modalEdit/(:num)', 'Meetings::modalEdit/$1');

    // yeni toplatı eklerken sadece birim ile alakalı kullanıcılar görünsün
    $routes->get('getUsersByUnit/(:num)', 'Meetings::getUsersByUnit/$1');

});




if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
