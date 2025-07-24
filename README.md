# 💬 ChatApp Backend API (PHP)

Este es el backend de **ChatApp**, un sistema de chat con autenticación, manejo de usuarios y conexión a una inteligencia artificial (Ollama). Esta API REST está diseñada para trabajar con un frontend moderno (React, Vue, etc.).

---

## 🚀 Requisitos

- PHP 8.x
- Composer
- MySQL o MariaDB
- Servidor local o hosting con soporte para PHP

---

## 📁 Estructura del proyecto

/auth → Manejo de login

/middleware → Middleware para verificar sesión

/public → Interfaz HTML clásica (opcional)

/src → Servicios del chatbot

/api → API REST para frontend moderno

---

## ⚙️ Instalación

## 1. Clona el repositorio:

git clone "Url del repositorio"

## 2. Accede al proyecto:

cd chatapp

## 3. Instala dependencias:

composer install

## 4. Crea el archivo .env con tus datos de conexión a la base de datos.

## 5. Inicia el servidor:

php -S localhost:8000 -t public

## 📝 Endpoint de Registro de Usuario

## 🆕 POST /api/register.php

Registra un nuevo usuario en la base de datos.

## Body (JSON):

{

  "nombre": "María Fernanda",
  
  "apellido_paterno": "García",
  
  "apellido_materno": "López",
  
  "edad": 25,
  
  "sexo": "Femenino",
  
  "username": "maria@example.com",
  
  "password": "supersegura123",
  
  "fecha_nacimiento": "2000-04-15",
  
  "telefono": "5551234567"
  
}

## 🟡 Campos obligatorios: nombre, username, password

Los demás campos son opcionales pero recomendados para completar el perfil.

## Respuesta 200 OK:

{ "message": "Usuario registrado exitosamente" }

## Errores posibles:

400 Bad Request: Faltan campos obligatorios (nombre, username, password)

409 Conflict: El nombre de usuario ya está registrado

## 🔐 Autenticación

## 📄 GET /api/profile.php

Consulta los datos del usuario autenticado.

Encabezados:

Requiere sesión activa (cookie o token en caso de usarse).

## Respuesta 200 OK:

{

  "id": 1,
  
  "nombre": "María Fernanda",
  
  "apellido_paterno": "García",
  
  "apellido_materno": "López",
  
  "edad": 25,
  
  "sexo": "Femenino",
  
  "username": "maria@example.com",
  
  "fecha_nacimiento": "2000-04-15",
  
  "telefono": "5551234567"
  
}

## Errores:

401 Unauthorized → Si no hay sesión válida

404 Not Found → Si no se encuentra el usuario

## ✏️ PUT /api/profile.php

Actualiza los datos del usuario autenticado.

## Body (JSON):

{

  "nombre": "Nuevo nombre",
  
  "apellido_paterno": "Nuevo apellido",
  
  "apellido_materno": "Actualizado",
  
  "edad": 26,
  
  "sexo": "Otro",
  
  "password": "opcional123",
  
  "fecha_nacimiento": "1999-12-31",
  
  "telefono": "5559876543"
  
}

## 🔐 El campo password es opcional. Si se incluye, se actualiza.

## Respuesta 200 OK:

{ "message": "Perfil actualizado correctamente" }

## Errores:

400 Bad Request → Datos inválidos o faltantes

401 Unauthorized → Si no hay sesión activa

## 📥 POST /api/login.php

Inicia sesión con usuario y contraseña.

## Request

{

  "username": "usuario",
  
  "password": "contraseña"
  
}

## Respuesta

{

  "message": "Login exitoso"
  
}

## ⚠️ Se requiere credentials: 'include' para mantener la sesión desde el frontend.

## 🚪 GET /api/logout.php

Cierra la sesión actual del usuario.

## Respuesta

{

  "message": "Sesión cerrada"
  
}

## 💬 Chat 

## POST /api/chat.php

Envía una pregunta al chatbot y obtiene la respuesta generada por IA.

## Request

{

  "question": "¿Qué productos hay en Amazon?"
  
}

## Respuesta

{

  "question": "¿Qué productos hay en Amazon?",
  
  "answer": "Te recomiendo revisar la categoría de más vendidos en Amazon..."
  
}

## 📚 Chats

## GET /api/chats.php

Devuelve todos los chats creados por el usuario autenticado.

## Respuesta

[

  { "id": 1, "title": "Mi primer chat" },
  
  { "id": 2, "title": "Amazon productos" }
  
]

## POST /api/chats.php

Crea un nuevo chat vacío.

## Respuesta

{

  "chat_id": 3
  
}

## DELETE /api/chats.php

Elimina un chat específico.

## Request

{

  "id": 3
  
}

## Respuesta

{

  "message": "Chat eliminado"
  
}

## 📝 Mensajes

## GET /api/messages.php?chat_id=1

Obtiene los mensajes de un chat específico (ordenados cronológicamente).

## Respuesta

[

  { "role": "user", "message": "Hola" },
  
  { "role": "bot", "message": "¡Hola! ¿En qué puedo ayudarte?" }
  
]

## ✅ Notas adicionales

Todos los endpoints REST requieren sesión activa.

Puedes usar el AuthMiddleware para proteger rutas privadas.

Configura CORS adecuadamente en producción.

El frontend puede comunicarse usando fetch() o axios con credentials: 'include'.
