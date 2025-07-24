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

## 🔐 Autenticación

## 📥 POST /api/login.php

Inicia sesión con usuario y contraseña.

Request

{

  "username": "usuario",
  "password": "contraseña"
  
}

Respuesta

{

  "message": "Login exitoso"
  
}

## ⚠️ Se requiere credentials: 'include' para mantener la sesión desde el frontend.

## 🚪 GET /api/logout.php

Cierra la sesión actual del usuario.

Respuesta

{

  "message": "Sesión cerrada"
  
}

## 💬 Chat 

## POST /api/chat.php

Envía una pregunta al chatbot y obtiene la respuesta generada por IA.

Request

{

  "question": "¿Qué productos hay en Amazon?"
  
}

Respuesta

{

  "question": "¿Qué productos hay en Amazon?",
  
  "answer": "Te recomiendo revisar la categoría de más vendidos en Amazon..."
  
}

## 📚 Chats

## GET /api/chats.php

Devuelve todos los chats creados por el usuario autenticado.

Respuesta

[

  { "id": 1, "title": "Mi primer chat" },
  
  { "id": 2, "title": "Amazon productos" }
  
]

## POST /api/chats.php

Crea un nuevo chat vacío.

Respuesta

{

  "chat_id": 3
  
}

## DELETE /api/chats.php

Elimina un chat específico.

Request

{

  "id": 3
  
}

Respuesta

{

  "message": "Chat eliminado"
  
}

## 📝 Mensajes

## GET /api/messages.php?chat_id=1

Obtiene los mensajes de un chat específico (ordenados cronológicamente).

Respuesta

[

  { "role": "user", "message": "Hola" },
  
  { "role": "bot", "message": "¡Hola! ¿En qué puedo ayudarte?" }
  
]

## ✅ Notas adicionales

Todos los endpoints REST requieren sesión activa.

Puedes usar el AuthMiddleware para proteger rutas privadas.

Configura CORS adecuadamente en producción.

El frontend puede comunicarse usando fetch() o axios con credentials: 'include'.
