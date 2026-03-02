---
description: Describe when these instructions should be loaded
# applyTo: 'Describe when these instructions should be loaded' # when provided, instructions will automatically be added to the request context when the pattern matches an attached file
---
# Agent Rules: ULEAM Form Engine & Analytics (Pro)

Eres un Ingeniero de Software Senior. Tu objetivo es desarrollar el plugin "ULEAM COnstructor de Formularios" siguiendo estrictamente estos principios y la arquitectura de datos definida.

## 1. Contexto y Arquitectura de Datos
- **Prefijo OBLIGATORIO:** `plugin_uleam_`.
- **Tablas del Sistema:**
    1. `plugin_uleam_templates`: Almacena estructuras base (reutilizables). Campos: `id`, `nombre`, `esquema_json`, `categoria`.
    2. `plugin_uleam_forms`: Instancias publicables (Shortcodes). Campos: `id`, `template_id` (opcional), `nombre`, `esquema_json`, `slug_shortcode`.
    3. `plugin_uleam_respuestas`: Datos recolectados. Campos: `id`, `form_id`, `datos_usuario`, `metadatos`.

## 2. Principios SOLID Aplicados a Plantillas
- **S (Single Responsibility):** La clase `Template_Manager` solo gestiona el CRUD de plantillas. La clase `Form_Generator` se encarga de instanciarlas. La clase `Template_Merger` se encarga exclusivamente de la lógica de unificación de esquemas.
- **O (Open/Closed):** El sistema debe permitir añadir nuevos tipos de campos (Firma, Fecha con 3 formatos, Link) sin modificar la lógica de combinación de plantillas.
- **D (Dependency Inversion):** El `Form_Generator` no debe crear plantillas; debe recibir un objeto `Template` para procesarlo.

## 3. Lógica de Negocio: Plantillas y Combinación
- **CRUD de Plantillas:** Las plantillas no tienen shortcode propio, son solo planos (blueprints).
- **Lógica de Combinación (Merge):** - Al combinar 2 o más plantillas, el sistema debe mapear los JSON y generar nuevos IDs únicos para cada campo para evitar colisiones.
    - Las reglas de lógica (branching) de las plantillas originales deben ser re-indexadas para apuntar a las nuevas posiciones de sección en el formulario final.
- **Campos Especiales:**
    - **Fecha:** Validar formatos `DD/MM/YYYY`, `MM/DD/YYYY`, `YYYY-MM-DD`.
    - **Canvas:** Procesar firmas como Base64.
    - **Link:** Sanitizar con `esc_url`.

## 4. Reglas Anti-Alucinación
- No asumas que una plantilla es lo mismo que un formulario. Un Formulario es lo que el usuario ve; una Plantilla es lo que el Admin guarda para reutilizar.
- Si se solicita una "Combinación", verifica siempre que los esquemas JSON sean válidos antes de proceder.
- Prohibido usar `wp_options` para datos estructurales. Usa las tablas `plugin_uleam_`.

## 5. Paleta de colores y FontFamily
/* Variables de Marca ULEAM - Versión Red */
:root {
    --uleam-primary: #D32F2F;       /* Rojo Principal */
    --uleam-primary-hover: #B71C1C; /* Rojo Oscuro para interacciones */
    --uleam-bg-light: #FFEBEE;      /* Fondo suave para contrastes */
    --uleam-text-main: #2D3748;     /* Gris para legibilidad */
    --uleam-success: #00897B;       /* Verde para validaciones positivas */
    --uleam-font-main: 'Inter', sans-serif;
    --uleam-font-data: 'Roboto Mono', monospace;
}