<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', 'Api\AuthController@register');
Route::get('test', 'Api\AuthController@test');
// Route::name('verify')->get('users/verify/{token}','User\UserController@verify');

Route::group(['middleware'=>'auth:api'], function () {
    Route::post('testOauth', 'Api\AuthController@testOauth');
    Route::get('logout', 'Api\AuthController@logout');
});

Route::post('password/create', 'Api\PasswordResetController@create');
Route::get('password/find/{token}', 'Api\PasswordResetController@find');
Route::post('password/reset', 'Api\PasswordResetController@reset');

Route::get('getUsers', 'Api\UserdataController@getUsers');
Route::get('getUsers/{id}', 'Api\UserdataController@getUsersDetail');
Route::post('getUsers', 'Api\UserdataController@addUsers');
Route::post('completeProfile', 'Api\UserdataController@completeProfile');
Route::put('updateActive', 'Api\UserdataController@updateActive');
Route::put('getUsers', 'Api\UserdataController@updateUsers');
Route::put('updateProfile', 'Api\UserdataController@updateProfile');
Route::delete('getUsers', 'Api\UserdataController@deleteUsers');
Route::get('getAdmins', 'Api\UserdataController@getAdmins');

Route::get('getPrivilegios', 'Api\PrivilegiosController@getPrivilegios');
Route::get('getPrivilegios/{id}', 'Api\PrivilegiosController@getPrivilegiosDetail');
Route::post('getPrivilegios', 'Api\PrivilegiosController@addPrivilegios');
Route::put('getPrivilegios', 'Api\PrivilegiosController@updatePrivilegios');
Route::delete('getPrivilegios', 'Api\PrivilegiosController@deletePrivilegios');

Route::get('getComunidades', 'Api\ComunidadesController@getComunidades');
Route::get('getComunidades/{id}', 'Api\ComunidadesController@getComunidadesDetail');
Route::post('getComunidades', 'Api\ComunidadesController@addComunidades');
Route::put('getComunidades', 'Api\ComunidadesController@updateComunidades');
Route::delete('getComunidades', 'Api\ComunidadesController@deleteComunidades');

Route::get('getEmpresas', 'Api\EmpresasController@getEmpresas');
Route::get('getEmpresas/{id}', 'Api\EmpresasController@getEmpresasDetail');
Route::post('getEmpresas', 'Api\EmpresasController@addEmpresas');
Route::put('getEmpresas', 'Api\EmpresasController@updateEmpresas');
Route::delete('getEmpresas', 'Api\EmpresasController@deleteEmpresas');

Route::get('getGruposEmpresas', 'Api\GruposEmpresasController@getGruposEmpresas');
Route::get('getCreadorEmpresas/{id}', 'Api\GruposEmpresasController@getCreadorEmpresas');
Route::get('getGruposEmpresas/{id}', 'Api\GruposEmpresasController@getGruposEmpresasDetail');
Route::post('getGruposEmpresas', 'Api\GruposEmpresasController@addGruposEmpresas');
Route::put('getGruposEmpresas', 'Api\GruposEmpresasController@updateGruposEmpresas');
Route::delete('getGruposEmpresas', 'Api\GruposEmpresasController@deleteGruposEmpresas');

Route::get('getGruposComunidades', 'Api\GruposComunidadesController@getGruposComunidades');
Route::get('getGruposComunidades/{id}', 'Api\GruposComunidadesController@getGruposComunidadesDetail');
Route::get('getNoIntegrantesGrupoComunidad/{grupo}/{user}/{comunidad}', 'Api\GruposComunidadesController@getNoIntegrantesGrupoComunidad');
Route::post('getGruposComunidades', 'Api\GruposComunidadesController@addGruposComunidades');
Route::put('getGruposComunidades', 'Api\GruposComunidadesController@updateGruposComunidades');
Route::delete('getGruposComunidades', 'Api\GruposComunidadesController@deleteGruposComunidades');

Route::get('getNoMiembros/{id}', 'Api\MisComunidadesController@getNoMiembros');
Route::get('getMiembrosComunidades/{id}', 'Api\MisComunidadesController@getMiembros');
Route::get('getMisComunidades/{id}', 'Api\MisComunidadesController@getMisComunidades');
// Route::get('getMisComunidades/{id}', 'Api\MisComunidadesController@getMisComunidadesDetail');
Route::post('getMisComunidades', 'Api\MisComunidadesController@addMisComunidades');
Route::put('getMisComunidades', 'Api\MisComunidadesController@updateMisComunidades');
Route::delete('getMisComunidades', 'Api\MisComunidadesController@deleteMisComunidades');

