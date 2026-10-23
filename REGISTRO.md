# Registro de revisión

| Fase | Qué generó la IA | Qué corregí o rechacé | Por qué |
|------|------------------|-----------------------|---------|
| Fase 1 | D0.md con RF3 incompleto | Solicité agregar precio vigente, fecha y hora | Cumplir el caso EPIA |
| Fase 1 | RNF1 con manejo de contraseñas | Rechacé autenticación y precisé PDO y htmlspecialchars | Respetar el alcance y la seguridad |
| Fase 1 | Supuestos y terminología | Solicité eliminar edición del stock mínimo y corregir términos | Aplicar KISS |
| Fase 1 | Trazabilidad incompleta | Solicité incorporar RN3 a RF3 y verifiqué el cambio | Mantener coherencia entre requisitos y reglas |
| Fase 2 | requirements.md con trazabilidad incompleta | Relacioné CA-RF1-04 con RNF2 y excluí la edición de productos | Mantener coherencia y aplicar KISS |
| Fase 2 | design.md con estructura y configuración inconsistentes | Corregí las carpetas, el nombre de BD y la configuración .env; adapté las pruebas para usar repositorios falsos | Cumplir la guía EPIA |