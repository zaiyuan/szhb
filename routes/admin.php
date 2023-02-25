<?php

use Illuminate\Support\Facades\Route;

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
include __DIR__.'/common/upload.php';

include __DIR__ . '/admin/admin.php';
include __DIR__ . '/admin/menu.php';
include __DIR__ . '/admin/role.php';
include __DIR__ . '/admin/carousel.php';
include __DIR__ . '/admin/user.php';
include __DIR__ . '/admin/option.php';
include __DIR__ . '/admin/wallet.php';
include __DIR__ . '/admin/announcement.php';
include __DIR__ . '/admin/instruction.php';
include __DIR__ . '/admin/recharge.php';
include __DIR__ . '/admin/withdraw.php';