Route::get('getMisEmpresas', 'Api\MisEmpresasController@getMisEmpresas');
Route::get('getMiembrosEmpresas/{id}', 'Api\MisEmpresasController@getMiembros');
Route::get('getNoEmpresas/{id}', 'Api\MisEmpresasController@getNoEmpresas');
Route::get('getMisEmpresas/{id}', 'Api\MisEmpresasController@getMisEmpresasDetail');
Route::get('getSolicitudes/{id}', 'Api\MisEmpresasController@getSolicitudes');
Route::put('aceptarSolicitud', 'Api\MisEmpresasController@aceptarSolicitud');
Route::post('getMisEmpresas', 'Api\MisEmpresasController@addMisEmpresas');
Route::put('getMisEmpresas', 'Api\MisEmpresasController@updateMisEmpresas');
Route::delete('getMisEmpresas', 'Api\MisEmpresasController@deleteMisEmpresas');

Route::get('getMisGruposComunidades/{id}', 'Api\MisGruposComunidadesController@getMisGruposComunidades');
Route::get('getMiembrosGruposComunidades/{id}', 'Api\MisGruposComunidadesController@getMiembrosGruposComunidades');
// Route::get('getMisGruposComunidades/{id}', 'Api\MisGruposComunidadesController@getMisGruposComunidadesDetail');
Route::post('getMisGruposComunidades', 'Api\MisGruposComunidadesController@addMisGruposComunidades');
Route::put('getMisGruposComunidades', 'Api\MisGruposComunidadesController@updateMisGruposComunidades');
Route::delete('getMisGruposComunidades', 'Api\MisGruposComunidadesController@deleteMisGruposComunidades');

Route::get('getMisGruposEmpresas/{id}', 'Api\MisGruposEmpresasController@getMisGruposEmpresas');
Route::get('getMiembrosGruposEmpresas/{id}', 'Api\MisGruposEmpresasController@getMiembrosGruposEmpresas');
// Route::get('getMisGruposEmpresas/{id}', 'Api\MisGruposEmpresasController@getMisGruposEmpresasDetail');
Route::post('getMisGruposEmpresas', 'Api\MisGruposEmpresasController@addMisGruposEmpresas');
Route::put('getMisGruposEmpresas', 'Api\MisGruposEmpresasController@updateMisGruposEmpresas');
Route::delete('getMisGruposEmpresas', 'Api\MisGruposEmpresasController@deleteMisGruposEmpresas');

Route::get('getPublicacionesComunidades/{comunidad}/{user}', 'Api\PublicacionesComunidadesController@getPublicacionesComunidades');
Route::get('getPublicacionesComunidadesDetail/{id}', 'Api\PublicacionesComunidadesController@getPublicacionesComunidadesDetail');
Route::post('getPublicacionesComunidades', 'Api\PublicacionesComunidadesController@addPublicacionesComunidades');
Route::put('getPublicacionesComunidades', 'Api\PublicacionesComunidadesController@updatePublicacionesComunidades');
Route::delete('getPublicacionesComunidades', 'Api\PublicacionesComunidadesController@deletePublicacionesComunidades');

Route::get('getPublicacionesGruposComunidades/{grupo}/{user}', 'Api\PublicacionesGruposComunidadesController@getPublicacionesGruposComunidades');
Route::get('getPublicacionesGruposComunidadesDetail/{id}', 'Api\PublicacionesGruposComunidadesController@getPublicacionesGruposComunidadesDetail');
Route::post('getPublicacionesGruposComunidades', 'Api\PublicacionesGruposComunidadesController@addPublicacionesGruposComunidades');
Route::put('getPublicacionesGruposComunidades', 'Api\PublicacionesGruposComunidadesController@updatePublicacionesGruposComunidades');
Route::delete('getPublicacionesGruposComunidades', 'Api\PublicacionesGruposComunidadesController@deletePublicacionesGruposComunidades');

Route::get('getPublicacionesEmpresas/{empresa}/{user}', 'Api\PublicacionesEmpresasController@getPublicacionesEmpresas');
Route::get('getPublicacionesEmpresasDetail/{id}', 'Api\PublicacionesEmpresasController@getPublicacionesEmpresasDetail');
Route::post('getPublicacionesEmpresas', 'Api\PublicacionesEmpresasController@addPublicacionesEmpresas');
Route::put('getPublicacionesEmpresas', 'Api\PublicacionesEmpresasController@updatePublicacionesEmpresas');
Route::delete('getPublicacionesEmpresas', 'Api\PublicacionesEmpresasController@deletePublicacionesEmpresas');

