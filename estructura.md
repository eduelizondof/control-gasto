Estructura BD completa — Finanzas Personales

Usuarios y Dispositivos
sqlusuarios
  id               UUID PK
  nombre           VARCHAR(100)
  email            VARCHAR(150) UNIQUE
  password_hash    TEXT
  avatar_url       TEXT
  moneda_default   CHAR(3) DEFAULT 'MXN'
  created_at       TIMESTAMP
  updated_at       TIMESTAMP
  deleted_at       TIMESTAMP  -- soft delete

dispositivos
  id               UUID PK
  usuario_id       UUID FK → usuarios
  push_token       TEXT
  plataforma       ENUM(ios, android, web)
  modelo           VARCHAR(100)
  activo           BOOLEAN DEFAULT true
  ultimo_acceso_at TIMESTAMP
  created_at       TIMESTAMP
  deleted_at       TIMESTAMP

Grupos / Familia
sqlgrupos
  id               UUID PK
  nombre           VARCHAR(100)
  descripcion      TEXT
  created_by       UUID FK → usuarios
  created_at       TIMESTAMP
  deleted_at       TIMESTAMP

usuarios_grupos
  id               UUID PK
  usuario_id       UUID FK → usuarios
  grupo_id         UUID FK → grupos
  rol              ENUM(admin, miembro, solo_lectura)
  invitado_por     UUID FK → usuarios
  fecha_ingreso    TIMESTAMP
  activo           BOOLEAN DEFAULT true

Catálogos
sqlcategorias
  id               UUID PK
  grupo_id         UUID FK → grupos
  nombre           VARCHAR(100)
  tipo             ENUM(ingreso, gasto, ahorro, transferencia)
  color            CHAR(7)   -- hex #FFFFFF
  icono            VARCHAR(50)
  categoria_padre_id UUID FK → categorias  -- subcategorías
  es_sistema       BOOLEAN DEFAULT false   -- categorías default de la app
  orden            INT
  created_at       TIMESTAMP
  deleted_at       TIMESTAMP

conceptos
  id               UUID PK
  grupo_id         UUID FK → grupos
  categoria_id     UUID FK → categorias
  nombre           VARCHAR(150)
  descripcion      TEXT
  es_sistema       BOOLEAN DEFAULT false
  created_at       TIMESTAMP
  deleted_at       TIMESTAMP

cuentas
  id               UUID PK
  grupo_id         UUID FK → grupos
  nombre           VARCHAR(100)
  tipo             ENUM(efectivo, debito, credito, inversion,
                        ahorro, emergencias, fondo)
  banco            VARCHAR(100)
  moneda           CHAR(3) DEFAULT 'MXN'

  -- Saldos
  saldo_inicial    DECIMAL(14,2) DEFAULT 0
  saldo_actual     DECIMAL(14,2) DEFAULT 0   -- se actualiza en cada mov
  saldo_estimado   DECIMAL(14,2) DEFAULT 0   -- calculado con recordatorios futuros
  saldo_corte      DECIMAL(14,2)             -- snapshot al cerrar periodo

  -- Para tarjetas de crédito
  limite_credito   DECIMAL(14,2)
  dia_corte        TINYINT   -- día del mes
  dia_pago         TINYINT

  color            CHAR(7)
  icono            VARCHAR(50)
  incluir_en_total BOOLEAN DEFAULT true
  activa           BOOLEAN DEFAULT true
  orden            INT
  created_at       TIMESTAMP
  updated_at       TIMESTAMP
  deleted_at       TIMESTAMP

Presupuesto Estimado Mensual
Esta es la pieza clave para el panorama de "¿qué necesito para vivir?".
sqlpresupuesto_mensual
  id               UUID PK
  grupo_id         UUID FK → grupos
  nombre           VARCHAR(100)  -- "Base mensual 2025"
  activo           BOOLEAN DEFAULT true
  created_at       TIMESTAMP
  updated_at       TIMESTAMP

