<?php

Route::post('etheris/cancel', 'Selfreliance\Etheris\Etheris@cancel_payment')->name('etheris.cancel');
Route::post('etheris/confirm', 'Selfreliance\Etheris\Etheris@validateIPNRequest')->name('etheris.confirm');