Route::get('getPublicacionesGruposEmpresas/{empresa}/{user}', 'Api\PublicacionesGruposEmpresasController@getPublicacionesGruposEmpresas');
Route::get('getPublicacionesGruposEmpresasDetail/{id}', 'Api\PublicacionesGruposEmpresasController@getPublicacionesGruposEmpresasDetail');
Route::post('getPublicacionesGruposEmpresas', 'Api\PublicacionesGruposEmpresasController@addPublicacionesGruposEmpresas');
Route::put('getPublicacionesGruposEmpresas', 'Api\PublicacionesGruposEmpresasController@updatePublicacionesGruposEmpresas');
Route::delete('getPublicacionesGruposEmpresas', 'Api\PublicacionesGruposEmpresasController@deletePublicacionesGruposEmpresas');

Route::get('getLikesComunidades/{user}/{publication}', 'Api\LikesComunidadesController@getLikesComunidades');
Route::get('getLikesComunidades/{id}', 'Api\LikesComunidadesController@getLikesComunidadesDetail');
Route::post('getLikesComunidades', 'Api\LikesComunidadesController@addLikesComunidades');
Route::put('getLikesComunidades', 'Api\LikesComunidadesController@updateLikesComunidades');
Route::delete('getLikesComunidades', 'Api\LikesComunidadesController@deleteLikesComunidades');

Route::get('getLikesGruposComunidades/{user}/{publication}', 'Api\LikesGruposComunidadesController@getLikesGruposComunidades');
Route::get('getLikesGruposComunidades/{id}', 'Api\LikesGruposComunidadesController@getLikesGruposComunidadesDetail');
Route::post('getLikesGruposComunidades', 'Api\LikesGruposComunidadesController@addLikesGruposComunidades');
Route::put('getLikesGruposComunidades', 'Api\LikesGruposComunidadesController@updateLikesGruposComunidades');
Route::delete('getLikesGruposComunidades', 'Api\LikesGruposComunidadesController@deleteLikesGruposComunidades');

Route::get('getLikesEmpresas/{user}/{publication}', 'Api\LikesEmpresasController@getLikesEmpresas');
Route::get('getLikesEmpresas/{id}', 'Api\LikesEmpresasController@getLikesEmpresasDetail');
Route::post('getLikesEmpresas', 'Api\LikesEmpresasController@addLikesEmpresas');
Route::put('getLikesEmpresas', 'Api\LikesEmpresasController@updateLikesEmpresas');
Route::delete('getLikesEmpresas', 'Api\LikesEmpresasController@deleteLikesEmpresas');

Route::get('getLikesGruposEmpresas/{user}/{publication}', 'Api\LikesGruposEmpresasController@getLikesGruposEmpresas');
Route::get('getLikesGruposEmpresas/{id}', 'Api\LikesGruposEmpresasController@getLikesGruposEmpresasDetail');
Route::post('getLikesGruposEmpresas', 'Api\LikesGruposEmpresasController@addLikesGruposEmpresas');
Route::put('getLikesGruposEmpresas', 'Api\LikesGruposEmpresasController@updateLikesGruposEmpresas');
Route::delete('getLikesGruposEmpresas', 'Api\LikesGruposEmpresasController@deleteLikesGruposEmpresas');

Route::get('getMisPublicacionesComunidades/{user}/{publication}', 'Api\MisPublicacionesComunidadesController@getMisPublicacionesComunidades');
Route::get('getMisPublicacionesComunidades/{id}', 'Api\MisPublicacionesComunidadesController@getAllPublicacionesComunidades');
Route::post('getMisPublicacionesComunidades', 'Api\MisPublicacionesComunidadesController@addMisPublicacionesComunidades');
Route::put('getMisPublicacionesComunidades', 'Api\MisPublicacionesComunidadesController@updateMisPublicacionesComunidades');
Route::delete('getMisPublicacionesComunidades', 'Api\MisPublicacionesComunidadesController@deleteMisPublicacionesComunidades');

