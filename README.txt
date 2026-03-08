=== BuilderForm ===
Contributors: sr-ralo
Donate link: https://google.com/
Tags: form builder, shortcode, analytics, pdf, wordpress, uleam
Requires at least: 5.8
Tested up to: 6.7
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Constructor de formularios para ULEAM-EP con plantillas, logica condicional, guardado de respuestas, analiticas y exportacion CSV/PDF.

== Description ==

BuilderForm es un plugin de WordPress para crear, publicar y analizar formularios institucionales de ULEAM-EP.

Incluye un panel administrativo completo con modulos de:

* Principal: listado y gestion de formularios creados.
* Constructor: diseno visual por secciones y campos.
* Plantillas: biblioteca de estructuras reutilizables.
* Logica: reglas de branching para flujos condicionales.
* Analiticas: resumen de respuestas, filtros y exportaciones.

Funciones principales:

* Publicacion por shortcode: `[uleam_form id="123"]`.
* Almacenamiento estructurado en tablas propias:
    `plugin_uleam_templates`, `plugin_uleam_forms`, `plugin_uleam_respuestas`.
* Validacion y sanitizacion de datos en envio publico.
* Soporte para tipos de campo especiales:
    fecha (formatos configurables), link seguro, firma digital por canvas (Base64), archivos.
* Exportacion de analiticas a CSV.
* Exportacion de respuestas a PDF usando Dompdf.

Este plugin esta orientado a equipos administrativos y academicos que necesitan formularios multi-seccion con trazabilidad de respuestas y reportes.

== Installation ==

1. Sube la carpeta del plugin a `/wp-content/plugins/`.
1. Verifica que el directorio `vendor/` este incluido (dependencias de Dompdf).
1. Activa el plugin desde el menu Plugins en WordPress.
1. En el admin, abre `ULEAM Formularios` y crea un formulario desde `Constructor`.
1. Publica el formulario con el shortcode `[uleam_form id="ID_DEL_FORMULARIO"]`.

== Frequently Asked Questions ==

= Como publico un formulario en una pagina? =

Usa el shortcode `[uleam_form id="ID"]`, reemplazando `ID` por el identificador real del formulario.

= Donde se guardan las respuestas? =

Las respuestas se guardan en la tabla `plugin_uleam_respuestas`, vinculadas al formulario en `plugin_uleam_forms`.

= El plugin genera PDF de respuestas? =

Si. Desde Analiticas puedes exportar respuestas individuales a PDF si Dompdf y sus dependencias estan disponibles en `vendor/`.

== Screenshots ==

1. Menu `ULEAM Formularios` con acceso a Principal, Constructor, Plantillas, Logica y Analiticas.
1. Vista del Constructor con secciones, campos y configuraciones.
1. Vista publica del formulario renderizado por shortcode.
1. Modulo de Analiticas con filtros y exportacion CSV/PDF.

== Changelog ==

= 1.0.0 =
* Version inicial del constructor de formularios ULEAM-EP.
* Gestion de formularios y plantillas en tablas dedicadas.
* Renderizado publico por shortcode.
* Registro de respuestas y metadatos.
* Panel de analiticas con exportacion CSV y PDF.

== Upgrade Notice ==

= 1.0.0 =
Primera version estable de BuilderForm para construccion y analisis de formularios institucionales.