presupuesto_items
  id               UUID PK
  presupuesto_id   UUID FK → presupuesto_mensual
  concepto_id      UUID FK → conceptos   -- nullable
  nombre_libre     VARCHAR(150)          -- si no hay concepto
  categoria_id     UUID FK → categorias
  monto_estimado   DECIMAL(14,2)
  frecuencia       ENUM(mensual, bimestral, trimestral,
                        semestral, anual)
  monto_mensual    DECIMAL(14,2)   -- calculado: monto / factor frecuencia
  cuenta_id        UUID FK → cuentas  -- de qué cuenta sale
  es_fijo          BOOLEAN DEFAULT true
  notas            TEXT
  orden            INT
  activo           BOOLEAN DEFAULT true
  deleted_at       TIMESTAMP

Con esto puedes calcular: gasto_base_mensual = SUM(monto_mensual) de todos los items activos. Al recibir un ingreso, la app muestra: ingreso − gasto_base = excedente disponible.


Movimientos
sqlmovimientos
  id               UUID PK
  grupo_id         UUID FK → grupos
  usuario_id       UUID FK → usuarios   -- quien registró
  fecha            DATE
  hora             TIME
  concepto_id      UUID FK → conceptos  -- nullable
  descripcion      VARCHAR(255)         -- texto libre, útil para chatbot
  categoria_id     UUID FK → categorias
  tipo             ENUM(ingreso, gasto, transferencia, ahorro, ajuste)
  cuenta_origen_id UUID FK → cuentas
  cuenta_destino_id UUID FK → cuentas  -- solo transferencias
  monto            DECIMAL(14,2)
  moneda           CHAR(3) DEFAULT 'MXN'
  estatus          ENUM(confirmado, pendiente, cancelado)
  notas            TEXT
  comprobante_url  TEXT
  deuda_id         UUID FK → deudas     -- nullable, si es abono
  recordatorio_id  UUID FK → recordatorios  -- nullable, si vino de agenda
  fuente           ENUM(manual, chatbot, importacion, automatico)
  created_at       TIMESTAMP
  updated_at       TIMESTAMP
  deleted_at       TIMESTAMP

Cada INSERT/UPDATE en movimientos debe actualizar saldo_actual en la cuenta correspondiente. Esto lo manejas con un trigger o en la lógica de tu backend.


Cortes / Snapshots
Para detectar fugas, comparar estimado vs real.
sqlcortes_periodo
  id               UUID PK
  grupo_id         UUID FK → grupos
  cuenta_id        UUID FK → cuentas   -- nullable = corte global
  periodo          CHAR(7)             -- '2025-03' formato año-mes
  fecha_inicio     DATE
  fecha_fin        DATE
  saldo_inicio     DECIMAL(14,2)
  saldo_fin        DECIMAL(14,2)
  total_ingresos   DECIMAL(14,2)
  total_gastos     DECIMAL(14,2)
  total_ahorros    DECIMAL(14,2)
  total_transferencias DECIMAL(14,2)
  gasto_estimado   DECIMAL(14,2)   -- del presupuesto_mensual
  diferencia       DECIMAL(14,2)   -- real vs estimado → fuga detectada
  cerrado          BOOLEAN DEFAULT false
  created_at       TIMESTAMP

Deudas y Capacidad de Endeudamiento
sqldeudas
  id               UUID PK
  grupo_id         UUID FK → grupos
  cuenta_id        UUID FK → cuentas
  nombre           VARCHAR(150)
  tipo             ENUM(credito_revolvente, meses_sin_intereses,
                        prestamo_personal, hipoteca, automotriz, otro)
  monto_total      DECIMAL(14,2)
  monto_pagado     DECIMAL(14,2) DEFAULT 0
  saldo_pendiente  DECIMAL(14,2)
  tasa_interes     DECIMAL(5,2)
  num_pagos_total  INT
  num_pagos_hechos INT DEFAULT 0
  monto_pago       DECIMAL(14,2)   -- mensualidad
  fecha_inicio     DATE
  fecha_fin        DATE
  fecha_proximo_pago DATE
  dia_corte        TINYINT
  dia_pago         TINYINT
  estatus          ENUM(activa, liquidada, pausada, vencida)
  notas            TEXT
  created_at       TIMESTAMP
  updated_at       TIMESTAMP
  deleted_at       TIMESTAMP

