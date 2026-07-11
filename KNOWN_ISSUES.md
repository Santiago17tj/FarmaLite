# KNOWN_ISSUES.md — FarmaLite
# Historial de incidencias conocidas y soluciones

> Este archivo es para el equipo de soporte y desarrollo.
> Nunca debe entregarse al cliente directamente.
> Cuando alguien llame con un problema, busca aquí primero.

---

## Cómo usar este archivo

1. Busca por palabras clave (Ctrl+F en cualquier editor).
2. Si ya existe el problema, sigue el **Workaround** hasta que haya fix.
3. Si es un problema nuevo, añade una entrada con la plantilla de abajo.
4. Cuando quede resuelto, actualiza el **Estado** y anota en qué versión.

---

## Plantilla

```
---

**ID:** #000
**Versión afectada:** 1.x.x
**Versión resuelta:** —
**Fecha detectado:** YYYY-MM-DD
**Fuente:** [Campo / Pruebas / Reporte de usuario]

**Descripción:**
Descripción clara del problema. Qué ocurre, cuándo ocurre y en qué condiciones.

**Pasos para reproducir:**
1.
2.
3.

**Impacto:** [Crítico / Alto / Medio / Bajo]
**Estado:** [Abierto / En análisis / Workaround disponible / Resuelto]

**Workaround:**
Qué puede hacer el usuario mientras se libera el fix.

**Fix:**
Qué se cambió para resolverlo (rama, archivo, línea).
```

---

## Incidencias Abiertas

*(Ninguna — versión 1.2.0 en campo)*

---

## Incidencias Resueltas

*(Se registrará aquí el historial a medida que aparezcan y se cierren)*

---

## Notas de compatibilidad por entorno

| Entorno              | Estado    | Notas                        |
|----------------------|-----------|------------------------------|
| Windows 10 (64-bit)  | ✅ Probado | Entorno principal            |
| Windows 11 (64-bit)  | ⬜ Pendiente | Probar en campo              |
| Windows 10 (32-bit)  | ⬜ Pendiente | Verificar binarios PHP       |
| Windows 7            | ❌ No soportado | CEF 130 requiere Win10+ |
| Antivirus activo     | ⚠️ Precaución | Puede bloquear php-cgi.exe  |

---

## Preguntas frecuentes de soporte

*(Se irá llenando con los primeros casos reales)*

**P: El cliente llama diciendo "no abre el programa".**
R: Pedir que abra Administrador de Tareas y verifique si `FarmaLite.exe` está en procesos. Si está, puede ser que el puerto local esté ocupado. Cerrar y volver a abrir.

**P: "Se perdieron los datos"**
R: Los datos nunca están en `Program Files`. Verificar que `C:\ProgramData\FarmaLite\data\farmacia.db` existe. Si existe, los datos están intactos. Pedir al cliente que vaya a Configuración → Información del Sistema para confirmar.