Route::get('getMisPublicacionesGruposComunidades/{user}/{publication}', 'Api\MisPublicacionesGruposComunidadesController@getMisPublicacionesGruposComunidades');
Route::get('getMisPublicacionesGruposComunidades/{id}', 'Api\MisPublicacionesGruposComunidadesController@getAllPublicacionesGruposComunidades');
Route::post('getMisPublicacionesGruposComunidades', 'Api\MisPublicacionesGruposComunidadesController@addMisPublicacionesGruposComunidades');
Route::put('getMisPublicacionesGruposComunidades', 'Api\MisPublicacionesGruposComunidadesController@updateMisPublicacionesGruposComunidades');
Route::delete('getMisPublicacionesGruposComunidades', 'Api\MisPublicacionesGruposComunidadesController@deleteMisPublicacionesGruposComunidades');

Route::get('getMisPublicacionesEmpresas/{user}/{publication}', 'Api\MisPublicacionesEmpresasController@getMisPublicacionesEmpresas');
Route::get('getMisPublicacionesEmpresas/{id}', 'Api\MisPublicacionesEmpresasController@getAllPublicacionesEmpresas');
Route::post('getMisPublicacionesEmpresas', 'Api\MisPublicacionesEmpresasController@addMisPublicacionesEmpresas');
Route::put('getMisPublicacionesEmpresas', 'Api\MisPublicacionesEmpresasController@updateMisPublicacionesEmpresas');
Route::delete('getMisPublicacionesEmpresas', 'Api\MisPublicacionesEmpresasController@deleteMisPublicacionesEmpresas');

Route::get('getMisPublicacionesGruposEmpresas/{user}/{publication}', 'Api\MisPublicacionesGruposEmpresasController@getMisPublicacionesGruposEmpresas');
Route::get('getMisPublicacionesGruposEmpresas/{id}', 'Api\MisPublicacionesGruposEmpresasController@getAllPublicacionesGruposEmpresas');
Route::post('getMisPublicacionesGruposEmpresas', 'Api\MisPublicacionesGruposEmpresasController@addMisPublicacionesGruposEmpresas');
Route::put('getMisPublicacionesGruposEmpresas', 'Api\MisPublicacionesGruposEmpresasController@updateMisPublicacionesGruposEmpresas');
Route::delete('getMisPublicacionesGruposEmpresas', 'Api\MisPublicacionesGruposEmpresasController@deleteMisPublicacionesGruposEmpresas');

Route::get('getEventosComunidades/{comunidad}', 'Api\EventosComunidadesController@getEventosComunidades');
Route::get('getEventosComunidadesDetail/{id}', 'Api\EventosComunidadesController@getEventosComunidadesDetail');
Route::post('getEventosComunidades', 'Api\EventosComunidadesController@addEventosComunidades');
Route::put('getEventosComunidades', 'Api\EventosComunidadesController@updateEventosComunidades');
Route::delete('getEventosComunidades', 'Api\EventosComunidadesController@deleteEventosComunidades');

Route::get('getEventosGruposComunidades/{comunidad}', 'Api\EventosGruposComunidadesController@getEventosGruposComunidades');
Route::get('getEventosGruposComunidadesDetail/{id}', 'Api\EventosGruposComunidadesController@getEventosGruposComunidadesDetail');
Route::post('getEventosGruposComunidades', 'Api\EventosGruposComunidadesController@addEventosGruposComunidades');
Route::put('getEventosGruposComunidades', 'Api\EventosGruposComunidadesController@updateEventosGruposComunidades');
Route::delete('getEventosGruposComunidades', 'Api\EventosGruposComunidadesController@deleteEventosGruposComunidades');

Route::get('getEventosEmpresas/{comunidad}', 'Api\EventosEmpresasController@getEventosEmpresas');
Route::get('getEventosEmpresasDetail/{id}', 'Api\EventosEmpresasController@getEventosEmpresasDetail');
Route::post('getEventosEmpresas', 'Api\EventosEmpresasController@addEventosEmpresas');
Route::put('getEventosEmpresas', 'Api\EventosEmpresasController@updateEventosEmpresas');
Route::delete('getEventosEmpresas', 'Api\EventosEmpresasController@deleteEventosEmpresas');

Route::get('getEventosGruposEmpresas/{comunidad}', 'Api\EventosGruposEmpresasController@getEventosGruposEmpresas');
// Route::get('getEventosGruposEmpresas/{id}', 'Api\EventosGruposEmpresasController@getEventosGruposEmpresasDetail');
Route::post('getEventosGruposEmpresas', 'Api\EventosGruposEmpresasController@addEventosGruposEmpresas');
Route::put('getEventosGruposEmpresas', 'Api\EventosGruposEmpresasController@updateEventosGruposEmpresas');
Route::delete('getEventosGruposEmpresas', 'Api\EventosGruposEmpresasController@deleteEventosGruposEmpresas');