-- Esta tabla te da el panorama de capacidad de endeudamiento
limites_deuda
  id               UUID PK
  grupo_id         UUID FK → grupos
  nombre           VARCHAR(100)   -- "Límite personal total"
  monto_limite     DECIMAL(14,2)  -- max que quiero deberme: 20k
  monto_comprometido DECIMAL(14,2) -- calculado: SUM de deudas activas
  monto_disponible DECIMAL(14,2)  -- limite - comprometido
  updated_at       TIMESTAMP

Agenda y Recordatorios
sqlrecordatorios
  id               UUID PK
  grupo_id         UUID FK → grupos
  nombre           VARCHAR(150)
  tipo             ENUM(pago_fijo, corte_tarjeta, anualidad,
                        vencimiento, deuda, custom)
  cuenta_id        UUID FK → cuentas     -- nullable
  deuda_id         UUID FK → deudas      -- nullable
  concepto_id      UUID FK → conceptos   -- nullable
  monto_estimado   DECIMAL(14,2)
  frecuencia       ENUM(unico, semanal, quincenal, mensual,
                        bimestral, trimestral, semestral, anual)
  dia_del_mes      TINYINT
  fecha_especifica DATE               -- para únicos o anualidades
  anticipacion_dias INT DEFAULT 3    -- avisar X días antes
  auto_crear_movimiento BOOLEAN DEFAULT false
  activo           BOOLEAN DEFAULT true
  proxima_fecha    DATE              -- calculada, para queries rápidos
  ultima_ejecucion_at TIMESTAMP
  created_at       TIMESTAMP
  deleted_at       TIMESTAMP

notificaciones
  id               UUID PK
  usuario_id       UUID FK → usuarios
  dispositivo_id   UUID FK → dispositivos  -- nullable
  recordatorio_id  UUID FK → recordatorios -- nullable
  titulo           VARCHAR(200)
  cuerpo           TEXT
  tipo             ENUM(recordatorio, alerta_saldo, corte,
                        deuda_vencida, fuga_detectada, ingreso)
  leida            BOOLEAN DEFAULT false
  accion_url       TEXT    -- deep link dentro de la app
  enviada_at       TIMESTAMP
  created_at       TIMESTAMP
```

---

### Relaciones clave resumidas
```
grupos ──< usuarios_grupos >── usuarios ──< dispositivos
  │
  ├──< cuentas
  │      └── saldo_actual (trigger desde movimientos)
  │      └── saldo_estimado (calculado desde recordatorios futuros)
  │
  ├──< categorias ──< conceptos
  ├──< presupuesto_mensual ──< presupuesto_items
  │
  ├──< movimientos >── cuentas (origen / destino)
  │      └── FK opcional → deudas, recordatorios
  │
  ├──< deudas ──< movimientos (abonos)
  ├──< limites_deuda
  ├──< recordatorios ──< notificaciones
  └──< cortes_periodo (snapshots por mes)

Flujo del dashboard que describes
Al abrir la app con el mes actual:

Corte activo → leer o generar cortes_periodo del mes
Por cuenta → saldo_actual directo, sin recalcular
Totales → total_ingresos / gastos / ahorros del corte
Panorama del mes → presupuesto_mensual activo → gasto_base_mensual
Excedente → ingresos del mes − gasto_base = lo que puedes destinar
Fuga → diferencia en el corte = estimado vs real
Próximos pagos → recordatorios ordenados por proxima_fecha
Capacidad deuda → limites_deuda.monto_disponible