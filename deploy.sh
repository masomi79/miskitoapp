#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
FRONTEND_DIR="$SCRIPT_DIR/miskitoapp-front"
BACKEND_DIR="$SCRIPT_DIR/miskitoapp-backend"

REMOTE_HOST="${REMOTE_HOST:-}"
REMOTE_USER="${REMOTE_USER:-$USER}"
REMOTE_FRONTEND_DIR="${REMOTE_FRONTEND_DIR:-/var/www/miskito/public}"
REMOTE_BACKEND_DIR="${REMOTE_BACKEND_DIR:-/home/upla/miskitoapp/miskitoapp-backend}"
REMOTE_PYTHON="${REMOTE_PYTHON:-python3}"
REMOTE_BACKEND_SERVICE="${REMOTE_BACKEND_SERVICE:-}"
REMOTE_APACHE_SERVICE="${REMOTE_APACHE_SERVICE:-apache2}"

if [[ -z "$REMOTE_HOST" ]]; then
  echo "Usage: REMOTE_HOST=example.com REMOTE_USER=deploy REMOTE_FRONTEND_DIR=/var/www/miskito/public REMOTE_BACKEND_DIR=/home/upla/miskitoapp/miskitoapp-backend bash deploy.sh"
  exit 1
fi

echo "[1/4] Building frontend"
(
  cd "$FRONTEND_DIR"
  npm run build
)

echo "[2/4] Uploading frontend build"
rsync -av --delete "$FRONTEND_DIR/dist/" "$REMOTE_USER@$REMOTE_HOST:$REMOTE_FRONTEND_DIR/"

echo "[3/4] Uploading backend code"
rsync -av --delete \
  --exclude '__pycache__' \
  --exclude '.venv' \
  --exclude 'venv' \
  "$BACKEND_DIR/" "$REMOTE_USER@$REMOTE_HOST:$REMOTE_BACKEND_DIR/"

echo "[4/4] Restarting services"
ssh "$REMOTE_USER@$REMOTE_HOST" "set -e; cd '$REMOTE_BACKEND_DIR'; $REMOTE_PYTHON -m pip install -r requirements.txt >/dev/null 2>&1 || true"

if [[ -n "$REMOTE_BACKEND_SERVICE" ]]; then
  ssh "$REMOTE_USER@$REMOTE_HOST" "sudo systemctl restart '$REMOTE_BACKEND_SERVICE'"
fi

ssh "$REMOTE_USER@$REMOTE_HOST" "sudo systemctl restart '$REMOTE_APACHE_SERVICE'"

echo "Deploy completed."
