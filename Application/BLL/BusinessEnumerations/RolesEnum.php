<?php
namespace Application\BLL\BusinessEnumerations;

abstract class RolesEnum {
    ## ROLES ADMINISTRATIVOS
    const   R_ADMIN_USERS                           =   "R_ADMIN_USERS";
    const   R_ADMIN_ENABLE_DISABLE_USERS            =   "R_ADMIN_ENABLE_DISABLE_USERS";
    const   R_ADMIN_EDIT_USERS                      =   "R_ADMIN_EDIT_USERS";
    const   R_ADMIN_CHANGE_OTHER_USER_PASSWORD      =   "R_ADMIN_CHANGE_OTHER_USER_PASSWORD";
    const   R_ADMIN_CREAR_NUEVO_USUARIO             =   "R_ADMIN_CREAR_NUEVO_USUARIO";
    const   R_ADMIN_VER_TODAS_LAS_LISTAS            =   "R_ADMIN_VER_TODAS_LAS_LISTAS";
    const   R_ADMIN_VER_DETALLE_LISTA               =   "R_ADMIN_VER_DETALLE_LISTA";

    ## ROLES NIVEL USUARIO
    const   R_USERS_VER_TODAS_LISTAS_GRUPOS         =   "R_USERS_VER_TODAS_LISTAS_GRUPOS";
    const   R_USERS_CREAR_NUEVA_LISTA               =   "R_USERS_CREAR_NUEVA_LISTA";
    const   R_USERS_VER_LISTAS                      =   "R_USERS_VER_LISTAS";
    const   R_USER_SWITCH_ESTADO_LISTA              =   "R_USER_SWITCH_ESTADO_LISTA";
    const   R_USER_ASIGAR_LISTA_CLIENTES            =   "R_USER_ASIGAR_LISTA_CLIENTES";
    const   R_USER_GESTIONAR_CLIENTES               =   "R_USER_GESTIONAR_CLIENTES";
    const   R_USER_DESCARGAR_LISTA                  =   "R_USER_DESCARGAR_LISTA";

    const   R_AGREGAR_COMENTARIO_CLIENTE            =   "R_AGREGAR_COMENTARIO_CLIENTE";
    const   R_BUSCAR_CLIENTES                       =   "R_BUSCAR_CLIENTES";
}