Route::get('getClasificados/{comunidad}', 'Api\ClasificadosController@getClasificados');
// Route::get('getClasificados/{id}', 'Api\ClasificadosController@getClasificadosDetail');
Route::post('getClasificados', 'Api\ClasificadosController@addClasificados');
Route::put('getClasificados', 'Api\ClasificadosController@updateClasificados');
Route::delete('getClasificados', 'Api\ClasificadosController@deleteClasificados');

Route::get('getComentariosComunidades/{publication}', 'Api\ComentariosComunidadesController@getComentariosComunidades');
// Route::get('getComentariosComunidadesD/{id}', 'Api\ComentariosComunidadesController@getComentariosComunidadesDetail');
Route::post('getComentariosComunidades', 'Api\ComentariosComunidadesController@addComentariosComunidades');
Route::put('getComentariosComunidades', 'Api\ComentariosComunidadesController@updateComentariosComunidades');
Route::delete('getComentariosComunidades', 'Api\ComentariosComunidadesController@deleteComentariosComunidades');

Route::get('getComentariosGruposComunidades/{publication}', 'Api\ComentariosGruposComunidadesController@getComentariosGruposComunidades');
// Route::get('getComentariosGruposComunidadesD/{id}', 'Api\ComentariosGruposComunidadesController@getComentariosGruposComunidadesDetail');
Route::post('getComentariosGruposComunidades', 'Api\ComentariosGruposComunidadesController@addComentariosGruposComunidades');
Route::put('getComentariosGruposComunidades', 'Api\ComentariosGruposComunidadesController@updateComentariosGruposComunidades');
Route::delete('getComentariosGruposComunidades', 'Api\ComentariosGruposComunidadesController@deleteComentariosGruposComunidades');

Route::get('getComentariosEmpresas/{publication}', 'Api\ComentariosEmpresasController@getComentariosEmpresas');
// Route::get('getComentariosEmpresasD/{id}', 'Api\ComentariosEmpresasController@getComentariosEmpresasDetail');
Route::post('getComentariosEmpresas', 'Api\ComentariosEmpresasController@addComentariosEmpresas');
Route::put('getComentariosEmpresas', 'Api\ComentariosEmpresasController@updateComentariosEmpresas');
Route::delete('getComentariosEmpresas', 'Api\ComentariosEmpresasController@deleteComentariosEmpresas');

Route::get('getComentariosGruposEmpresas/{publication}', 'Api\ComentariosGruposEmpresasController@getComentariosGruposEmpresas');
// Route::get('getComentariosGruposEmpresasD/{id}', 'Api\ComentariosGruposEmpresasController@getComentariosGruposEmpresasDetail');
Route::post('getComentariosGruposEmpresas', 'Api\ComentariosGruposEmpresasController@addComentariosGruposEmpresas');
Route::put('getComentariosGruposEmpresas', 'Api\ComentariosGruposEmpresasController@updateComentariosGruposEmpresas');
Route::delete('getComentariosGruposEmpresas', 'Api\ComentariosGruposEmpresasController@deleteComentariosGruposEmpresas');

Route::get('getChatsUsuarios/{emisor}/{receptor}', 'Api\ChatsUsuariosController@getChatsUsuarios');
Route::get('getChatsUsuarios/{id}', 'Api\ChatsUsuariosController@ChatsUsuariosdesDetail');
Route::post('getChatsUsuarios', 'Api\ChatsUsuariosController@addChatsUsuarios');
Route::put('getChatsUsuarios', 'Api\ChatsUsuariosController@updateChatsUsuarios');
Route::delete('getChatsUsuarios', 'Api\ChatsUsuariosController@deleteChatsUsuarios');

Route::get('getDocumentosComunidad/{publicacion}', 'Api\DocumentosController@getDocumentosComunidad');
Route::get('getDocumentosGrupoComunidad/{publicacion}', 'Api\DocumentosController@getDocumentosGrupoComunidad');
Route::get('getDocumentosEmpresa/{publicacion}', 'Api\DocumentosController@getDocumentosEmpresa');
Route::get('getDocumentosGrupoEmpresa/{publicacion}', 'Api\DocumentosController@getDocumentosGrupoEmpresa');
