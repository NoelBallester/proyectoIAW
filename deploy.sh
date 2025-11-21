#!/usr/bin/env bash
# deploy.sh - Despliega el proyecto al docroot de Apache
# Uso: sudo ./deploy.sh [--dry-run] [--fast] [--no-perms]
# Requisitos: sudo, rsync

set -euo pipefail
PROJECT_DIR="$(cd "$(dirname "$0")" && pwd)"
TARGET="/var/www/html/proyectoIAW"
DRY_RUN=0
FAST=0
SET_PERMS=1

for arg in "$@"; do
  case "$arg" in
    --dry-run) DRY_RUN=1 ;;
    --fast) FAST=1 ;;
    --no-perms) SET_PERMS=0 ;;
  esac
done

if [[ $EUID -ne 0 ]]; then
  echo "[ERROR] Ejecuta con sudo: sudo ./deploy.sh" >&2
  exit 1
fi

# Crear destino si no existe
if [[ ! -d "$TARGET" ]]; then
  echo "[INFO] Creando directorio destino $TARGET";
  mkdir -p "$TARGET";
fi

# Opciones base rsync
RSYNC_OPTS="-av --delete"
[[ $FAST -eq 1 ]] && RSYNC_OPTS="-a --delete"
[[ $DRY_RUN -eq 1 ]] && RSYNC_OPTS="$RSYNC_OPTS --dry-run"

sync_dir() {
  local src="$1" dest="$2"
  if [[ -d "$src" ]]; then
    echo "[SYNC] $src -> $dest"
    rsync $RSYNC_OPTS "$src/" "$dest/"
  fi
}

# Copiar carpetas principales
sync_dir "$PROJECT_DIR/app" "$TARGET/app"
sync_dir "$PROJECT_DIR/public" "$TARGET/public"
# Opcional: schema y README
cp -f "$PROJECT_DIR/README.md" "$TARGET/" 2>/dev/null || true
cp -f "$PROJECT_DIR/sql/schema.sql" "$TARGET/sql-schema.sql" 2>/dev/null || true

if [[ $SET_PERMS -eq 1 ]]; then
  echo "[PERMS] Ajustando ownership www-data:www-data"
  chown -R www-data:www-data "$TARGET"
  echo "[PERMS] Ajustando modos (dirs 775 / files 664)"
  find "$TARGET" -type d -exec chmod 775 {} +
  find "$TARGET" -type f -exec chmod 664 {} +
fi

echo "[OK] Despliegue completado"
[[ $DRY_RUN -eq 1 ]] && echo "(dry-run: no se copiaron cambios reales)"
exit 0
